<?php

//define('ROOT', dirname(__FILE__).'/');
define('ROOT', $_SERVER['DOCUMENT_ROOT']);

$sc_site= 'profzapas.ru';
$sm_base= '../assets/modules/px_dispute/';


$module_url= MODX_MANAGER_URL .'?a='. $_GET[ 'a' ] .'&id='. $_GET[ 'id' ];
//$module_url_orders= MODX_MANAGER_URL .'?a=112&id=3';


$webuser= intval( $_GET[ 'wu' ] );

if (isset( $_GET[ 'spg' ])){
	$subpage= $_GET[ 'spg' ];
}else {
	$subpage ='main';
}

$act= $_GET[ 'act' ];




//================================EVENT START=============
if (is_numeric($_GET['order'])){
    
    
    if (isset($_POST['sendTextSubmit']) && isset($_POST['sendText']) && is_numeric($_GET['id_user'])) {
        
            $text = addslashes($_POST['sendText']);
        
        
	        $modx->runSnippet( '_SHOP_Msg_dispute', array( 'event' => 'sendNewMessageFromAdmin', 
												   'text' => $text, 
												   'to' =>    $_GET['id_user'],
												   'order' => $_GET['order']
											   ));
        
    }  
    
    
    if (isset($_GET['close']) ) { 
        
        $idorder = addslashes ($_GET['order']);

        $newStatus = 'closed';
        $timestamp = time();

        $sqlUpdateOrders = "UPDATE  ".$modx->getFullTableName( '_dispute_list' )." SET status = '{$newStatus}' WHERE order_id = '{$idorder}' ";
        $resultUpdateOrders = mysql_query($sqlUpdateOrders) or die ("Error: 234747".mysql_error());

        if ($resultUpdateOrders){
            $text = 'Администратор закрыл спор';
            

	       $modx->runSnippet( '_SHOP_Msg_dispute', array( 'event' => 'sendNewMessageFromAdmin', 
												   'text' => $text, 
												   'to' =>    $_GET['close'],
												   'order' => $idorder
											   ));
        }
	
        
    }
    
    
    
}
  



//================================EVENT END===============

$result = mysql_query("SELECT COUNT(id) FROM ".$modx->getFullTableName( '_dispute_list' )." WHERE status = 'opened' LIMIT 1");
$summOpenedDispute = mysql_fetch_row($result)[0];


$result = mysql_query("SELECT COUNT(id) FROM ".$modx->getFullTableName( '_dispute_list' )." WHERE status = 'closed' LIMIT 1");
$summClosedDispute = mysql_fetch_row($result)[0];





?>


<div class="modul_scorn_all">
<!-- -------------------------- -->
<link rel="stylesheet" type="text/css" href="<?php print $sm_base;?>_styles.css" />


<script type="text/javascript" src="https://code.jquery.com/jquery-2.2.3.min.js"   integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo="   crossorigin="anonymous"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"   integrity="sha256-DI6NdAhhFRnO2k51mumYeDShet3I8AKCQf/tf7ARNhI="   crossorigin="anonymous"></script>


<script type="text/javascript" src="<?php print $sm_base;?>_script.js"></script>





<div class="topmenu">
	<ul>
		<li><a href="<?= $module_url ?>&spg=openedDispute">Открытые споры <?= $summOpenedDispute> 0 ? '(+'.$summOpenedDispute.')': ''  ?></a></li>
		<li><a href="<?= $module_url ?>&spg=closedDispute">Закрытые споры <?= $summClosedDispute> 0 ? '(+'.$summClosedDispute.')': ''  ?></a></li>


		
	</ul>
	<div class="clr">&nbsp;</div>
</div>





<div class="_LK_wrapper _LK_wrapper_big">	
	<div class="lkItem_tit">
		<!--div class="checkboxs"></div-->
		<div class="order_number_tit">№ заказа</div>
		<div class="date_w_tit">Дата заказа</div>
		<div class="summOneOrder_tit">Сумма заказа</div>
		<!--div class="status_tit">Статус</div><div class="action_tit">Действие</div-->
		<!--div class="expandDown_tit"> </div-->
		<div class=""></div>
	</div>
	
	
	
	
<?php	













//выборка заказов START
    
if ($subpage == 'main' || $subpage == 'openedDispute'){
    
    $typeStatus = 'opened';
    
}elseif ($subpage == 'closedDispute') {
    
    $typeStatus = 'closed';
    
}
    
    
    
if( true ){

	
	
	$result= mysql_query( "SELECT ord.*, uwh.address, uwh.city , disp.id AS disputeId FROM ".$modx->getFullTableName( '_orders' )." AS ord
                        INNER JOIN ".$modx->getFullTableName( '_dispute_list' )." AS disp ON ord.id = disp.order_id
                        LEFT JOIN ".$modx->getFullTableName( '_orders_fk_warehouse' )." AS fkwh ON ord.id = fkwh.id_order
                        LEFT JOIN ".$modx->getFullTableName( '_user_warehouse' )." AS uwh ON uwh.id = fkwh.id_wh
                        WHERE disp.status = '".$typeStatus."'
						LIMIT 100" ) or die(mysql_error());
	if (mysql_num_rows($result ) > 0) {
		$rowOrder = [];
		while 	($rowtmp = mysql_fetch_assoc($result)){
			$rowOrder[] = $rowtmp;
		}
		//return $row;
	} else return false;
}
	

	
	foreach ($rowOrder as $row) {

		if( true ){
			if (isset($row['id'])){
				$result= mysql_query( "SELECT ord.id_item,  ord.price , ord.count ,cat.title , user.email , cat.parent , cat.code
										FROM ".$modx->getFullTableName( '_order_items' )." AS ord
										INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id =  ord.id_item
										INNER JOIN ".$modx->getFullTableName( '_user' )." AS user ON cat.seller =  user.id
										WHERE  ord.id_order = '".$row['id']."'  LIMIT 200" ) or die(mysql_error());
				if (mysql_num_rows($result ) > 0) {
					$rowsItem = [];
					while 	($rowtmp = mysql_fetch_assoc($result)){
						$rowsItem[] = $rowtmp;
					}
					//return $rowsItem;
				} //else return false;
			}//else return falsee
		}


 


		$allsumOrder = 0;
		$itemsList = '';
		foreach ($rowsItem as $rowItem) {
			

			
			$itemsList .= '
			<div class="itemOrderList">
				<a class="as1" href="'. $modx->makeUrl( $rowItem[ 'parent' ] ) .'i/'. $rowItem[ 'id_item' ] .'/">'.$rowItem['code'].'</a>
				<span class="title">'.$rowItem['title'].'</span>
				<span class="price"><span>'.$modx->runSnippet( 'Price', array( 'price' => $rowItem['price'], 'round' => 0)).'</span><span class="rubl">a</span></span>
				<span class="count">'.$rowItem['count'].'</span>
				<span class="summOnePosition"><span>'.$modx->runSnippet( 'Price', array( 'price' => $rowItem['count'] *  $rowItem['price'], 'round' => 0)).'</span><span class="rubl">a</span></span>
				<span class="selectWH">'.$warehouseInputBuffer.'</span>
			</div>';
			$allsumOrder = $allsumOrder + $rowItem['price'] * $rowItem['count'];
		}

		if ($row['t_payment'] == 'schetPay'){
			$row['t_payment'] = 'Оплата через банк';
			$typePayment ="waitConfirm";
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



			
		//$row['sellerId']
		$sellerId=$row['sellerId'];
		$sqlNameSeller = "SELECT email FROM  ".$modx->getFullTableName( '_user' )." WHERE id = '{$sellerId}' ";
		$resultNameSeller = mysql_query($sqlNameSeller) or die ("Error: 7377".mysql_error());
		$nameSeller = mysql_fetch_assoc($resultNameSeller)['email'];
		//
        
        
       //print_r($row);
		$sqlNameByer = "SELECT email FROM  ".$modx->getFullTableName( '_user' )." WHERE id = ".$row['user_id'];
		$resultNameByer = mysql_query($sqlNameByer) or die ("Error: 7377".mysql_error());
		$nameBuyer = mysql_fetch_assoc($resultNameByer)['email'];
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
		

		

	
        
        if ($row['city'] != '' && $row['address'] != '') {
            
            $shCity_px = $row['city'].', '.$row['address'];
        }elseif ($row['temp_city_ship'] != '' && $row['temp_adress_ship'] != '') {
             $shCity_px = $row['temp_city_ship'].', '.$row['temp_adress_ship'];
        } else {
            $shCity_px ='не указано';
        }
        
        /////////msg STAR
        
        
        
        $msgContent='';
		
		$msgResult = $modx->runSnippet( '_SHOP_Msg_dispute', array( 'event' => 'getMsgFromAlltoAdm'  , 'from' => $row['user_id'] , 'order' => $idorder));
		
      //  print_r($msgResult);
       // echo ($msgResult);
      //  
        $unreadCount = 0;
		if ($msgResult) {

        
			foreach ($msgResult AS $msg){
				$msgContent .= '
					<div class="'.($msg['from_user'] != $row['user_id'] ? 'msgMe' : 'msgNotMe').' '.($msg['readstatus'] == 1 ? 'msgUnread' : '').'">'.$msg['message'].'<span> '.($msg['readstatus'] == 'unred' ? $unreadIcon : $readIcon).' '.date("d.m.Y H:i",$msg['timestamp']).'</span></div>
					<div class="clr"></div>
					';
                    if ($msg['readstatus'] == 'unred' && $msg['to_user'] == -1) $unreadCount++;
				} 
			}
        
        if ( $typeStatus == 'opened') {
            
            $formEna = '<form action="'.$module_url.'?&order='.$idorder.'&id_user='.$row['user_id'].'" method="POST">
								<textarea name="sendText" placeholder="Введите текст" required></textarea>
								<input type="submit" class="sendMessageButton" value="Отправить" name="sendTextSubmit">
                                <a class="sendMessageButton redButton" href="'.$module_url.'&order='.$idorder.'&close='.$row['user_id'].'">Закрыть! спор</a>

							</form>';
        }else{
            
            $formEna = '<div class="Closeddispute">Спор закрыт</div>';
        }
        
        if ($unreadCount > 0) $seeDisputeMSG = '<span class="newUnreadMsg">Новых сообщений: '.$unreadCount.'</span>';
        
        $seeDisputeMSG .= '
        
        <div class="seemoreinfoDispute" data-did="'.$row['disputeId'].'"><span class="seePopupMSGDispute yellowButton">Подробнее</span>
        
                <div class="dsrkBG">
                    <div class="messagesSmall">
					
						<div class="sendForm">
							'.$formEna.'
						</div>					
					
						<div class="writersInfo">
								<div class="writerFrom">Пользователь</div>
								<div class="writerMe">Вы</div>
						</div>

						<div class="msgContent">

							'.$msgContent.'

						</div>

                    </div>
                    
                </div>
        </div>';
    
        /////////msg END 
	
		if ($calculatedHash != $etalHash) return "За попытку взлома можно получить в бубен!";
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
			<div class="action">'.$seeDisputeMSG.'</div>
			<div class="more"></div>
			
			<form method="POST" action="'.$module_url.'&spg=calcShipment&orderId='.$row['id'].'">
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
						<div class="sh_city"><span>Продавец:</span> <a href="/manager/?a=112&id=2&spg=seeUserInfo&userId='.$sellerId.'">'.$nameSeller.'</a></div>
						<div class="sh_city"><span>Покупатель:</span> <a href="/manager/?a=112&id=2&spg=seeUserInfo&userId='.$row['user_id'].'">'.$nameBuyer.'</a></div>
                        
						<div class="sh_city"><span>Адрес доставки:</span> '.$row['sh_city'].', '.$row['sh_index'].', '.$row['sh_address'].'</div>
                        
                        <div class="sh_city"><span>Адрес забора груза:</span> '.$shCity_px.'</div>
                        
						<div class="sh_shipment"><span>Способ доставки:</span> '.$row['t_shipment'].'</div>
						<div class="sh_payment"><span>Способ оплаты:</span> '.$row['t_payment'].'</div>
						'.$calculateShipmentChunk.'
					
					</div>
					
				</div>
			</form>
			
			<div class="clr" style="width:100%;height:0;padding:0;margin:0;"></div>
		</div>
		'; 
		
		
	}
	
	

	
	
	
	

		
		
?>		
		

		

		
		<div class="clr" style="width:100%;height:0;padding:0;margin:0;"></div>
	
</div>




<?php



	

print $listOrdersWait;


?>