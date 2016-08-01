<?php

$lk= 106;
$reg= 107;
$auth= 108;

$currentPage = $modx->documentIdentifier;

if($_SESSION['mgrShortname'] == 'admin' || $_SESSION['mgrShortname'] == 'manager') {
	
}else {
	
	if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ]  )
	{
		if ($currentPage != $auth) {
			header( 'location: '. $modx->makeUrl( $auth ) );
			exit();
		}else return false;
		
	}
	
	$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];


	$topage= ( $topage ? intval( $topage ) : $modx->documentIdentifier );
	$topage_url= $modx->makeUrl( $topage );
}








//getUserWritingListInbox/
if ($event == 'getUserWritingListInbox' && is_numeric($to)) {
	
	if ($dispute){
		$addOnSQL = ' AND `dispute` = 0 ';
	}else {
		$addOnSQL = ' ';
	}
	
	$sql = "SELECT msg.`from` AS `user`  ,msg.`date` , usr.firstname , usr.surname , usr.email , msg.`order_id`  FROM ".$modx->getFullTableName( '_messages' )." AS msg
			LEFT JOIN ".$modx->getFullTableName( '_user' )."  AS usr ON usr.id  =  msg.`from`
			WHERE `to` = {$to} ".$addOnSQL." GROUP BY msg.`from` ";
	$result = mysql_query($sql);
	if ($result && mysql_num_rows($result) > 0 ){
		$row = [];
		while ($tmp = mysql_fetch_assoc($result)){
			$row[] = $tmp;
		}
		if (count($row) > 0) {
			return $row;
		}else  {
			return false;
		}
		
	}else return false;

}


//getUserWritingListOutbox/
if ($event == 'getUserWritingListOutbox' && is_numeric($to)) {
	
	if ($dispute){
		$addOnSQL = ' AND `dispute` = 0 ';
	}else {
		$addOnSQL = ' ';
	}
	
	
	$sql = "SELECT msg.`to` AS `user`  , msg.`date` , usr.firstname , usr.surname , usr.email , msg.`order_id` FROM ".$modx->getFullTableName( '_messages' )." AS msg
			LEFT JOIN ".$modx->getFullTableName( '_user' )."  AS usr ON usr.id  =  msg.`to`
			WHERE `from` = {$to} ".$addOnSQL." GROUP BY msg.`to` ";
	$result = mysql_query($sql);
	if ($result && mysql_num_rows($result) > 0 ){
		$row = [];
		while ($tmp = mysql_fetch_assoc($result)){
			$row[] = $tmp;
		}
		if (count($row) > 0) {
			return $row;
		}else  {
			return false;
		}
		
	}else return false;

}

//getUserInfo/
if ($event == 'getUserInfo' && is_numeric($from)) {
	$sql = "SELECT usr.firstname , usr.surname , usr.email FROM ".$modx->getFullTableName( '_user' )." AS usr
			WHERE `id` = {$from} LIMIT 1";
	$result = mysql_query($sql);
	if ($result && mysql_num_rows($result) > 0 ){
		$row = [];
		if ($tmp = mysql_fetch_assoc($result)){
			$row = $tmp;
		}
		if (count($row) > 0) {
			return $row;
		}else  {
			return false;
		}
		
	}else return false;

}



//getCountMsgFromWriter/
if ($event == 'getCountMsgFromWriter' && is_numeric($to) && is_numeric($from)) {
	
	if ($dispute == true){
		$addOnSQL = ' AND `dispute` = 0 ';
	}else {
		$addOnSQL = ' AND `dispute` = -1 ';
	}
	
	$sql = "SELECT COUNT( id ) 
			FROM  ".$modx->getFullTableName( '_messages' )." 
			WHERE  `to` ={$to}
			AND  `from` ={$from}
			AND readstatus = 1 ".$addOnSQL;
	$result = mysql_query($sql);
	if ($result){
		if  ($row = mysql_fetch_row($result)[0]){
			return $row;
		}else return false;	
	}else return false;

}



//chechMsgFromAdmin/
if ($event == 'chechMsgFromAdmin' && is_numeric($to) ) {
	
	if ($dispute == true){
		$addOnSQL = ' AND `dispute` = 0 ';
	}else {
		$addOnSQL = ' AND `dispute` = -1 ';
	}
	
	$sql = "SELECT  id 
			FROM  ".$modx->getFullTableName( '_messages' )." 
			WHERE  `to` ={$to}
			AND  `from` =-1 ".$addOnSQL;
	$result = mysql_query($sql);
	if (mysql_num_rows($result) > 0){
		return true;	
	}else return false;

}



if ($event == 'getCountMsgDisp' && is_numeric($to) ) {
	if ($dispute == true){
		$addOnSQL = ' AND `dispute` = 0 ';
	}else {
		$addOnSQL = ' AND `dispute` = -1 ';
	}
	$sql = "SELECT COUNT( id ) 
			FROM  ".$modx->getFullTableName( '_messages' )." 
			WHERE  `to` ={$to}
			AND readstatus = 1 ".$addOnSQL;
	$result = mysql_query($sql);
	if ($result){
		if  ($row = mysql_fetch_row($result)[0]){
			return $row;
		}else return false;	
	}else return false;

}




if ($event == 'getCountMsgAll' && is_numeric($to) ) {
	$sql = "SELECT COUNT(*) 
			FROM  ".$modx->getFullTableName( '_messages' )." 
			WHERE  `to` ={$to}
			AND readstatus = 1 ";
	$result = mysql_query($sql);
	if ($result){
		if  ($row = mysql_fetch_row($result)[0]){
			return $row;
		}else return false;	
	}else return false;

}


//sendNewMessage/
if ($event == 'sendNewMessage' && $text != '' && is_numeric($from)) {
	$timestamp = time();
	
echo 'fgdfgdfg';
	$sql = "SELECT `date` FROM ".$modx->getFullTableName( '_messages' )." 
			WHERE `to` = -1 AND `text` = '{$text}' LIMIT 1";
	$result = mysql_query($sql) or die ("ERR 5645 ".mysql_error());
	if (mysql_num_rows($result) > 0){
		return false;		
	}
	echo 'fgdfgdfg2';
	$sql = "INSERT INTO  ".$modx->getFullTableName( '_messages' )."
			(
			`from`,
			`to`,
			`text`,
			`date`,
			`readstatus`,
			`dispute`,
			`order_id`
			)VALUES (
			".$from.",
			-1,
			'".$text."',
			'".$timestamp."',
			1,
			-1,
			-1
			)";
	$result = mysql_query($sql) or die ("ERR 5645 ".mysql_error());
	if ($result){
		return true;	
	}else return false;

}



//getMsgFrom
if ($event == 'getMsgFrom' && is_numeric($to) ) {
	$sql = "SELECT * FROM ".$modx->getFullTableName( '_messages' )." 
			WHERE ((`to` = {$to}  AND `from` = -1)
			OR (`to` = -1  AND `from` = {$to}))
			ORDER by id DESC
			LIMIT 200 ";
	$result = mysql_query($sql);
	if ($result && mysql_num_rows($result) > 0 ){
		$row = [];
		while ($tmp = mysql_fetch_assoc($result)){
			$row[] = $tmp;
		}
		if (count($row) > 0) {
			return $row;
		}else  {
			return false;
		}
		
	}else return false;

}



//setMsgReadStatus/
if ($event == 'setMsgReadStatus' && is_numeric($to)) {

	
	$sql = "UPDATE ".$modx->getFullTableName( '_messages' )." 
			SET `readstatus` = -1
			WHERE `to` = {$to}  AND `from` = -1 ";
	$result = mysql_query($sql);
	if ($result){
		return true;
	}else return false;

}






if ($event == 'in_multiarray' && isset($e) && isset($a)) {
	$t = sizeof( $a ) - 1;
	$b = 0;
	while($b <= $t){
		if( isset( $a[ $b ] ) ){
			if( $a[ $b ] == $e )
				return true;
			else
				if( is_array( $a[ $b ] ) )
					//if( in_multiarray( $e, ( $a[ $b ] ) ) )
					if( $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'in_multiarray', 'e' => $e , 'a' => $a[ $b ])) )
						return true;
		}
		$b++;
	}


}





?>