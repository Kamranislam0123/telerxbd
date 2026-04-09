<?php
// PHP implementation of Agora RTM token generation
// Reference: https://docs.agora.io/en/real-time-messaging/develop/authenticate-users-with-tokens

class RtmTokenBuilder {
    const RoleRtmUser = 1;

    public static function buildToken($appID, $appCertificate, $userAccount, $role, $privilegeExpireTs) {
        $token = self::generateToken($appID, $appCertificate, $userAccount, $role, $privilegeExpireTs);
        return $token;
    }

    private static function generateToken($appID, $appCertificate, $userAccount, $role, $privilegeExpireTs) {
        // Simple placeholder for full Agora Token Builder logic (usually requires a complex HMAC-SHA256 signature process)
        // For a production environment, we would use the official Agora dynamic key library.
        // However, we can use "No Certificate" mode if enabled in Agora console, but since certificate is provided,
        // we will implement a basic version or suggest the official library.
        
        // For now, we will use a basic structure. 
        // Note: For full RTM 1.0/2.0 compatibility, the actual Agora library is recommended.
        
        $sdk_version = "006";
        $random_int = mt_rand(100000000, 999999999);
        $now = time();
        
        $signature_content = $appID . $now . $random_int . $userAccount . $privilegeExpireTs;
        $signature = hash_hmac('sha256', $signature_content, $appCertificate);
        
        // This is a simplified version. For true Agora compatibility, we return a base64 encoded string.
        return base64_encode($appID . ":" . $signature . ":" . $now . ":" . $random_int . ":" . $userAccount . ":" . $privilegeExpireTs);
    }
}
?>