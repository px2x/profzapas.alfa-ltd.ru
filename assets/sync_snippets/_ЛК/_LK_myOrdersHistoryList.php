<?php

//$list = 
		//wait_check
		//wait_coins	
		//wait_shipment
		//wait_testing

//return $list;	

$auth= 108;	
$payBuy = 208;
$toBuyPayPage_url = $modx->makeUrl($payBuy);
$pageId_messages = 122;
$page_sellerInfo = 217; 

if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	header( 'location: '. $modx->makeUrl( $auth ) );
	exit();
}
$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];


if (empty($list)) return false;
	
$topage_url= $modx->makeUrl( $modx->documentIdentifier );

/*
if (is_numeric($_GET['disputeFrom']) && is_numeric($_GET['order'])){
	$sql = "UPDATE ".$modx->getFullTableName( '_messages' )." SET dispute = 1 WHERE order_id = ".$_GET['order'];
	mysql_query($sql) or die ("ERR 90216 ".mysql_error());
}

*/


/*
if (is_numeric($_GET['disputeFrom']) && is_numeric($_GET['order'])){
	$sql = "UPDATE ".$modx->getFullTableName( '_messages' )." SET dispute = 1 WHERE order_id = ".$_GET['order'];
	mysql_query($sql) or die ("ERR 90216 ".mysql_error());
}

*/

if (is_numeric($_GET['openDispute']) &&  is_numeric($_GET['order']) ){
	
	$timestamp = time();
	$user_id = $webuserinfo['id'];
	$seller_id = $_GET['openDispute'];
	$order_id = $_GET['order'];
	
//	$sqlGetDisputStatus = "SELECT count(id) AS sumDisp  FROM  ".$modx->getFullTableName( '_dispute_list' )." WHERE status = 'opened' AND order_id = ".$order_id;
//	$resultGetDisputStatus = mysql_query($sqlGetDisputStatus) or die("ERROR 471956 ".mysql_error() );
	//if ($tmp = mysql_fetch_assoc($resultGetDisputStatus)){
		
		//if (!$tmp['sumDisp'] > 0) {
		if (true) {
				
			$sql = "INSERT INTO  ".$modx->getFullTableName( '_dispute_list' )." (
					`status`,
					`date_open`,
					`user_id`,
					`seller_id`,
					`order_id`
					) VALUES (
					'opened',
					'".$timestamp."',
					".$user_id.",
					".$seller_id.",
					".$order_id." 
					) ON DUPLICATE KEY UPDATE 
                    `status` = 'opened' , 
                    `date_open` = '".(time())."'";
			$result = mysql_query($sql) or die("ERROR 6574584 ".mysql_error() );
			
			$sql = "SELECT id FROM  ".$modx->getFullTableName( '_dispute_list' )." WHERE  order_id = ".$order_id." AND user_id = ".$user_id;
			$result = mysql_query($sql) or die("ERROR 52773 ".mysql_error() );
			if ($lastDisputeId = mysql_fetch_assoc($result)['id']){
				
				//echo '11111';
				$sqlOrder = "SELECT order_number FROM  ".$modx->getFullTableName( '_orders' )." WHERE id = ".$order_id;
				$resultOrder = mysql_query($sqlOrder) or die("ERROR 56457 ".mysql_error() );
				if ($tmpOrder = mysql_fetch_assoc($resultOrder)['order_number']){
					
					//echo '!!!!';
					$message = 'Открыт спор касательно заказа №'.$tmpOrder;
						
					$sql = "INSERT INTO  ".$modx->getFullTableName( '_dispute_msg' )." (
							`id_dispute`,
							`from_user`,
							`to_user`,
							`message`,
							`timestamp`,
							`readstatus`
							) VALUES (
							".$lastDisputeId.",
							".$user_id.",
							-1,
							'".$message."',
							'".$timestamp."',
							'unred'
							)";
					
					$result = mysql_query($sql) or die("ERROR 773378 ".mysql_error() );
					if ($result) {
						header( 'location: '. $topage_url."?tab=5" );
						exit();
					}
				}	
			}	
		}
	//}
}


$rows = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyOrdersList', 'list' => $list));
//return print_r($rows);
$listOrdersWait = '';
if ($rows) { 
	
	
	$head = '';
		
	$head .= '
	<div class="lkItem_tit">
		<!--div class="checkboxs"></div-->
		<div class="order_number_tit">№ заказа</div>
		<div class="date_w_tit">Дата заказа</div>
		<!--div class="sh_city_tit">Город</div>
		<div class="sh_index_tit">Индекс</div>
		<div class="sh_shipment_tit">Способ оплаты</div-->
		<div class="summOneOrder_tit">Сумма заказа</div>
		<div class="status_tit">Статус</div>';

	$head .= '<div class="action_tit">Действие</div>';

	$head .= '
		<!--div class="expandDown_tit"> </div-->
		<div class=""></div>
	</div>
	';

	
	
	foreach ($rows as $row) {
		
		//$rowsItem = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyOrdersItemsList', 'idorder' => $row['id']));
		//<a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'id' ] .'/">'.$row['code'].'</a>
		$itemsList = '';
		$rowsItem = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyOrdersItemsList', 'idorder' => $row['id']));
		//return $row['id'].'_'.print_r($rowsItem);
		$allsumOrder = 0;
		
		foreach ($rowsItem as $rowItem) {
			$itemsList .= '
			<div class="itemOrderList">
				<a class="as1" href="'. $modx->makeUrl( $rowItem[ 'parent' ] ) .'i/'. $rowItem[ 'id_item' ] .'/">'.$rowItem['code'].'</a>
				<span class="title">'.$rowItem['title'].'</span>
				<span class="price"><span>'.$modx->runSnippet( 'Price', array( 'price' => $rowItem['price'], 'round' => 0)).'</span><span class="rubl">a</span></span>
				<span class="count">'.$rowItem['count'].'</span>
				<span class="summOnePosition"><span>'.$modx->runSnippet( 'Price', array( 'price' => $rowItem['count'] *  $rowItem['price'], 'round' => 0)).'</span><span class="rubl">a</span></span>
			</div>';
			$allsumOrder = $allsumOrder + $rowItem['price'] * $rowItem['count'];
		}
		
		$itemsList .= '
			<div class="itemOrderList">
				<span class="as1"></span>
				<span class="title">Доставка</span>
				<span class="price"> </span>
				<span class="count"> </span>
				<span class="summOnePosition"><span>'.$modx->runSnippet( 'Price', array( 'price' => $row['price_shipment'], 'round' => 0)).'</span><span class="rubl">a</span></span>
			</div>';
		
		$allsumOrder = $allsumOrder + $row['price_shipment'];
		
		$itemsList .= '
			<div class="itemOrderList brayBG">
				<div class="itogoTEXT">Итого: </div>
				<div class="itogoPrice">'.$modx->runSnippet( 'Price', array( 'price' => $allsumOrder, 'round' => 0)).'<span class="rubl miniRubl">a</span></div>
			</div>
			<div class="clr"></div>
			
			<div class="itemOrderList brayBG">
				<div class="itogoTEXT">НДС 18%: </div>
				<div class="itogoPrice">'.$modx->runSnippet( 'Price', array( 'price' => $allsumOrder / 100 * 18, 'round' => 0)).'<span class="rubl miniRubl">a</span></div>
			</div>
			<div class="clr"></div>
			
			<div class="itemOrderList brayBG">
				<div class="itogoTEXT">Итого с НДС: </div>
				<div class="itogoPrice">'.$modx->runSnippet( 'Price', array( 'price' => $allsumOrder + $allsumOrder / 100 * 18, 'round' => 0)).'<span class="rubl miniRubl">a</span></div>
			</div>
			<div class="clr"></div>';
		
		

		if ($row['t_payment'] == 'schetPay'){
			$row['t_payment'] = 'Оплата через банк';
		}elseif ($row['t_payment'] == 'coinsPay'){
			$row['t_payment'] = 'Оплата с внутреннего счета';
		}elseif ($row['t_payment'] == 'roboPay'){
			$row['t_payment'] = 'Банковская карта';
		}


		if ($row['t_shipment'] == 'defaultShipment'){
			$row['t_shipment'] = 'Обычная';
		}elseif ($row['t_shipment'] == 'fastShipment'){
			$row['t_shipment'] = 'Срочная';
		}


		
		//ведется ли спор
		$sqlGetDisputStatus = "SELECT count(id) AS sumDisp  FROM  ".$modx->getFullTableName( '_dispute_list' )." WHERE  status = 'opened' AND order_id = ".$row['id'];
		$resultGetDisputStatus = mysql_query($sqlGetDisputStatus) or die("ERROR 471956 ".mysql_error() );
		if ($tmp = mysql_fetch_assoc($resultGetDisputStatus)){
			if ($tmp['sumDisp'] > 0) {
				$buttonDispute = '<a class="redButton"  href="'.$topage_url.'?seeDispute='.$row['sellerId'].'&order='.$row['id'].'">Ведется спор</a>';
			}else {
				$buttonDispute = '<a class="blueButton"  href="'.$topage_url.'?openDispute='.$row['sellerId'].'&order='.$row['id'].'">Открыть спор</a>';
			}
			
		}else {
			$buttonDispute = '<a class="blueButton"  href="'.$topage_url.'?seeDispute='.$row['sellerId'].'&order='.$row['id'].'">Ведется спор</a>';
		}

		if ($row['status'] == 'aborted'){
			$cellStatusContent = 'Отменен';
			$actionButtons = '';
		}elseif ($row['status'] == 'calculateShipment'){
			$cellStatusContent = 'Расчет доставки';
			$actionButtons = '<a href="'.$topage_url.'?abortId='.$row['id'].'" class="redButton">Отменить</a>';

			$dateToDie = 'Доставка будет расчитана до '.date("d.m.Y H:i", $row['date_w_check'] + 60*60*24);
			
		}elseif ($row['status'] == 'waitPayment'){
			$cellStatusContent = 'Ожидание оплаты';
			$actionButtons = '<a href="'.$toBuyPayPage_url.'?payId='.$row['id'].'" class="yellowButton">Оплатить</a>';
			$actionButtons .= '<a href="'.$topage_url.'?abortId='.$row['id'].'" class="redButton">Отменить</a>';
			
			$dateToDie = 'Необходимо оплатить до '.date("d.m.Y H:i", $row['date_w_coins'] + 60*60*24*5);
			
		}elseif ($row['status'] == 'waitShipment'){
			$cellStatusContent = 'Ожидается отправка';
			//$actionButtons = '<a href="'.$topage_url.'?disputeId='.$row['id'].'"  class="blueButton">Открыть спор</a>';
			$actionButtons = $buttonDispute;
			//<a class="newMessage" href="'.$modx->makeUrl($pageId_messages).'?getMsgFrom='.$sellerId.'">'.$nameSeller.'</a>
			
			
			$dateToDie = 'Бедет отправлено до '.date("d.m.Y H:i", $row['date_w_ship'] + 60*60*24*5);
			
		}elseif ($row['status'] == 'inShipment'){
			$cellStatusContent = 'Товар отправлен';
			//$actionButtons = '<a href="'.$topage_url.'?disputeId='.$row['id'].'"  class="blueButton">Открыть спор</a>';
			$actionButtons = $buttonDispute;
			$actionButtons .= '<a href="'.$topage_url.'?confirmShipId='.$row['id'].'"  class="greenButton">Подтвердить получение</a>';
			
			$dateToDie = 'Будет доставлено до '.date("d.m.Y H:i", $row['date_w_ship'] + 60*60*24*45);
			
			
		}elseif ($row['status'] == 'waitTesting'){
			$cellStatusContent = 'Тестирование';
			//$actionButtons = '<a href="'.$topage_url.'?disputeId='.$row['id'].'"  class="blueButton">Открыть спор</a>';
			$actionButtons = $buttonDispute;
			$actionButtons .= '<a href="'.$topage_url.'?confirmTestId='.$row['id'].'" class="greenButton">Подтвердить качество</a>';
			$dateToDie = 'Необходимо протестировать до '.date("d.m.Y H:i", $row['date_w_test'] + 60*60*24*5);
			

			
		}elseif ($row['status'] == 'ended' || $row['status'] == 'waitEnd'){
			$cellStatusContent = 'Завершен';
			$dateToDie = 'Завершен '.date("d.m.Y H:i", $row['date_end']);
			$actionButtons .= '';
			//$actionButtons .= $modx->runSnippet( '_response', array( 'orderId' => $row['id']));
			
		}
		
		
		//$row['sellerId']
		$sellerId=$row['sellerId'];
		$sqlNameSeller = "SELECT firstname, surname FROM  ".$modx->getFullTableName( '_user' )." WHERE id = '{$sellerId}' ";
		$resultNameSeller = mysql_query($sqlNameSeller) or die ("Error: 7377".mysql_error());
		if ($tmp = mysql_fetch_assoc($resultNameSeller)) {
			$nameSeller = $tmp['firstname']." ".$tmp['surname'];
		}
		
		//
		
		
		
		
		
		$sumShipmentText = '';
		$sumShipment = '';
		if ( $row['price_shipment'] > 0){
			$sumShipment = $row['price_shipment'];
			$sumShipmentText = '<br/>'.$modx->runSnippet( 'Price', array( 'price' => $row['price_shipment'], 'round' => 0)).'<span class="rubl">a</span> - доставка';
		}
		
		//переделать
		$idorder = $row['id'];
		
		$sqlAllSums = "SELECT SUM(count) AS sumcount, SUM(price) AS sumprice, SUM(id_item) AS sumiditem, SUM(id_user) AS sumiduser FROM  ".$modx->getFullTableName( '_order_items' )." WHERE id_order = '{$idorder}' ";
		$resultSumm = mysql_query($sqlAllSums) or die ("Error: 213947".mysql_error());
		$sumsVal = mysql_fetch_assoc($resultSumm);
		
		
		//$calculatedHash = md5($sumsVal['sumcount']+$sumsVal['sumprice']+$sumsVal['sumiditem']+$sumsVal['sumiduser']+$sumShipment.$row['status']);
		$calculatedHash = ($sumsVal['sumcount'].'_'.$sumsVal['sumprice'].'_'.$sumsVal['sumiditem'].'_'.$sumsVal['sumiduser'].'_'.$sumShipment.$row['status']);
		
		
		$sqlEtalonHash = "SELECT haval FROM  ".$modx->getFullTableName( '_orders_ha' )." WHERE id_order = '{$idorder}' ";
		$resultEtalonHash = mysql_query($sqlEtalonHash) or die ("Error: 7947".mysql_error());
		$etalHash = mysql_fetch_assoc($resultEtalonHash)['haval'];
		
		

		
		if ($calculatedHash != $etalHash) return $calculatedHash."<br/>".$etalHash."<br/>За попытку взлома можно получить в бубен!";
		
		
        if ($row['city'] != '') {
            $shCity_px = $row['city'];
        }elseif ($row['temp_city_ship'] != '') {
             $shCity_px = $row['temp_city_ship'];
        } else {
            $shCity_px ='не указано';
        }
          
        
		$listOrdersWait .= '
		<div class="clr" style="width:100%;height:0;padding:0;margin:0;"></div>
		<div class="lkItem ">
			<!--div class="checkboxs">
				<input type="checkbox" name="abortID['.$row['id'].']" value="'.$row['id'].'">
			</div-->
			<div class="order_number">#'.$row['order_number'].'</div>
			<div class="date_w">'.date("d.m.Y. H:i:s", $row['date_w_check']).'</div>
			
			<div class="summOneOrder">'.$modx->runSnippet( 'Price', array( 'price' => $allsumOrder + $allsumOrder / 100 * 18 , 'round' => 0)).'<span class="rubl">a</span></div>
			<div class="status">'.$cellStatusContent.'</div>
			<div class="action">'.$actionButtons.'</div>
			<div class="more"></div>
			<div class="seeMoreInfo" style="height:0;">
				<div class="left">
					<div class="itemOrderList_tit">
						<div class="seeMoreInfo_code">Код</div>
						<div class="seeMoreInfo_name">Наименование</div>
						<div class="seeMoreInfo_price">Цена</div>
						<div class="seeMoreInfo_count">Кол-во</div>
						<div class="seeMoreInfo_summ">Сумма</div>
					</div>
					'.$itemsList.'
				</div>
				<div class="right">			
					<div class="sh_city"><span>Продавец: </span><a class="newMessage" href="'.$modx->makeUrl($page_sellerInfo).'?sellerID='.$sellerId.'">'.$nameSeller.'</a></div>
					<div class="sh_city"><span>Адрес доставки:</span> '.$row['sh_city'].', '.$row['sh_index'].', '.$row['sh_address'].'</div>
                    
                    <div class="sh_city"><span>Адрес забора груза:</span> '.$shCity_px.'</div>
                    
                    
					<div class="sh_shipment"><span>Способ доставки:</span> '.$row['t_shipment'].'</div>
					<div class="sh_payment"><span>Способ оплаты:</span> '.$row['t_payment'].'</div>
					<div>'.$dateToDie.'</div>
				
				</div>
				
			</div>
			<div class="clr" style="width:100%;height:0;padding:0;margin:0;"></div>
		</div>
		'; 
		
		
	}
	
	//return true;
}
return $head.$listOrdersWait;





?>