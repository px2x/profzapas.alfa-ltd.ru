<?php

//[!checkSMScode? &event=`check` &code=`43048`!]
	
	
	
	
if(  is_array($_SESSION[ 'webuserinfo' ][ 'info' ])  ){
	$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];		
} else {
	$webuserinfo['id'] = -1;
}

$sessid = session_id();
$id_user = $webuserinfo['id'];
$timestamp = time();	
	


if ($event == 'generate') {
	$randCode = rand(10000,99999);
	
	$sql = "SELECT * FROM ".$modx->getFullTableName( '_sms_accept_code' )." WHERE (`id_user` = '".$id_user."' OR `sessid` = '".$sessid."') AND exp = 1  AND timestamp+120 > ".$timestamp;
	$result = mysql_query($sql) or die ("ERR: 4534 ".mysql_error());
	if ($result && mysql_num_rows($result) > 0 ){
		return 'no120SecExp';
	}
	
	
	$sql = "INSERT INTO ".$modx->getFullTableName( '_sms_accept_code' )." (
	`id`,
	`id_user`,
	`code`,
	`timestamp`,
	`sessid`
	) VALUES (
	NULL,
	".$id_user.",
	'".$randCode."',
	'".$timestamp."',
	'".$sessid."'
	) ";
	
	$result = mysql_query($sql) or die ("ERR: 7373 ".mysql_error());
	if ($result) {
		return $randCode;
	}else {
		return false;
	}

}




if ($event == 'check' && is_numeric($code)) {

	$sql = "SELECT * FROM ".$modx->getFullTableName( '_sms_accept_code' )." WHERE (`id_user` = '".$id_user."' AND `sessid` = '".$sessid."') AND timestamp+86400 > '".$timestamp."' AND exp = 1 ORDER BY `timestamp` DESC LIMIT 1";
	$result = mysql_query($sql) or die ("ERR: 3767 ".mysql_error());
	if ($result && mysql_num_rows($result) > 0 ){
		if ($tmp = mysql_fetch_assoc($result)){
			if ($tmp['code'] == $code) {
				
				$sql = "UPDATE ".$modx->getFullTableName( '_sms_accept_code' )." SET exp = 0 WHERE `sessid` = '".$sessid."' AND  `id_user` = '".$id_user."' AND `code` = '".$code."' LIMIT 1 ";
				$result = mysql_query($sql) or die ("ERR: 5476 ".mysql_error());
				if ($result) {
					$sql2 = "UPDATE ".$modx->getFullTableName( '_sms_log' )." SET exp = 0 WHERE `sessid` = '".$sessid."' AND  `id_user` = '".$id_user."' ";
					$result2 = mysql_query($sql2) or die ("ERR: 6373 ".mysql_error());
					if ($result2) {
						return true;
					}else return false;
				}else return false;
				
			} else return false;		
		} else return false;	
	} else return false;	

}





?>