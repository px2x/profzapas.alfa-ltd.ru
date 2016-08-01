<?php

//====MOD 
	

	//=====
if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	
	header( 'location: '. $modx->makeUrl( $auth ) );
	exit();
}

if(  is_array($_SESSION[ 'webuserinfo' ][ 'info' ])  ){
	$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];	
	$userID = $webuserinfo['id'];
} else {
	$webuserinfo['id'] = -1;
	$userID = false;
}

	
if(!empty($_GET['query'])){
    $query = (string)$_GET['query'];
    $array = array();
	//$sql = "SELECT `name` FROM `profzapas__city` WHERE `name` LIKE '%". $query . "%' OR `name` LIKE '%". $query . "%' LIMIT 0, 10";
	$sql = "SELECT city.name AS city, region.name AS region FROM `profzapas__city` as city
			INNER JOIN `profzapas__region` as region on city.region_id = region.region_id
			WHERE city.name LIKE '%". $query . "%' OR city.name LIKE '%". $query . "%' LIMIT 0, 10";
	$result = mysql_query($sql) or die ();
    while($data = mysql_fetch_assoc($result)){
        $array[] = $data['city'].'||'.$data['region'];
    }
 
    echo "['".implode("','", $array)."']";
}


if(!empty($_GET['getTwoLevelCat']) && is_numeric($_GET['getTwoLevelCat'])){
    $onewLevelId =  addslashes($_GET['getTwoLevelCat']);
	$result= mysql_query( "SELECT id, pagetitle FROM ".$modx->getFullTableName( 'site_content' )." WHERE parent=". $onewLevelId ."   ORDER BY menuindex ASC" ) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)){
		$htmlcategoryOnelevel .='<option value="'.$row["id"].'">'.$row['pagetitle'].'</option>';
	}
    echo $htmlcategoryOnelevel;
}



 
if( isset( $_GET['uploadfiles'] ) ){
	$data = array();
    $error = false;
    $files = array();
    $uploaddir = './assets/documents/'.$_SESSION[ 'webuserinfo' ][ 'info' ]['id'].'/'; 
    if( ! is_dir( $uploaddir ) ) mkdir( $uploaddir, 0777 );
	
	$arrayZips = array("application/zip", 
					   "application/x-zip", 
					   "application/x-zip-compressed",
					   "application/msword",
					   "application/excel",
					   "application/vnd.ms-excel",
					   "application/x-excel",
					   "application/x-msexcel",
					   "application/vnd.ms-office",
					   "application/pdf",
					   "image/jpeg", 
					   "image/pjpeg",
					   "image/png",
					   "image/gif",
					   "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
					   "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
					  );
	$arrayExtensions = array(".docx", ".xlsx",".doc", ".xls" , ".pdf",".jpg",".jpeg",".png",".gif");
	
    foreach( $_FILES as $file ){
		//$original_extension = (false === $pos = strrpos($file['name'], '.')) ? '' : substr($file['name'], $pos);
		
		$original_extension =  '.'.end(explode(".", $file['name']));
		
		$targetName =  time().'_'.rand(100,999).$original_extension;
		
		$originalName = addslashes(basename($file['name']));
		
		
		//$finfo = new finfo($file['tmp_name'], FILEINFO_MIME);
		//$type = $finfo->file($file);
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$type = finfo_file($finfo, $file['tmp_name']) ;
		//echo finfo_file($finfo, $file['tmp_name']).'|'.$original_extension ;
		
		if (in_array($type, $arrayZips) && in_array($original_extension, $arrayExtensions)){
		    if( move_uploaded_file( $file['tmp_name'], $uploaddir.$targetName  )){
			   // $files[] = realpath( $uploaddir . $file['name'] );
				$files['link'][] = $uploaddir.$targetName ;
				$files['name'][] = $originalName ;
			}
			else{
				$error = true;
				$errorText='fileNotMoved';
			}
		}else {
			$error = true;
			$errorText='fileDenied';
		}
		
      
    }
    $data = $error ? array('error' => $errorText) : array('files' => $files );
    echo json_encode( $data );
}




if( isset( $_GET['uploadimgs'] ) ){
	$data = array();
    $error = false;
    $files = array();
 
    $uploaddir = './assets/productphoto/'.$_SESSION[ 'webuserinfo' ][ 'info' ]['id'].'/'; 
    if( ! is_dir( $uploaddir ) ) mkdir( $uploaddir, 0777 );
 
    $arrayZips = array("image/jpeg", 
					   "image/pjpeg",
					   "image/png",
					   "image/gif"
					  );
	$arrayExtensions = array(".jpg", ".jpeg", ".png", ".gif");
	
	
    foreach( $_FILES as $file ){
		$targetName =  time().'___'. basename($file['name']);
		
		

		$original_extension = (false === $pos = strrpos($file['name'], '.')) ? '' : substr($file['name'], $pos);
		//$finfo = new finfo($file['tmp_name'], FILEINFO_MIME);
		//$type = $finfo->file($file);
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$type = finfo_file($finfo, $file['tmp_name']) ;
		//echo finfo_file($finfo, $file['tmp_name']).'|'.$original_extension ;
		
		if (in_array($type, $arrayZips) && in_array($original_extension, $arrayExtensions)){
		    if( move_uploaded_file( $file['tmp_name'], $uploaddir.$targetName  )){
			   // $files[] = realpath( $uploaddir . $file['name'] );
				$files[] = $uploaddir.$targetName ;
			}
			else{
				$error = true;
				$errorText='imageNotMoved';
			}
		}else {
			$error = true;
			$errorText='imageDenied';
		}
    }
    $data = $error ? array('error' => $errorText) : array('files' => $files );
    echo json_encode( $data );
}




if( isset( $_GET['abortDocs'] ) ){
	$abortDocs = addslashes($_GET['abortDocs']);
	if (file_exists($_GET['abortDocs'])){
		$result= mysql_query( "SELECT  id FROM ".$modx->getFullTableName( '_user' )." WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ."  AND seller = 'y'" ) or die(mysql_error());
		if (mysql_num_rows($result ) == 1) {
			echo 'one num rows';
			$type = explode('/', $abortDocs)[2];
			$seller = explode('/', $abortDocs)[3];
			
			if ($seller == $_SESSION[ 'webuserinfo' ][ 'id' ] ){
				
				if ($type == 'documents') {
					$result= mysql_query( "DELETE FROM ".$modx->getFullTableName( '_catalog_docs' )." WHERE link='". $abortDocs."'" ) or die(mysql_error());
				}elseif ($type == 'productphoto') {
					$result= mysql_query( "DELETE FROM ".$modx->getFullTableName( '_catalog_images' )." WHERE  link='". $abortDocs."'" ) or die(mysql_error());
				}else echo 'error type';
				
			}else echo 'error Path';
			
			
			
			if (unlink($_GET['abortDocs'])){
				echo 'true';
			}else{
				echo 'false';
			}
		}	
	}else{
		echo 'fileNotExists';
	}
}





if (isset($_GET['type']) && $_GET['type'] == 'addToReqNewPrice') {
	if (is_numeric($_POST['addToReqNewPriceId'])  && is_numeric($_POST['addToReqNewPriceCount']) && is_numeric($_POST['addToReqNewPriceVal'])){
		
		$count = $_POST['addToReqNewPriceCount'];
		$newprice = $_POST['addToReqNewPriceVal'];
		$userId = $webuserinfo['id'];
		$itemId = $_POST['addToReqNewPriceId'];
		
		$timestamp = time();
		
		$sql = "SELECT seller FROM   ".$modx->getFullTableName( '_catalog' )." WHERE id = ".$itemId;
		$result = mysql_query($sql) or die("ERR 43563 ".mysql_error());
		if ($tmp = mysql_fetch_assoc($result)) {
			$selleId = $tmp['seller'];

		} 
		
		
		$sql = "INSERT INTO ".$modx->getFullTableName( '_request_price' )." (
		`id`,
		`id_user`,
		`id_item`,
		`request_price`,
		`count_item`,
		`response`,
		`date_req`,
		`seller`
		) VALUES (
		NULL,
		'".$userId."',
		'".$itemId."',
		'".$newprice."',
		'".$count."',
		'',
		'".$timestamp."',
		".$selleId."
		)";
		
		$result = mysql_query($sql) or die("ERR 784785 ".mysql_error());
		if ($result) {
			return '200';
		}else {
			return "ERR 08975289";
		}
		

	}else return "ERR 5461";

}




if (isset($_GET['type']) && $_GET['type'] == 'addToMyFavour') {
	if (is_numeric($_POST['addToMyFavourUid']) && is_numeric($_POST['addToMyFavourId']) ){
		
	
		$userId = $_POST['addToMyFavourUid'];
		$itemId = $_POST['addToMyFavourId'];
		
		$timestamp = time();
		
		$sql = "SELECT * FROM  ".$modx->getFullTableName( '_favorites' )." WHERE id_user = {$userId} AND id_item={$itemId}";
		$result = mysql_query($sql);
		if (mysql_num_rows($result)>0) {
			$result = mysql_query("DELETE FROM  ".$modx->getFullTableName( '_favorites' )." WHERE id_user = {$userId} AND id_item={$itemId}");
			if ($result) return '500';
		}
		
		$sql = "INSERT INTO ".$modx->getFullTableName( '_favorites' )." (
		`id`,
		`id_user`,
		`id_item`,
		`timestamp`
		) VALUES (
		NULL,
		'".$userId."',
		'".$itemId."',
		'".$timestamp."'
		)";
		
		$result = mysql_query($sql) or die("ERR 7384 ".mysql_error());
		if ($result) {
			return '200';
		}else {
			return "ERR 48437";
		}
		
 
	}else return "ERR 36756";

}





	//sendConfirmCode
	if(isset($_GET['sendConfirmCode'])){
	
		if ($userID !== false) {
			$sql = "SELECT mobile FROM  ".$modx->getFullTableName( '_user' )." WHERE id = {$userID}";
			$result = mysql_query($sql);
			if ($result && mysql_num_rows($result)>0) {
				if ($tmp = mysql_fetch_assoc($result)){
					$mobile = $tmp['mobile'];
				}
			}
		}

		//$mobile = preg_replace('/[^0-9]/', '', $mobile);
		if ($code = $modx->runSnippet( 'checkSMScode', array( 'event' => 'generate'))){
			//sendQuickSMS
			switch ($code) {
				case false:
					return 'err1';
					break;
				case "no120SecExp":
					return 'no120';
					break;
				default:
					if ($modx->runSnippet( 'sendQuickSMS', array( 'phone' => $mobile, 'text' => 'Код подтверждения: '.$code.'. Никому не сообщайте этот код.')) !== false){
						return 'ok';
					}else return 'err2';
			}
	
		}else return 'err3';
		
	}





	//checkConfirmCode
	if(isset($_GET['checkConfirmCode']) && is_numeric($_GET['code'])){
		if ($modx->runSnippet( 'checkSMScode', array( 'event' => 'check' , 'code' => $_GET['code']))){
			return 'yes';
		}else return 'no';
	}








	//checkPassswd
	if(isset($_GET['checkPassswd'])){
		
		if (isset($_POST['passwh'])){
			//return $_POST['passwh'];
			
			if ($userID !== false) {
				$sql = "SELECT password FROM  ".$modx->getFullTableName( '_user' )." WHERE id = {$userID}";
				$result = mysql_query($sql);
				if ($result && mysql_num_rows($result)>0) {
					if ($tmp = mysql_fetch_assoc($result)){
						$passs = $tmp['password'];
						
						//return md5($_POST['passwh']).' == '.$passs;
						if (md5($_POST['passwh']) == $passs){
							
							return 'yes';
						}
					}
				}
			}
		}
		
	//return $_POST['passwh'].' - '.$passs;
	return 'err';

		
	}





	//========================================RESPONSES START===================================

	if( $_GET['type'] == 'addresponse' ){
		
		if (is_numeric($_POST['id_order']) && is_numeric($_POST['id_item']) && is_numeric($_POST['rank']) && $_POST['text'] != '' ) {
			
			$text = addslashes($_POST['text']);
			$rank = $_POST['rank'];
			
			$sql = "INSERT INTO ".$modx->getFullTableName( '_responses' )." (
			id,
			id_cat_item,
			id_user,
			timest,
			visible,
			response,
			id_order,
            rank
			) VALUES (
			NULL,
			".$_POST['id_item'].",
			".$webuserinfo['id'].",
			'".(time())."',
			0,
			'".$text."',
			".$_POST['id_order'].",
            '".$rank."'
			)";
			
			$result = mysql_query($sql) or die("ERR 44562 ".mysql_error());
			
			return 'Ok';
		}
		
		return 'err';
		
		
	}



exit();





?>