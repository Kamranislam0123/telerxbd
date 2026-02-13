<?php

namespace Agora\AgoraDynamicKey;

class DynamicKey5
{
    const VERSION = "005";
    const NO_UPLOAD = "0";
    const AUDIO_VIDEO_UPLOAD = "3";

    // InChannelPermissionKey
    const ALLOW_UPLOAD_IN_CHANNEL = 1;

    // Service Type
    const MEDIA_CHANNEL_SERVICE = 1;
    const RECORDING_SERVICE = 2;
    const PUBLIC_SHARING_SERVICE = 3;
    const IN_CHANNEL_PERMISSION = 4;

    public static function generateRecordingKey($appId, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs)
    {
        return self::generateDynamicKey(
            $appId,
            $appCertificate,
            $channelName,
            $ts,
            $randomInt,
            $uid,
            $expiredTs,
            self::RECORDING_SERVICE,
            []
        );
    }

    public static function generateMediaChannelKey($appId, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs)
    {
        return self::generateDynamicKey(
            $appId,
            $appCertificate,
            $channelName,
            $ts,
            $randomInt,
            $uid,
            $expiredTs,
            self::MEDIA_CHANNEL_SERVICE,
            []
        );
    }

    public static function generateInChannelPermissionKey($appId, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs, $permission)
    {
        $extra[self::ALLOW_UPLOAD_IN_CHANNEL] = $permission;

        return self::generateDynamicKey(
            $appId,
            $appCertificate,
            $channelName,
            $ts,
            $randomInt,
            $uid,
            $expiredTs,
            self::IN_CHANNEL_PERMISSION,
            $extra
        );
    }

    public static function generateDynamicKey($appId, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs, $serviceType, $extra)
    {
        $signature = self::generateSignature(
            $serviceType,
            $appId,
            $appCertificate,
            $channelName,
            $uid,
            $ts,
            $randomInt,
            $expiredTs,
            $extra
        );

        $content = self::packContent($serviceType, $signature, hex2bin($appId), $ts, $randomInt, $expiredTs, $extra);

        return self::VERSION . base64_encode($content);
    }

    public static function generateSignature($serviceType, $appId, $appCertificate, $channelName, $uid, $ts, $salt, $expiredTs, $extra)
    {
        $rawAppID = hex2bin($appId);
        $rawAppCertificate = hex2bin($appCertificate);

        $buffer = pack("S", $serviceType);
        $buffer .= pack("S", strlen($rawAppID)) . $rawAppID;
        $buffer .= pack("I", $ts);
        $buffer .= pack("I", $salt);
        $buffer .= pack("S", strlen($channelName)) . $channelName;
        $buffer .= pack("I", $uid);
        $buffer .= pack("I", $expiredTs);

        $buffer .= pack("S", count($extra));
        foreach ($extra as $key => $value) {
            $buffer .= pack("S", $key);
            $buffer .= pack("S", strlen($value)) . $value;
        }

        return strtoupper(hash_hmac('sha1', $buffer, $rawAppCertificate));
    }

    public static function packString($value)
    {
        return pack("S", strlen($value)) . $value;
    }

    public static function packContent($serviceType, $signature, $appId, $ts, $salt, $expiredTs, $extra)
    {
        $buffer = pack("S", $serviceType);
        $buffer .= self::packString($signature);
        $buffer .= self::packString($appId);
        $buffer .= pack("I", $ts);
        $buffer .= pack("I", $salt);
        $buffer .= pack("I", $expiredTs);

        $buffer .= pack("S", count($extra));
        foreach ($extra as $key => $value) {
            $buffer .= pack("S", $key);
            $buffer .= self::packString($value);
        }

        return $buffer;
    }
}
