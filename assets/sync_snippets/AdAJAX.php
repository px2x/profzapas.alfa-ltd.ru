<?php

//return print_r($_SESSION);


//if($_SESSION['mgrShortname'] == 'admin' || $_SESSION['mgrShortname'] == 'manager') {

if(true) {
	
	//create cat
	if(!empty($_GET['enableiditem']) && is_numeric($_GET['enableiditem'])){
		$catalog = 8;
		$template = 4;


		$enableiditem = $_GET['enableiditem'];
		$sql = "SELECT * FROM  ".$modx->getFullTableName( '_request_new_path' )."  WHERE id_item = '{$enableiditem}' LIMIT 1";
		$result = mysql_query($sql) or die (mysql_error());
		//print_r( mysql_fetch_assoc($result));
		if ($data = mysql_fetch_assoc($result)) {
			//echo print_r($data);
			 $aliasCat  =  $modx->runSnippet( 'GenerAlias', array( 'txt' => $data[ 'category' ]));
			 $aliasSubCat  =  $modx->runSnippet( 'GenerAlias', array( 'txt' => $data[ 'subcategory' ]));


			 $sql = "SELECT id FROM  ".$modx->getFullTableName( 'site_content' )."  WHERE pagetitle = '".$data['category']."'  LIMIT 1";
			 $result = mysql_query($sql) or die (mysql_error());
			 if (mysql_num_rows($result) < 1) {

				 $curtime = time();
				 $sql = "INSERT INTO ".$modx->getFullTableName( 'site_content' )." (pagetitle, alias, published, parent, isfolder,template, introtext, content, createdby , createdon, editedon, publishedon, editedby, publishedby) 
																			VALUES ('".$data['category']."', '{$aliasCat}' , '1', '{$catalog}' , '1', '{$template}', ' ' , ' ',  '1' , '{$curtime}', '{$curtime}', '{$curtime}' , '1', '1')";
				 $result = mysql_query($sql) or die (mysql_error());


				 $sql = "SELECT id FROM  ".$modx->getFullTableName( 'site_content' )."  WHERE alias = '{$aliasCat}' LIMIT 1";
				 $result = mysql_query($sql) or die (mysql_error());
				 if ($idNewCat = mysql_fetch_assoc($result)['id']) {


				 }
			 } else {
			 	$idNewCat = mysql_fetch_assoc($result)['id'];
			 }
			//else {echo 'cat already exists';}
			
			 $sql = "SELECT id FROM  ".$modx->getFullTableName( 'site_content' )."  WHERE pagetitle = '".$data['subcategory']."' AND pagetitle = '".$data['category']."'  LIMIT 1";
			 $result = mysql_query($sql) or die (mysql_error());
			 if (mysql_num_rows($result) < 1) {

				 $sql = "INSERT INTO ".$modx->getFullTableName( 'site_content' )." (pagetitle, alias, published, parent, isfolder, template, introtext, content, createdby , createdon, editedon, publishedon, editedby, publishedby)
				VALUES ('".$data['subcategory']."', '{$aliasSubCat}' , '1', '{$idNewCat}' , '1', '{$template}' , ' ' , ' ',  '1' , '{$curtime}', '{$curtime}', '{$curtime}' , '1' , '1')";
					 $result = mysql_query($sql) or die (mysql_error());
					 $sql = "SELECT id FROM  ".$modx->getFullTableName( 'site_content' )."  WHERE alias = '{$aliasSubCat}' LIMIT 1";
					 $result = mysql_query($sql) or die (mysql_error());
					 if ($idNewSubCat = mysql_fetch_assoc($result)['id']) {
						 //echo 'category created';
						 $sql = "DELETE FROM  ".$modx->getFullTableName( '_request_new_path' )."  WHERE id_item = '{$enableiditem}' LIMIT 1";
						 $result = mysql_query($sql) or die (mysql_error());

						 $sql = "UPDATE  ".$modx->getFullTableName( '_catalog' )." SET parent = {$idNewSubCat}  WHERE id = '{$enableiditem}' LIMIT 1";
						 $result = mysql_query($sql) or die (mysql_error());

						 $arr = array('cat' => $data['category'], 'subcat' => $data['subcategory'], 'result' => 'OK', 'idItem' => $enableiditem);
						 echo json_encode($arr);
						 
						 	$modx->clearCache();
							include_once MODX_BASE_PATH . 'manager/processors/cache_sync.class.processor.php';
							$sync= new synccache();
							$sync->setCachepath( MODX_BASE_PATH . "assets/cache/" );
							$sync->setReport( false );
							$sync->emptyCache();
	

					 }
				 
			 } else {
			 	 echo 'cat already exists';
			 }
			
			
			
			
			
			
			
		}
	}	
	
	
	
	
	
	//getAnalog llist Analog
	if(!empty($_GET['getItemList']) && is_numeric($_GET['getItemList'])){
		$idcat = $_GET['getItemList'];
		$currentAnalog = $_GET['currentAnalog'];
		$result=mysql_query("SELECT id, title, price, manufacturer, code,currency, discount FROM  ".$modx->getFullTableName( '_catalog' )." WHERE parent = '{$idcat}' AND id <> '{$currentAnalog}'");
	
		if   (mysql_num_rows($result) > 0){
			$items = array();
			while($itm =  mysql_fetch_assoc($result)){
				$cats[] =  $itm;
			}
			 echo json_encode($cats);
		}else {
			echo "noFinded";
		}

	}
	

	
	
		
	//getAnalog llist to item
	if(!empty($_GET['getItemAnalogsList']) && is_numeric($_GET['getItemAnalogsList'])){
		$getItemAnalogsList = $_GET['getItemAnalogsList'];
		//$result=mysql_query("SELECT id_analog FROM  ".$modx->getFullTableName( '_catalog_analogs' )." WHERE id_item = '{$getItemAnalogsList}'");
		//echo  $getItemAnalogsList; 

			//while($idAnalog =  mysql_fetch_assoc($result)['id_analog']){
				$result2=mysql_query("SELECT cat2.id AS id, c_anal.id AS aid, cat2.title, cat2.price, cat2.manufacturer, cat2.code,cat2.currency, cat2.discount FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
										INNER JOIN ".$modx->getFullTableName( '_catalog_analogs' )." AS c_anal ON c_anal.id_item = cat.id
										INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat2 ON c_anal.id_analog = cat2.id
										WHERE cat.id = '{$getItemAnalogsList}'") or die (mysql_error());

				if   (mysql_num_rows($result2) > 0){
					$items = array();
					while($itm =  mysql_fetch_assoc($result2)){
						$cats[] =  $itm;
					}
					
				}	
			//}
			echo json_encode($cats);
			
			
			


	}
	
	
	
	
	
	
	
			
	//disableAnalog
	if(!empty($_GET['disableAnalogList']) && is_numeric($_GET['disableAnalogList'])){
		$disableAnalogList = $_GET['disableAnalogList'];
		//echo $disableAnalogList;
		$err = false;
		foreach ($_POST['sdata'] AS $elem){
			
			//$printtt.=$elem;
			
			if   (mysql_query("DELETE FROM  ".$modx->getFullTableName( '_catalog_analogs' )." WHERE id  = '{$elem}' ") or die (mysql_error())){
				//DELETE FROM `alfa-ltd-ru_profzapas`.`profzapas__catalog_analogs` WHERE `profzapas__catalog_analogs`.`id` = 45
			}else {
				$err = true;
			}
			
		}
		//echo $printtt;
		if ($err == true) {
			echo 'noDeleted';
		} else {
			echo 'Deletet';
		}


	}
	
	
	
	
	
	
	//setAnalogList
	if(!empty($_GET['setAnalogList']) && is_numeric($_GET['setAnalogList'])){
		//return  print_r($_POST);
		$currentID = $_GET['setAnalogList'];
		
		$err = false;
		foreach ($_POST['sdata'] AS $elem){
		
			$result=mysql_query("SELECT id FROM  ".$modx->getFullTableName( '_catalog' )." WHERE id  = '{$elem}'") or die (mysql_error());
			if   (mysql_num_rows($result) > 0){
				
				$sql2 = "SELECT id FROM  ".$modx->getFullTableName( '_catalog_analogs' )." WHERE id_item = {$currentID} AND id_analog = {$elem}";
				$result2=mysql_query($sql2) or die (mysql_error());
				if   (!mysql_num_rows($result2) > 0){
					$result=mysql_query("INSERT INTO ".$modx->getFullTableName( '_catalog_analogs' )." (`id`, `id_item`,`id_analog`) VALUES (NULL, '{$currentID}', '{$elem}' )") or die (mysql_error());
				//echo "analogAdded";
				}
			}else {
				$err = true;
			}
			
		}
		if ($err == true) {
			echo 'noAdded';
		} else {
			echo 'added';
		}
	}
		
	
	
	
	
		
	
	//getItemListInOrder
	if(isset($_GET['getItemListInOrder']) && is_numeric($_GET['numberIdOrder'])){

		$id_order = $_GET['numberIdOrder'];
		
		$itemListData = '';
		$itemInOrder = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getItemInOrder', 'order' => $id_order));
		$summsInOrder = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getSummItemInOrder', 'order' => $id_order));
		$itemListData = '<div class="inPopup_summOrder">Сумма заказа : <span>'.$modx->runSnippet( 'Price', array( 'price' => $summsInOrder['summ'], 'round' => 0 )).'<span class="rubl">a</span></span></div>
						 <div class="inPopup_summOrder NDS">Сумма заказа с НДС : <span>'.$modx->runSnippet( 'Price', array( 'price' => $summsInOrder['summWithNDS'], 'round' => 0 )).'<span class="rubl">a</span></span></div>';
	
		$itemListData .= '<div class="listItemsInOrder">';
		
		if (is_array($itemInOrder)){
			foreach ($itemInOrder AS $ioRow){
				
				if( $ioRow[ 'currency' ] != 'rub' ) $row[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$ioRow[ 'price' ], 'c'=>$ioRow[ 'currency' ] ) );
			
				$itemListData .= '
					<div class="oneItemLine">
						<div class="code">'.$ioRow['code'].'</div>
						<div class="title">'.$ioRow['title'].'</div>
						<div class="manuf">'.$ioRow['manufacturer'].'</div>
						<div class="price">'.($ioRow['price'] > $ioRow['buyprice'] ? '<span class="throughPrice">'.$modx->runSnippet( 'Price', array( 'price' => $ioRow['price'], 'round' => 0 )).'<span class="rubl">a</span></span><span class="normalPrice">'.$modx->runSnippet( 'Price', array( 'price' => $ioRow['buyprice'], 'round' => 0 )).'<span class="rubl">a</span></span>':'<span class="normalPrice">'.$modx->runSnippet( 'Price', array( 'price' => $ioRow['price'], 'round' => 0 )).'<span class="rubl">a</span></span>').'</div>
						<div class="count">'.$ioRow['buycount'].'</div>
				
					</div>
					<div class="clr"></div>';
			}
		}
		
		$itemListData .= '</div>';	
		return $itemListData;
		
		
	}
		
	
	
	
	


    
    //acceptResponse
	if(isset($_GET['acceptresponse']) && is_numeric($_POST['idresponse'])){

		$idresponse = $_POST['idresponse'];

		$sql = "UPDATE ".$modx->getFullTableName( '_responses' )." SET visible = 1 WHERE id = ".$idresponse;
        
        $result=mysql_query($sql) or die ("ERR 535 ".mysql_error());
        
        if ($result) {
            return "Ok";
            
        }else {
            return "Err query";  
        }
	}
		
	
    
        
    //blockresponse
	if(isset($_GET['blockresponse']) && is_numeric($_POST['idresponse'])){

		$idresponse = $_POST['idresponse'];

		$sql = "UPDATE ".$modx->getFullTableName( '_responses' )." SET visible = '-1' WHERE id = ".$idresponse;
        
        $result=mysql_query($sql) or die ("ERR 535 ".mysql_error());
        
        if ($result) {
            return "Ok";
            
        }else {
            return "Err query";  
        }
	}

	
	
	
	
	
	    //blockresponse
	if($_GET['type'] == 'setreadMsg' && is_numeric($_GET['did'])){

		$did = $_GET['did'];

		$sql = "UPDATE ".$modx->getFullTableName( '_dispute_msg' )." SET readstatus = 'read' WHERE id_dispute = ".$did." AND to_user = -1 ";
        
        $result=mysql_query($sql) or die ("ERR 453 ".mysql_error());
        
        if ($result) {
            return "Ok";
            
        }else {
            return "Err query";  
        }
	}

	
	
	
} else {echo 'acces denied'; }





?>