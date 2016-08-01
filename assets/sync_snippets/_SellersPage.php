<?php

$auth= 108;
$topage_url= $modx->makeUrl( $modx->documentIdentifier );
if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	header( 'location: '. $modx->makeUrl( $auth ) );
	exit();
}

$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];




if (!is_numeric($_GET['sellerID'])) {
    header( 'location: '. $modx->makeUrl( 1 ) );
    exit();
}

$sellerID = $_GET['sellerID'];


$sql = "SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE  id = ".$sellerID;
if ($result = mysql_query($sql)){
	if ($rowUser  = mysql_fetch_assoc($result)){

	}
}




$sqlAVG = "SELECT  AVG(resp.`rank`) AS avgRank , resp.id_user , COUNT(resp.id) AS sumRespons 
            FROM ".$modx->getFullTableName( '_responses' )."  AS resp
            INNER JOIN ".$modx->getFullTableName( '_catalog' )."  AS cat ON resp.id_cat_item = cat.id
            WHERE cat.seller = ".$sellerID."
            AND resp.visible = '1' ";
    
$resultAVG = mysql_query($sqlAVG) or die (mysql_error());
if ($resultAVG && mysql_num_rows($resultAVG) > 0) {
    if ($avgUser = mysql_fetch_assoc($resultAVG)) {

    }
}
  



$sqlShop = "SELECT * FROM  ".$modx->getFullTableName( '_user_seller_descr' )."   WHERE id_seller =  ".$sellerID;
$resultShop = mysql_query($sqlShop) or die (mysql_error());
if ($resultShop && mysql_num_rows($resultShop) > 0) {
    if ($rowShop= mysql_fetch_assoc($resultShop)) {

    }
}
    



$sqlAllItem = "SELECT COUNT(id) AS cnt FROM  ".$modx->getFullTableName( '_catalog' )."   WHERE seller =  ".$sellerID;
$resultAllItem = mysql_query($sqlAllItem) or die (mysql_error());
if ($resultAllItem && mysql_num_rows($resultAllItem) > 0) {
    if ($catCnt= mysql_fetch_assoc($resultAllItem)) {

    }
}



$sqlAllOrders = "SELECT COUNT(id) AS cnt FROM  ".$modx->getFullTableName( '_orders' )."   WHERE status = 'ended' AND sellerId =  ".$sellerID;
$resultAllOrders = mysql_query($sqlAllOrders) or die (mysql_error());
if ($resultAllOrders && mysql_num_rows($resultAllOrders) > 0) {
    if ($ordCnt= mysql_fetch_assoc($resultAllOrders)) {

    }
}






echo '
    <div class="sellerInfo">
        <div class="sellerInfo_tit">
            <div class="name_ps">'.$rowUser['firstname'].' '.$rowUser['surname'].' </div>
            <div class="datereg_ps">Зарегиcтрирован: <span>'.(date("d.m.Y",$rowUser['dt'])).'</span></div>
            <div class="sellerInfo_left">Всего товаров: '.$catCnt['cnt'].'</div>
            <div class="sellerInfo_left">Продано товаров: '.$ordCnt['cnt'].'</div>
        </div>
        
        <div class="sellerInfo_Rank">
            '.(round($avgUser['avgRank'],1)).'
        </div>
    
    
        <div class="clr"></div>
        
        <div class="sellerInfo_descripyionShop">
            <span>Описание магазина:</span>
            <div class="descrrr">'.$rowShop['description'].'</div>
        </div>
        
        <div class="clr"></div>
        

        <!--div class="sellerInfo_AVG">
            <div class="sellerInfo_left">Всего товаров: '.$catCnt['cnt'].'</div>
            <div class="sellerInfo_right">Продано товаров: '.$ordCnt['cnt'].'</div>
        </div-->
        <div class="clr"></div>
    </div>
';



$sqlResponse  = "SELECT resp.* , cat.title , cat.code, usr.firstname, usr.surname, cat.manufacturer, cat.id AS cat_id FROM ".$modx->getFullTableName( '_responses' )." AS resp
INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = resp.id_cat_item 
INNER JOIN ".$modx->getFullTableName( '_user' )." AS usr ON resp.id_user = usr.id 
WHERE cat.seller = ".$sellerID." AND resp.visible = 1
ORDER BY resp.timest DESC";

$resultResponse = mysql_query($sqlResponse) or die (mysql_error());
if ($resultResponse && mysql_num_rows($resultResponse) > 0) {
    
     echo '<div class="sellerPageResponses">Отзывы о товарах продавца';
         
    while ($row = mysql_fetch_assoc($resultResponse)) {

        echo '
            <div class="sellerPa_respBlock"> 
            
                <div class="respBlock_itemInfo">
                    <span class="psiArt">Арт.: '.$row['code'].'</span> '.$row['title'].'<br/>
                    <span class="psiManuf">'.$row['manufacturer'].'</span> 
                    <div class="sell_rank">'.$row['rank'].'</div>
                </div>
                
                <div class="respBlock_resp"> 
                    <div class="respBlock_from"> 
                        От: <span class="buyerName">'.$row['firstname'].' '.$row['surname'].'</span>
                        <span class="sellerP_respdate">'.(date("d.m.Y" , $row['timest'])).'</span>
                    </div>
                    <div class="respBlock_respBody"> 
                        
                        <span class="sell_text">'.$row['response'].'</span>
                    </div>
                    
                </div>
                <div class="clr"></div>
                
            </div>
        ';

        
    }
    
     echo '</div>';
         
}else  {
    
    echo '<div class="sellerPageResponses">У этого продавца пока нет отзывов<br/><span class="fweig500">Будьте первым!</span></div>'; 
} 






?>