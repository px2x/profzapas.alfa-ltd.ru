<?php





if (is_numeric($_GET['enableiditem'])){
	$id= $_GET['enableiditem'];
	$sql = "SELECT * FROM  ".$modx->getFullTableName( '_request_new_path' )." AS cat";
				

}



?>