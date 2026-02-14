<?php

namespace Agora\AgoraDynamicKey;

class AccessToken
{
    private $appId;
    private $appCertificate;
    private $channelName;
    private $uid;

    /**
     * @var Message
     */
    private $message;

    // roles
    const ROLE_ATTENDEE   = 0;  // for communication
    const ROLE_PUBLISHER  = 1;  // for live broadcast
    const ROLE_SUBSCRIBER = 2;  // for live broadcast
    const ROLE_ADMIN      = 101;

    // privileges
    const PRIVILEGE_JOIN_CHANNEL                 = 1;
    const PRIVILEGE_PUBLISH_AUDIO_STREAM         = 2;
    const PRIVILEGE_PUBLISH_VIDEO_STREAM         = 3;
    const PRIVILEGE_PUBLISH_DATA_STREAM          = 4;
    const PRIVILEGE_PUBLISH_AUDIO_CDN            = 5;
    const PRIVILEGE_PUBLISH_VIDEO_CDN            = 6;
    const PRIVILEGE_REQUEST_PUBLISH_AUDIO_STREAM = 7;
    const PRIVILEGE_REQUEST_PUBLISH_VIDEO_STREAM = 8;
    const PRIVILEGE_REQUEST_PUBLISH_DATA_STREAM  = 9;
    const PRIVILEGE_INVITE_PUBLISH_AUDIO_STREAM  = 10;
    const PRIVILEGE_INVITE_PUBLISH_VIDEO_STREAM  = 11;
    const PRIVILEGE_INVITE_PUBLISH_DATA_STREAM   = 12;
    const PRIVILEGE_ADMINISTRATE_CHANNEL         = 101;
    const PRIVILEGE_RTM_LOGIN                    = 1000;

    private function __construct()
    {
        $this->message = new Message();
    }

    public function setUid($uid)
    {
        if ($uid === 0) {
            $this->uid = "";
        } else {
            $this->uid = $uid . '';
        }
    }

    /**
     * @param $appId
     * @param $appCertificate
     * @param $channelName
     * @param $uid
     *
     * @return static|null
     */
    public static function init($appId, $appCertificate, $channelName, $uid)
    {
        $accessToken = new static();

        $accessToken->throwExceptionIfEmptyString("appID", $appId);
        $accessToken->throwExceptionIfEmptyString("appCertificate", $appCertificate);
        $accessToken->throwExceptionIfEmptyString("channelName", $channelName);

        $accessToken->appId = $appId;
        $accessToken->appCertificate = $appCertificate;
        $accessToken->channelName = $channelName;

        $accessToken->setUid($uid);

        $accessToken->message = new Message();
        return $accessToken;
    }


    /**
     * @param $token
     * @param $appCertificate
     * @param $channelName
     * @param $uid
     *
     * @return null|static
     */
    public static function initWithToken($token, $appCertificate, $channelName, $uid)
    {
        $accessToken = new static();

        if (!$accessToken->extract($token, $appCertificate, $channelName, $uid)) {
            return null;
        }

        return $accessToken;
    }

    /**
     * @param $key
     * @param $expireTimestamp
     * @return $this
     */
    public function addPrivilege($key, $expireTimestamp)
    {
        $this->message->privileges[$key] = $expireTimestamp;

        return $this;
    }

    /**
     * @param $privilege
     * @return $this
     */
    public function removePrivilege($privilege)
    {
        unset($this->message->privileges[$privilege]);

        return $this;
    }

    /**
     * @param $salt
     */
    public function setSalt($salt)
    {
        $this->message->salt = $salt;
    }

    /**
     * @param int $ts
     */
    public function setTs($ts)
    {
        $this->message->ts = $ts;
    }

    /**
     * @return string
     */
    public function build()
    {
        $msg = $this->message->packContent();
        $val = array_merge(unpack("C*", $this->appId), unpack("C*", $this->channelName), unpack("C*", $this->uid), $msg);

        $sig = hash_hmac('sha256', implode(array_map("chr", $val)), $this->appCertificate, true);

        $crc_channel_name = crc32($this->channelName) & 0xffffffff;
        $crc_uid = crc32($this->uid) & 0xffffffff;

        $content = array_merge(unpack("C*", self::packString($sig)), unpack("C*", pack("V", $crc_channel_name)), unpack("C*", pack("V", $crc_uid)), unpack("C*", pack("v", count($msg))), $msg);
        $version = "006";
        $ret = $version . $this->appId . base64_encode(implode(array_map("chr", $content)));
        return $ret;
    }

    /**
     * @param string $name Property name.
     * @param string $str String value.
     *
     * @throws \InvalidArgumentException
     *   Throw this exception if $str value is empty string
     */
    private function throwExceptionIfEmptyString($name, $str)
    {
        if (is_string($str) && $str !== "") {
            return;
        }

        throw new \InvalidArgumentException($name . " check failed, should be a non-empty string");
    }

    /**
     * @param $token
     * @param $appCertificate
     * @param $channelName
     * @param $uid
     * @return bool
     */
    private function extract($token, $appCertificate, $channelName, $uid)
    {
        $ver_len = 3;
        $appid_len = 32;
        $version = substr($token, 0, $ver_len);
        if ($version !== "006") {
            echo 'invalid version '.$version;
            return false;
        }

        $this->throwExceptionIfEmptyString("token", $token);
        $this->throwExceptionIfEmptyString("appCertificate", $appCertificate);
        $this->throwExceptionIfEmptyString("channelName", $channelName);

        $appid = substr($token, $ver_len, $appid_len);
        $content = (base64_decode(substr($token, $ver_len + $appid_len, strlen($token) - ($ver_len + $appid_len))));

        $pos = 0;
        $len = unpack("v", $content.substr($pos, 2))[1];
        $pos += 2;
        $sig = substr($content, $pos, $len);
        $pos += $len;
        $crc_channel = unpack("V", substr($content, $pos, 4))[1];
        $pos += 4;
        $crc_uid = unpack("V", substr($content, $pos, 4))[1];
        $pos += 4;
        $msgLen = unpack("v", substr($content, $pos, 2))[1];
        $pos += 2;
        $msg = substr($content, $pos, $msgLen);

        $this->appId = $appid;
        $message = new Message();
        $message->unpackContent($msg);
        $this->message = $message;


        //non reversable values
        $this->appCertificate = $appCertificate;
        $this->channelName = $channelName;
        $this->setUid($uid);
        return true;
    }

    /**
     * @param $value
     * @return string
     */
    private static function packString($value)
    {
        return pack("v", strlen($value)) . $value;
    }
}
