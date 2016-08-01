<?php

if(  is_array($_SESSION[ 'webuserinfo' ][ 'info' ])  ){
	$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];		
} else {
	$webuserinfo['id'] = -1;
}


$id_user = $webuserinfo['id'];		
	


if ($event == 'check') {
	$t_stamp_s = time();
	$sessid = session_id();

	$sql = "SELECT * FROM ".$modx->getFullTableName( '_sms_log' )." WHERE `sessid` = '".$sessid."' AND `exp` = 1 AND `t_stamp_s` + 120 > ".$t_stamp_s;
	$result = mysql_query($sql) or die ("ERR: 6737 ".mysql_error());
	if ($result && mysql_num_rows($result) > 0 ){
		//return false;
		return 'no120';
	}
}





if ($event == 'checkStack') {
	$timestamp = time();
	$sessid = session_id();
	
	$sql = "SELECT * FROM ".$modx->getFullTableName( '_sms_send' )." WHERE `sessid` = '".$sessid."' AND `status` = 'notSended' AND timestamp+120 > ".$timestamp;
	$result = mysql_query($sql) or die ("ERR: 67457 ".mysql_error());
	if ($result && mysql_num_rows($result) > 0 ){
		//return false;
		return 'no120';
	}
}






if ($event == 'push') {
	$jsonObj = json_decode($obj);
	$t_stamp_s = time();
	$phone = $jsonObj->result->phone;
	$price = $jsonObj->result->price;
	$smsid = $jsonObj->result->sms_id;
	$error_code = $jsonObj->result->error;
	//$text = $text;
	$sessid = session_id();
	
	
	$arrCH = array (
	  'event' => 'check'
	);
	
	if ($modx->runSnippet( 'logSendedMessage', $arrCH )){
		//return false;
	}
	
	
	$sql = "INSERT INTO ".$modx->getFullTableName( '_sms_log' )." (
	`id`,
	`id_user`,
	`phone`,
	`price`,
	`smsid`,
	`t_stamp_s`,
	`t_stamp_d`,
	`error_code`,
	`message`,
	`sessid`
	) VALUES (
	NULL,
	".$id_user.",
	'".$phone."',
	'".$price."',
	'".$smsid."',
	'".$t_stamp_s."',
	'',
	'".$error_code."',
	'".$text."',
	'".$sessid."'
	) ";
	
	$result = mysql_query($sql) or die ("ERR: 25346 ".mysql_error());
	if ($result) return true;
}







if ($event == 'pushToStack') {


	
	$timestamp = time();
	//$mobile = $mobile;
	//$text = $text;
	$sessid = session_id();
	
	
	
	$sql = "INSERT INTO ".$modx->getFullTableName( '_sms_send' )." (
	`id`,
	`mobile`,
	`text`,
	`timestamp`,
	`status`,
	`stamp_sended`,
	`id_user`,
	`sessid`
	) VALUES (
	NULL,
	".$mobile.",
	'".$text."',
	'".$timestamp."',
	'notSended',
	'',
	'".$id_user."',
	'".$sessid."'
	) ";
	
	$result = mysql_query($sql) or die ("ERR: 3446 ".mysql_error());
	if ($result) return true;
}





?>