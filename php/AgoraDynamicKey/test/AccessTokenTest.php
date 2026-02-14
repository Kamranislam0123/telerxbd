<?php

namespace Agora\AgoraDynamicKey\Test;

use Agora\AgoraDynamicKey\AccessToken;
use Agora\AgoraDynamicKey\SimpleTokenBuilder;
use PHPUnit\Framework\TestCase;

class AccessTokenTest extends TestCase
{
    private $appID = "970CA35de60c44645bbae8a215061b33";
    private $appCertificate = "5CFd2fd1755d40ecb72977518be15d3b";
    private $channelName = "7d72365eb983485397e3e3f9d460bdda";
    private $ts = 1111111;
    private $salt = 1;
    private $uid = "2882341273";
    private $expiredTs = 1446455471;

    public function testInitializeWithAppId()
    {
        $expected = "006970CA35de60c44645bbae8a215061b33IACV0fZUBw+72cVoL9eyGGh3Q6Poi8bgjwVLnyKSJyOXR7dIfRBXoFHlEAABAAAAR/QQAAEAAQCvKDdW";
        $builder = AccessToken::init($this->appID, $this->appCertificate, $this->channelName, $this->uid);
        $builder->setSalt($this->salt);
        $builder->setTs($this->ts);
        $builder->addPrivilege(AccessToken::PRIVILEGE_JOIN_CHANNEL, $this->expiredTs);
        $result = $builder->build();

        $this->assertEquals($expected, $result);
    }

    public function testInitializeWithToken()
    {
        $expected = "006970CA35de60c44645bbae8a215061b33IACV0fZUBw+72cVoL9eyGGh3Q6Poi8bgjwVLnyKSJyOXR7dIfRBXoFHlEAABAAAAR/QQAAEAAQCvKDdW";

        $builder2 = AccessToken::initWithToken($expected, $this->appCertificate, $this->channelName, $this->uid);
        $result2 = $builder2->build();

        $this->assertEquals($expected, $result2);
    }

    public function testInitializeWithAppIdAndStringZeroUid()
    {
        $expected = "006970CA35de60c44645bbae8a215061b33IABNRUO/126HmzFc+J8lQFfnkssUdUXqiePeE2WNZ7lyubdIfRAh39v0EAABAAAAR/QQAAEAAQCvKDdW";
        $builder = AccessToken::init($this->appID, $this->appCertificate, $this->channelName, "0");
        $builder->setSalt($this->salt);
        $builder->setTs($this->ts);
        $builder->addPrivilege(AccessToken::PRIVILEGE_JOIN_CHANNEL, $this->expiredTs);
        $result = $builder->build();

        $this->assertEquals($expected, $result);
    }

    public function testInitializeWithTokenAndStringZeroUid()
    {
        $expected = "006970CA35de60c44645bbae8a215061b33IABNRUO/126HmzFc+J8lQFfnkssUdUXqiePeE2WNZ7lyubdIfRAh39v0EAABAAAAR/QQAAEAAQCvKDdW";

        $builder2 = AccessToken::initWithToken($expected, $this->appCertificate, $this->channelName, "0");
        $result2 = $builder2->build();

        $this->assertEquals($expected, $result2);
    }

    public function testInitializeWithAppIdAndNumberZeroUid()
    {
        $expected = "006970CA35de60c44645bbae8a215061b33IACw1o7htY6ISdNRtku3p9tjTPi0jCKf9t49UHJhzCmL6bdIfRAAAAAAEAABAAAAR/QQAAEAAQCvKDdW";
        $builder = AccessToken::init($this->appID, $this->appCertificate, $this->channelName, 0);
        $builder->setSalt($this->salt);
        $builder->setTs($this->ts);
        $builder->addPrivilege(AccessToken::PRIVILEGE_JOIN_CHANNEL, $this->expiredTs);
        $result = $builder->build();

        $this->assertEquals($expected, $result);
    }

    public function testInitializeWithTokenAndNumberZeroUid()
    {
        $expected = "006970CA35de60c44645bbae8a215061b33IACw1o7htY6ISdNRtku3p9tjTPi0jCKf9t49UHJhzCmL6bdIfRAAAAAAEAABAAAAR/QQAAEAAQCvKDdW";

        $builder2 = AccessToken::initWithToken($expected, $this->appCertificate, $this->channelName, 0);
        $result2 = $builder2->build();

        $this->assertEquals($expected, $result2);
    }

    public function testInitializeWithSimpleTokenBuilder()
    {
        $expected = "006970CA35de60c44645bbae8a215061b33IACV0fZUBw+72cVoL9eyGGh3Q6Poi8bgjwVLnyKSJyOXR7dIfRBXoFHlEAABAAAAR/QQAAEAAQCvKDdW";

        $builder = new SimpleTokenBuilder($this->appID, $this->appCertificate, $this->channelName, $this->uid);
        $builder->setTs(1111111);
        $builder->setSalt(1);
        $builder->setUid(2882341273);
        $builder->setPrivilege(AccessToken::PRIVILEGE_JOIN_CHANNEL, $this->expiredTs);
        $result = $builder->buildToken();

        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInitializeWithInvalidValue()
    {
        AccessToken::init("", $this->appCertificate, $this->channelName, $this->uid);
    }
}
