<?php

//====MOD hh
	
return 'rrr';
	//=====
if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	
	header( 'location: '. $modx->makeUrl( $auth ) );
	exit();
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
			if (unlink($_GET['abortDocs'])){
				echo 'true';
			}else{
				echo 'false';
			}
		}	
	}else{
		echo 'false';
	}
}






if( is_numeric( $_GET['addToCartId'] ) && is_numeric( $_GET['addToCartUid'] ) && is_numeric( $_GET['addToCartCount'] ) ){
	
	$userId = addslashes($_GET['addToCartId']);
	$itemId = addslashes($_GET['addToCartUid']);
	$count = addslashes($_GET['addToCartCount']);
	$timestamp = time();
	$sessid = session_id();
	$result= mysql_query( "SELECT  id FROM ".$modx->getFullTableName( '_shop_basket' )." WHERE id_user=".$userId."  AND id_item = ".$itemId." LIMIT 1" ) or die(mysql_error());
	if (mysql_num_rows($result ) == 1) {
		if ($resultUpdateCount= mysql_query( "UPDATE ".$modx->getFullTableName( '_shop_basket' )." SET count = {$count}, adddate = {$timestamp} WHERE id_user=".$userId."  AND id_item = ".$itemId." LIMIT 1" ) or die(mysql_error())) {
			echo 'Ok';
			exit();
		}else {
			echo 'Error';
			exit();
		}
		
	} else {
	 	if (mysql_query( "INSERT INTO  ".$modx->getFullTableName( '_shop_basket' )." (`id`, `id_user`,`id_item`,`count`,`adddate`,`sessid`) 
			VALUES (NULL , {$userId}, {$itemId}, {$count} , '{$timestamp}' , '{$sessid}')" ) or die(mysql_error())) {
			echo 'Ok';
			exit();
		}else {
			echo 'Error';
			exit();
		}
	}
	

}


exit();





?>