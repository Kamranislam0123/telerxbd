<?php

namespace Agora\AgoraDynamicKey\Test;

use PHPUnit\Framework\TestCase;

class DynamicKeyTest extends TestCase
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
        $expected = '004e0c24ac56aae05229a6d9389860a1a0e25e56da8970ca35de60c44645bbae8a215061b3314464554720383bbf51446455471';

        $actual = \Agora\AgoraDynamicKey\DynamicKey4::generateRecordingKey(
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
        $expected = '004d0ec5ee3179c964fe7c0485c045541de6bff332b970ca35de60c44645bbae8a215061b3314464554720383bbf51446455471';

        $actual = \Agora\AgoraDynamicKey\DynamicKey4::generateMediaChannelKey(
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
}
