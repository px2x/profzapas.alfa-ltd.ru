<?php

//v01
//============================================================
$usd= $modx->runSnippet( 'ExchangeRates', array( 'c'=>'usd' ) );
$eur= $modx->runSnippet( 'ExchangeRates', array( 'c'=>'eur' ) );


$finances = 120;
$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];


	//select hash
$sqlHash = "SELECT hash FROM  ".$modx->getFullTableName( '_coins' )." WHERE id_user =  '".$webuserinfo['id']."' LIMIT 1";
$resultHash = mysql_query($sqlHash) or die ('Error Select coins hash');
$coinsHash = mysql_fetch_row($resultHash)[0];
	
	//select summ wait output
$sqlSummOutput = "SELECT SUM(sum) FROM  ".$modx->getFullTableName( '_points_up' )." WHERE id_user =  '".$webuserinfo['id']."' AND type = 'output'  AND status = 'wait' LIMIT 1";
$resultSummOutput = mysql_query($sqlSummOutput) or die ('Error SUMMPrep coins history');
$coinsSummOutputInt = mysql_fetch_row($resultSummOutput)[0];
	
	//select last rec history
$sqlLast = "SELECT * FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$webuserinfo['id']."' ORDER BY id DESC LIMIT 1 ";
$resultLast = mysql_query($sqlLast) or die ('Error Select coins history');
$coinsLast = mysql_fetch_assoc($resultLast);

	
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

	
	
	//всего средств
$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$webuserinfo['id']."' LIMIT 1";
$resultSumm = mysql_query($sqlSumm) or die ('Error всего средств');
$coinsSummHH = mysql_fetch_row($resultSumm)[0];
	
//$hash = md5($coinsLast['date'].$coinsLast['event'].$coinsLast['type'].$coinsLast['id_user'].$coinsSummHH);
$hash =($coinsLast['date'].'_'.$coinsLast['event'].'_'.$coinsLast['type'].'_'.$coinsLast['id_user'].'_'.$coinsSummHH);
if ($coinsHash==$hash) {
	$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$webuserinfo['id']."' LIMIT 1";
	$resultSumm = mysql_query($sqlSumm) or die ('Error SUMM coins history');
	$coinsSumm = mysql_fetch_row($resultSumm)[0] - $coinsSummOutputInt - $resultNotConfirmInt;

	//оплаченный аванс
	$sqlSummPrepayPay = "SELECT SUM(ABS(event)) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$webuserinfo['id']."' AND type = 'prepaymentPAY' AND event < 0 LIMIT 1";
	$resultSummPrepayPay = mysql_query($sqlSummPrepayPay) or die ('Error в качестве аванса');
	$coinsSummPrepayPay = mysql_fetch_row($resultSummPrepayPay)[0];
	
	$text_coins = $modx->runSnippet( 'Price', array( 'price' => $coinsSumm +$coinsSummPrepayPay, 'round' => 0 ) );
}else {
	$text_coins = 'x';
}
	
	
return '<div class="exchange_rates"><a class="myCoinsInfo" href="[~'. $finances .'~]">На Вашем счете: '.$text_coins.' <span class="rubl">a</span></a> &nbsp;|&nbsp; Курс ЦБ &nbsp;|&nbsp; USD <span class="usd">'. $usd .'</span> &nbsp;|&nbsp; EUR <span class="eur">'. $eur .'</span></div>';





?>