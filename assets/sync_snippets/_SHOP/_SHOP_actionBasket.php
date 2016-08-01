<?php

if ($type == '') {$type = addslashes($_GET['type']);}
if ($userId == '') {$userId = addslashes($_GET['userId']);}
if ($itemId == '') {$itemId = addslashes($_GET['itemId']);}




if ($type == '') return false;
$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];
	
/*
*
*
*
*/

if ($type == 'checkItem') {
	if (is_numeric ($webuserinfo['id'])) {
		$userId = $webuserinfo['id'];
		$sqlWhere = "`id_user` = ".$userId;
	}else {
		$userId = false;
		$sqlWhere = "`sessid` = '".session_id()."'";
	}
	
	if (is_numeric($itemId)) {
		$result = mysql_query ("SELECT id FROM ".$modx->getFullTableName( '_shop_basket' )." WHERE ".$sqlWhere." AND `id_item` = {$itemId} LIMIT 1");
		if (mysql_num_rows($result) > 0){
			return "finded";
		}else return "notfound";
		
	}else {
		return false;
	}
}


if ($type == 'printInfoInHead') {
	if (is_numeric ($webuserinfo['id'])) {
		$userId = $webuserinfo['id'];
		$sqlWhere = "`id_user` = ".$userId;
	}else {
		$userId = false;
		$sqlWhere = "`sessid` = '".session_id()."'";
	}
		
		$result = mysql_query ("SELECT COUNT(id) FROM ".$modx->getFullTableName( '_shop_basket' )." WHERE ".$sqlWhere." ");
		if ($count = mysql_fetch_row($result)[0]){
			$text = 'товаров';
			if ($count == 1 || $count % 10 == 1 || $count % 100 == 1 ) $text = 'товар';
			if (($count > 1 && $count <5) || ($count % 10 > 1 && $count % 10 < 5) || ($count % 100 %10 > 1 && $count % 100%10 < 5) ) $text = 'товара';
			
			$simmm = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getSummMyOrder', 'userId' => $userId));

			//return '<a href="'.$modx->makeUrl( 200 ).'"><div class="myCart">'.$count." ".$text."<br/>на сумму: ".$modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getSummMyOrder', 'userId' => $userId)).'</div></a>';
			
			$myBasketList = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyBasketList', 'userId' => $userId));
			
			
			if ($myBasketList) {
			$last = 1;
			$result_Page = '<table cellspacing="0" cellpadding="0" border="0" class="tableBasketInHead">';
			foreach ($myBasketList AS $row){

				//////////////////////////////////////////////
				
					$last = $last*-1;
		if( $row[ 'currency' ] != 'rub' ) $row[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$row[ 'price' ], 'c'=>$row[ 'currency' ] ));
		$row[ 'text' ]= $modx->runSnippet( 'str_replace', array( 'from'=>"\n", 'to'=>'<br />', 'txt'=>$row[ 'text' ] ) );
		
			$result_Page .= '<tr class="itmInHead_row '.( $last >0 ? 'grayBGinhead' : '' ).'">';
			$result_Page .= '<td class="itmInHead_col_code"><a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'id' ] .'/">'. $row[ 'code' ] .'</a></td>';
			$result_Page .= '<td class="itmInHead_col_name">'. $row[ 'title' ].'</td>';
		

		//personalDiscount
			$sqlPDiscount = "SELECT p_discount FROM  ".$modx->getFullTableName( '_personal_discount' )." WHERE id_user =  '".$webuserinfo[ 'id' ]."'  LIMIT 1";
			$resultPDiscount = mysql_query($sqlPDiscount) or die ('Error 43432');
			if  ($row_pd = mysql_fetch_assoc($resultPDiscount)['p_discount']){
				$pagerow_pd = $row_pd;
			} else {
				$pagerow_pd = 0;
			}	

			if ($pagerow_pd > $row[ 'discount' ]){
				$row[ 'discount' ] = $pagerow_pd;
			}
			//
			$accepted_price= 0;
			if ($row['accepted_price'] > 0 &&  $row['accepted_price'] < $row['price']) {
				$accepted_price = $row['accepted_price'] ;
			}
		//personalDiscountERR
			
				
		//agregatePrice
			if ($row[ 'discount' ] > 0 ){
				if ($accepted_price >0 ) {
					$oneprice =  $accepted_price;
					$countContrors = 'disabled';
				}else {
					$oneprice =  round(($row['price'] - ($row['price'] / 100 * $row['discount'])));
				}

				$result_Page .= '<td class="itmInHead_col_price">
				<div class="oldprice"><nobr><span class="throughprice">'. $modx->runSnippet( 'Price', array( 'price' => $row[ 'price' ], 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></div>
				<div class="newprice"><nobr><span class="newprice">'. $modx->runSnippet( 'Price', array( 'price' => $oneprice, 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></div>
				</td>';
			}else  {
				if ($accepted_price >0 ) {
					$oneprice =  $accepted_price;
					$countContrors = 'disabled';
				}else {
					$oneprice = $row[ 'price' ];
				}
				$result_Page .= '<td class="itmInHead_col_price"><nobr><span class="price">'. $modx->runSnippet( 'Price', array( 'price' => $oneprice, 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></td>';
			}
		//agregatePrice
			
		$result_Page .= '<td class="itmInHead_col_count"> x'. $row['count'] .'</td>';
				
			$result_Page .= '<td class="itmInHead_col_summ"  id="sumCountPrice_'.$row['id'].'"><span class="sumPrice">'. $modx->runSnippet( 'Price', array( 'price' => $oneprice * $row['count'] , 'round' => 0 ) ) .'</span> <span class="rubl">a</span></td>';
		$result_Page .= '';
		$result_Page .= '</tr>';
		
				///////////////////////////////////////////////////
			}
			$result_Page .= '</table>';
			}
				
			return '
			<div class="myCartInHead">
				<div class="myCartInHead_Notice">'.$count.'</div>
				<div class="myCartInHead_itemsList">
					<div class="myCartInHead_strUp"></div>
					<div class="myCartInHead_bordWr">
						<div class="myCartInHead_tit">В Вашей корзане '.$count.' '.$text.' на сумму '.$simmm.'</div>
						'.$result_Page.'
						<a class="linkDooOrder" href="'.$modx->makeUrl( 200 ).'">Оформить заказ</a>
					</div>
				</div>
			</div>';
			
		}else return '
			<div class="myCartInHead">
				
				<div class="myCartInHead_itemsList">
					<div class="myCartInHead_strUp"></div>
					<div class="myCartInHead_bordWr">
						<div class="myCartInHead_tit_cent">Ваша корзина пуста!</div>
					</div>
				</div>
			</div>';
		

}




if( $type == "addToCart" && is_numeric( $_GET['addToCartId'] ) && is_numeric( $_GET['addToCartCount'])){
	
	if (!is_numeric( $_GET['addToCartUid'])){
		$userId = -1;
	}else {
		$userId = addslashes($_GET['addToCartUid']);
	}
	$itemId = addslashes($_GET['addToCartId']);
	$count = addslashes($_GET['addToCartCount']);
	$timestamp = time();
	$sessid = session_id();
	$result= mysql_query( "SELECT  id FROM ".$modx->getFullTableName( '_shop_basket' )." WHERE id_user=".$userId."  AND id_item = ".$itemId." LIMIT 1" ) or die(mysql_error());
	if (mysql_num_rows($result ) == 1) {
		if ($resultUpdateCount= mysql_query( "UPDATE ".$modx->getFullTableName( '_shop_basket' )." SET `count` = {$count}, adddate = {$timestamp} WHERE id_user=".$userId."  AND id_item = ".$itemId." LIMIT 1" ) or die(mysql_error())) {
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



//addToCartNotGET

if( $type == "addToCartNotGET" && is_numeric( $addToCartId ) && is_numeric($addToCartCount)){
	
	if ($acc_price <= 0 ) {
		return false;
	}
	
	$itemId = $addToCartId;
	$count = $addToCartCount;
	$timestamp = time();
	$sessid = session_id();
	$result= mysql_query( "SELECT  id FROM ".$modx->getFullTableName( '_shop_basket' )." WHERE id_user=".$userId."  AND id_item = ".$itemId." LIMIT 1" ) or die("ERR 53463 ".mysql_error());
	if (mysql_num_rows($result ) == 1) {
		if ($resultUpdateCount= mysql_query( "UPDATE ".$modx->getFullTableName( '_shop_basket' )." SET `count` = {$count}, adddate = {$timestamp}, 	accepted_price = '{$acc_price}' WHERE id_user=".$userId."  AND id_item = ".$itemId." LIMIT 1" ) or die("ERR 98578438 ".mysql_error())) {
			return true;
			exit();
		}else {
			return false;
			exit();
		}
		
	} else {
	 	if (mysql_query( "INSERT INTO  ".$modx->getFullTableName( '_shop_basket' )." (`id`, `id_user`,`id_item`,`count`,`adddate`,`sessid` ,`accepted_price`) 
			VALUES (NULL , {$userId}, {$itemId}, {$count} , '{$timestamp}' , '{$sessid}' , '{$acc_price}')" ) or die("ERR 2627828 ".mysql_error())) {
			return true;
			exit();
		}else {
			return false;
			exit();
		}
	}
}


if( $type == "getMyBasketList"){
	if (is_numeric($userId)){
		$sqlWhere = "`id_user` = ".$userId;
	}else {
		$sqlWhere = "`sessid` = '".session_id()."'";
	}
	$result= mysql_query( "SELECT basket.id as bid, basket.id_item, basket.accepted_price, cat.code, cat.price, cat.currency, cat.in_stock, cat.parent, cat.id, cat.title, cat.text, cat.manufacturer, cat.manufacturer_country, cat.discount, basket.`count`
						FROM ".$modx->getFullTableName( '_shop_basket' )."  AS basket
						INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = basket.id_item
						WHERE ".$sqlWhere." " ) or die(mysql_error());
	if (mysql_num_rows($result ) > 0) {
		$row = [];
		while 	($rowtmp = mysql_fetch_assoc($result)){
			$row[] = $rowtmp;
		}
		return $row;
	} else return false;
}


if( $type == "getMyBasketSellersList"){
	if (is_numeric($userId)){
		$sqlWhere = "`basket`.`id_user` = ".$userId;
	}else {
		$sqlWhere = "`basket`.`sessid` = '".session_id()."'";
	}

	
	$result= mysql_query( "SELECT cat.seller
						FROM ".$modx->getFullTableName( '_shop_basket' )."  AS basket
						INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = basket.id_item
						WHERE ".$sqlWhere." GROUP BY cat.seller " ) or die(mysql_error());
	if (mysql_num_rows($result ) > 0) {
		$row = [];
		while 	($rowtmp = mysql_fetch_assoc($result)){
			$row[] = $rowtmp['seller'];
		}
		return $row;
	} else return false;
}


if( $type == "getMyBasketListWitnDiscount"){
	if (is_numeric($userId)){
		$sqlWhere = "`id_user` = ".$userId;
	}else {
		$sqlWhere = "`sessid` = '".session_id()."'";
	}
	//personalDiscount
	$sqlPDiscount = "SELECT p_discount FROM  ".$modx->getFullTableName( '_personal_discount' )." WHERE id_user =  '".$userId."'  LIMIT 1";
	$resultPDiscount = mysql_query($sqlPDiscount) or die ('Error 6576');
	if  ($row_pd = mysql_fetch_assoc($resultPDiscount)['p_discount']){
		$personalDiscount = $row_pd;
	}
	//
	
	
	$result= mysql_query( "SELECT basket.id as bid, basket.id_item, basket.accepted_price, cat.code, cat.price, cat.currency, cat.in_stock, cat.parent, cat.id, cat.title, cat.text, cat.manufacturer, cat.manufacturer_country, cat.discount, basket.`count` 
						FROM ".$modx->getFullTableName( '_shop_basket' )."  AS basket
						INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = basket.id_item
						WHERE ".$sqlWhere." AND cat.seller = {$sellerId} " ) or die('Error 45363'.mysql_error());
	if (mysql_num_rows($result ) > 0) {
		$row = [];
		while 	($rowtmp = mysql_fetch_assoc($result)){
			
			
			
			
			
			if ($rowtmp['discount'] < $personalDiscount){
				$rowtmp['discount'] = $personalDiscount;
			}
			
			$accepted_price= 0;
			
			if( $rowtmp[ 'currency' ] != 'rub' ) $priceInRUR= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$rowtmp[ 'price' ], 'c'=>$rowtmp[ 'currency' ]));
			
			if ($rowtmp['accepted_price'] > 0 &&  $rowtmp['accepted_price'] < $priceInRUR) {
				$rowtmp['price'] = $rowtmp['accepted_price'] ;
				$rowtmp[ 'currency' ] = 'rub';
				$rowtmp['discount'] = 0;
			}
			
			if( $rowtmp[ 'currency' ] != 'rub' ) $rowtmp[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$rowtmp[ 'price' ], 'c'=>$rowtmp[ 'currency' ] ));
			$rowtmp['priceWithDiscount'] = round(($rowtmp['price'] - ($rowtmp['price'] / 100 * $rowtmp['discount'])));
			$row[] = $rowtmp;
		}
		return $row;
	} else return false;
}



/*
if( $type == "getMyOrdersList"){
	if (isset($list)){
		if ($list == 'wait_check') $sqlType = "status = 'calculateShipment' AND date_end = '' ";
		if ($list == 'wait_coins') $sqlType = "status = 'waitPayment' AND date_end = ''";
		if ($list == 'wait_shipment') $sqlType = "(status = 'waitShipment' OR status = 'inShipment' ) AND date_end = ''";
		if ($list == 'wait_testing') $sqlType = "status = 'aborted'   OR status = 'ended' OR status = 'waitTesting' OR date_end <>  '' ";
	}
	$result= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_orders' )."
						WHERE `user_id` = '".$webuserinfo['id']."' AND ".$sqlType."  LIMIT 100" ) or die(mysql_error());
	if (mysql_num_rows($result ) > 0) {
		$row = [];
		while 	($rowtmp = mysql_fetch_assoc($result)){
			$row[] = $rowtmp;
		}
		return $row;
	} else return false;
}
*/

if( $type == "getMyOrdersList"){
	if (isset($list)){
		if ($list == 'wait_check') $sqlType = "status = 'calculateShipment' AND date_end = '' ";
		if ($list == 'wait_coins') $sqlType = "status = 'waitPayment' AND date_end = ''";
		if ($list == 'wait_shipment') $sqlType = "(status = 'waitShipment' OR status = 'inShipment' ) AND date_end = ''";
		if ($list == 'wait_testing') $sqlType = "(status = 'aborted'   OR status = 'ended' OR status = 'waitTesting' OR date_end <>  '') ";
	}
//	$result= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_orders' )."
//						WHERE `user_id` = '".$webuserinfo['id']."' AND ".$sqlType."  LIMIT 100" ) or die(mysql_error());
//    
    
  	$result= mysql_query( "SELECT ord.*, uwh.address, uwh.city FROM ".$modx->getFullTableName( '_orders' )." AS ord
                        LEFT JOIN ".$modx->getFullTableName( '_orders_fk_warehouse' )." AS fkwh ON ord.id = fkwh.id_order
                        LEFT JOIN ".$modx->getFullTableName( '_user_warehouse' )." AS uwh ON uwh.id = fkwh.id_wh
						WHERE `user_id` = '".$webuserinfo['id']."' AND ".$sqlType."  LIMIT 100" ) or die(mysql_error());
    
    
    
	if (mysql_num_rows($result ) > 0) {
		$row = [];
		while 	($rowtmp = mysql_fetch_assoc($result)){
			$row[] = $rowtmp; 
		}
		return $row;
	} else return false;
}

 

//getMyOrdersDispute
if( $type == "getMyOrdersDispute"){

	$result= mysql_query( "SELECT  ord.*, uwh.address, uwh.city, dl.status AS dlstatus FROM ".$modx->getFullTableName( '_orders' )." AS ord
						INNER JOIN  ".$modx->getFullTableName( '_dispute_list' )." AS dl ON ord.id = dl.order_id
                        LEFT JOIN ".$modx->getFullTableName( '_orders_fk_warehouse' )." AS fkwh ON ord.id = fkwh.id_order
                        LEFT JOIN ".$modx->getFullTableName( '_user_warehouse' )." AS uwh ON uwh.id = fkwh.id_wh
						WHERE ord.`user_id` = '".$webuserinfo['id']."'
						 LIMIT 100" ) or die(mysql_error());
    
                        //AND dl.status = 'opened' LIMIT 100" ) or die(mysql_error());
    
    
	if (mysql_num_rows($result ) > 0) {
		$row = [];
		while 	($rowtmp = mysql_fetch_assoc($result)){
			$row[] = $rowtmp;
		}
		return $row;
	} else return false;
}

	





if( $type == "getMyOrdersItemsList"){
	if (isset($idorder)){
		$result= mysql_query( "SELECT ord.id_item,  ord.price , ord.`count` ,cat.title , user.email , cat.parent , cat.code
								FROM ".$modx->getFullTableName( '_order_items' )." AS ord
								INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id =  ord.id_item
								INNER JOIN ".$modx->getFullTableName( '_user' )." AS user ON cat.seller =  user.id
								WHERE ord.id_user = '".$webuserinfo['id']."' AND ord.id_order = '".$idorder."'  LIMIT 200" ) or die(mysql_error());
		if (mysql_num_rows($result ) > 0) {
			$row = [];
			while 	($rowtmp = mysql_fetch_assoc($result)){
				$row[] = $rowtmp;
			}
			return $row;
		} else return false;
	}else return false;

}





	
if( $type == "rechangeCountItem"){
	if (is_numeric ($webuserinfo['id'])) {
		$userId = $webuserinfo['id'];
		$sqlWhere = "`id_user` = ".$userId;
	}else {
		$userId = false;
		$sqlWhere = "`sessid` = '".session_id()."'";
	}
	
	if (is_numeric($_GET['itemId']) && is_numeric($_GET['newCount'])) {
		$timestamp = time();
		$itemId = addslashes($_GET['itemId']);
		$newCount = addslashes($_GET['newCount']);

	$sql = "SELECT accepted_price FROM ".$modx->getFullTableName( '_shop_basket' )." WHERE id_user=".$webuserinfo[ 'id' ]." AND accepted_price > 0 AND id_item =".$itemId;
	$resultAccP = mysql_query($sql);
	if ( mysql_num_rows($resultAccP) > 0 ){
		return false;
		
	}
						
						
		if ($resultUpdateCount= mysql_query( "UPDATE ".$modx->getFullTableName( '_shop_basket' )." SET `count` = {$newCount}, adddate = {$timestamp} WHERE ".$sqlWhere."  AND id_item = ".$itemId." LIMIT 1" ) or die(mysql_error())) {
			
			$result= mysql_query( "SELECT id ,price, discount, currency FROM ".$modx->getFullTableName( '_catalog' )."  
							WHERE `id`=".$itemId." LIMIT 1 " ) or die(mysql_error());
			if (mysql_num_rows($result ) > 0) {
				if 	($rowcat = mysql_fetch_assoc($result)){
					if( $rowcat[ 'currency' ] != 'rub' ) $rowcat[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$rowcat[ 'price' ], 'c'=>$rowcat[ 'currency' ]));
					//personalDiscount
					$sqlPDiscount = "SELECT p_discount FROM  ".$modx->getFullTableName( '_personal_discount' )." WHERE id_user =  '".$webuserinfo[ 'id' ]."'  LIMIT 1";
					$resultPDiscount = mysql_query($sqlPDiscount) or die ('Error 1231');
					if  ($row_pd = mysql_fetch_assoc($resultPDiscount)['p_discount']){
						$pagerow_pd = $row_pd;
					} else {
						$pagerow_pd = 0;
					}	

					if ($pagerow_pd > $rowcat[ 'discount' ]){
						$rowcat[ 'discount' ] = $pagerow_pd;
					}
						//
					$accepted_price= 0;
					
					$sql = "SELECT accepted_price FROM ".$modx->getFullTableName( '_shop_basket' )." WHERE id_user=".$webuserinfo[ 'id' ]." AND id_item =".$rowcat['id'];
					$resultAccP = mysql_query($sql);
					if ($accepted_priceTMP = mysql_fetch_assoc($resultAccP)['accepted_price']){
						if ($accepted_priceTMP > 0 &&  $accepted_priceTMP < $rowcat['price']) {
							$accepted_price = $accepted_priceTMP;
						}
					}
					
					
					if ($rowcat[ 'discount' ] > 0 ){
						if ($accepted_price >0 ) {
						$oneprice =  $accepted_price;
						}else {
							$oneprice =  round(($rowcat['price'] - ($rowcat['price'] / 100 * $rowcat['discount'])));
						}

					}else  {
						if ($accepted_price >0 ) {
							$oneprice =  $accepted_price;
						}else {
							$oneprice = $rowcat[ 'price' ];
						}


					}
					$resultPrice  = $modx->runSnippet( 'Price', array( 'price' => $oneprice * $newCount, 'round' => 0 ) );
					//$resultPrice = $oneprice;
					return $resultPrice;
					exit();
				}else return false;
			}else return 'mysql_num_rows<0';
		}else return false;
	} else return false;

}





//getSummMyOrder
if( $type == "getSummMyOrder" ){
	
	if (is_numeric ($webuserinfo['id'])) {
		$userId = $webuserinfo['id'];
		$sqlWhere = "`id_user` = ".$userId;
		$tryPersDiscount = true;
	}else {
		$userId = false;
		$sqlWhere = "`sessid` = '".session_id()."'";
		$tryPersDiscount = false;
	}
	
	$rows = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyBasketList', 'userId' => $userId));
	if ($rows) {
		$allSumm=0;
		foreach ($rows AS $row){
			if( $row[ 'currency' ] != 'rub' ) $row[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$row[ 'price' ], 'c'=>$row[ 'currency' ] ));
			if ($tryPersDiscount){
				//personalDiscount
				$sqlPDiscount = "SELECT p_discount FROM  ".$modx->getFullTableName( '_personal_discount' )." WHERE ".$sqlWhere." LIMIT 1";
				$resultPDiscount = mysql_query($sqlPDiscount) or die ('Error 9877 -'.mysql_error());
				if  ($row_pd = mysql_fetch_assoc($resultPDiscount)['p_discount']){
					$pagerow_pd = $row_pd;
				} else {
					$pagerow_pd = 0;
				}	

				if ($pagerow_pd > $row[ 'discount' ]){
					$row[ 'discount' ] = $pagerow_pd;
				}
				//
			}
			
			$accepted_price= 0;
			if ($row['accepted_price'] > 0 &&  $row['accepted_price'] < $row['price']) {
				$accepted_price = $row['accepted_price'] ;
			}
			
			if ($row[ 'discount' ] > 0 ){
				if ($accepted_price >0 ) {
				$oneprice =  $accepted_price;
				}else {
					$oneprice =  round(($row['price'] - ($row['price'] / 100 * $row['discount'])));
				}
				
			}else  {
				if ($accepted_price >0 ) {
					$oneprice =  $accepted_price;
				}else {
					$oneprice = $row[ 'price' ];
				}
				
				
			}
			$allSumm = $allSumm + $oneprice * $row['count'];
		
		}
		
		if ($nds == '') {$type = addslashes($_GET['nds']);}
		if (isset($_GET['nds'])) {
			if ($nds == '') {$nds = addslashes($_GET['nds']);}
		}
		
		if ($nds == 'only') {
			return $modx->runSnippet( 'Price', array( 'price' => $allSumm / 100 * 18  , 'round' => 1 ) ) .'  <span class="rubl">a</span>';
		}elseif ($nds == 'with') {
			return $modx->runSnippet( 'Price', array( 'price' => $allSumm + $allSumm / 100 * 18  , 'round' => 1 ) ) .'  <span class="rubl">a</span>';
		} else {
			return $modx->runSnippet( 'Price', array( 'price' => $allSumm  , 'round' => 1 ) ) .'  <span class="rubl">a</span>';
		}
		
	}else return 'empty';
}
	
	

//getSummMyOrderINT
if( $type == "getSummMyOrderINT" ){
	if (is_numeric ($webuserinfo['id'])) {
		$userId = $webuserinfo['id'];
		$sqlWhere = "`id_user` = ".$userId;
		$tryPersDiscount = true;
	}else {
		$userId = false;
		$sqlWhere = "`sessid` = '".session_id()."'";
		$tryPersDiscount = false;
	}
	$rows = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyBasketList', 'userId' => $userId));
	if ($rows) {
		$allSumm=0;
		foreach ($rows AS $row){
			if( $row[ 'currency' ] != 'rub' ) $row[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$row[ 'price' ], 'c'=>$row[ 'currency' ] ));
			if ($tryPersDiscount){
				//personalDiscount
				$sqlPDiscount = "SELECT p_discount FROM  ".$modx->getFullTableName( '_personal_discount' )." WHERE ".$sqlWhere." LIMIT 1";
				$resultPDiscount = mysql_query($sqlPDiscount) or die ('Error 9877 -'.mysql_error());
				if  ($row_pd = mysql_fetch_assoc($resultPDiscount)['p_discount']){
					$pagerow_pd = $row_pd;
				} else {
					$pagerow_pd = 0;
				}	

				if ($pagerow_pd > $row[ 'discount' ]){
					$row[ 'discount' ] = $pagerow_pd;
				}
				//
			}
			if ($row[ 'discount' ] > 0 ){
				$oneprice =  round(($row['price'] - ($row['price'] / 100 * $row['discount'])));
			}else  {
				$oneprice = $row[ 'price' ];
			}
			$allSumm = $allSumm + $oneprice * $row['count'];
		
		}
		return  $allSumm;
	}else return false;
}





if( $type == "delFromCart" && is_numeric( $_GET['delFromCartId'] )){
	$delFromCartId = addslashes($_GET['delFromCartId']);

	
	if (is_numeric ($webuserinfo['id'])) {
		$userId = $webuserinfo['id'];
		$sqlWhere = "`id_user` = ".$userId;
	}else {
		$userId = false;
		$sqlWhere = "`sessid` = '".session_id()."'";
	}
	
	
	$result= mysql_query( "DELETE FROM ".$modx->getFullTableName( '_shop_basket' )." WHERE id_item=".$delFromCartId."  AND  ".$sqlWhere." LIMIT 1" ) or die(mysql_error());
	if ($result) {
		echo "Ok";
		//return ITOGO;
	}else return false;
	
}





?>