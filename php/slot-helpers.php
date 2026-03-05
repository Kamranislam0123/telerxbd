<?php
/**
 * Helpers for doctor availability: convert between time ranges and 15-min slot lists.
 * Used by get-doctor-availability-ranges.php, save-doctor-availability-ranges.php, get-available-slots.php.
 */

/**
 * Expand ranges into 15-min slot_times (HH:MM).
 * @param array $ranges Array of ['start_time'=>'09:00','end_time'=>'12:00']
 * @return array Sorted unique slot_times
 */
function rangesToSlotTimes($ranges) {
    $slots = [];
    foreach ($ranges as $r) {
        $start = $r['start_time'] ?? '';
        $end   = $r['end_time'] ?? '';
        if ($start === '' || $end === '') continue;
        $parts = array_map('intval', explode(':', $start));
        $h = $parts[0] ?? 0;
        $m = $parts[1] ?? 0;
        $min = $h * 60 + $m;
        $endParts = array_map('intval', explode(':', $end));
        $endH = $endParts[0] ?? 0;
        $endM = $endParts[1] ?? 0;
        $endMin = $endH * 60 + $endM;
        if ($endMin <= $min && $endMin !== 0) $endMin = 24 * 60; // e.g. end 00:00 = midnight next
        while ($min < $endMin) {
            $slots[] = sprintf('%02d:%02d', (int)($min / 60) % 24, $min % 60);
            $min += 15;
        }
    }
    $slots = array_unique($slots);
    sort($slots);
    return array_values($slots);
}

/**
 * Collapse sorted 15-min slot_times into contiguous ranges.
 * @param array $slotTimes Array of 'HH:MM' (will be sorted)
 * @return array Array of ['start_time'=>'09:00','end_time'=>'12:00']
 */
function slotTimesToRanges($slotTimes) {
    $normalize = function ($t) {
        $t = trim($t);
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $t, $m)) return null;
        return sprintf('%02d:%02d', (int)$m[1], (int)$m[2]);
    };
    $slotTimes = array_filter(array_map($normalize, $slotTimes));
    $slotTimes = array_unique($slotTimes);
    usort($slotTimes, function ($a, $b) {
        $toMin = function ($t) {
            $p = array_map('intval', explode(':', $t));
            return ($p[0] ?? 0) * 60 + ($p[1] ?? 0);
        };
        return $toMin($a) - $toMin($b);
    });
    $ranges = [];
    $i = 0;
    while ($i < count($slotTimes)) {
        $start = $slotTimes[$i];
        $p = array_map('intval', explode(':', $start));
        $min = ($p[0] ?? 0) * 60 + ($p[1] ?? 0);
        $j = $i + 1;
        while ($j < count($slotTimes)) {
            $next = $slotTimes[$j];
            $np = array_map('intval', explode(':', $next));
            $nextMin = ($np[0] ?? 0) * 60 + ($np[1] ?? 0);
            if ($nextMin !== $min + 15) break;
            $min = $nextMin;
            $j++;
        }
        $endMin = $min + 15;
        $endH = (int)($endMin / 60) % 24;
        $endM = $endMin % 60;
        $end = sprintf('%02d:%02d', $endH, $endM);
        $ranges[] = ['start_time' => $start, 'end_time' => $end];
        $i = $j;
    }
    return $ranges;
}
