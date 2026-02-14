<?php
namespace Agora\AgoraDynamicKey\Test;

use Agora\AgoraDynamicKey\DynamicKey5;
use PHPUnit\Framework\TestCase;

class DynamicKey5Test extends TestCase
{
    private $appID = '970ca35de60c44645bbae8a215061b33';
    private $appCertificate = '5cfd2fd1755d40ecb72977518be15d3b';
    private $channelName = "7d72365eb983485397e3e3f9d460bdda";
    private $ts = 1446455472;
    private $randomInt = 58964981;
    private $uid = 2882341273;
    private $expiredTs = 1446455471;

    public function testRecordingKey()
    {
        $expected = "005AgAoADkyOUM5RTQ2MTg3QTAyMkJBQUIyNkI3QkYwMTg0MzhDNjc1Q0ZFMUEQAJcMo13mDERkW7roohUGGzOwKDdW9buDA68oN1YAAA==";
        $actual = DynamicKey5::generateRecordingKey(
            $this->appID,
            $this->appCertificate,
            $this->channelName,
            $this->ts,
            $this->randomInt,
            $this->uid,
            $this->expiredTs
        );

        $this->assertEquals($expected, $actual);
    }

    public function testMediaChannelKey()
    {
        $expected = "005AQAoAEJERTJDRDdFNkZDNkU0ODYxNkYxQTYwOUVFNTM1M0U5ODNCQjFDNDQQAJcMo13mDERkW7roohUGGzOwKDdW9buDA68oN1YAAA==";

        $actual = DynamicKey5::generateMediaChannelKey(
            $this->appID,
            $this->appCertificate,
            $this->channelName,
            $this->ts,
            $this->randomInt,
            $this->uid,
            $this->expiredTs
        );

        $this->assertEquals($expected, $actual);
    }

    public function testInChannelPermission()
    {
        $noUpload = "005BAAoADgyNEQxNDE4M0FGRDkyOEQ4REFFMUU1OTg5NTg2MzA3MTEyNjRGNzQQAJcMo13mDERkW7roohUGGzOwKDdW9buDA68oN1YBAAEAAQAw";
        $generatedNoUpload = DynamicKey5::generateInChannelPermissionKey(
            $this->appID,
            $this->appCertificate,
            $this->channelName,
            $this->ts,
            $this->randomInt,
            $this->uid,
            $this->expiredTs,
            DynamicKey5::NO_UPLOAD
        );

        $this->assertEquals($noUpload, $generatedNoUpload);

        $audioVideoUpload = "005BAAoADJERDA3QThENTE2NzJGNjQwMzY5NTFBNzE0QkI5NTc0N0Q1QjZGQjMQAJcMo13mDERkW7roohUGGzOwKDdW9buDA68oN1YBAAEAAQAz";
        $generatedAudioVideoUpload = DynamicKey5::generateInChannelPermissionKey(
            $this->appID,
            $this->appCertificate,
            $this->channelName,
            $this->ts,
            $this->randomInt,
            $this->uid,
            $this->expiredTs,
            DynamicKey5::AUDIO_VIDEO_UPLOAD
        );

        $this->assertEquals($audioVideoUpload, $generatedAudioVideoUpload);
    }
}
