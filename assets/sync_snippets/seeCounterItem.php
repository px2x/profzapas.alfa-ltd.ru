<?php

$webuserinfo = $_SESSION[ 'webuserinfo' ][ 'info' ];

	if (is_numeric($idItem)){
		
		if ($type == 'search') {
			$sqType = 'find';
		}else {
			$sqType = 'see';
		}
		
		$timestamp = time();
		$userIp = $_SERVER['REMOTE_ADDR'];
		$userSession = session_id();
		if (is_numeric($webuserinfo[ 'id' ])){
			$idUser = $webuserinfo[ 'id' ];
		}else {
			$idUser=-1;
		}
		
			
		$sql ="SELECT t_stamp FROM ".$modx->getFullTableName( '_catalog_see_stat' )." 
			WHERE id_user =  ".$idUser." 
			AND type = 'see' 
			AND id_cat = ".$idItem."
			AND sessid = '".$userSession."'
			AND ip = '".$userIp."'
			AND t_stamp > '".($timestamp - 180)."'";
		$result = mysql_query($sql) or die ("ERR 8782 ".mysql_error());
		if (mysql_num_rows($result) > 0) return false;
		
		//	$stamp_tmp = mysql_fetch_assoc()
		
		$sql = "INSERT INTO ".$modx->getFullTableName( '_catalog_see_stat' )." (
		id,
		type,
		id_cat,
		t_stamp,
		sessid,
		ip,
		id_user
		) VALUES (
		NULL,
		'".$sqType."',
		".$idItem.",
		'".$timestamp."',
		'".$userSession."',
		'".$userIp."',
		".$idUser."	
		)   "; 
		
		$result = mysql_query($sql) or die ("ERR 4522 ".mysql_error());
	
	}





?>