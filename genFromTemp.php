<?php

//header("Content-Disposition:attachment;filename='schet_".date("d-m-Y H-i").".docx'");
//include_once 'Sample_Header.php';


require_once __DIR__ . '/print_word/src/PhpWord/Autoloader.php';


 

require_once($_SERVER['DOCUMENT_ROOT'].'/manager/includes/protect.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/manager/includes/config.inc.php');
define('MODX_API_MODE', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/manager/includes/document.parser.class.inc.php');
$modx = new DocumentParser;
$modx->getSettings();
$modx->db->connect();
startCMSSession();
$modx->minParserPasses=2;


use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

error_reporting(E_ALL);
define('CLI', (PHP_SAPI == 'cli') ? true : false);
define('EOL', CLI ? PHP_EOL : '<br />');
define('SCRIPT_FILENAME', basename($_SERVER['SCRIPT_FILENAME'], '.php'));
define('IS_INDEX', SCRIPT_FILENAME == 'index');

define('TAB_PREFIX', 'profzapas_');

Autoloader::register();
Settings::loadConfig();


$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('template2.docx');

// Will clone everything between ${tag} and ${/tag}, the number of times. By default, 1.



//$webuserinfo = $_SESSION[];
//print_r($_SESSION);

$webuserinfo = $_SESSION[ 'webuserinfo' ][ 'info' ];
$order_id = addslashes($_GET["orederId"]);

$err = false;


if (!is_numeric($order_id )){
    $err=true;
}


//get user info
$sql = "SELECT * FROM ".TAB_PREFIX."_user WHERE id = ".$webuserinfo['id']." LIMIT 1";
$result = mysql_query($sql);
if (!$userInfo = mysql_fetch_assoc($result)){
    $err=true;
}



//get order info
$sql = "SELECT * FROM ".TAB_PREFIX."_orders WHERE id = ".$order_id." LIMIT 1";
$result = mysql_query($sql);
if (!$orderInfo = mysql_fetch_assoc($result)){
    $err=true;
}



//if (file_exists('output/'.$orderInfo['order_number'].'_'.date("d.m.y__H.i.s",$orderInfo['date_w_check']).'.docx')){
//    
//}


//get order items info
$sql = "SELECT oi.id AS oi_id, oi.price AS oi_price, oi.`count` AS oi_count, cat.* 
        FROM profzapas__order_items AS oi
        INNER JOIN profzapas__catalog  AS cat ON oi.id_item = cat.id
        WHERE oi.id_order = ".$orderInfo['id'];
$result = mysql_query($sql);
if (mysql_num_rows($result) > 0){
    while ($tmp = mysql_fetch_assoc($result)){
        $orderItemsInfo[] = $tmp;
    } 
}else {
    $err = true;
    
}

//print_r($orderItemsInfo);

if (count($orderItemsInfo) > 0) {
    $countitem = count($orderItemsInfo);
    
    //echo count($orderItemsInfo);
    
    
    if ($userInfo['urlico'] == 'n' || $userInfo['urlico'] == 'test') {
        $nameBuyer = $userInfo['firstname'].' '.$userInfo['surname'];
        $bankData = $userInfo['mobile'].' ('.$userInfo['email'].')';
    }elseif ($userInfo['urlico'] == 'y'){
        $nameBuyer = $userInfo['company'];
        $bankData = 'Р/с: '.$userInfo['rschet'].'
Банк: '.$userInfo['bank'].'
ОГРН: '.$userInfo['ogrn'].' БИК: '.$userInfo['bik'].'
К/с: '.$userInfo['kschet'].'
ИНН: '.$userInfo['inn'].' КПП: '.$userInfo['kpp'];
    }
    
    $bankData = trim($bankData);
        
    $templateProcessor->setValue('orderNumber', $orderInfo['order_number']);
    $templateProcessor->setValue('orderDate' ,  date("d.m.y H:i", $orderInfo['date_w_check']));
    $templateProcessor->setValue('nameBuyer' , $nameBuyer);
    $templateProcessor->setValue('bankData' , $bankData);

    
    /*
  
    for ($i = 1 ; $i<=$countitem ; $i++){
        $templateProcessor->setValue('positionNumber#'.$i , htmlspecialchars($i));
        $templateProcessor->setValue('nameItem#'.$i , htmlspecialchars($orderItemsInfo[$i]['title']));
        $templateProcessor->setValue('unitsMetr#'.$i , htmlspecialchars('шт.'));
        $templateProcessor->setValue('counts#'.$i , htmlspecialchars($orderItemsInfo[$i]['oi_count']));
        $templateProcessor->setValue('oneItemPrice#'.$i , htmlspecialchars($orderItemsInfo[$i]['oi_price']));
        $templateProcessor->setValue('sumItemPrice#'.$i , htmlspecialchars($orderItemsInfo[$i]['oi_price'] * $orderItemsInfo[$i]['oi_count']));
    } 
    */
    
    
    $templateProcessor->cloneRow('nameItem', $countitem + 1);
    
    
    $i = 1;
    $sumItem = 0;
    foreach ($orderItemsInfo as $row) {
        $templateProcessor->setValue('positionNumber#'.$i , htmlspecialchars($i));
        $templateProcessor->setValue('nameItem#'.$i , htmlspecialchars($row['title']));
        $templateProcessor->setValue('unitsMetr#'.$i , htmlspecialchars('шт.'));
        $templateProcessor->setValue('counts#'.$i , htmlspecialchars($row['oi_count']));
        $templateProcessor->setValue('oneItemPrice#'.$i , htmlspecialchars($row['oi_price']));
        $templateProcessor->setValue('sumItemPrice#'.$i , htmlspecialchars($row['oi_price'] * $row['oi_count']));
        
        $sumItem = $sumItem + $row['oi_price'] * $row['oi_count'];
        
        $i++;
    }
    
    $templateProcessor->setValue('positionNumber#'.$i , htmlspecialchars($i));
    $templateProcessor->setValue('nameItem#'.$i , "Доставка");
    $templateProcessor->setValue('unitsMetr#'.$i , '');
    $templateProcessor->setValue('counts#'.$i , '');
    $templateProcessor->setValue('oneItemPrice#'.$i , '');
    $templateProcessor->setValue('sumItemPrice#'.$i , htmlspecialchars($orderInfo['price_shipment']));
    
    
    $sumItem = $sumItem + $orderInfo['price_shipment'];
         
    $sumNDS = $sumItem / 100 * 18;
    $sumItemWithNDS = $sumItem + $sumNDS;
    
    
    $templateProcessor->setValue('sumAllPrice', $sumItem);
    $templateProcessor->setValue('sumNDS' ,  $sumNDS);
    $templateProcessor->setValue('sumPlusNds' , $sumItemWithNDS);
}




//create table items




//header("Content-Type:text/html; charset=WIN-1251");

//echo SCRIPT_FILENAME.'<br>';
//echo IS_INDEX.'<br>';
//echo EOL.'<br>';
//echo CLI.'<br>';

//$dccCopy = $templateProcessor;
//$dccCopy->saveAs('output/'.$orderInfo['order_number'].'_'.date("d.m.y__H.i.s",$orderInfo['date_w_check']).'.docx');
///@$dccCopy->saveAs('output/qq.doc');



header("Content-Type:application/vnd.openxmlformats-officedocument.wordprocessingml.document");
header("Content-Disposition:attachment;filename='schet_".$orderInfo['order_number'].'_'.date('d.m.y__H.i.s',$orderInfo['date_w_check']).".docx'");
$templateProcessor->saveAs('php://output');






