<?php

//дописать в плане безопасности и поместить в скрипт для робокассы (RESULT)
	
$toDBdate_fk = addslashes($_GET['timest']);
$toDBdate = time();
$toDBevent = addslashes($_GET['sum']); // может быть со знаком минус
$toDBtype = 'payUp';
$userId = addslashes($_GET['id_user']);


$sql ="UPDATE ".$modx->getFullTableName( '_points_up' )." SET status = 'ok' WHERE id_user = '".$_GET['id_user']."' AND timest =".$_GET['timest'];	
mysql_query($sql) or die(mysql_error());	


//всего средств
$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' LIMIT 1";
$resultSumm = mysql_query($sqlSumm) or die ('Error всего средств');
if ($coinsSummHH = mysql_fetch_row($resultSumm)){
	$coinsSummHH = $coinsSummHH[0]+$toDBevent;
}else {
	$coinsSummHH = $toDBevent;
}

//$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
//TEMPORARY WITHOUT HASH
$hash = ($toDBdate.'_'.$toDBevent.'_'.$toDBtype.'_'.$userId.'_'.$coinsSummHH);


$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins' )." (`id`, `id_user`, `hash`) VALUES (NULL, '{$userId}', '{$hash}') ON DUPLICATE KEY UPDATE `hash` = '".$hash."' ") or die ("Error 546: ".mysql_error());
$result = mysql_query("SELECT id FROM ".$modx->getFullTableName( '_coins' )." WHERE  id_user = {$userId} LIMIT 1") or die ("Error 547: ".mysql_error());
if (!$id_coins = mysql_fetch_assoc($result)['id']) {
	exit();
}else{
	$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins_history' )." (`id`, `id_user`, `id_coins`, `event`, `date`, `type` , `timest_fk`) VALUES (NULL, {$userId}, {$id_coins}, {$toDBevent},'{$toDBdate}', '{$toDBtype}' , {$toDBdate_fk} )") or die ("Error 548: ".mysql_error());
	if ($result){
		echo 'Оплата на сумму '.$toDBevent.' произведена<br>';
		echo '<a href="http://www.profzapas.alfa-ltd.ru/lk/finasy.html?tab=1">Вернуться на сайт</a>';
	}else exit();
}
$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins_history' )." (id, ) VALUES ()");





?>