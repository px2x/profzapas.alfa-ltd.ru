<?php

$lk= 106;
$reg= 107;
$auth= 108;
$payBuy = 208;
$ordersPage = 117;
$load_bill_page = $_SERVER['$_SERVER'].'genFromTemp.php';

//HTTP_HOST

$topage_url= $modx->makeUrl( $modx->documentIdentifier );
$toBuyPayPage_url = $modx->makeUrl($payBuy);


if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] ){
	header( 'location: '. $modx->makeUrl( $auth ) );
	exit();
}

$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];


if (is_numeric($_GET['payId'])) {
	$payId = addslashes($_GET['payId']);
	$timestamp = time();
	$sql = "SELECT * FROM  ".$modx->getFullTableName( '_orders' )." WHERE id = {$payId} LIMIT 1";
	$result = mysql_query($sql) or die ("Error: 01534267".mysql_error());
	if ($row = mysql_fetch_assoc($result)){

		if (is_numeric($row['order_number']) && $row['w_check'] < 0 && is_numeric($row['price_shipment'])){
			/////======типы оплат
			
			$idorder = $row['id'];
			//return $row['order_number'];
			
			$orderSumm = $modx->runSnippet( '_SHOP_actionBasket', array( 'type'=>'getSummMyOrderINT')) + $row['price_shipment'];


			if ($row['t_payment'] == 'schetPay'){
				$pBuffer = 'В ближайщее время наш Администратор свяжется с Вами c целью уточнения данных для выставления счета.';
				
				
				header( 'location: '. $load_bill_page."?orederId=".$payId );
				exit();
				
				
				//добавть в таблицу инфу о том что нужно выставить счет
				//отослать на почту
			}

			if ($row['t_payment'] == 'roboPay'){
				//здесь должно все отправляться в робокассу GET
				//result длжен изменить знач в таб _ORDERS
				//result длжен ЗАПИСАТЬ Значения в _COINS & _COINS_history c id заказа (( прииер ниже в ($row['t_payment'] == 'coinsPay'
				//Этот код потом убрать (В RESULT)
				//дописать проверки 
				//нужно достать цену закза и доставки и номер заказа - отправть на робокассу
				//$sql = "UPDATE ".$modx->getFullTableName( '_orders' )." SET status = 'waitShipment', date_w_coins = '{$timestamp}' , w_coins = -1 , w_ship = 1 WHERE id = {$payId} LIMIT 1";
				//mysql_query($sql) or die ("Error: 01947". mysql_error());
				//
				//здесь пересчитывать хеш и обновлять
	
			
				header( 'location: '. $modx->makeUrl( $ordersPage ) );
				exit();			
				
			}


			if ($row['t_payment'] == 'coinsPay'){
				//а вот тут пи**ец
				
				//select summ wait output
				$sqlSummOutput = "SELECT SUM(sum) FROM  ".$modx->getFullTableName( '_points_up' )." WHERE id_user =  '".$webuserinfo['id']."' AND type = 'output'  AND status = 'wait' LIMIT 1";
				$resultSummOutput = mysql_query($sqlSummOutput) or die ('Error SUMMPrep coins history');
				$coinsSummOutputInt = mysql_fetch_row($resultSummOutput)[0];
				
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
				
				
				$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$webuserinfo['id']."' LIMIT 1";
				$resultSumm = mysql_query($sqlSumm) or die ('Error SUMM coins history');
				$coinsSumm = mysql_fetch_row($resultSumm)[0] - $coinsSummOutputInt - $resultNotConfirmInt;
				//оплаченный аванс
				
				$sqlSummPrepayPay = "SELECT SUM(ABS(event)) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$webuserinfo['id']."' AND type = 'prepaymentPAY' AND event < 0 LIMIT 1";
				$resultSummPrepayPay = mysql_query($sqlSummPrepayPay) or die ('Error в качестве аванса');
				$coinsSummPrepayPay = mysql_fetch_row($resultSummPrepayPay)[0];

				$myCoinsSum = $coinsSumm +$coinsSummPrepayPay; //хранит все доступные средства
				
				//return $myCoinsSum ;
				//$myOrderSum = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getSummMyOrderINT')); //сумма заказа
				//return $myOrderSum ;
				
				
				//переделать
				$sqlAllSums = "SELECT SUM(count) AS sumcount, SUM(price) AS sumprice, SUM(id_item) AS sumiditem, SUM(id_user) AS sumiduser FROM  ".$modx->getFullTableName( '_order_items' )." WHERE id_order = '{$idorder}' ";
				$resultSumm = mysql_query($sqlAllSums) or die ("Error: 213947".mysql_error());
				$sumsVal = mysql_fetch_assoc($resultSumm);
					
				//return $calculatedHash;
				//return print_r($sumsVal);
				
				$rowsItem = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyOrdersItemsList', 'idorder' => $idorder));
				$allsumOrder = 0;
				foreach ($rowsItem as $rowItem) {
					$allsumOrder = $allsumOrder + $rowItem['price'] * $rowItem['count'];
				}
				
				
				//
				//allsumOrder - реальная стоимость заказа - цена + кол-во
				//$sumsVal['sumprice'] - сумма товаров в заказе по 1 шт. ( только для расчета хеша)
				//allsumOrder  - сумма заказа без доставки
				
				$sqlSumShipment = "SELECT price_shipment FROM  ".$modx->getFullTableName( '_orders' )." WHERE id = '{$idorder}' ";
				$resultSumShipment = mysql_query($sqlSumShipment) or die ("Error: 45947".mysql_error());
				$sumShipment = mysql_fetch_assoc($resultSumShipment)['price_shipment'];
				
				$sqlEtalonHash = "SELECT haval FROM  ".$modx->getFullTableName( '_orders_ha' )." WHERE id_order = '{$idorder}' ";
				$resultEtalonHash = mysql_query($sqlEtalonHash) or die ("Error: 7947".mysql_error());
				$etalHash = mysql_fetch_assoc($resultEtalonHash)['haval'];
				
				
				
				$sqlIdSeller = "SELECT sellerId FROM  ".$modx->getFullTableName( '_orders' )." WHERE id = '{$idorder}' ";
				$resultIdSeller = mysql_query($sqlIdSeller) or die ("Error: 63573".mysql_error());
				$idSeller = mysql_fetch_assoc($resultIdSeller)['sellerId'];
				
				
				//return $etalHash;
				
				//CALCULATE HASH
				//$calculatedHash = md5($sumsVal['sumcount']+$sumsVal['sumprice']+$sumsVal['sumiditem']+$sumsVal['sumiduser']+$sumShipment.$row['status']);
				$calculatedHash = ($sumsVal['sumcount'].'_'.$sumsVal['sumprice'].'_'.$sumsVal['sumiditem'].'_'.$sumsVal['sumiduser'].'_'.$sumShipment.$row['status']);
				//return $calculatedHash;
				$sumWithoutShipment = $allsumOrder;
					
				$allsumOrder = $allsumOrder + $sumShipment;
				$sumWithoutNDS = $allsumOrder;
				
				$allsumOrder = ceil($allsumOrder + $allsumOrder / 100 *18);
				//$sumShipment - стоимость доставки 
				
				if ($allsumOrder > $myCoinsSum){
					return "Недостаточно средств на счете";
				}else {
					if ($etalHash == $calculatedHash) {
						
						//доступный аванс
						$sqlSummPrepay = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$webuserinfo['id']."' AND type = 'prepayment' LIMIT 1";
						$resultSummPrepay = mysql_query($sqlSummPrepay) or die ('Error SUMMPrep coins history');
						$coinsSummPrepay = mysql_fetch_row($resultSummPrepay)[0];
						
						$summOrder = $allsumOrder;
						//return $summOrder;
						//$summOrderWithoutPrepay = $summOrder;
						if ($coinsSummPrepay > 0) {
							if ($coinsSummPrepay <= $summOrder ) {
								//оплата частями - сначало доступным авансом - затем остаток нормальными дентгами 
								$summOrderPrepay = $coinsSummPrepay;
								$summOrderWithoutPrepay = $summOrder-$coinsSummPrepay;
								$resultOpperate = $modx->runSnippet( 'spentPrepayment', array( 'toDBevent'=>$summOrderPrepay  , 'toDBshopId' => $payId));
								//return $resultOpperate."--".$summOrderPrepay;
								if ($resultOpperate == $summOrderPrepay) {
									$resultOpperateW = $modx->runSnippet( 'spentCoins', array( 'toDBevent'=>$summOrderWithoutPrepay  , 'toDBshopId' => $payId));
									if ($resultOpperateW == $summOrderWithoutPrepay) {
										//если дентги списаны - перекидываем их продавцу
										$resultOpperateUpSeller = $modx->runSnippet( 'sellerUpCoins', array( 'toDBevent'=>$summOrderWithoutPrepay + $summOrderPrepay - $sumShipment, 'toDBshopId' => $payId, 'toDBsumShip' => $sumShipment, 'toDBsellerId' =>$idSeller));
										if ($resultOpperateUpSeller ){
											$statusPAY = 'ok';
											//return 'Оплачено';
										}
										
									}else return "ERROR 26373";
								}else return "ERROR 78269";
							}else {
								//вызов сниппета оплаты авансом (ТОЛЬКО)
								$resultOpperate = $modx->runSnippet( 'spentPrepayment', array( 'toDBevent'=>$summOrder  , 'toDBshopId' => $payId));
								if ($resultOpperate == $summOrder) {
									$resultOpperateUpSeller = $modx->runSnippet( 'sellerUpCoins', array( 'toDBevent'=>$sumWithoutShipment , 'toDBshopId' => $payId , 'toDBsumShip' => $sumShipment,  'toDBsellerId' =>$idSeller));
										if ($resultOpperateUpSeller ){
											$statusPAY = 'ok';
											//return 'Оплачено';
										}
									
									
			
								}else  return "ERROR 24373";
							}
							
						} else {
							//вызов сниппета оплаты нормальными деьгами (ТОЛЬКО)
							$resultOpperateW = $modx->runSnippet( 'spentCoins', array( 'toDBevent'=>$summOrder  , 'toDBshopId' => $payId));
							if ($resultOpperateW == $summOrder) {
								$resultOpperateUpSeller = $modx->runSnippet( 'sellerUpCoins', array( 'toDBevent'=>$sumWithoutShipment , 'toDBshopId' => $payId, 'toDBsumShip' => $sumShipment,  'toDBsellerId' =>$idSeller));
								if ($resultOpperateUpSeller ){
									$statusPAY = 'ok';
									//return 'Оплачено';
								}
							}else return "ERROR 26873";
							//вызов сниппета оплаты личными средствами
						}
						//все ок - списываем деньги
						if ($statusPAY == 'ok') {
							$timestamp = time();
							$sqlSummPrepay = "UPDATE ".$modx->getFullTableName( '_orders' )." SET w_coins = -1 , date_w_ship = '{$timestamp}', w_ship = 1, status = 'waitShipment'  WHERE id =  '{$payId}'  LIMIT 1";
							$resultPay = mysql_query($sqlSummPrepay) or die ("ERROR 62578 ". mysql_error());
							if ($resultPay){
								//здесь пересчитывать хеш и обновлять
								
								$sqlAllSums = "SELECT SUM(count) AS sumcount, SUM(price) AS sumprice, SUM(id_item) AS sumiditem, SUM(id_user) AS sumiduser FROM  ".$modx->getFullTableName( '_order_items' )." WHERE id_order = '{$payId}' ";
								$resultSumm = mysql_query($sqlAllSums) or die ("Error: 21457".mysql_error());
								$sumsVal = mysql_fetch_assoc($resultSumm);
								$sumShipment = $row['price_shipment'];
								$calculatedHash = ($sumsVal['sumcount'].'_'.$sumsVal['sumprice'].'_'.$sumsVal['sumiditem'].'_'.$sumsVal['sumiduser'].'_'.$sumShipment.'waitShipment');
								//пересчет хеша UPDATE ORDERS_HA
								$sqlUpdateHash = "UPDATE  ".$modx->getFullTableName( '_orders_ha' )." SET haval = '$calculatedHash' WHERE id_order = '{$idorder}' ";
								$resultUpdateHash = mysql_query($sqlUpdateHash) or die ("Error: 2136247".mysql_error());
								
								if ($resultUpdateHash) {
									header( 'location: '. $modx->makeUrl( $ordersPage ).'?tab=1&event=ok&payId='.$idorder );
									exit();
								
								}
								
								
							}
						}
						
					}else {
						return "Ошибка внутреннего счета";
					}
				
				}
				
			}

			/////======типы оплат
		}else {
			return 'error:2763';
		}//err
	}else{
		return 'error:3572';
	}	
	
}else {
	//err
}
return $pBuffer;





?>