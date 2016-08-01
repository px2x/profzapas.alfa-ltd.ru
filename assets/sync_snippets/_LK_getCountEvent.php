<?php

//ewewewghfh j
if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] ){

	$webuserinfo['id'] = -99;
}	else {

	$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];
}

$topage_url= $modx->makeUrl( $modx->documentIdentifier );


if ($event == 'inShip') {
	$cnt = 0;
	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE ( status = 'inShipment' OR    status = 'waitShipment')  AND  user_id = ".$webuserinfo['id'];
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}




//ended
if ($event == 'ended') {
	$cnt = 0;
	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE ( status = 'waitTesting' )  AND  user_id = ".$webuserinfo['id'];
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}



//waitPayment
if ($event == 'waitPayment') {
	$cnt = 0;
	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE  status = 'waitPayment'   AND  user_id = ".$webuserinfo['id'];
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}



//calcShipment
if ($event == 'calcShipment') {
	$cnt = 0;
	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE  status = 'calculateShipment'   AND  user_id = ".$webuserinfo['id'];
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}



//allOrders
if ($event == 'allOrders') {
	$cnt = 0;
	$allOrders = 0;
	$allDispute = 0;
	
	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE  (status <> 'ended' AND status <> 'waitEnd' )   AND  user_id = ".$webuserinfo['id'];
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {		
			if ($cnt['cnt'] > 0  ) {
				$allOrders = $cnt['cnt'];
			}
		}
	}
	
	
	
	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_dispute_list' )." WHERE  status = 'opened'   AND  user_id = ".$webuserinfo['id'];
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				$allDispute = $cnt['cnt'];
			}
		}
	}
	
	if ($allOrders > 0 || $allDispute > 0 ) {
		return '<span class="menuNotice">'.($allOrders + $allDispute).'</span>';
	}
	
	
	return false;
}



//disputeOp
if ($event == 'disputeOp') {
	$cnt = 0;
	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_dispute_list' )." WHERE  status = 'opened'   AND  user_id = ".$webuserinfo['id'];
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}





//sellerOrdProcess
if ($event == 'sellerOrdProcess') {
	$cnt = 0;
	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE  status <> 'ended'   AND  sellerId = ".$webuserinfo['id'];
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}




//allended
if ($event == 'allended') {
	$cnt = 0;
	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE  status = 'ended'   AND  sellerId = ".$webuserinfo['id'];
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}




//allSellerOrd
if ($event == 'allSellerOrd') {
	$cnt = 0;
	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_orders' )." WHERE sellerId = ".$webuserinfo['id'];
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}


//requestPriceU
if ($event == 'requestPriceU') {
	$cnt = 0;
/*	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_request_price' )." AS req
    INNER JOIN ".$modx->getFullTableName( '_catalog' )."  AS cat ON cat.id=req.id_item 
    WHERE cat.enabled = y req.id_user = ".$webuserinfo['id'];	
    */
    
    $sql = "SELECT COUNT(req.id) AS cnt FROM ".$modx->getFullTableName( '_request_price' )." AS req
    INNER JOIN ".$modx->getFullTableName( '_catalog' )."  AS cat ON cat.id=req.id_item 
    WHERE cat.enabled = 'y' AND req.response = 0 AND req.id_user = ".$webuserinfo['id'];	
    
    
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}


//requestPriceS
if ($event == 'requestPriceS') {
	$cnt = 0;
	/*$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_request_price' )." WHERE seller = ".$webuserinfo['id'];*/
    
    
    $sql = "SELECT COUNT(req.id) AS cnt FROM ".$modx->getFullTableName( '_request_price' )." AS req
    INNER JOIN ".$modx->getFullTableName( '_catalog' )."  AS cat ON cat.id=req.id_item 
    WHERE cat.enabled = 'y' AND req.response = 0  AND req.seller = ".$webuserinfo['id'];	
    
    
    
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}


//requestPriceAll
if ($event == 'requestPriceA') {
	$cnt = 0;
/*	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_request_price' )." WHERE seller = ".$webuserinfo['id']." OR id_user = ".$webuserinfo['id'];*/
    
    $sql = "SELECT COUNT(req.id) AS cnt FROM ".$modx->getFullTableName( '_request_price' )." AS req
    INNER JOIN ".$modx->getFullTableName( '_catalog' )."  AS cat ON cat.id=req.id_item 
    WHERE cat.enabled = 'y'  AND req.response = 0  AND (req.seller = ".$webuserinfo['id']." OR req.id_user = ".$webuserinfo['id']." )";	
    
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}



//requestPriceAll
if ($event == 'newFavourPrice') {
	$cnt = 0;
	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_favorites' )." WHERE id_user = ".$webuserinfo['id']." AND new_notice = 1";
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}





//waitResponse
if ($event == 'waitResponse') {
	$cnt = 0;
   // return '<span class="menuNotice">4</span>';
//	$sql = "SELECT COUNT(id) AS cnt FROM ".$modx->getFullTableName( '_request_price' )." WHERE seller = ".$webuserinfo['id']." OR id_user = ".$webuserinfo['id'];
	
    
    $sql = "SELECT  COUNT(oi.id) AS cnt
		FROM ".$modx->getFullTableName( '_order_items' )."  AS oi
		INNER JOIN ".$modx->getFullTableName( '_orders' )."  AS ordrs ON oi.id_order = ordrs.id
		INNER JOIN ".$modx->getFullTableName( '_catalog' )."  AS cat ON cat.id = oi.id_item
		LEFT JOIN ".$modx->getFullTableName( '_responses' )."   AS resp ON (resp.id_cat_item = oi.id_item AND resp.id_order = ordrs.id)
		WHERE ordrs.user_id = ".$webuserinfo['id']."
		AND (ordrs.`status` = 'waitEnd'
			OR ordrs.`status` = 'ended'
			)
		AND resp.id_cat_item is NULL
		";
    
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}




if ($event == 'publicResponse') {
	$cnt = 0; 
    $sql = "SELECT  COUNT(id) AS cnt
		FROM ".$modx->getFullTableName( '_responses' )." 
		WHERE id_user = ".$webuserinfo['id']."
		AND `visible` = '1'
		";
    
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}





if ($event == 'myItemResponse') {
	$cnt = 0;

    $sql = "SELECT  COUNT(resp.id) AS cnt
		FROM ".$modx->getFullTableName( '_responses' )." AS resp
        INNER JOIN  ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = resp.id_cat_item
		WHERE cat.seller = ".$webuserinfo['id']."
		AND  resp.`visible` = '1'
		";
    
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}








if ($event == 'newFinancesEvents') {
	$cnt = 0;

    $sql = "SELECT  COUNT(id) AS cnt
		FROM ".$modx->getFullTableName( '_finances_events' )." 
		WHERE id_user = ".$webuserinfo['id']."
		AND  see = 0
		";
    
	if ($result = mysql_query($sql)) {
		if ($cnt = mysql_fetch_assoc($result)) {
			if ($cnt['cnt'] > 0 ) {
				return '<span class="menuNotice">'.$cnt['cnt'].'</span>';
			}
		}
	}
	return false;
}









?>