<?php

    include("RtcTokenBuilder.php");

    $appID = $_GET['app_id'];
    $appCertificate = "2f58c46f0a864088887c6d5115f41457";
    $channelName = $_GET['channel_name'];
    $uid = (int)$_GET['uid'];

    $role = RtcTokenBuilder::RoleAttendee;
    $expireTimeInSeconds = 3600 * 24;
    $currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
    $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

    $token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);
    echo $token

    // $token = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $uidStr, $role, $privilegeExpiredTs);
    // echo 'Token with user account: ' . $token . PHP_EOL;

?>
