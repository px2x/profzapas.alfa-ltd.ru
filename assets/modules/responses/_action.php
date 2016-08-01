<?php

//define('ROOT', dirname(__FILE__).'/');
define('ROOT', $_SERVER['DOCUMENT_ROOT']);

$sc_site= 'profzapas.ru';
$sm_base= '../assets/modules/responses/';


$module_url= MODX_MANAGER_URL .'?a='. $_GET[ 'a' ] .'&id='. $_GET[ 'id' ];
$module_url_orders= MODX_MANAGER_URL .'?a=112&id=3';

$webuser= intval( $_GET[ 'wu' ] );

if (isset( $_GET[ 'spg' ])){
	$subpage= $_GET[ 'spg' ];
}else {
	$subpage ='mainpage';
}

$act= $_GET[ 'act' ];


$sql = "SELECT COUNT(id) cnt FROM ".$modx->getFullTableName( '_responses' )."   
        WHERE visible = 0";
if ($result = mysql_query($sql) or die (mysql_error())) {
    $countNewResponses = mysql_fetch_assoc($result)['cnt'];
    
}


$sql = "SELECT COUNT(id) cnt FROM ".$modx->getFullTableName( '_responses' )."   
        WHERE visible = '1'";
if ($result = mysql_query($sql) or die (mysql_error())) {
    $countArchiveResponses = mysql_fetch_assoc($result)['cnt'];
    
}


$sql = "SELECT COUNT(id) cnt FROM ".$modx->getFullTableName( '_responses' )."   
        WHERE visible = '-1'";
if ($result = mysql_query($sql) or die (mysql_error())) {
    $countBlockedResponses = mysql_fetch_assoc($result)['cnt'];
    
}

?>

<div class="modul_scorn_all">
<!-- -------------------------- -->
<link rel="stylesheet" type="text/css" href="<?php print $sm_base;?>_styles.css" />



<script type="text/javascript" src="https://code.jquery.com/jquery-2.2.3.min.js"   integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo="   crossorigin="anonymous"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"   integrity="sha256-DI6NdAhhFRnO2k51mumYeDShet3I8AKCQf/tf7ARNhI="   crossorigin="anonymous"></script>


<script type="text/javascript" src="<?php print $sm_base;?>_script.js"></script>





<div class="topmenu">
	<ul>
		<li><a href="<?= $module_url ?>&spg=mainpage">Рейтинг продавцов</a></li>
		<li><a href="<?= $module_url ?>&spg=newResponse">Новые отзывы (+<?php echo $countNewResponses?>)</a></li>
		<li><a href="<?= $module_url ?>&spg=archiveResponse">Архив (+<?php echo $countArchiveResponses?>)</a></li>
		<li><a href="<?= $module_url ?>&spg=blockedResponse">Заблокированные (+<?php echo $countBlockedResponses?>)</a></li>
        
        
	</ul>
	<div class="clr">&nbsp;</div>
</div>

<script type="text/javascript">
</script>



<?php






//=================================START==AVG======================================
    
if( $subpage == 'mainpage' ) {
	
    $result_page= '<div class="titlePage">Рейтинг продавцов</div>';
    
    $result_page.= '<div class="summryRank">';
    
    
    $sql = "SELECT  AVG(resp.`rank`) AS avgRank , resp.id_user , COUNT(resp.id) AS sumRespons , usr.firstname ,   usr.surname, usr.mobile , usr.email, usr.dt
            FROM ".$modx->getFullTableName( '_responses' )."  AS resp
            INNER JOIN ".$modx->getFullTableName( '_catalog' )."  AS cat ON resp.id_cat_item = cat.id
            INNER JOIN ".$modx->getFullTableName( '_user' )."  AS usr ON cat.seller = usr.id
            WHERE resp.visible = '1'
            GROUP BY cat.seller
            ORDER BY avgRank DESC";

    $result = mysql_query($sql) or die (mysql_error());
    
    
   
    
    if ($result && mysql_num_rows($result) > 0) {
    

        
        
        while ($row = mysql_fetch_assoc($result)){
            
            $result_page.= '
            <div class="oneUserRank">
                <div class="username">'.$row['firstname'].' '.$row['surname'].'</div>
                
                <div class="email">'.$row['email'].'</div>
                <div class="mobile">'.$row['mobile'].'</div>
                <div class="avg">Рейтинг <span>'.round($row['avgRank'],1).'<span></div>
                <div class="countResp">Всего отзывов '.$row['sumRespons'].'</div>
                <div class="date">Зарегистрирован '.date("d.m.Y",$row['dt']).'</div>
            </div>
            ';
            
        }
        
    }
    
    
     $result_page.='</div>';
}
               
               
//===========================END==AVG============
	

	
	
	

               
               
               
               
               
               



//=================================START==NEW RESPONSE======================================
    
if( $subpage == 'newResponse' ) {
    
    $result_page= '<div class="titlePage">Новые отзывы</div>';
    
    $result_page.= '<div class="summryRank">';
    
    $sql = "SELECT resp.* ,  cat.title , cat.code , cat.manufacturer, 
            usr.firstname AS buyerName , usr.surname  AS buyerSurName, usr.email  AS buyerEmail , 
            usrS.firstname AS sellerName , usrS.surname  AS sellerSurName, usrS.email  AS sellerEmail 
            FROM ".$modx->getFullTableName( '_responses' )." AS resp
            INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = resp.id_cat_item
            INNER JOIN ".$modx->getFullTableName( '_user' )." AS usr ON usr.id = resp.id_user
            INNER JOIN ".$modx->getFullTableName( '_user' )." AS usrS ON usrS.id = cat.seller
            WHERE resp.visible = 0
            ";
    $result = mysql_query($sql) or die (mysql_error());
    
    if ($result && mysql_num_rows($result) > 0) {
     
        while ($row = mysql_fetch_assoc($result)){
            
            $result_page.= '
            <div class="oneResponse">
                <div class="resp_from">От: '.$row['buyerName'].' '.$row['buyerSurName'].' ('.$row['buyerEmail'].')
                    <button class="responseBut greenBut" id="resp_accept" data-idresponse="'.$row['id'].'">Одобрить</button>
                    <button class="responseBut redBut" id="resp_block" data-idresponse="'.$row['id'].'">Блокировать</button>
                </div>
                <div class="resp_for_item">'.$row['title'].' <span class="largest">(Арт.:'.$row['code'].') '.$row['manufacturer'].'</span></div>
                <div class="resp_for_seller">Продавец: '.$row['sellerName'].' '.$row['sellerSurName'].' ('.$row['sellerEmail'].')</div>
                <div class="resp_rank">'.$row['rank'].'</div>
                <div class="resp_text">'.$row['response'].'</div>
                <div class="crl"></div>
                <div class="date">Дата отзыва '.date("d.m.Y",$row['timest']).'</div>
            </div>
            ';
            
        }
        
    }
    
    
    
    $result_page.='</div>';
}
               
               
//===========================END==NEW RESPONSE==================================

	


               

               
               
//=================================START==ARCHIVE RESPONSE======================================
    
if( $subpage == 'archiveResponse' ) {
    
    $result_page= '<div class="titlePage">Архив</div>';
    
    $result_page.= '<div class="summryRank">';
    
    $sql = "SELECT resp.* ,  cat.title , cat.code , cat.manufacturer, 
            usr.firstname AS buyerName , usr.surname  AS buyerSurName, usr.email  AS buyerEmail , 
            usrS.firstname AS sellerName , usrS.surname  AS sellerSurName, usrS.email  AS sellerEmail 
            FROM ".$modx->getFullTableName( '_responses' )." AS resp
            INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = resp.id_cat_item
            INNER JOIN ".$modx->getFullTableName( '_user' )." AS usr ON usr.id = resp.id_user
            INNER JOIN ".$modx->getFullTableName( '_user' )." AS usrS ON usrS.id = cat.seller
            WHERE resp.visible = '1'
            ";
    $result = mysql_query($sql) or die (mysql_error());
    
    if ($result && mysql_num_rows($result) > 0) {
     
        while ($row = mysql_fetch_assoc($result)){
            
            $result_page.= '
            <div class="oneResponse">
                <div class="resp_from">От: '.$row['buyerName'].' '.$row['buyerSurName'].' ('.$row['buyerEmail'].')
                    <!--button class="responseBut greenBut" id="resp_accept" data-idresponse="'.$row['id'].'">Одобрить</button>
                    <button class="responseBut redBut" id="resp_block" data-idresponse="'.$row['id'].'">Блокировать</button-->
                </div>
                <div class="resp_for_item">'.$row['title'].' <span class="largest">(Арт.:'.$row['code'].') '.$row['manufacturer'].'</span></div>
                <div class="resp_for_seller">Продавец: '.$row['sellerName'].' '.$row['sellerSurName'].' ('.$row['sellerEmail'].')</div>
                <div class="resp_rank">'.$row['rank'].'</div>
                <div class="resp_text">'.$row['response'].'</div>
                <div class="crl"></div>
                <div class="date">Дата отзыва '.date("d.m.Y",$row['timest']).'</div>
            </div>
            ';
            
        }
        
    }
    
    
    
    $result_page.='</div>';
}
               
               
//===========================END==ARCHIVE RESPONSE==================================

	

               
               
               
               
               
               
               
               
               
//=================================START==BLOCKED RESPONSE======================================
    
if( $subpage == 'blockedResponse' ) {
    
    $result_page= '<div class="titlePage">Заблокированные</div>';
    
    $result_page.= '<div class="summryRank">';
    
    $sql = "SELECT resp.* ,  cat.title , cat.code , cat.manufacturer, 
            usr.firstname AS buyerName , usr.surname  AS buyerSurName, usr.email  AS buyerEmail , 
            usrS.firstname AS sellerName , usrS.surname  AS sellerSurName, usrS.email  AS sellerEmail 
            FROM ".$modx->getFullTableName( '_responses' )." AS resp
            INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = resp.id_cat_item
            INNER JOIN ".$modx->getFullTableName( '_user' )." AS usr ON usr.id = resp.id_user
            INNER JOIN ".$modx->getFullTableName( '_user' )." AS usrS ON usrS.id = cat.seller
            WHERE resp.visible = '-1'
            ";
    $result = mysql_query($sql) or die (mysql_error());
    
    if ($result && mysql_num_rows($result) > 0) {
     
        while ($row = mysql_fetch_assoc($result)){
            
            $result_page.= '
            <div class="oneResponse">
                <div class="resp_from">От: '.$row['buyerName'].' '.$row['buyerSurName'].' ('.$row['buyerEmail'].')
                    <!--button class="responseBut greenBut" id="resp_accept" data-idresponse="'.$row['id'].'">Одобрить</button>
                    <button class="responseBut redBut" id="resp_block" data-idresponse="'.$row['id'].'">Блокировать</button-->
                </div>
                <div class="resp_for_item">'.$row['title'].' <span class="largest">(Арт.:'.$row['code'].') '.$row['manufacturer'].'</span></div>
                <div class="resp_for_seller">Продавец: '.$row['sellerName'].' '.$row['sellerSurName'].' ('.$row['sellerEmail'].')</div>
                <div class="resp_rank">'.$row['rank'].'</div>
                <div class="resp_text">'.$row['response'].'</div>
                <div class="crl"></div>
                <div class="date">Дата отзыва '.date("d.m.Y",$row['timest']).'</div>
            </div>
            ';
            
        }
        
    }
    
    
    
    $result_page.='</div>';
}
               
               
//===========================END==BLOCKED RESPONSE==================================

	
	
	
	
echo $result_page;
?>