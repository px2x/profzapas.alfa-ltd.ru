<?php

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
$topage_url= $modx->makeUrl( $modx->documentIdentifier );


//sendTextSubmit
if (isset($_POST['sendTextSubmit']) && $_POST['sendText'] != '' && is_numeric($_GET['order'])){
	$text = addslashes($_POST['sendText']);
	$modx->runSnippet( '_SHOP_Msg_dispute', array( 'event' => 'sendNewMessage', 
												   'text' => $text, 
												   'from' => $webuserinfo['id'],
												   'order' => $_GET['order']
											   ));
	
}





$rows = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyOrdersDispute'));

$listOrdersWait = '';
if ($rows) { 
	
	$unreadIcon = '<img src="/template/images/msg_unread.png" />';
	$readIcon = '<img src="/template/images/msg_read.png" />';
	
	
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
		
        
        //print_r($row);
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
			$actionButtons = '';
		}
		
		
		//$row['sellerId']
		$sellerId=$row['sellerId'];
		$sqlNameSeller = "SELECT * FROM  ".$modx->getFullTableName( '_user' )." WHERE id = '{$sellerId}' ";
		$resultNameSeller = mysql_query($sqlNameSeller) or die ("Error: 7377".mysql_error());
		$tmp = mysql_fetch_assoc($resultNameSeller);
		$nameSeller = $tmp['firstname']." ".$tmp['surname'];
		//
		
		
		
		
		
		$sumShipmentText = '';
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
		
		$msgContent='';
		
		$msgResult = $modx->runSnippet( '_SHOP_Msg_dispute', array( 'event' => 'getMsgFrom'  , 'to' => $webuserinfo['id'] , 'order' => $idorder));
		
		if ($msgResult) {

			foreach ($msgResult AS $msg){
				$msgContent .= '
					<div class="'.($msg['from_user'] == $webuserinfo['id'] ? 'msgMe' : 'msgNotMe').' '.($msg['readstatus'] == 1 ? 'msgUnread' : '').'">'.$msg['message'].'<span> '.($msg['readstatus'] == 'unred' ? $unreadIcon : $readIcon).' '.date("d.m.Y H:i",$msg['timestamp']).'</span></div>
					<div class="clr"></div>
					';
				}
			}
		
		if ($calculatedHash != $etalHash) return "За попытку взлома можно получить в бубен!";
		
		$itemsListFull = '<div class="itemOrderList_tit">
							<div class="seeMoreInfo_code">Код</div>
							<div class="seeMoreInfo_name">Наименование</div>
							<div class="seeMoreInfo_price">Цена</div>
							<div class="seeMoreInfo_count">Кол-во</div>
							<div class="seeMoreInfo_summ">Сумма</div>
						</div>
						'.$itemsList;
		
        
        
        if ($row['city'] != '') {
            $shCity_px = $row['city'];
        }elseif ($row['temp_city_ship'] != '') {
             $shCity_px = $row['temp_city_ship'];
        } else {
            $shCity_px ='не указано';
        }
          
        
        
       // echo $row['dlstatus'].'!!!!!!!!!';
        if ($row['dlstatus'] == 'opened') {
            $stateDispute=true;
            $stateDisputeT = '
                <form action="'.$topage_url.'?tab=5&order='.$idorder.'" method="POST">
								<textarea name="sendText" placeholder="Введите текст" required></textarea>
								<input type="submit" class="sendMessageButton" value="Отправить" name="sendTextSubmit">

				</form>
            ';
            
        }elseif($row['dlstatus'] == 'closed') {
            $stateDispute=false;
            $stateDisputeT = '<div class="statusScloseddisPute">Спор закрыт</div>';
        }
        
		$listOrdersWait .= '
		<div class="clr" style="width:100%;height:0;padding:0;margin:0;"></div>
		<div class="lkItem ">
			<!--div class="checkboxs">
				<input type="checkbox" name="abortID['.$row['id'].']" value="'.$row['id'].'">
			</div-->
			<div class="order_number">#'.$row['order_number'].'</div>
			<div class="date_w">'.date("d.m.Y. H:i:s", $row['date_w_check']).'</div>
			
			<div class="summOneOrder" '.($sumShipmentText == '' ? '': 'style="line-height: normal;"').'>'.$modx->runSnippet( 'Price', array( 'price' => $allsumOrder, 'round' => 0)).'<span class="rubl">a</span>'.$sumShipmentText.'</div>
			<div class="status">'.$cellStatusContent.'</div>
			<div class="action">'.$actionButtons.'</div>
			<div class="more" data-longer="true"></div>
			<div class="clr" style="width:100%;height:0;padding:0;margin:0;"></div>
			
			<div class="seeMoreInfo" style="height:0;">
				<div class="left">
				
					<div class="messagesSmall">
					
						<div class="sendForm">
							'.$stateDisputeT.'
						</div>					
					
						<div class="writersInfo">
								<div class="writerFrom">Администратор</div>
								<div class="writerMe">Вы</div>
						</div>

						<div class="msgContent">

							'.$msgContent.'

						</div>
						
						

						
						
					</div>
					<div class="clr"></div>
					
				</div>
				<div class="right" style="height: 385px;">			
					<div class="sh_city"><span>Продавец: </span> <a class="newMessage" href="'.$modx->makeUrl($page_sellerInfo).'?sellerID='.$sellerId.'">'.$nameSeller.'</a></div>
					<div class="sh_city"><span>Адрес доставки:</span> '.$row['sh_city'].', '.$row['sh_index'].', '.$row['sh_address'].'</div>
                    
                    <div class="sh_city"><span>Адрес забора груза:</span> '.$shCity_px.'</div>
                    
                    
					<div class="sh_shipment"><span>Способ доставки:</span> '.$row['t_shipment'].'</div>
					<div class="sh_payment"><span>Способ оплаты:</span> '.$row['t_payment'].'</div>
					<div>'.$dateToDie.'</div>
					<div class="blueDiv">Просмотр товаров <div class="itemListPopup">'.$itemsListFull.'</div></div>
					<div class="clr"></div>
                    '.($stateDispute ? '<div class=""><a href="'.$topage_url.'?closeDispute='.$idorder.'" class="greenButton">Закрыть спор</a></div>' : '' ).'
					
					
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