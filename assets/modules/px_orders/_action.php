<?php

//define('ROOT', dirname(__FILE__).'/');
define('ROOT', $_SERVER['DOCUMENT_ROOT']);

$sc_site= 'profzapas.ru';
$sm_base= '../assets/modules/px_orders/';


$module_url= MODX_MANAGER_URL .'?a='. $_GET[ 'a' ] .'&id='. $_GET[ 'id' ];
//$module_url_orders= MODX_MANAGER_URL .'?a=112&id=3';


$webuser= intval( $_GET[ 'wu' ] );

if (isset( $_GET[ 'spg' ])){
	$subpage= $_GET[ 'spg' ];
}else {
	$subpage ='calcShipment';
}

$act= $_GET[ 'act' ];

/*
**
*
*
*
*
		if ($row['status'] == 'aborted'){
			$cellStatusContent = 'Отменен';
			$actionButtons = '';
		}elseif ($row['status'] == 'calculateShipment'){
			$cellStatusContent = 'Расчет доставки';
			$actionButtons = '<a href="'.$topage_url.'?abortId='.$row['id'].'" class="redButton">Отменить</a>';
		}elseif ($row['status'] == 'waitPayment'){
			$cellStatusContent = 'Ожидание оплаты';
			$actionButtons = '<a href="'.$toBuyPayPage_url.'?payId='.$row['id'].'" class="yellowButton">Оплатить</a>';
			$actionButtons .= '<a href="'.$topage_url.'?abortId='.$row['id'].'" class="redButton">Отменить</a>';
		}elseif ($row['status'] == 'waitShipment'){
			$cellStatusContent = 'Ожидается отправка';
			$actionButtons = '<a href="'.$topage_url.'?disputeId='.$row['id'].'"  class="blueButton">Открыть спор</a>';
		}elseif ($row['status'] == 'inShipment'){
			$cellStatusContent = 'Товар отправлен';
			$actionButtons = '<a href="'.$topage_url.'?disputeId='.$row['id'].'"  class="blueButton">Открыть спор</a>';
			$actionButtons .= '<a href="'.$topage_url.'?confirmShipId='.$row['id'].'"  class="greenButton">Подтвердить получение</a>';
		}elseif ($row['status'] == 'waitTesting'){
			$cellStatusContent = 'Тестирование';
			$actionButtons = '<a href="'.$topage_url.'?disputeId='.$row['id'].'"  class="blueButton">Открыть спор</a>';
			$actionButtons .= '<a href="'.$topage_url.'?confirmTestId='.$row['id'].'" class="greenButton">Подтвердить качество</a>';
		}elseif ($row['status'] == 'ended'){
			$cellStatusContent = 'Завершен';
			$actionButtons = '';
		}
		

*/


//================================EVENT START=============

if (is_numeric($_GET['orderId'])){
	
	
	 
	
	//оценка доставки
	if (isset($_POST['calcShipBut']) && is_numeric($_POST['calcShipVal'])) {
		$idorder = $_GET['orderId'];
		$sumShipment = $_POST['calcShipVal'];
		$sumShipmentCity = $_POST['calcShipValCity'];
		$sumShipmentAddress = $_POST['calcShipValAddress'];
		$newStatus = 'waitPayment';
		$timestamp = time();
		
		$sqlAllSums = "SELECT SUM(count) AS sumcount, SUM(price) AS sumprice, SUM(id_item) AS sumiditem, SUM(id_user) AS sumiduser FROM  ".$modx->getFullTableName( '_order_items' )." WHERE id_order = '{$idorder}' ";
		$resultSumm = mysql_query($sqlAllSums) or die ("Error: 21457".mysql_error());
		$sumsVal = mysql_fetch_assoc($resultSumm);
		
		
		$sqlUpdateOrders = "UPDATE  ".$modx->getFullTableName( '_orders' )." SET w_check = -1 , w_coins = 1,  date_w_coins = '{$timestamp}', price_shipment = {$sumShipment}, status = '{$newStatus}',
        temp_adress_ship = '{$sumShipmentAddress}' , temp_city_ship = '{$sumShipmentCity}'
        WHERE id = '{$idorder}' ";
		$resultUpdateOrders = mysql_query($sqlUpdateOrders) or die ("Error: 263447".mysql_error());

		if ($resultUpdateOrders){
			//$calculatedHash = md5($sumsVal['sumcount']+$sumsVal['sumprice']+$sumsVal['sumiditem']+$sumsVal['sumiduser']+$sumShipment.$newStatus);
			$calculatedHash = ($sumsVal['sumcount'].'_'.$sumsVal['sumprice'].'_'.$sumsVal['sumiditem'].'_'.$sumsVal['sumiduser'].'_'.$sumShipment.$newStatus);
			//пересчет хеша UPDATE ORDERS_HA
			$sqlUpdateHash = "UPDATE  ".$modx->getFullTableName( '_orders_ha' )." SET haval = '$calculatedHash' WHERE id_order = '{$idorder}' ";
			$resultUpdateHash = mysql_query($sqlUpdateHash) or die ("Error: 2136247".mysql_error());
			
			//insert to 

			
			
			if (isset($_POST['itemWarehouse'])){

				foreach ($_POST['itemWarehouse'] as $key => $value){
					
					//if (is_numeric($value)){
					if (true){
						//echo $key.'_'.$value;
						$id_item = $key;
						$id_wh = $value;
						
						$sql = "INSERT INTO  ".$modx->getFullTableName( '_orders_fk_warehouse' )." (id , id_order , id_item , id_wh) VALUES (NULL , {$idorder}, {$id_item}, '{$id_wh}') ";
						$resultInsWH = mysql_query($sql) or die ("ERROR 7528956".mysql_error());
						
						
						
					}
				}
			}
            
            
			
			
			
		}	
	}
	
	
	
	
	
	
		
	//заказ завершен - перевести денежку продавцу
	if (isset($_POST['sendCoinsSellerBut']) && is_numeric($_POST['sendCoinsSeller'])) {
		$idorder = $_GET['orderId'];
		$sumSumToSeller = $_POST['sendCoinsSeller'];
		$newStatus = 'ended';
		$timestamp = time();
		
		$sqlAllSums = "SELECT SUM(count) AS sumcount, SUM(price) AS sumprice, SUM(id_item) AS sumiditem, SUM(id_user) AS sumiduser FROM  ".$modx->getFullTableName( '_order_items' )." WHERE id_order = '{$idorder}' ";
		$resultSumm = mysql_query($sqlAllSums) or die ("Error: 21457".mysql_error());
		$sumsVal = mysql_fetch_assoc($resultSumm);
		
		
		
		$sqlPriceShipment = "SELECT price_shipment  FROM  ".$modx->getFullTableName( '_orders' )." WHERE id= '{$idorder}' ";
		$resultPriceShipment = mysql_query($sqlPriceShipment) or die ("Error: 21457".mysql_error());
		$arrTmpl = mysql_fetch_assoc($resultPriceShipment);
		$sumShipment = $arrTmpl['price_shipment'];
	
		
		
		$sqlUpdateOrders = "UPDATE  ".$modx->getFullTableName( '_orders' )." SET date_end = '{$timestamp}',  status = '{$newStatus}' WHERE id = '{$idorder}' ";
		$resultUpdateOrders = mysql_query($sqlUpdateOrders) or die ("Error: 263447".mysql_error());

		if ($resultUpdateOrders){
			//$calculatedHash = md5($sumsVal['sumcount']+$sumsVal['sumprice']+$sumsVal['sumiditem']+$sumsVal['sumiduser']+$sumShipment.$newStatus);
			$calculatedHash = ($sumsVal['sumcount'].'_'.$sumsVal['sumprice'].'_'.$sumsVal['sumiditem'].'_'.$sumsVal['sumiduser'].'_'.$sumShipment.$newStatus);
			//пересчет хеша UPDATE ORDERS_HA
			$sqlUpdateHash = "UPDATE  ".$modx->getFullTableName( '_orders_ha' )." SET haval = '{$calculatedHash}' WHERE id_order = '{$idorder}' ";
			$resultUpdateHash = mysql_query($sqlUpdateHash) or die ("Error: 2136247".mysql_error());
			
			if ($resultUpdateHash ) {
				
				
				$sql = "SELECT sellerId, price_shipment FROM  ".$modx->getFullTableName( '_orders' )." WHERE id = '{$idorder}' ";
				$result = mysql_query($sql) or die ('Error 346357');
				if ($toDBsellerIdArr = mysql_fetch_assoc($result)){
					$toDBsellerId= $toDBsellerIdArr['sellerId'];
					$toDBshipHash= $toDBsellerIdArr['price_shipment'];
				}
				
				
				$toDBdate = time();
				$toDBtype = 'payUp';
				$userId = $webuserinfo['id'];
				$toDBevent = $sumSumToSeller;

				$sqlNewEvent = "UPDATE  ".$modx->getFullTableName( '_coins_history' )." SET event = '".$toDBevent."' , date = '".$toDBdate."' , `key` = 1 WHERE type = 'payUp' AND  id_user =  '".$toDBsellerId."' AND shop_id = '".$idorder."' LIMIT 1";
				$resultNewEvent = mysql_query($sqlNewEvent) or die ('Error 645748' .mysql_error());
				if ($resultNewEvent){
					
					$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$toDBsellerId."' LIMIT 1";
					$resultSumm = mysql_query($sqlSumm) or die ('Error 4693238' .mysql_error());
					if (!$coinsSummHH = mysql_fetch_row($resultSumm)){
						exit();
					}else {
						
						$coinsSummHH = $coinsSummHH[0];
					}
									
					
					//$hash = md5($toDBdate.$toDBevent.$toDBtype.$toDBsellerId.$coinsSummHH);
                    $hash = ($toDBdate.'_'.$toDBevent.'_'.$toDBtype.'_'.$toDBsellerId.'_'.$coinsSummHH);
					//$hash = ($toDBdate.'_'.$toDBevent.'_'.$toDBtype.'_'.$toDBsellerId.'_'.$coinsSummHH);
					$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins' )." (`id`, `id_user`, `hash` ) VALUES (NULL, '{$toDBsellerId}', '{$hash}') ON DUPLICATE KEY UPDATE `hash` = '".$hash."' ") or die ("Error 2628: ".mysql_error());
					$result = mysql_query("SELECT id FROM ".$modx->getFullTableName( '_coins' )." WHERE  id_user = '{$toDBsellerId}' LIMIT 1") or die ("Error 53462: ".mysql_error());
					if (!$id_coins = mysql_fetch_assoc($result)['id']) {
						exit();
					}
                    
                     $sqlEvent = "INSERT INTO  ".$modx->getFullTableName( '_finances_events' )."  
                    (`timestamp`, `id_user`, `see`) VALUES 
                    (UNIX_TIMESTAMP(), '".$toDBsellerId."', 0)";
                    $resultEvent = mysql_query($sqlEvent);
				
						
					
					
				}
				
				
	
				
			}

			
			
		}	
	}
	
	
	
	
	
	
	//подтвердить оплату 
	if ($_GET['event'] == 'confirmPay') {
		$idorder = $_GET['orderId'];
		//$sumShipment = $_POST['calcShipVal'];
		$newStatus = 'waitShipment';
		$timestamp = time();
		
		$sqlAllSums = "SELECT SUM(count) AS sumcount, SUM(price) AS sumprice, SUM(id_item) AS sumiditem, SUM(id_user) AS sumiduser FROM  ".$modx->getFullTableName( '_order_items' )." WHERE id_order = '{$idorder}' ";
		$resultSumm = mysql_query($sqlAllSums) or die ("Error: 21457".mysql_error());
		$sumsVal = mysql_fetch_assoc($resultSumm);
		
		$sqlPriceShipment = "SELECT price_shipment , sellerId FROM  ".$modx->getFullTableName( '_orders' )." WHERE id= '{$idorder}' ";
		$resultPriceShipment = mysql_query($sqlPriceShipment) or die ("Error: 21457".mysql_error());
		$arrTmpl = mysql_fetch_assoc($resultPriceShipment);
		$sumShipment = $arrTmpl['price_shipment'];
		$sellerId = $arrTmpl['sellerId'];
		
		$sqlUpdateOrders = "UPDATE  ".$modx->getFullTableName( '_orders' )." SET w_coins = -1 , w_ship = 1, date_w_ship = '{$timestamp}', status = '{$newStatus}' WHERE id = '{$idorder}' ";
		$resultUpdateOrders = mysql_query($sqlUpdateOrders) or die ("Error: 234747".mysql_error());

		if ($resultUpdateOrders){
			//$calculatedHash = md5($sumsVal['sumcount']+$sumsVal['sumprice']+$sumsVal['sumiditem']+$sumsVal['sumiduser']+$sumShipment.$newStatus);
			$calculatedHash = ($sumsVal['sumcount'].'_'.$sumsVal['sumprice'].'_'.$sumsVal['sumiditem'].'_'.$sumsVal['sumiduser'].'_'.$sumShipment.$newStatus);
			//пересчет хеша UPDATE ORDERS_HA
			$sqlUpdateHash = "UPDATE  ".$modx->getFullTableName( '_orders_ha' )." SET haval = '$calculatedHash' WHERE id_order = '{$idorder}' ";
			$resultUpdateHash = mysql_query($sqlUpdateHash) or die ("Error: 2136247".mysql_error());
			
			
			if ($resultUpdateHash ){
				
				
				$resultItemsInOrder= mysql_query( "SELECT ord.id_item,  ord.price , ord.count ,cat.title , user.email , cat.parent , cat.code
										FROM ".$modx->getFullTableName( '_order_items' )." AS ord
										INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id =  ord.id_item
										INNER JOIN ".$modx->getFullTableName( '_user' )." AS user ON cat.seller =  user.id
										WHERE  ord.id_order = '".$idorder."'  LIMIT 200" ) or die(mysql_error());
				if (mysql_num_rows($resultItemsInOrder ) > 0) {
					$rowsItem = [];
					while 	($rowtmp = mysql_fetch_assoc($resultItemsInOrder)){
						$rowsItem[] = $rowtmp;
					}
					//return $rowsItem;
				} //else return false;
				
				
				$allsumOrder = 0;
				foreach ($rowsItem as $rowItem) {
					$allsumOrder = $allsumOrder + $rowItem['price'] * $rowItem['count'];
				}
			
		
		
				
				//перебирать foreach
				//$summOrder = $allsumOrder  + $sumShipment;
				$resultOpperateUpSeller = $modx->runSnippet( 'sellerUpCoins', array( 'toDBevent'=>$allsumOrder , 'toDBshopId' => $idorder , 'toDBsumShip' => $sumShipment, 'toDBsellerId' =>$sellerId));
					if ($resultOpperateUpSeller ){
						$statusPAY = 'ok';
							//return 'Оплачено';
					}
			}
			
			
			
			
		}	
		
	}
	
	
	
	//подтвердить отправку
	//confirmSendShip
	if ($_GET['event'] == 'confirmSendShip') {
		$idorder = $_GET['orderId'];
		//$sumShipment = $_POST['calcShipVal'];
		$newStatus = 'inShipment';
		$timestamp = time();
		
		$sqlAllSums = "SELECT SUM(count) AS sumcount, SUM(price) AS sumprice, SUM(id_item) AS sumiditem, SUM(id_user) AS sumiduser FROM  ".$modx->getFullTableName( '_order_items' )." WHERE id_order = '{$idorder}' ";
		$resultSumm = mysql_query($sqlAllSums) or die ("Error: 21457".mysql_error());
		$sumsVal = mysql_fetch_assoc($resultSumm);
		
		$sqlPriceShipment = "SELECT price_shipment FROM  ".$modx->getFullTableName( '_orders' )." WHERE id= '{$idorder}' ";
		$resultPriceShipment = mysql_query($sqlPriceShipment) or die ("Error: 21457".mysql_error());
		$sumShipment = mysql_fetch_assoc($resultPriceShipment)['price_shipment'];
		
		
		$sqlUpdateOrders = "UPDATE  ".$modx->getFullTableName( '_orders' )." SET w_coins = -1 , w_ship = 1, date_w_ship = '{$timestamp}', status = '{$newStatus}' WHERE id = '{$idorder}' ";
		$resultUpdateOrders = mysql_query($sqlUpdateOrders) or die ("Error: 234747".mysql_error());

		if ($resultUpdateOrders){
			//$calculatedHash = md5($sumsVal['sumcount']+$sumsVal['sumprice']+$sumsVal['sumiditem']+$sumsVal['sumiduser']+$sumShipment.$newStatus);
			$calculatedHash = ($sumsVal['sumcount'].'_'.$sumsVal['sumprice'].'_'.$sumsVal['sumiditem'].'_'.$sumsVal['sumiduser'].'_'.$sumShipment.$newStatus);
			//пересчет хеша UPDATE ORDERS_HA
			$sqlUpdateHash = "UPDATE  ".$modx->getFullTableName( '_orders_ha' )." SET haval = '$calculatedHash' WHERE id_order = '{$idorder}' ";
			$resultUpdateHash = mysql_query($sqlUpdateHash) or die ("Error: 2136247".mysql_error());
			
			//выковыриваем кол - во чтоб отнять от склада
			$sql = "SELECT ofkwh.id_item , ofkwh.id_wh , cwh.quantity , oi.count FROM  ".$modx->getFullTableName( '_orders_fk_warehouse' )." AS ofkwh
					LEFT JOIN ".$modx->getFullTableName( '_catalog_warehouse' )." AS cwh ON ofkwh.id_item = cwh.item AND ofkwh.id_wh = cwh.warehouse
					INNER JOIN ".$modx->getFullTableName( '_order_items' )." AS oi ON ofkwh.id_item = oi.id_item AND ofkwh.id_order = oi.id_order
					
					WHERE ofkwh.id_order = '{$idorder}' ";
			$resOFKWH = mysql_query ($sql) or die ('ERR: 76784623'.mysql_error()) ;
			if ($resOFKWH) {
				if (mysql_num_rows($resOFKWH)>0){
					while ($resVal = mysql_fetch_assoc($resOFKWH)){
						if (is_numeric($resVal['id_wh'])){
							$newQyantity = $resVal['quantity'] - $resVal['count'];
							//здесь если не хватает на одном складн то отнимать от другого .....наверн
							mysql_query ("UPDATE  ".$modx->getFullTableName( '_catalog_warehouse' )." SET quantity = {$newQyantity} WHERE item = ".$resVal['id_item']." AND warehouse =".$resVal['id_wh']." LIMIT 1" ) or die ('ERR:3463437');
							$resSummQ = mysql_query ("SELECT SUM(quantity) AS summQ FROM   ".$modx->getFullTableName( '_catalog_warehouse' )." WHERE item = ".$resVal['id_item']."  LIMIT 1" ) or die ('ERR:74573');
							if ($resSummQVal = mysql_fetch_assoc($resSummQ)['summQ']){
								mysql_query ("UPDATE  ".$modx->getFullTableName( '_catalog' )." SET in_stock = {$resSummQVal} WHERE id = ".$resVal['id_item']." LIMIT 1" ) or die ('ERR:64576');
								//mysql_query ("DELETE FROM  ".$modx->getFullTableName( '_orders_fk_warehouse' )."  WHERE id_item = ".$resVal['id_item']." id_order = ".$idorder."   LIMIT 1" ) or die ('ERR:65763f'.mysql_error());
								mysql_query ("DELETE FROM ".$modx->getFullTableName( '_orders_fk_warehouse' )." WHERE id_order = ".$idorder." AND id_item =  ".$resVal['id_item'] ) or die ('ERR:356838'.mysql_error());
								
								$resMiddle = mysql_query ("SELECT id , item  ,quantity FROM ".$modx->getFullTableName( '_catalog_warehouse' )." WHERE quantity < 0 " ) or die ('ERR:3473'.mysql_error());
								while ($resMiddleVal = mysql_fetch_assoc($resMiddle)){
									mysql_query ("UPDATE ".$modx->getFullTableName( '_catalog_warehouse' )." SET quantity = 0 WHERE id = ".$resMiddleVal['id'] ) or die ('ERR:54547'.mysql_error());
									$resMiddleNxt = mysql_query ("SELECT id , quantity  FROM ".$modx->getFullTableName( '_catalog_warehouse' )." WHERE quantity > 0  AND item = ".$resMiddleVal['item'] ) or die ('ERR:653'.mysql_error());
									while ($resMiddleNxtVal = mysql_fetch_assoc($resMiddleNxt)){
										$newQyantityReplaseMid = $resMiddleNxtVal['quantity'] + $resMiddleVal['quantity'];
										mysql_query ("UPDATE ".$modx->getFullTableName( '_catalog_warehouse' )." SET quantity = '{$newQyantityReplaseMid}' WHERE id = '".$resMiddleNxtVal['id']."'" ) or die ('ERR:54547'.mysql_error());
									}
									
								}
								
							}
						}else{
							echo $resVal['id_item'];
							$resInStock = mysql_query ("SELECT in_stock FROM ".$modx->getFullTableName( '_catalog' )."  WHERE id = ".$resVal['id_item']." LIMIT 1" ) or die ('ERR:4726378');
							if ($resInStockVal = mysql_fetch_assoc($resInStock)['in_stock']){
								echo $resInStockVal;
								$resInStockVal = $resInStockVal - $resVal['count'];
								mysql_query ("UPDATE  ".$modx->getFullTableName( '_catalog' )." SET in_stock = {$resInStockVal} WHERE id = ".$resVal['id_item']." LIMIT 1" ) or die ('ERR:36737');
								mysql_query ("DELETE FROM ".$modx->getFullTableName( '_orders_fk_warehouse' )." WHERE id_order = ".$idorder." AND id_item =  ".$resVal['id_item'] ) or die ('ERR:835685'.mysql_error());
							}
							
						}
					}
					
					

				}

				//print_r($resVal);
			}
			
			
			
		}	
		
	}
    
    
header( 'location: '.$module_url.'&spg=calcShipment');
exit();   

}




//================================EVENT END===============
//подчет кол-ва событий для выода в заголовках

//ожидают расчета доставуи 
$result = mysql_query("SELECT COUNT(id) FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'calculateShipment' LIMIT 1");
$summCalcShipment = mysql_fetch_row($result)[0];


//ожидают оплаты 
$result = mysql_query("SELECT COUNT(id) FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'waitPayment' LIMIT 1");
$summWaitPayment = mysql_fetch_row($result)[0];

//ожидают отправку 
$result = mysql_query("SELECT COUNT(id) FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'waitShipment' LIMIT 1");
$summWaitShipment = mysql_fetch_row($result)[0];

//в пути
$result = mysql_query("SELECT COUNT(id) FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'inShipment' LIMIT 1");
$summInShipment = mysql_fetch_row($result)[0];


//тенстмрование
$result = mysql_query("SELECT COUNT(id) FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'waitTesting' LIMIT 1");
$summWaitTesting = mysql_fetch_row($result)[0];



//тенстмрование
$result = mysql_query("SELECT COUNT(id) FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'waitEnd' LIMIT 1");
$summEnded = mysql_fetch_row($result)[0];




?>


<div class="modul_scorn_all">
<!-- -------------------------- -->
<link rel="stylesheet" type="text/css" href="<?php print $sm_base;?>_styles.css" />


<script type="text/javascript" src="https://code.jquery.com/jquery-2.2.3.min.js"   integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo="   crossorigin="anonymous"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"   integrity="sha256-DI6NdAhhFRnO2k51mumYeDShet3I8AKCQf/tf7ARNhI="   crossorigin="anonymous"></script>


<script type="text/javascript" src="<?php print $sm_base;?>_script.js"></script>





<div class="topmenu">
	<ul>
		<li><a href="<?= $module_url ?>&spg=calcShipment">Расчет доставки <?= $summCalcShipment> 0 ? '(+'.$summCalcShipment.')': ''  ?></a></li>
		<li><a href="<?= $module_url ?>&spg=waitPayment">Ожидают оплаты <?= $summWaitPayment> 0 ? '(+'.$summWaitPayment.')': ''  ?></a></li>
		<li><a href="<?= $module_url ?>&spg=waitShipment">Ожидают отправку <?= $summWaitShipment> 0 ? '(+'.$summWaitShipment.')': ''  ?></a></li>
		<li><a href="<?= $module_url ?>&spg=inShipment">В пути <?= $summInShipment> 0 ? '(+'.$summInShipment.')': ''  ?></a></li>
		<li><a href="<?= $module_url ?>&spg=waitTesting">Тестирование <?= $summWaitTesting> 0 ? '(+'.$summWaitTesting.')': ''  ?></a></li>
		<li><a href="<?= $module_url ?>&spg=waitEnd">Завершенные <?= $summEnded> 0 ? '(+'.$summEnded.')': ''  ?></a></li>

		
	</ul>
	<div class="clr">&nbsp;</div>
</div>





<div class="_LK_wrapper _LK_wrapper_big">	
	<div class="lkItem_tit">
		<!--div class="checkboxs"></div-->
		<div class="order_number_tit">№ заказа</div>
		<div class="date_w_tit">Дата заказа</div>
		<div class="summOneOrder_tit">Сумма заказа</div>
		<div class="status_tit">Статус</div><div class="action_tit">Действие</div>
		<!--div class="expandDown_tit"> </div-->
		<div class=""></div>
	</div>
	
	
	
	
<?php	













$list = 'wait_check';


	
if ($subpage == 'mainpage' OR $subpage == 'calcShipment'){
//ожидают расчета доставуи 
	$list = 'wait_check';
}	


if ($subpage == 'waitPayment'){
//ожидают расчета доставуи 
	$list = 'wait_coins';
}
	

	
if ($subpage == 'waitShipment'){
//ожидают расчета доставуи 
	$list = 'wait_shipment';
}


if ($subpage == 'inShipment'){
//ожидают расчета доставуи 
	$list = 'in_shipment';
}



if ($subpage == 'waitTesting'){
//ожидают расчета доставуи 
	$list = 'wait_testing';
}

if ($subpage == 'waitEnd'){
//ожидают расчета доставуи 
	$list = 'wait_end';
}



//выборка заказов START
if( true ){
	if (isset($list)){
		if ($list == 'wait_check') $sqlType = "status = 'calculateShipment' AND date_end = '' ";
		if ($list == 'wait_coins') $sqlType = "status = 'waitPayment' AND date_end = ''";
		if ($list == 'wait_shipment') $sqlType = "status = 'waitShipment' AND date_end = ''";
		if ($list == 'in_shipment') $sqlType = "status = 'inShipment'  AND date_end = ''";
		if ($list == 'wait_testing') $sqlType = "status = 'waitTesting' ";
		if ($list == 'wait_end') $sqlType =  "status = 'waitEnd' ";
		//if ($list == 'ended') $sqlType = "status = 'aborted'   OR status = 'ended' OR date_end <>  '' ";
	}
	
	
	$result= mysql_query( "SELECT ord.*, uwh.address, uwh.city FROM ".$modx->getFullTableName( '_orders' )." AS ord
                        LEFT JOIN ".$modx->getFullTableName( '_orders_fk_warehouse' )." AS fkwh ON ord.id = fkwh.id_order
                        LEFT JOIN ".$modx->getFullTableName( '_user_warehouse' )." AS uwh ON uwh.id = fkwh.id_wh
						WHERE  ".$sqlType."  LIMIT 100" ) or die(mysql_error());
	if (mysql_num_rows($result ) > 0) {
		$rowOrder = [];
		while 	($rowtmp = mysql_fetch_assoc($result)){
			$rowOrder[] = $rowtmp;
		}
		//return $row;
	} else return false;
}
	

	

	
	
	
	foreach ($rowOrder as $row) {
		//$rowsItem = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyOrdersItemsList', 'idorder' => $row['id']));
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
			
			
			if ($subpage == 'mainpage' OR $subpage == 'calcShipment') {
			//
				$sqlWH = "SELECT uwh.id AS uwhid , uwh.address, uwh.city, cwh.quantity FROM ".$modx->getFullTableName( '_catalog_warehouse' )." AS cwh 
				INNER JOIN ".$modx->getFullTableName( '_user_warehouse' )." AS uwh ON cwh.warehouse = uwh.id
				WHERE cwh.item = ".$rowItem[ 'id_item' ]." AND cwh.quantity > 0  ";
				$resultWH= mysql_query($sqlWH); 
				$warehouseInputBuffer = '<select name="itemWarehouse['.$rowItem[ 'id_item' ].']" required>';
				
				
				
				if ($resultWH){
					if (mysql_num_rows($resultWH) > 0) {
						
						$warehouseInputBuffer .= '<option value="">Не выбран</option>';
						while ($tmp = mysql_fetch_assoc($resultWH)){
							$warehouseInputBuffer.='<option value='.$tmp['uwhid'].'>'.$tmp['city'].' - '.$tmp['quantity'].' шт. ('.$tmp['address'].')'.'</option>';
						}
					}else {
						$warehouseInputBuffer .= '<option value="noWH" selected>Нет склада</option>';
						
					}
					
						
				}
					
					
				$warehouseInputBuffer .= '</select>';
			}
			
			
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



		
	
	
	
		//$actionButtons  - действия для определенной страницы
		//$module_url
		
		
		if ($row['status'] == 'aborted'){
			$cellStatusContent = 'Отменен';
			$actionButtons = '';
		}elseif ($row['status'] == 'calculateShipment'){
			$cellStatusContent = 'Расчет доставки';
			$actionButtons = '<a href="'.$topage_url.'?abortId='.$row['id'].'" class="redButton">Отменить</a>';
			$actionWareHouse =  '';
		}elseif ($row['status'] == 'waitPayment'){
			$cellStatusContent = 'Ожидание оплаты';
			$actionButtons = '';
			if ($typePayment == 'waitConfirm') {
				$actionButtons = '<a href="'.$module_url.'&spg=waitPayment&event=confirmPay&orderId='.$row['id'].'" class="yellowButton">Подтвердить оплату</a>';
			}
			
			$actionButtons .= '<a href="'.$topage_url.'?abortId='.$row['id'].'" class="redButton">Отменить</a>';
		}elseif ($row['status'] == 'waitShipment'){
			$cellStatusContent = 'Ожидается отправка';
			$actionButtons = '<a href="'.$module_url.'&spg=waitShipment&event=confirmSendShip&orderId='.$row['id'].'" class="yellowButton">Подтвердить отправку</a>';
		}elseif ($row['status'] == 'inShipment'){
			$cellStatusContent = 'Товар отправлен';
			$actionButtons = '<a href="'.$topage_url.'?disputeId='.$row['id'].'"  class="blueButton">Открыть спор</a>';
			$actionButtons .= '<a href="'.$topage_url.'?confirmShipId='.$row['id'].'"  class="greenButton">Подтвердить получение</a>';
		}elseif ($row['status'] == 'waitTesting'){
			$cellStatusContent = 'Тестирование';
			$actionButtons = '<a href="'.$topage_url.'?disputeId='.$row['id'].'"  class="blueButton">Открыть спор</a>';
			$actionButtons .= '<a href="'.$topage_url.'?confirmTestId='.$row['id'].'" class="greenButton">Подтвердить качество</a>';
		}elseif ($row['status'] == 'ended'){
			$cellStatusContent = 'Завершен';
			$actionButtons = '';
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
		
		$calculateShipmentChunk = '';
		if ($subpage == 'mainpage' OR $subpage == 'calcShipment'){
			$calculateShipmentChunk = '<div class="sh_payment"><span>Стоимость доставки: </span><input type="text" name="calcShipVal" style="width: 100;"></div>
            
            <div class="sh_payment"><span>Если склада нет в списке - введите вручную: </span>
            Город: <input type="text" name="calcShipValCity"><br/>
            Адрес: <input type="text" name="calcShipValAddress"><br/>
            <input  name="calcShipBut" type="submit" value="Отправить"></div>
            ';
		}
		
		
		if ($subpage == 'waitEnd'){
			$calculateShipmentChunk = '<div class="sh_payment"><span>Перечислить продавцу: </span><input type="text" style="width:50px;" name="sendCoinsSeller"><span class="rubl">a</span><input  name="sendCoinsSellerBut" type="submit" value="Перечислить"></div>';
		}
	
        
        if ($row['city'] != '' && $row['address'] != '') {
            
            $shCity_px = $row['city'].', '.$row['address'];
        }elseif ($row['temp_city_ship'] != '' && $row['temp_adress_ship'] != '') {
             $shCity_px = $row['temp_city_ship'].', '.$row['temp_adress_ship'];
        } else {
            $shCity_px ='не указано';
        }
        
	
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
			<div class="action">'.$actionButtons.'</div>
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