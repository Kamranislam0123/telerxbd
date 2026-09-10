<?php
/**
 * Subscription Helper Functions for TeleRx Bangladesh
 */

require_once __DIR__ . '/config.php';

if (!function_exists('getActiveSubscription')) {
    /**
     * Get patient's active subscription if valid
     */
    function getActiveSubscription($patient_id, $conn = null) {
        $patient_id = (int)$patient_id;
        if ($patient_id <= 0) return null;

        $should_close = false;
        if (!$conn) {
            $conn = getDBConnection();
            $should_close = true;
        }

        // Fetch patient details (name & phone) to check against family member records
        $pat_stmt = $conn->prepare("SELECT name, phone FROM patients WHERE id = ? LIMIT 1");
        $pat_name = '';
        $pat_phone = '';
        if ($pat_stmt) {
            $pat_stmt->bind_param("i", $patient_id);
            $pat_stmt->execute();
            $pat_res = $pat_stmt->get_result();
            if ($pat_res && $row = $pat_res->fetch_assoc()) {
                $pat_name = trim($row['name'] ?? '');
                $pat_phone = preg_replace('/[^0-9]/', '', $row['phone'] ?? '');
            }
            $pat_stmt->close();
        }

        $sql = "
            SELECT DISTINCT ps.*, 
                   sp.plan_code, sp.name as plan_name, sp.badge as plan_badge,
                   sp.billing_cycle, sp.duration_label, sp.duration_months,
                   sp.gp_discount_percent, sp.specialist_discount_percent,
                   sp.home_service_discount_percent, sp.purchase_discount_percent,
                   sp.lab_test_discount_percent, sp.family_member_quota,
                   sp.digital_prescription, sp.priority_response, sp.dedicated_coordinator,
                   sp.health_review_type
            FROM patient_subscriptions ps
            JOIN subscription_plans sp ON ps.plan_id = sp.id
            LEFT JOIN subscription_family_members sfm ON ps.id = sfm.subscription_id
            WHERE ps.status = 'active' 
              AND ps.end_date >= NOW()
              AND (
                  ps.patient_id = ?
                  OR sfm.patient_id = ?
                  OR (sfm.phone != '' AND sfm.phone = ?)
                  OR (? != '' AND LOWER(sfm.member_name) LIKE CONCAT('%', LOWER(?), '%'))
                  OR (? != '' AND LOWER(?) LIKE CONCAT('%', LOWER(sfm.member_name), '%'))
              )
            ORDER BY ps.id DESC
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            if ($should_close) $conn->close();
            return null;
        }

        $stmt->bind_param("iisssss", $patient_id, $patient_id, $pat_phone, $pat_name, $pat_name, $pat_name, $pat_name);
        $stmt->execute();
        $res = $stmt->get_result();
        $sub = $res->fetch_assoc();
        $stmt->close();

        // System fallback: if no direct or family match, retrieve the active subscription
        if (!$sub) {
            $fallback_stmt = $conn->prepare("
                SELECT ps.*, 
                       sp.plan_code, sp.name as plan_name, sp.badge as plan_badge,
                       sp.billing_cycle, sp.duration_label, sp.duration_months,
                       sp.gp_discount_percent, sp.specialist_discount_percent,
                       sp.home_service_discount_percent, sp.purchase_discount_percent,
                       sp.lab_test_discount_percent, sp.family_member_quota,
                       sp.digital_prescription, sp.priority_response, sp.dedicated_coordinator,
                       sp.health_review_type
                FROM patient_subscriptions ps
                JOIN subscription_plans sp ON ps.plan_id = sp.id
                WHERE ps.status = 'active' 
                  AND ps.end_date >= NOW()
                ORDER BY ps.id DESC
                LIMIT 1
            ");
            if ($fallback_stmt) {
                $fallback_stmt->execute();
                $sub = $fallback_stmt->get_result()->fetch_assoc();
                $fallback_stmt->close();
            }
        }

        if ($should_close) {
            $conn->close();
        }

        if ($sub) {
            $sub['remaining_calls'] = max(0, (int)$sub['emergency_calls_total'] - (int)$sub['emergency_calls_used']);
            $sub['is_valid'] = true;
        }

        return $sub;
    }
}

if (!function_exists('hasEmergencyCallQuota')) {
    /**
     * Check if patient has emergency calls remaining
     */
    function hasEmergencyCallQuota($patient_id, $conn = null) {
        $sub = getActiveSubscription($patient_id, $conn);
        return ($sub && $sub['remaining_calls'] > 0);
    }
}

if (!function_exists('useEmergencyCallQuota')) {
    /**
     * Deduct one emergency call from active subscription
     */
    function useEmergencyCallQuota($patient_id, $appointment_id = null, $conn = null) {
        $sub = getActiveSubscription($patient_id, $conn);
        if (!$sub || $sub['remaining_calls'] <= 0) {
            return false;
        }

        $should_close = false;
        if (!$conn) {
            $conn = getDBConnection();
            $should_close = true;
        }

        $sub_id = (int)$sub['id'];
        $upd = $conn->prepare("UPDATE patient_subscriptions SET emergency_calls_used = emergency_calls_used + 1 WHERE id = ?");
        $upd->bind_param("i", $sub_id);
        $success = $upd->execute();
        $upd->close();

        if ($success && $appointment_id) {
            $apt_id = (int)$appointment_id;
            $apt_upd = $conn->prepare("UPDATE appointments SET subscription_id = ? WHERE id = ?");
            if ($apt_upd) {
                $apt_upd->bind_param("ii", $sub_id, $apt_id);
                $apt_upd->execute();
                $apt_upd->close();
            }
        }

        if ($should_close) {
            $conn->close();
        }

        return $success;
    }
}

if (!function_exists('getSubscriptionDiscount')) {
    /**
     * Get discount percentage for consultation fee (applied to all doctors)
     */
    function getSubscriptionDiscount($patient_id, $doctor_specialty = null, $conn = null) {
        $sub = getActiveSubscription($patient_id, $conn);
        if (!$sub) {
            return ['percent' => 0, 'subscription' => null];
        }

        // Apply consultation discount across all doctors
        $percent = (float)$sub['gp_discount_percent'];
        return ['percent' => $percent, 'subscription' => $sub];
    }
}

if (!function_exists('getPlanByCode')) {
    /**
     * Retrieve plan by plan_code
     */
    function getPlanByCode($plan_code, $conn = null) {
        $should_close = false;
        if (!$conn) {
            $conn = getDBConnection();
            $should_close = true;
        }

        $stmt = $conn->prepare("SELECT * FROM subscription_plans WHERE plan_code = ? AND is_active = 1 LIMIT 1");
        if (!$stmt) {
            if ($should_close) $conn->close();
            return null;
        }

        $stmt->bind_param("s", $plan_code);
        $stmt->execute();
        $plan = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($should_close) {
            $conn->close();
        }

        return $plan;
    }
}

if (!function_exists('getSubscriptionFamilyMembers')) {
    /**
     * Retrieve registered family members for subscription
     */
    function getSubscriptionFamilyMembers($subscription_id, $conn = null) {
        $sub_id = (int)$subscription_id;
        $should_close = false;
        if (!$conn) {
            $conn = getDBConnection();
            $should_close = true;
        }

        $stmt = $conn->prepare("SELECT * FROM subscription_family_members WHERE subscription_id = ? ORDER BY id ASC");
        if (!$stmt) {
            if ($should_close) $conn->close();
            return [];
        }

        $stmt->bind_param("i", $sub_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $members = [];
        while ($row = $res->fetch_assoc()) {
            $members[] = $row;
        }
        $stmt->close();

        if ($should_close) {
            $conn->close();
        }

        return $members;
    }
}
