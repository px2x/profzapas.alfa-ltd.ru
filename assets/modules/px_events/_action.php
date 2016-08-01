<?php


//define('ROOT', dirname(__FILE__).'/');
define('ROOT', $_SERVER['DOCUMENT_ROOT']);

$sc_site= 'profzapas.ru';
$sm_base= '../assets/modules/px_events/';


$module_url= MODX_MANAGER_URL .'?a='. $_GET[ 'a' ] .'&id='. $_GET[ 'id' ];
//$module_url_orders= MODX_MANAGER_URL .'?a=112&id=3';





?>

<link rel="stylesheet" type="text/css" href="<?php print $sm_base;?>_styles.css" />
<script type="text/javascript" src="https://code.jquery.com/jquery-2.2.3.min.js"   integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo="   crossorigin="anonymous"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"   integrity="sha256-DI6NdAhhFRnO2k51mumYeDShet3I8AKCQf/tf7ARNhI="   crossorigin="anonymous"></script>
<script type="text/javascript" src="<?php print $sm_base;?>_script.js"></script>

 
<?php











if (isset($_GET['chunk'])){
    
    if ($_GET['chunk'] == 'wrap_tab_users') {
        
        
    }
    
    
    
    if ($_GET['chunk'] == 'wrap_tab_users') {
        
        
    }
    
}else {
    
    echo '
    <table>
        <tr>
            <td><div id="wrap_tab_users">'.print_users($modx).'</div></td>
            <td><div id="wrap_tab_items">'.print_items($modx).'</div></td>
            <td><div id="wrap_tab_orders">'.print_orders($modx).'</div></td>
            
        </tr>
        <tr>
            <td><div id="wrap_tab_respons">'.print_responses($modx).'</div></td>
            <td><div id="wrap_tab_dispute">'.print_dispute($modx).'</div></td>
            <td><div id="wrap_tab_msg">'.print_msg($modx).'</div></td>
        </tr>
    </table>
    ';

    
}




function print_users($modx){
    
    
    
    $module_url_users = MODX_MANAGER_URL.'?a=112&id=2&spg=requests';
    
    
    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_user' )." ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countAllUser = $row['cnt'];   
        }
    }
    
    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_user' )." WHERE urlico = 'y' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countUrlicoUser = $row['cnt'];   
        }
    }
    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_user' )." WHERE seller = 'y' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countSellerUser = $row['cnt'];   
        }
    }
    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_user' )." WHERE seller <> 'y'  AND urlico <> 'y' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countBuyerUser = $row['cnt'];   
        }
    }
    
    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_user' )." WHERE seller = 'test'  OR urlico = 'test' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countReqUser = $row['cnt'];   
        }
    }
    
    $page = '<div class="panelBlock">';
    $page .= '
        <div class="panelBlock_tit">Пользователи</div>
        <div class="panelBlock_str">Всего пользователей: <span class="numb">'.$countAllUser.'</span></div>
        <div class="panelBlock_str">Продавцов: <span class="numb">'.$countSellerUser.'</span></div>
        <div class="panelBlock_str">Юр. лиц: <span class="numb">'.$countUrlicoUser.'</span></div>
        <div class="panelBlock_str">Физ. лиц: <span class="numb">'.$countBuyerUser.'</span></div>
        <div class="panelBlock_str">Заявки пользователей: <a href="'.$module_url_users.'"><span class="numb '.($countReqUser > 0 ? 'notice' : '').'">'.$countReqUser.'</span></a></div>
        
        <div class="panelBlock_updated">обновлено: <span class="numb_upd">'.date("H:i:s", time()).'</span></div>
    ';
    $page .= '</div>';
    
    return $page;
    
}

	



function print_items($modx){
    
    $module_url_items = MODX_MANAGER_URL.'?a=112&id=4&ces=n';

    


    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_catalog' )." WHERE enabled = 'n' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countReqItems = $row['cnt'];   
        }
    }
    
    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_catalog' )." WHERE enabled = 'y' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countOkItems = $row['cnt'];   
        }
    }
    
    
        
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_catalog' )." ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countAllItems = $row['cnt'];   
        }
    }
    
    
            
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_catalog' )." WHERE in_stock  < 1";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countNullItems = $row['cnt'];   
        }
    }
    
    $page = '<div class="panelBlock">';
    $page .= '
        <div class="panelBlock_tit">Товары</div>
        <div class="panelBlock_str">Всего товаров: <span class="numb">'.$countAllItems.'</span></div>
        <div class="panelBlock_str">Одобренных: <span class="numb">'.$countOkItems.'</span></div>
        <div class="panelBlock_str">Ожидают модерации: <a href="'.$module_url_items.'"><span class="numb '.($countReqItems > 0 ? 'notice' : '').'">'.$countReqItems.'</span></a></div>
        <div class="panelBlock_str">Ноль на складе: <span class="numb">'.$countNullItems.'</span></div>
        
        <div class="panelBlock_updated">обновлено: <span class="numb_upd">'.date("H:i:s", time()).'</span></div>
    ';
    $page .= '</div>';
    
    return $page;
    
}







function print_orders($modx){
    
    
    $module_url_orders = MODX_MANAGER_URL.'?a=112&id=5';
    
      
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countAllOrders = $row['cnt'];   
        }
    }
    
    
      
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'calculateShipment' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countCalcShipOrders = $row['cnt'];   
        }
    }
    
    
      
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'waitShipment' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countWaitShipOrders = $row['cnt'];   
        }
    }
        
      
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'waitPayment' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countWaitPayOrders = $row['cnt'];   
        }
    }
    
          
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'inShipment' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countInShipOrders = $row['cnt'];   
        }
    }
    
        
      
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'waitTesting' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countWaitTestOrders = $row['cnt'];   
        }
    }
    
    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'waitEnd' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countWaitEndOrders = $row['cnt'];   
        }
    }
    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE status = 'ended' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countEndedOrders = $row['cnt'];   
        }
    }

    
    $page = '<div class="panelBlock">';
    $page .= '
        <div class="panelBlock_tit">Заказы</div>
        
        <div class="panelBlock_str">Всего заказов: <span class="numb">'.$countAllOrders.'</span></div>
        
        <div class="panelBlock_str">Ожидают расчета доставки: 
            <a href="'.$module_url_orders.'&spg=calcShipment">
                <span class="numb '.($countCalcShipOrders > 0 ? 'notice' : '').'">'.$countCalcShipOrders.'</span>
            </a>
        </div>
        
        
        <div class="panelBlock_str">Ожидают оплаты: 
            <a href="'.$module_url_orders.'&spg=waitPayment">
                 <span class="numb '.($countWaitPayOrders > 0 ? 'notice' : '').'">'.$countWaitPayOrders.'</span>
            </a>
        </div>
        
        
        <div class="panelBlock_str">Ожидают отправку: 
            <a href="'.$module_url_orders.'&spg=waitShipment">
              <span class="numb '.($countWaitShipOrders > 0 ? 'notice' : '').'">'.$countWaitShipOrders.'</span>
            </a>
        </div>
        
        
        <div class="panelBlock_str">В пути: 
            <a href="'.$module_url_orders.'&spg=inShipment">
                <span class="numb">'.$countInShipOrders.'</span>
            </a>        
        </div>
        
        
        <div class="panelBlock_str">Тестирование: 
            <a href="'.$module_url_orders.'&spg=waitTesting">
                <span class="numb">'.$countWaitTestOrders.'</span>
            </a>       
        </div>
        
        
        <div class="panelBlock_str">Перевод стредств продавцу: 
            <a href="'.$module_url_orders.'&spg=waitEnd">
                <span class="numb '.($countWaitEndOrders > 0 ? 'notice' : '').'">'.$countWaitEndOrders.'</span>
            </a>       
        </div>
        
        
        <div class="panelBlock_str">Завершенные: <span class="numb">'.$countEndedOrders.'</span></div>
        
        <div class="panelBlock_updated">обновлено: <span class="numb_upd">'.date("H:i:s", time()).'</span></div>
    ';
    $page .= '</div>';
    
    return $page;
    
}








function print_responses($modx){
    
    
    $module_url_responses = MODX_MANAGER_URL.'?a=112&id=6';
    
      
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_responses' )." ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countAllRespons = $row['cnt'];   
        }
    }
    
     
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_responses' )." WHERE visible = 1 ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countEnaRespons = $row['cnt'];   
        }
    }
    
    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_responses' )." WHERE visible = -1 ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countBlocRespons = $row['cnt'];   
        }
    }

    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_responses' )." WHERE visible = 0 ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countWaitRespons = $row['cnt'];   
        }
    }
    
    
    $page = '<div class="panelBlock">';
    $page .= '
        <div class="panelBlock_tit">Отзывы</div>
        <div class="panelBlock_str">Всего отзывов: <span class="numb">'.$countAllRespons.'</span></div>
        <div class="panelBlock_str">Ожидают модерации: <a href="'.$module_url_responses.'&spg=newResponse"><span class="numb '.($countWaitRespons > 0 ? 'notice' : '').'">'.$countWaitRespons.'</span></a></div>
        
        <div class="panelBlock_str">Одобренных:  <a href="'.$module_url_responses.'&spg=archiveResponse"><span class="numb">'.$countEnaRespons.'</span></a></div>
        
        <div class="panelBlock_str">Заблокированных:  <a href="'.$module_url_responses.'&spg=blockedResponse"><span class="numb">'.$countBlocRespons.'</span></a></div>
 
 
        <div class="panelBlock_updated">обновлено: <span class="numb_upd">'.date("H:i:s", time()).'</span></div>
    ';
    $page .= '</div>';
    
    return $page;
    
}






function print_dispute($modx){
    
    
    $module_url_dispute= MODX_MANAGER_URL.'?a=112&id=7';
    
      
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_dispute_list' )." ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countAllDispute = $row['cnt'];   
        }
    }
    
     
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_dispute_list' )." WHERE status = 'opened' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countOpenedDispute = $row['cnt'];   
        }
    }
    
    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_dispute_list' )." WHERE status = 'closed' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countClosedDispute = $row['cnt'];   
        }
    }

    
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_dispute_msg' )." WHERE to_user = -1  AND readstatus = 'unred' ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countMsgDispute = $row['cnt'];   
        }
    }
    
    
    $page = '<div class="panelBlock">';
    $page .= '
        <div class="panelBlock_tit">Споры</div>
        <div class="panelBlock_str">Всего споров: <span class="numb">'.$countAllDispute.'</span></div>
        
        
        <div class="panelBlock_str">Открытых: <a href="'.$module_url_dispute.'&spg=openedDispute"><span class="numb '.($countOpenedDispute > 0 ? 'notice' : '').'">'.$countOpenedDispute.'</span></a></div>
        
        
        <div class="panelBlock_str">Закрытых: <a href="'.$module_url_dispute.'&spg=closedDispute"><span class="numb">'.$countClosedDispute.'</span></a></div>
        
        
        <div class="panelBlock_str">Сообщений по спорам: <span class="numb '.($countMsgDispute > 0 ? 'notice' : '').'">'.$countMsgDispute.'</span></div>
        
        <div class="panelBlock_updated">обновлено: <span class="numb_upd">'.date("H:i:s", time()).'</span></div>
    ';
    $page .= '</div>';
    
    return $page;
    
}






function print_msg($modx){
    
      
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_messages' )." ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countAllMsg = $row['cnt'];   
        }
    }
    
     
    $sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_messages' )." WHERE readstatus = 1 ";
    if ($result = mysql_query($sql)){
        if ($row = mysql_fetch_assoc($result)) {
            $countUnreadMsg = $row['cnt'];   
        }
    }
    
    

    
    
    $page = '<div class="panelBlock">';
    $page .= '
        <div class="panelBlock_tit">Сообщения</div>
        <div class="panelBlock_str">Всего сообщений: <span class="numb">'.$countAllMsg.'</span></div>
        <div class="panelBlock_str">Непрочитано: <span class="numb '.($countUnreadMsg > 0 ? 'notice' : '').'">'.$countUnreadMsg.'</span></div>
 
        <div class="panelBlock_updated">обновлено: <span class="numb_upd">'.date("H:i:s", time()).'</span></div>
    ';
    $page .= '</div>';
    
    return $page;
    
}



?>