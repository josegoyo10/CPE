<?php
/* 
 * Copyright 2008 Cencosud S.A.
 * Original Author Frank D. Martinez M.
 */
function _trackhack() {
    $conn = UDataSource::GetConnection();
    $usrId       = $_SESSION['ses_usr_id'];
	$tracktime   = date("Y-m-d H:i:s", time());
    $remote_addr = $_SERVER['REMOTE_ADDR'];
    $referer     = $_SERVER['HTTP_REFERER'];
    $host        = $_SERVER['REMOTE_HOST'];
    $uid         = session_id();
    $requri      = $conn->qstr($_SERVER['REQUEST_URI']);
    $conn->Execute("insert into tracking values (
        '$usrId',
        '$uid',
        '$tracktime',
        '$remote_addr',
        '$host',
        '$referer',
        $requri)");
    // TODO: Must be moved to a cron job
    $trackhack_limit = date('Y-m-d 00:00:00', strtotime(TRACKHACK_DAYS . ' days'));
    $conn->Execute("delete from tracking where tra_tracktime <= '$trackhack_limit'");
}
_trackhack();
?>
