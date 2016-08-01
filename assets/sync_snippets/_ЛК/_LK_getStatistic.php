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

}

$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];
$topage_url= $modx->makeUrl( $currentPage );


if ($event == 'getDisputeItems' && is_numeric($sellerID)) {

	$sql = "SELECT ord.*, disp.date_open FROM ".$modx->getFullTableName( '_orders' )." AS ord 
			INNER JOIN ".$modx->getFullTableName( '_dispute_list' )." AS disp ON ord.id = disp.order_id
			WHERE ord.sellerId = ".$sellerID."
			AND disp.status = 'opened'
			ORDER BY date_w_check DESC LIMIT 100";
	
	$result = mysql_query($sql) or die ('ERR 35261 '.mysql_error());
	
	if ($result && mysql_num_rows($result) > 0 ){
		$row = [];
		
		while ($tmp = mysql_fetch_assoc($result)){
			$row[] = $tmp;
		}
		
		
		return count($row);
		if (count($row) > 0) {
			return $row;
		}else  {
			return false;
		}
		
	}else return false;
	

}

//////////////////////////////////////////////////////////






if ($event == 'getRow' && is_numeric($sellerID)) {
	/*
	$sql = "SELECT ord.*, disp.date_open FROM ".$modx->getFullTableName( '_orders' )." AS ord 
			INNER JOIN ".$modx->getFullTableName( '_dispute_list' )." AS disp ON ord.id = disp.order_id
			INNER JOIN ".$modx->getFullTableName( '_order_items' )." AS list ON ord.id = list.id_order
			WHERE ord.sellerId = ".$webuserinfo['id']."
			AND disp.status = 'opened'
			ORDER BY date_w_check DESC LIMIT 100";
	*/
	
	
	$sql = "SELECT ord.* , usr.firstname, usr.surname  FROM ".$modx->getFullTableName( '_orders' )." AS ord 
			INNER JOIN ".$modx->getFullTableName( '_user' )." AS usr ON ord.user_id = usr.id
			WHERE ord.sellerId = ".$sellerID."
			AND  ord.status = '".$status."'
			ORDER BY date_w_check DESC LIMIT 100";
	
	$result = mysql_query($sql) or die ('ERR 52626 '.mysql_error());
	
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

//////////////////////////////////////////////////////////





if ($event == 'getItemInOrder' && is_numeric($order)) {

	$sql = "SELECT cat.* , oi.price AS buyprice , oi.count AS buycount , oi.price_nds  FROM ".$modx->getFullTableName( '_order_items' )." AS oi
			INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = oi.id_item
			WHERE oi.id_order = ".$order."
			LIMIT 100";
	
	$result = mysql_query($sql) or die ('ERR 563476 '.mysql_error());

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


if ($event == 'getSummItemInOrder' && is_numeric($order)) {

	$sql = "SELECT  oi.price , oi.count , oi.price_nds  FROM ".$modx->getFullTableName( '_order_items' )." AS oi
			INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = oi.id_item
			WHERE oi.id_order = ".$order."
			LIMIT 100";
	
	$result = mysql_query($sql) or die ('ERR 563476 '.mysql_error());

	
	if ($result && mysql_num_rows($result) > 0 ){
		$returnedArr ['distinctItem'] = 0;
		$returnedArr ['summ'] = 0;
		$returnedArr ['summWithNDS'] = 0;
		
		while ($tmp = mysql_fetch_assoc($result)){
			$returnedArr ['distinctItem']++;
			$returnedArr ['summ'] = $returnedArr ['summ'] + $tmp['price'] * $tmp['count'] ;
			$returnedArr ['summWithNDS'] = $returnedArr ['summWithNDS'] + $tmp['price_nds'] * $tmp['count'] ;
		}
		

		return $returnedArr;
	}else return false;
	
}









//in statistic

if ($event == 'getAllEnded' && is_numeric($sellerID)) {

	$sql = "SELECT ord.* , hist.event  FROM ".$modx->getFullTableName( '_orders' )." AS ord
			INNER JOIN ".$modx->getFullTableName( '_coins_history' )." AS hist ON ord.id = hist.shop_id
			WHERE ord.sellerId = ".$sellerID." 
			AND ord.status = 'ended'
			AND hist.type = 'payUp'
			LIMIT 100";
	
	$result = mysql_query($sql) or die ('ERR 453 '.mysql_error());

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


//getBigestItemBuy

if ($event == 'getBigestItemBuy' && is_numeric($sellerID)) {

	//$sql = "SELECT DISTINCT id_item, COUNT(*) cnt FROM ".$modx->getFullTableName( '_order_items' )." GROUP BY id_item ORDER BY cnt DESC LIMIT 10";
	
	$sql = "SELECT DISTINCT oi.id_item, SUM(`oi`.`count`) summItem, COUNT(*) cnt , cat.* FROM ".$modx->getFullTableName( '_order_items' )." AS oi
			INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = oi.id_item
			INNER JOIN ".$modx->getFullTableName( '_orders' )." AS ord ON oi.id_order = ord.id
			INNER JOIN ".$modx->getFullTableName( '_coins_history' )." AS hist ON hist.shop_id = ord.id
			WHERE hist.`type` = 'payUp'
			AND cat.seller = ".$sellerID."
			GROUP BY oi.id_item ORDER BY cnt DESC LIMIT 10";
	
	$result = mysql_query($sql) or die ('ERR 4325 '.mysql_error());
	//return mysql_fetch_assoc($result);
	
	
	
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




//in getTopSeeItem

if ($event == 'getTopSeeItem' && is_numeric($sellerID) && isset($type)) {

	$sql = "SELECT DISTINCT css.id_cat, COUNT(css.id_cat) cnt , css.t_stamp, cat.*   FROM ".$modx->getFullTableName( '_catalog_see_stat' )." as css
			INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = css.id_cat
			WHERE css.`type` = '".$type."'
			AND cat.seller = ".$sellerID."
			GROUP BY css.id_cat ORDER BY cnt DESC LIMIT 10";
	
	$result = mysql_query($sql) or die ('ERR 453 '.mysql_error());

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


//======================MAIN RESOLVE FUNC START

if ($event == 'getHTML' && isset($type) ) {
	//==========CALCULATE BLOCK START
	$inCalculateShip = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getRow', 'status' => $type, 'sellerID' => $webuserinfo['id']));
	
	$countInCalculateShip = count ($inCalculateShip);
	$inCalculateShipBlock = '';

	
	if (!is_array($inCalculateShip)){
		return false;
	}
	
	if ($countInCalculateShip < 1) {
		return false;
	}
	
	foreach ($inCalculateShip as $calcShipRow) {
		
		
		/*
		$itemListData = '';
		$itemInOrder = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getItemInOrder', 'order' => $calcShipRow['id']));
		$summsInOrder = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getSummItemInOrder', 'order' => $calcShipRow['id']));
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
		
		
		*/
		
		/*
		if ($calcShipRow['status'] == 'calculateShipment'){
			$cellStatusContent = 'Расчет доставки';
			$dateToDie = 'Доставка будет расчитана до '.date("d.m.Y H:i", $row['date_w_check'] + 60*60*24);
		}
		*/
		
		if ($calcShipRow['status'] == 'calculateShipment'){
			$cellStatusContent = 'Расчет доставки';
			$dateToDie = 'Доставка будет расчитана до '.date("d.m.Y H:i", $row['date_w_check'] + 60*60*24);
			$h2Text = 'Сейчас происходит расчет доставки';
		}elseif ($calcShipRow['status'] == 'waitPayment'){
			$cellStatusContent = 'Ожидание оплаты';
			$dateToDie = 'Необходимо оплатить до '.date("d.m.Y H:i", $row['date_w_coins'] + 60*60*24*5);	
			$h2Text = 'Ожидается оплата';
		}elseif ($calcShipRow['status'] == 'waitShipment'){
			$cellStatusContent = 'Ожидается отправка';	
			$dateToDie = 'Бедет отправлено до '.date("d.m.Y H:i", $row['date_w_ship'] + 60*60*24*5);
			$h2Text = 'Ожидается отправка';
		}elseif ($calcShipRow['status'] == 'inShipment'){
			$cellStatusContent = 'Товар отправлен';
			$dateToDie = 'Будет доставлено до '.date("d.m.Y H:i", $row['date_w_ship'] + 60*60*24*45);
			$h2Text = 'Товаров в пути';
		}elseif ($calcShipRow['status'] == 'waitTesting'){
			$cellStatusContent = 'Тестирование';	
			$dateToDie = 'Необходимо протестировать до '.date("d.m.Y H:i", $row['date_w_test'] + 60*60*24*5);	
			$h2Text = 'Тестирование товаров';
		}elseif ($calcShipRow['status'] == 'waitEnd'){
			$cellStatusContent = 'Завершен';
			$dateToDie = 'Завершен '.date("d.m.Y H:i", $row['date_end']);
			$h2Text = 'Ожидают подтверждения завершения';
		}elseif ($calcShipRow['status'] == 'ended'){
			$cellStatusContent = 'Завершен';
			$dateToDie = 'Завершен '.date("d.m.Y H:i", $row['date_end']);
			$h2Text = 'Завершенный заказ';
		}
		
		
		
		$inCalculateShipBlock .= '
		<div class="openedDisputeLine">
			<div class="stat_orederNumber" data-idOrder="'.$calcShipRow['id'].'">#'.$calcShipRow['order_number'].'</div>
			<div class="stat_buyer">'.$calcShipRow['firstname'].' '.$calcShipRow['surname'].'</div>
			<div class="stat_dateOrder">'.date("d.m.Y H:i", $calcShipRow['date_w_check']).'</div>
			<div class="stat_dateOpenDispute">&nbsp;</div>
			<div class="stat_status">'.$cellStatusContent.'</div>
			<div class="stat_messages"><span>подробнее</span><div class="seeItemsInOrder">'.$itemListData.'</div></div>
		</div>
		<div class="clr"></div>';
	}

	$calculateShipBlock = '
	<div class="statisticMainLine openedDispute ">
	
		<span>'.$h2Text.' : '.$countInCalculateShip.'</span>
		<div class="seeMoreStat"><img src="/template/images/dropDownList.png"></div>
		<div class="clr"></div>
		<div class="seeMoreStatBlock" id="smstas_dispute">
			'.$inCalculateShipBlock.'
		</div>
		<div class="clr"></div>
	</div>
	
	<div class="clr"></div>';

	//===========CALCULATE BLOCK END
	
	return $calculateShipBlock;
}





?>