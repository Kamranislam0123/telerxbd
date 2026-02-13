<?php

use Agora\AgoraDynamicKey\DynamicKey5;

$appID = '970ca35de60c44645bbae8a215061b33';
$appCertificate = '5cfd2fd1755d40ecb72977518be15d3b';
$channelName = "7d72365eb983485397e3e3f9d460bdda";
$ts = 1446455472;
$randomInt = 58964981;
$uid = 2882341273;
$expiredTs = 1446455471;

echo DynamicKey5::generateRecordingKey(
    $appID,
    $appCertificate,
    $channelName,
    $ts,
    $randomInt,
    $uid,
    $expiredTs
) . "\n";

echo DynamicKey5::generateMediaChannelKey(
    $appID,
    $appCertificate,
    $channelName,
    $ts,
    $randomInt,
    $uid,
    $expiredTs
) . "\n";

global $NO_UPLOAD;

echo DynamicKey5::generateInChannelPermissionKey(
    $appID,
    $appCertificate,
    $channelName,
    $ts,
    $randomInt,
    $uid,
    $expiredTs,
    DynamicKey5::NO_UPLOAD
) . "\n";

echo DynamicKey5::generateInChannelPermissionKey(
    $appID,
    $appCertificate,
    $channelName,
    $ts,
    $randomInt,
    $uid,
    $expiredTs,
    DynamicKey5::AUDIO_VIDEO_UPLOAD
) . "\n";
