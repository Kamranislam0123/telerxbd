<?php

namespace Agora\AgoraDynamicKey;

class SimpleTokenBuilder
{
    private $token;

    private $rolePrivileges;

    /**
     * SimpleTokenBuilder constructor.
     * @param $appId
     * @param $appCertificate
     * @param $channelName
     * @param $uid
     */
    public function __construct($appId, $appCertificate, $channelName, $uid)
    {
        $this->token = AccessToken::init($appId, $appCertificate, $channelName, $uid);
        $this->init();
    }

    /**
     * @param $token
     * @param $appCertificate
     * @param $channel
     * @param $uid
     */
    public function initWithToken($token, $appCertificate, $channel, $uid)
    {
        $this->token = AccessToken::initWithToken($token, $appCertificate, $channel, $uid);
        $this->init();
    }

    /**
     * @param $role
     */
    public function initPrivilege($role)
    {
        $p = $this->rolePrivileges[$role];
        foreach ($p as $key => $value) {
            $this->setPrivilege($key, $value);
        }
    }

    /**
     * @param $salt
     */
    public function setSalt($salt)
    {
        $this->token->setSalt($salt);
    }

    /**
     * @param $ts
     */
    public function setTs($ts)
    {
        $this->token->setTs($ts);
    }

    /**
     * @param $uid
     */
    public function setUid($uid)
    {
        $this->token->setUid($uid);
    }

    /**
     * @param $privilege
     * @param $expireTimestamp
     */
    public function setPrivilege($privilege, $expireTimestamp)
    {
        $this->token->addPrivilege($privilege, $expireTimestamp);
    }

    /**
     * @param $privilege
     */
    public function removePrivilege($privilege)
    {
        $this->token->removePrivilege($privilege);
    }

    /**
     * @return string
     */
    public function buildToken()
    {
        return $this->token->build();
    }

    /**
     *
     */
    private function init()
    {
        $attendeePrivileges = [
            AccessToken::PRIVILEGE_JOIN_CHANNEL         => 0,
            AccessToken::PRIVILEGE_PUBLISH_AUDIO_STREAM => 0,
            AccessToken::PRIVILEGE_PUBLISH_VIDEO_STREAM => 0,
            AccessToken::PRIVILEGE_PUBLISH_DATA_STREAM  => 0,
        ];


        $publisherPrivileges = [
            AccessToken::PRIVILEGE_JOIN_CHANNEL         => 0,
            AccessToken::PRIVILEGE_PUBLISH_AUDIO_STREAM => 0,
            AccessToken::PRIVILEGE_PUBLISH_VIDEO_STREAM => 0,
            AccessToken::PRIVILEGE_PUBLISH_DATA_STREAM  => 0,
            AccessToken::PRIVILEGE_PUBLISH_AUDIO_CDN    => 0,
            AccessToken::PRIVILEGE_PUBLISH_VIDEO_CDN    => 0,
            AccessToken::PRIVILEGE_INVITE_PUBLISH_AUDIO_STREAM => 0,
            AccessToken::PRIVILEGE_INVITE_PUBLISH_VIDEO_STREAM => 0,
            AccessToken::PRIVILEGE_INVITE_PUBLISH_DATA_STREAM  => 0,
        ];

        $subscriberPrivileges = [
            AccessToken::PRIVILEGE_JOIN_CHANNEL                 => 0,
            AccessToken::PRIVILEGE_REQUEST_PUBLISH_AUDIO_STREAM => 0,
            AccessToken::PRIVILEGE_REQUEST_PUBLISH_VIDEO_STREAM => 0,
            AccessToken::PRIVILEGE_REQUEST_PUBLISH_DATA_STREAM  => 0,
        ];

        $adminPrivileges = [
            AccessToken::PRIVILEGE_JOIN_CHANNEL          => 0,
            AccessToken::PRIVILEGE_PUBLISH_AUDIO_STREAM  => 0,
            AccessToken::PRIVILEGE_PUBLISH_VIDEO_STREAM  => 0,
            AccessToken::PRIVILEGE_PUBLISH_DATA_STREAM   => 0,
            AccessToken::PRIVILEGE_ADMINISTRATE_CHANNEL  => 0,
        ];

        $this->rolePrivileges = [
            AccessToken::ROLE_ATTENDEE   => $attendeePrivileges,
            AccessToken::ROLE_PUBLISHER  => $publisherPrivileges,
            AccessToken::ROLE_SUBSCRIBER => $subscriberPrivileges,
            AccessToken::ROLE_ADMIN      => $adminPrivileges,
        ];
    }
}
