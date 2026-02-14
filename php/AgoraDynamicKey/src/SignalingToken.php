<?php

namespace Agora\AgoraDynamicKey;

class SignalingToken
{
    const SDK_VERSION = "1";

    public static function getToken($appid, $appcertificate, $account, $validTimeInSeconds)
    {
        $expiredTime = time() + $validTimeInSeconds;

        $token_items = array();
        array_push($token_items, self::SDK_VERSION);
        array_push($token_items, $appid);
        array_push($token_items, $expiredTime);
        array_push($token_items, md5($account . $appid . $appcertificate . $expiredTime));
        return join(":", $token_items);
    }
}
