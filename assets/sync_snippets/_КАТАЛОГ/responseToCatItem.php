<?php

if (!is_numeric($id)){
    return false;
}



    $row = [];

     $sql = "SELECT resp.* , cat.parent, cat.id AS catid, cat.title , cat.code , cat.manufacturer, 
            usr.firstname AS buyerName , usr.surname  AS buyerSurName, usr.email  AS buyerEmail , 
            usrS.firstname AS sellerName , usrS.surname  AS sellerSurName, usrS.email  AS sellerEmail 
            FROM ".$modx->getFullTableName( '_responses' )." AS resp
            INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = resp.id_cat_item
            INNER JOIN ".$modx->getFullTableName( '_user' )." AS usr ON usr.id = resp.id_user
            INNER JOIN ".$modx->getFullTableName( '_user' )." AS usrS ON usrS.id = cat.seller
            WHERE resp.visible = '1'
            AND cat.id = ".$id;

    $result = mysql_query($sql) or die (mysql_error());
    
    if ($result && mysql_num_rows($result) > 0) {
        
        while ($tmp = mysql_fetch_assoc($result)){
            
           $row[] = $tmp; 
            
        }
        return $row;
    }else {
        return false;
        
    }


?>