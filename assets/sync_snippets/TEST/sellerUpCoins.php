<?php

$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];
$toDBdate = time();

//$toDBsellerId - id продавца которому зачислить деньги
//$toDBshopId - id заказа	
//toDBsumShip - стоим дост
$toDBtype = 'payUp';
$userId = $webuserinfo['id'];	
/*
if (!is_numeric($toDBshopId)){
	$toDBshopId = -99;
}
*/

/*
//select summ wait output
$sqlSummOutput = "SELECT SUM(sum) FROM  ".$modx->getFullTableName( '_points_up' )." WHERE id_user =  '".$userId."' AND type = 'output'  AND status = 'wait' LIMIT 1";
$resultSummOutput = mysql_query($sqlSummOutput) or die ('Error select summ wait output');
$coinsSummOutputInt = mysql_fetch_row($resultSummOutput)[0];
						
// от продаж или пополнений
$sqlSummPrivate = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' AND type <> 'prepayment'  AND type <> 'prepaymentPAY' LIMIT 1";
$resultSummPrivate = mysql_query($sqlSummPrivate) or die ('Error от продаж или пополнений');
$coinsSummPrivate = mysql_fetch_row($resultSummPrivate)[0] - $coinsSummOutputInt;
*/

//всего средств
$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$toDBsellerId."' LIMIT 1";
$resultSumm = mysql_query($sqlSumm) or die ('Error всего средств');
if ($coinsSummHH = mysql_fetch_row($resultSumm)){
	$coinsSummHH = $coinsSummHH[0]+$toDBevent;
}else {
	$coinsSummHH = $toDBevent;
}


	//$hash = md5($toDBdate.$toDBevent.$toDBtype.$toDBsellerId.$coinsSummHH);
	/////$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
	//TEMPORARY WITHOUT HASH
	$hash = ($toDBdate.'_'.$toDBevent.'_'.$toDBtype.'_'.$toDBsellerId.'_'.$coinsSummHH);
		
	

$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins' )." (`id`, `id_user`, `hash` ) VALUES (NULL, '{$toDBsellerId}', '{$hash}') ON DUPLICATE KEY UPDATE `hash` = '".$hash."' ") or die ("Error 546: ".mysql_error());
	$result = mysql_query("SELECT id FROM ".$modx->getFullTableName( '_coins' )." WHERE  id_user = {$toDBsellerId} LIMIT 1") or die ("Error 547: ".mysql_error());
	if (!$id_coins = mysql_fetch_assoc($result)['id']) {
		exit();
	}else{
		$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins_history' )." (`id`, `id_user`, `id_coins`, `event`, `date`, `type`, `shop_id` ) VALUES (NULL, {$toDBsellerId}, {$id_coins}, {$toDBevent},'{$toDBdate}', '{$toDBtype}' , '{$toDBshopId}' )") or die ("Error 548: ".mysql_error());
		if ($result){
			return true;
		}else exit();
	}





?>