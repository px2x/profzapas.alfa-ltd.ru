<?php


//error_reporting(7);
/*
require_once($_SERVER['DOCUMENT_ROOT'].'/manager/includes/protect.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/manager/includes/config.inc.php');
define('MODX_API_MODE', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/manager/includes/document.parser.class.inc.php');
$modx = new DocumentParser;
$modx->getSettings();
$modx->db->connect();
startCMSSession();
$modx->minParserPasses=2;

define('TAB_PREFIX', 'profzapas_');*/




define('MODX_API_MODE', true);
include_once 'manager/includes/config.inc.php';
include_once 'manager/includes/document.parser.class.inc.php';
$modx = new DocumentParser;
$modx->db->connect();
$modx->getSettings();
startCMSSession();
$modx->minParserPasses=2;


define('TAB_PREFIX', 'profzapas_');






//====================SMS SENDER START  ==================
$sql="SELECT * FROM ".$modx->getFullTableName( '_sms_send' )." WHERE status = 'notSended' LIMIT 50";
if ($result = mysql_query($sql)) {
    if ($result && mysql_num_rows($result)> 0) {
        while ($row = mysql_fetch_assoc($result)){
            $row['mobile'] = preg_replace('/[^0-9]/', '', $row['mobile']);
            $arr = array( 'phone' => $row['mobile'],
                          'text' => $row['text'],
                          'senderStack' => "y"
                        );
            
             
            if ($sSender = $modx->runSnippet( 'sendQuickSMS', $arr)){
                
                echo $sSender ;
				updateSMSstatus($row['id']);
                
            }
            
            usleep(300000);
        }
        
    }
    
}
//====================SMS SENDER END   ==================








//====================EMAIL SENDER START  ==================
$sql="SELECT * FROM ".$modx->getFullTableName( '_email_send' )." WHERE status = 'notSended' LIMIT 50";
if ($result = mysql_query($sql) or die (mysql_error())) {
    if ($result && mysql_num_rows($result)> 0) {
        while ($row = mysql_fetch_assoc($result)){
            
            $textBody = '';
            if ($row['type_sender'] == 'changedPrice' ) {
                
                
                
                $sqlCAT="SELECT * FROM ".$modx->getFullTableName( '_catalog' )." WHERE id = ".$row['id']." LIMIT 1";
                
                if ($resultCAT = mysql_query($sqlCAT) or die (mysql_error())) {
                     if ($resultCAT && mysql_num_rows($resultCAT)> 0) {
                         $rowCAT = mysql_fetch_assoc($resultCAT);

                     }
                }
                
               
                if( $row[ 'newCurrency' ] != 'rub' ) $row[ 'newPrice' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$row[ 'newPrice' ], 'c'=>$row[ 'newCurrency' ] ) );
                    
                //_getLinkToItem
               // $href = '<a href="'. $modx->makeUrl( $rowCAT[ 'parent' ] ) .'i/'. $rowCAT[ 'id' ] .'/ ">'.$rowCAT[ 'title' ].'</a>';
                
                            
                $href = $modx->runSnippet( '_getLinkToItem', array( 'parent'=>$rowCAT[ 'parent' ], 'id'=> $rowCAT[ 'id' ]  , 'title'=>$rowCAT[ 'title' ] ) );
                
                $textBody = 'Добрый день! Хотим сообщить вам о изменении цены на товар: '.$href.'<br>';
                $textBody .= 'Товар '.($row['curv'] < 0 ? "подешевел" : "подорожал").' на '.abs($row['curv']).' '.$row['newCurrency'].' <br>';
                $textBody .= 'Новая цена: '. $row[ 'newPrice' ] . ' руб.';
                
                
                
            }elseif($row['type_sender'] == 'orderStatus' ){
                
                $textBody = 'Добрый день! Хотим сообщить вам о изменении статуса заказа<br>';
                
            }
            
            
        
            $arr = array( 'reg_email' => $row['email'],
                          'subject' => "Информация о заказе на сайте profzapas.ru",
                          'emailtext' => $textBody
                         
                        );
            
             
            if ($textBody!='') {
                if ($sSender = $modx->runSnippet( 'sendQuickEmail', $arr)){
				    updateEMAILstatus($row['id']);
                
                    usleep(100000);
                }
                
            }

            
            
        }
        
    }
    
}
//====================EMAIL SENDER END   ==================





function updateSMSstatus ($id) {
    
    $sql="UPDATE ".TAB_PREFIX."_sms_send SET status = 'sended' , stamp_sended = '".(time())."' WHERE id = ".$id;
    if ($result = mysql_query($sql)) {
        return true;

    }
    
    return false;   
}



function updateEMAILstatus ($id) {
    
    $sql="UPDATE ".TAB_PREFIX."_email_send SET status = 'sended'  WHERE id = ".$id;
    if ($result = mysql_query($sql)) {
        return true;

    }
    
    return false;   
}


?>