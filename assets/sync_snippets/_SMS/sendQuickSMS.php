<?php

$APIhost =  'http://api.unisender.com/ru/api/sendSms?format=json';
$APIkey ='5hfutkasaxemywq36wdro4r91b3s6xqxs4c1ncwa';
$APIlang ='';
$APImethod ='';
$APIsender = 'profzapas.ru'; 

/*
$phone = '79281231188';
$text = 'lat LAgh Кир кио';
*/


$phone = preg_replace('/[^0-9]/', '', $phone);



if ( ! preg_match("/[0-9]{11}/" , $phone)) {
	return false;
}


if ( ! preg_match("/.{5}/" , $text)) {
	return false;
}


if ($toStack == 'true') {
	
	$arrCH = array (
		'event' => 'checkStack'
	);

	if ($check = $modx->runSnippet( 'logSendedMessage', $arrCH ) == 'no120'){
		//echo $check ;
		return false;
	}
	
	
	$arr = array (
	  'mobile' => $phone,
	  'event' => 'pushToStack',
	  'text' => $text
	);
	if ($modx->runSnippet( 'logSendedMessage', $arr )) return true;
	
}


if ( $senderStack  != 'y'){
    
    $arrCH = array (
        'event' => 'check'
    );

    if ($check = $modx->runSnippet( 'logSendedMessage', $arrCH )){
        //echo $check ;
        return false;
    }
    
    
}








$POST = array (
  'api_key' => $APIkey,
  'phone' => $phone,
  'sender' => $APIsender,
  'text' => $text
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_URL, $APIhost);
$result = curl_exec($ch);






if ($result) {
	$jsonObj = json_decode($result);
	//print_r($jsonObj);
	
	$arr = array (
	  'obj' => $result,
	  'event' => 'push',
	  'text' => $text
	);

	
	
	if(null===$jsonObj) {
		//echo "Ошибка API-сервера";
		$modx->runSnippet( 'logSendedMessage', $arr );
		return false;
		
	} elseif (isset($jsonObj->result->error)) {
		//echo "Ошибка отправки: " . $jsonObj->result->error . "(code: " . $jsonObj->result->code . ")";
		$modx->runSnippet( 'logSendedMessage', $arr );
		return false;
		
		
	} else {
     
		//echo "Отправлено. Message id " . $jsonObj->result->sms_id;
		//echo "Стоимость " . $jsonObj->result->price . " " . $jsonObj->result->currency;
		if ($modx->runSnippet( 'logSendedMessage', $arr )) return true;	
		//return true;
	}
	
} else {
	//echo "Ошибка соединения с API-сервером";
	$modx->runSnippet( 'logSendedMessage', $arr );
	return false;
}

?>