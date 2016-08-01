<?php

//print_r($_POST);

$thisPageLink = $modx->makeUrl( $modx->documentIdentifier );

$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];	
if (is_numeric ($webuserinfo['id'])) {
	$userId = $webuserinfo['id'];
	$sqlWhere = "`id_user` = ".$userId;
}else {
	$userId = session_id();
	$sqlWhere = "`sessid` = '".session_id()."'";
}	

$toMyOrderPage = $modx->makeUrl(117);




//select summ wait output
$sqlSummOutput = "SELECT SUM(sum) FROM  ".$modx->getFullTableName( '_points_up' )." WHERE id_user =  '".$webuserinfo['id']."' AND type = 'output'  AND status = 'wait' LIMIT 1";
$resultSummOutput = mysql_query($sqlSummOutput) or die ('Error SUMMPrep coins history');
$coinsSummOutputInt = mysql_fetch_row($resultSummOutput)[0];


$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$webuserinfo['id']."' LIMIT 1";
$resultSumm = mysql_query($sqlSumm) or die ('Error SUMM coins history');
$coinsSumm = mysql_fetch_row($resultSumm)[0] - $coinsSummOutputInt;

	//ДЕНЬГИ ЗА ПРОДАЖИ КОТОРЫЕ НА ТЕК. МОМЕНТ В ДОСТАВКЕ
$sqlSummNotConfirm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." AS hist
					LEFT JOIN ".$modx->getFullTableName( '_orders' )." AS ord ON ord.id = hist.shop_id
					WHERE hist.id_user =  '".$webuserinfo['id']."'   
					AND (ord.status <>  'ended'OR hist.key = -1)
					AND hist.shop_id >0
					AND hist.type =  'payUp'
					LIMIT 1";
$resultNotConfirm = mysql_query($sqlSummNotConfirm) or die ('Error 634437 SUMMPrep coins history');
$resultNotConfirmInt = mysql_fetch_row($resultNotConfirm)[0];

//оплаченный аванс
$sqlSummPrepayPay = "SELECT SUM(ABS(event)) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$webuserinfo['id']."' AND type = 'prepaymentPAY' AND event < 0 LIMIT 1";
$resultSummPrepayPay = mysql_query($sqlSummPrepayPay) or die ('Error в качестве аванса');
$coinsSummPrepayPay = mysql_fetch_row($resultSummPrepayPay)[0];

$myCoinsSum = $coinsSumm +$coinsSummPrepayPay - $resultNotConfirmInt;
$myOrderSum = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getSummMyOrderINT'));

$noCoinsFlag = false;
if ($myCoinsSum < $myOrderSum) {
	$coinsAttributes = 'disabled';
	$schetAttributes = 'checked';
	$noCoinsFlag = true;
}else {
	$coinsAttributes = 'checked';
}


//return print_r($webuserinfo);
$errArr = [];

$debug = 'www';
// допилить проверки при добавлении
if (isset($_POST['dooModOrder'])){
	//return print_r($_POST);
	if ($_POST['iamagree'] != 1) {
		$errArr[] = 'Ознакомьтесь с условиями доставки и офертой';
	}
	
	if ($_POST['fio'] == '') {
		$errArr[] = 'Введите Ваше имя и фамилию';
	}
	
	if ($_POST['phone'] == '') {
		$errArr[] = 'Введите номер мобильного телефона';
	}
	
	
	//return $_POST['typepayment'].' '.$noCoinsFlag;
	if ($_POST['typepayment'] == 'coinsPay' && $noCoinsFlag) {
		$errArr[] = 'Не хватает средств для оплаты с внутреннего счета';
	}
	

	if (count($errArr)  == 0 ) {
		
		$bfPage = '';
		
		foreach ($_POST as $key => $value) {
			$filteredPost[$key] = $value;
		}
		
		
		////////////////////////////////////////////////============
		//echo $_POST['smsinfo'].'!!!!!!!!!!!!!!!!!!';
		//getMyBasketSellersList
		$rowsSellers = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyBasketSellersList', 'userId' => $userId));
		//return print_r($rowsSellers);
		foreach ($rowsSellers AS $sellerid){
		
			//макс заказ №
			$sql = "SELECT MAX(id) FROM ".$modx->getFullTableName( '_orders' )."  LIMIT 1";
			$resultMaxOrder = mysql_query($sql) or die ('Error Max Order');
			$lastOrder = mysql_fetch_row($resultMaxOrder)[0] + 1001;

            
            if ($_POST['smsinfo'] == 1) {
                $noticeMeSMS = 1;
            } else {
                $noticeMeSMS = 0;
            }
			$date_wCheck = time();
			$sql = "INSERT INTO ".$modx->getFullTableName( '_orders' )." (
					`id`,
					`order_number`,
					`user_id`,
					`comment`,
					`sh_city`,
					`sh_index`,
					`sh_address`,
					`sms`,
					`phone`,
					`email`,
					`t_shipment`,
					`t_payment`,
					`w_check`,
					`date_w_check`,
					`w_coins`,
					`date_w_coins`,
					`w_ship`,
					`date_w_ship`,
					`w_test`,
					`date_w_test`,
					`status`,
					`sellerId`,
                    `noticeMeSMS`) VALUES (
					NULL,
					'".$lastOrder."',
					'".$userId."',
					'".$filteredPost['notice']."',
					'".$filteredPost['shipmentcity']."',
					'".$filteredPost['postindex']."',
					'".$filteredPost['address']."',
					'".$filteredPost['sms']."',
					'".$filteredPost['phone']."',
					'".$filteredPost['email']."',
					'".$filteredPost['typeshipment']."',
					'".$filteredPost['typepayment']."',
					'1',
					'".$date_wCheck."',
					'0',
					'0',
					'0',
					'0',
					'0',
					'0',
					'calculateShipment',
					'".$sellerid."',
                    ".$noticeMeSMS."
					)";
			$resultInsOrder = mysql_query($sql) or die ('Error Ins Order: '.mysql_error() );
			if ($resultInsOrder){
				$debug = $debug.'g';
				$sql = "SELECT id FROM ".$modx->getFullTableName( '_orders' )." WHERE order_number = '{$lastOrder}' LIMIT 1";
				$resultLastId = mysql_query($sql) or die ('Error Sel Last id');
				$lastInsId = mysql_fetch_row($resultLastId)[0];
				
				$debug = $debug.'l'.$lastInsId;
				
				$rows = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyBasketListWitnDiscount', 'userId' => $userId , 'sellerId' =>$sellerid));
				//return print_r($rows);
				//return 0;
				if ($rows) {
					//$bfPage = print_r($rows);
					$catchError = true;

					$sumCount = 0;
					$sumPrice = 0;
					$sumIdItem = 0;
					$sumIdUser = 0;

					foreach ($rows as $row){
						$sqlInsItem = "INSERT INTO ".$modx->getFullTableName( '_order_items' )." (
						`id`,
						`id_order`,
						`id_item`,
						`id_user`,
						`price`,
						`count`,
						`price_nds`
						) VALUES (
						NULL,
						'".$lastInsId."',
						'".$row['id_item']."',
						'".$userId."',
						'".$row['priceWithDiscount']."',
						'".$row['count']."',
						'".($row['priceWithDiscount'] + $row['priceWithDiscount'] / 100 * 18)."'
						)";
						$resultInsItem = mysql_query($sqlInsItem) or die ('Error Ins Items: '.mysql_error());

						$sumCount = $sumCount + $row['count'];
						$sumPrice = $sumPrice + $row['priceWithDiscount'];
						$sumIdItem = $sumIdItem + $row['id_item'];
						$sumIdUser = $sumIdUser + $userId;

						if ($resultInsItem) {
							//_shop_basket
							
							/*
							$sqlDel = "DELETE FROM ".$modx->getFullTableName( '_shop_basket' )." WHERE  id_user = '{$userId}'";
							$resultDelBasket = mysql_query($sqlDel) or die ('Error 454: '.mysql_error());
							if (!$resultDelBasket) {
								$catchError = false;
							}
							*/

						}else {
							$catchError = false;
						}


					}
					$summSipment = '';
					$order_ha = md5($sumCount+$sumPrice+$sumIdItem+$sumIdUser.'calculateShipment');
					$order_ha = ($sumCount.'_'.$sumPrice.'_'.$sumIdItem.'_'.$sumIdUser.'_'.$summSipment.'calculateShipment');
					
					
					$sqlHA = "INSERT INTO ".$modx->getFullTableName( '_orders_ha' )." (`id`, `id_order`, `haval`) VALUES (NULL ,{$lastInsId} , '{$order_ha}' )";
					$resHA = mysql_query($sqlHA) or die ('Error 3463: '.mysql_error());
					if (!$resHA) {
						$catchError = false;
						$bfPage .= "error: 89692";
					}
					


				}





			}
		}
		
		
		$sqlDel = "DELETE FROM ".$modx->getFullTableName( '_shop_basket' )." WHERE  id_user = '{$userId}'";
		$resultDelBasket = mysql_query($sqlDel) or die ('Error 454: '.mysql_error());
		if (!$resultDelBasket) {
			$catchError = false;
		}
		
		if ($catchError) {
			header( 'location: '. $toMyOrderPage.'?event=ok&tab=4&inid='.$lastInsId );
			exit();
		}
		
		///////////////////////////////////////////===========================
		//return $debug;
	}

}
	


/*
if ($_GET['event'] == 'ok' && is_numeric($_GET['inid'])) {
	$okPageBuffer = '';
	
}
*/

	
	
$rows = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyBasketList', 'userId' => $userId));
$print_items_head .= '<table class="catalog_table" cellpadding="0" cellspacing="0">';
$print_items_head .= '<tr class="jqt_row_tit">';
$print_items_head .= '<td class="jqtrt_col_order order_code">Код</td>';
$print_items_head .= '<td class="jqtrt_col_order order_manuf">Производитель</td>';
$print_items_head .= '<td class="jqtrt_col_order order_title">Наименование</td>';
$print_items_head .= '<td class="jqtrt_col_order order_price">Цена, руб.</td>';
$print_items_head .= '<td class="jqtrt_col_order order_count">Количество</td>';
$print_items_head .= '<td class="jqtrt_col_order order_summ">Стоимость</td>';
$print_items_head .= '</tr>';

if ($rows) {
	foreach ($rows AS $row){
		
		//personalDiscount
		$sqlPDiscount = "SELECT p_discount FROM  ".$modx->getFullTableName( '_personal_discount' )." WHERE id_user =  '".$webuserinfo[ 'id' ]."'  LIMIT 1";
		$resultPDiscount = mysql_query($sqlPDiscount) or die ('Error 6576');
		if  ($row_pd = mysql_fetch_assoc($resultPDiscount)['p_discount']){
			$pagerow_pd = $row_pd;
		} else {
			$pagerow_pd = 0;
		}	

		if ($pagerow_pd > $row[ 'discount' ]){
			$row[ 'discount' ] = $pagerow_pd;
		}
		//
		
		
		if( $row[ 'currency' ] != 'rub' ) $row[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$row[ 'price' ], 'c'=>$row[ 'currency' ] ));
		$result .= '<tr class="">';
		
		$result .= '<td class="order_code"><a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'id' ] .'/">'. $row[ 'code' ] .'</a></td>';
		$result .= '<td class="order_manuf">'. $row[ 'manufacturer' ] .'</td>';
		$result .= '<td class="order_title">'. $row[ 'title' ] .'</td>';
		
		
		
		$accepted_price= 0;
		if ($row['accepted_price'] > 0 &&  $row['accepted_price'] < $row['price']) {
			$accepted_price = $row['accepted_price'] ;
		}
		

		if ($row[ 'discount' ] > 0 ){
			//$oneprice =  round(($row['price'] - ($row['price'] / 100 * $row['discount'])));
			if ($accepted_price >0 ) {
				$oneprice =  $accepted_price;
				$countContrors = 'disabled';
			}else {
				$oneprice =  round(($row['price'] - ($row['price'] / 100 * $row['discount'])));
			}
			$result .= '<td class="order_price"><nobr><span class="price">'. $modx->runSnippet( 'Price', array( 'price' => $oneprice, 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></td>';
		}else  {
			//$oneprice = $row[ 'price' ];
			if ($accepted_price >0 ) {
				$oneprice =  $accepted_price;
				$countContrors = 'disabled';
			}else {
				$oneprice = $row[ 'price' ];
			}
			$result .= '<td class="order_price"><nobr><span class="price">'. $modx->runSnippet( 'Price', array( 'price' => $oneprice, 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></td>';
		}
		
		
		
		$result .= '<td class="order_count">'. $row['count'] .'</td>';
		$result .= '<td class="order_summ">'. $modx->runSnippet( 'Price', array( 'price' => $oneprice * $row['count'] , 'round' => 0 ) ) .'<span class="rubl">a</span></td>';
		$result .= '</tr>';
	}
	$result .= '<tr class="lasttrgray"><th class="tr_itogo" colspan=5>Итого: </th><td class="allSummMyOrder">'.$modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getSummMyOrder', 'nds' => 'none')).'</td></tr>';
	$result .= '<tr class="lasttrgray"><th class="tr_itogo" colspan=5>НДС 18%: </th><td class="allSummMyOrder">'.$modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getSummMyOrder', 'nds' => 'only')).'</td></tr>';
	$result .= '<tr class="lasttrgray"><th class="tr_itogo" colspan=5>Итого с НДС: </th><td class="allSummMyOrder">'.$modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getSummMyOrder', 'nds' => 'with')).'</td></tr>';
	$print_items_foot .= '</table>';
	
	$itemsList =  $print_items_head.$result.$print_items_foot;
}




if  (count($errArr)  > 0 ){
	$printpage.= '<div class="errorBlocks"><div class="title">При оформлении заказа обнаружены следующие ошибки:</div>';
	foreach ($errArr AS $elem){
		$printpage.= '<span> - '.$elem.'</span>';
	}
	$printpage.= '</div>';
}

$printpage .= '
<form action="'.$thisPageLink.'" method="POST">
<div class="order_contactFace">
	<div class="orederBlockTitle">Заказчик</div>
	<div class="orederBlockform">
		<label for="fio">ФИО</label>
		<input type="text" name="fio" id="fio" value="'.$webuserinfo['firstname'].' '.$webuserinfo['surname'].'" required/>
		<div class="clr"></div>
		
		<label for="email">Электронная почта</label>
		<input type="text" name="email" id="email" value="'.$webuserinfo['email'].'" />
		<div class="clr"></div>
		
		<label for="phone">Мобильный телефон</label>
		<input type="text" name="phone" id="phone" value="'.$webuserinfo['mobile'].'" />
		<div class="clr"></div>
		
		<label for="smsinfo" class="longlabel">Информировать о статусе заказа по SMS</label>
		<input type="checkbox" name="smsinfo" id="smsinfo" value="1" style="width: 18px;"/>
		<div class="clr"></div>
	</div>
	<div class="orederBlockTitle">Адрес доставки</div>
	<div class="orederBlockform">
		<label for="search_box1">Город</label>
		<input type="text" id="search_box1" autocomplete="off" name="shipmentcity" value="">
		<div id="search_advice_wrapper1" style="display: none;"></div>
		<div class="clr"></div>
		
		<label for="postindex">Индекс</label>
		<input type="text" name="postindex" id="postindex" value="" />
		<div class="clr"></div>
		
		<label for="address">Адресс</label>
		<input type="text" name="address" id="address" value="" />
		<div class="clr"></div>
		
		<label for="notice" style="height: 60px; display: block; float: left;">Примечание</label>
		<textarea name="notice" id="notice"></textarea>
		<div class="clr"></div>
		
	</div>
</div>';
$printpage .= '<div class="order_shipment">
	<div class="orederBlockTitle">Способ доставки</div>
		
		<input type="radio" name="typeshipment" id="defaultShipment" value="defaultShipment" checked />
		<label for="defaultShipment">Курьерская доставка (обычная)</label>
		<div class="clr"></div>
		
		
		<input type="radio" name="typeshipment" id="fastShipment" value="fastShipment" />
		<label for="fastShipment">Курьерская доставка (срочная)</label>
		<div class="clr"></div>
	
</div>';
$printpage .= '<div class="order_payment">
		<div class="orederBlockTitle">Способ оплаты</div>
		
		<input type="radio" name="typepayment" id="coinsPay" value="coinsPay" '.$coinsAttributes.'/>
		<label for="coinsPay">Оплата с внутреннего счета (баллами)</label>
		<div class="clr"></div>
		
		
		<input type="radio" name="typepayment" id="schetPay" value="schetPay" '.$schetAttributes.'/>
		<label for="schetPay">Оплата через банк (счет для юр.лиц)</label>
		<div class="clr"></div>
		
		
		<input type="radio" name="typepayment" id="roboPay" value="roboPay" />
		<label for="roboPay">Банковская карта (Робокасса)</label>
		<div class="clr"></div>
	
</div>';
$printpage .= '<div class="order_notice">
	<div class="notice">Минимальная сумма заказа составляет 5000 рублей. В случае, если ваш заказ не превышает указанную сумму, Портал оставляет за собой право на увеличение стоимости доставки на 550 рублей.</div>
	<div class="clr"></div>
	<input type="checkbox" name="iamagree" id="iamagree" value="1" required/>
	<label for="iamagree">Я согласен с условиями доставки и офертой</label>
	<div class="clr"></div>
	<input type="submit" style="float:left;" class="linkDooOrder" name="dooModOrder" value="Оформить заказ" />
</div>
</form>';

return $itemsList.$printpage;

?>