<?php

/*	


if (!is_numerick($ordeghrId)){
	return false;
	
}


*/


	
$auth= 108;
$topage_url= $modx->makeUrl( $modx->documentIdentifier );
if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	header( 'location: '. $modx->makeUrl( $auth ) );
	exit();
}

$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];

$vkladka_active= 1;
if ($_GET['tab'] == 2) $vkladka_active= 2;
?>

<div class="vkladki_butts">
	<div class="vkldk_butt <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>" data-id="1">Ожидается отзыв <?=$modx->runSnippet( '_LK_getCountEvent', array('event' => 'waitResponse'))?></div>
	<div class="vkldk_butt <?= ( $vkladka_active == 2 ? 'active' : '' ) ?>" data-id="2">Опубликованные мной <?=$modx->runSnippet( '_LK_getCountEvent', array('event' => 'publicResponse'))?></div>
    <div class="vkldk_butt <?= ( $vkladka_active == 3 ? 'active' : '' ) ?>" data-id="3">О моих товарах <?=$modx->runSnippet( '_LK_getCountEvent', array('event' => 'myItemResponse'))?></div>
	<div class="clr">&nbsp;</div>
</div>


<div class="vkladki_divs">
	
	


	
	
	<!--========================TAB 1 START================================================-->
	
<?php
	
$sql = "SELECT  resp.* , usr.firstname , usr.surname, ordrs.order_number,ordrs.date_w_check AS dateorder, ordrs.id, cat.id AS catid, cat.title , cat.parent, cat.code , cat.manufacturer , cat.manufacturer_country, cat.`text` 
		FROM ".$modx->getFullTableName( '_order_items' )."  AS oi
		INNER JOIN ".$modx->getFullTableName( '_orders' )."  AS ordrs ON oi.id_order = ordrs.id
		INNER JOIN ".$modx->getFullTableName( '_catalog' )."  AS cat ON cat.id = oi.id_item
		INNER JOIN ".$modx->getFullTableName( '_user' )."  AS usr ON usr.id = cat.seller
		LEFT JOIN ".$modx->getFullTableName( '_responses' )."   AS resp ON (resp.id_cat_item = oi.id_item AND resp.id_order = ordrs.id)
		WHERE ordrs.user_id = ".$webuserinfo['id']."
		AND (ordrs.`status` = 'waitEnd'
			OR ordrs.`status` = 'ended'
			)
		AND resp.id_cat_item is NULL
		";

$rows = false;
if ($result = mysql_query($sql)){
	while ($tmp  = mysql_fetch_assoc($result)){
		$rows[] = $tmp;
	}

}



if (is_array($rows)){
    
    $bodyWaitResponse = '<div class="bodyWaitResponse">';

    
    $bodyWaitResponse .='<div class="titlePage">Пожалуйста, оставьте отзыв о недавно купленных Вами товарах </div>';
    
    
	$bodyWaitResponse .='
	<div class="bodyWaitResp_line_tit">
		<div class="resp_code">Арт.</div>
		<div class="resp_title">Наименование</div>
		<div class="resp_manuf">Производитель</div>
		<div class="resp_order">№ заказа</div>
		<div class="resp_seller">Продавец</div>
	</div>';

    
	foreach ($rows AS $row) {
		
	//достаем картинки -----START
	$sqlimg = "SELECT ci.link
				FROM  ".$modx->getFullTableName( '_catalog_images' )." AS ci
				WHERE ci.id_item =  '".$row['id']."' ";
	$resultimg = mysql_query($sqlimg) or die ('Error Select sqlimg: '. mysql_error());
	$mainImg = '';
	while ($rowimg = mysql_fetch_assoc($resultimg)){
		if ($mainImg == '') {
			$mainImg = '<div class="previevBimg" style="background-image:url('. $modx->runSnippet( 'ImgCrop6', array( 'img' => $rowimg[ 'link' ], 'w' => 440, 'h' => 400, 'fill' => 0 , 'wm' =>1 ) ) .');" /> </div>';
		}
	}
	if ($mainImg == '') {
		$mainImg = '<div class="previevBimg" style="background-image:url('. $modx->runSnippet( 'ImgCrop6', array( 'img' => 'template/images/notphoto.png', 'w' => 340, 'h' => 300, 'fill' => 0 , 'wm' =>1 ) ) .');" /> </div>';

	}

	//достаем картинки -----END
		
		
		$bodyWaitResponse .='
		<div class="bodyWaitResp_line">
			<div class="resp_code"><a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'catid' ] .'/">'.$row['code'].'</a></div>
			<div class="resp_title">'.$row['title'].'</div>
			<div class="resp_manuf">'.$row['manufacturer'].' ('.$row['manufacturer_country'].')</div>
			<div class="resp_order">'.$row['order_number'].'</div>
			<div class="resp_seller">'.$row['firstname'].' '.$row['surname'].'</div>
			<div class=hid_px_popup_data>
				<div class="px_pp_title">'.$row['firstname'].' '.$row['surname'].' ('.$row['title'].')</div>
				<div class="px_pp_photo">'.$mainImg.'</div>
				<div class="px_pp_vendor">'.$row['manufacturer'].' <span>('.$row['manufacturer_country'].')<span></div>
				<div class="px_pp_dateOrd">'.(date("d.m.Y H:i",$row['dateorder'])).'</div>
				<div class="px_pp_description">'.$row['text'].'</div>
				<div class="clr"></div>
                <div class="ratingStars">
                    <div class="wrap_rank_OS"><div class="oneStarRank" id="starRank_1" data-rank="1">1</div></div>
                    <div class="wrap_rank_OS"><div class="oneStarRank" id="starRank_2" data-rank="2">2</div></div>
                    <div class="wrap_rank_OS"><div class="oneStarRank" id="starRank_3" data-rank="3">3</div></div>
                    <div class="wrap_rank_OS"><div class="oneStarRank" id="starRank_4" data-rank="4">4</div></div>
                    <div class="wrap_rank_OS"><div class="oneStarRank" id="starRank_5" data-rank="5">5</div></div>
                </div>
				<textarea class="px_pp_inp_response" placeholder="Введите текст отзыва о продавце"></textarea>
				<button class="px_pp_inp_response_send" data-order_id="'.$row['id'].'" data-item_id="'.$row['catid'].'">Отправить</button>
				<div class="waitCheckRes">
					<div class="checkResult"></div>
					<div class="wrap">
						<div class="dot"></div>
					</div>
				</div>
			</div>
		</div>';
	}
    $bodyWaitResponse .= '</div>';
}else  {
    
    $bodyWaitResponse .='<div class="titlePage">Отзывы о купленных Вами товарах можно оставлять когда вы получите товар</div>';
}




?>
	
	<div class="vkldk_div vkldk_div_1 <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<?=$bodyWaitResponse?>
			<div class="clr">&nbsp;</div>
		</div>
		<div class="clr">&nbsp;</div>
	</div>
	<div class="clr">&nbsp;</div>
	<!--========================TAB 1 END================================================-->	
	
	
	
	
	
	
	<!--========================TAB 2 END================================================-->	
    
    
    
<?php
    
     $sql = "SELECT resp.* , cat.parent, cat.id AS catid, cat.title , cat.code , cat.manufacturer, 
            usr.firstname AS buyerName , usr.surname  AS buyerSurName, usr.email  AS buyerEmail , 
            usrS.firstname AS sellerName , usrS.surname  AS sellerSurName, usrS.email  AS sellerEmail 
            FROM ".$modx->getFullTableName( '_responses' )." AS resp
            INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = resp.id_cat_item
            INNER JOIN ".$modx->getFullTableName( '_user' )." AS usr ON usr.id = resp.id_user
            INNER JOIN ".$modx->getFullTableName( '_user' )." AS usrS ON usrS.id = cat.seller
            WHERE resp.visible = '1'
            AND resp.id_user = ".$webuserinfo['id'];

    $result = mysql_query($sql) or die (mysql_error());
    
    if ($result && mysql_num_rows($result) > 0) {
        
        $result_page= '<div class="summryRank">';
        
         while ($row = mysql_fetch_assoc($result)){
            
            $result_page.= '
            <div class="oneResponse">
                <div class="resp_for_item">'.$row['title'].' <span class="largest">(Арт.:<a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'catid' ] .'/">'.$row['code'].'</a>) '.$row['manufacturer'].'</span></div>
                <div class="resp_for_seller">Продавец: '.$row['sellerName'].' '.$row['sellerSurName'].'</div>
                <div class="resp_rank">'.$row['rank'].'</div>
                <div class="resp_text">'.$row['response'].'</div>
                <div class="crl"></div>
                <div class="date">Дата отзыва '.date("d.m.Y",$row['timest']).'</div>
            </div>
            ';
            
        }
        
        $result_page.='</div>';
        
    }
    

    
?>
    
    
    
	<div class="vkldk_div vkldk_div_2 <?= ( $vkladka_active == 2 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			     <?=$result_page?>
			<div class="clr">&nbsp;</div>
		</div>
		<div class="clr">&nbsp;</div>
	</div>
	<div class="clr">&nbsp;</div>
	<!--========================TAB 2 END================================================-->
	
	
	
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    	
	
	<!--========================TAB 3 END================================================-->	
    
    
    
<?php
    
     $sql = "SELECT resp.* , cat.parent, cat.id AS catid, cat.title , cat.code , cat.manufacturer, 
            usr.firstname AS buyerName , usr.surname  AS buyerSurName, usr.email  AS buyerEmail , 
            usrS.firstname AS sellerName , usrS.surname  AS sellerSurName, usrS.email  AS sellerEmail 
            FROM ".$modx->getFullTableName( '_responses' )." AS resp
            INNER JOIN ".$modx->getFullTableName( '_catalog' )." AS cat ON cat.id = resp.id_cat_item
            INNER JOIN ".$modx->getFullTableName( '_user' )." AS usr ON usr.id = resp.id_user
            INNER JOIN ".$modx->getFullTableName( '_user' )." AS usrS ON usrS.id = cat.seller
            WHERE resp.visible = '1'
            AND cat.seller = ".$webuserinfo['id'];

    $result = mysql_query($sql) or die (mysql_error());
    
    if ($result && mysql_num_rows($result) > 0) {
        
        $result_page= '<div class="summryRank">';
        
         while ($row = mysql_fetch_assoc($result)){
            
            $result_page.= '
            <div class="oneResponse">
                <div class="resp_for_item">'.$row['title'].' <span class="largest">(Арт.:<a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'catid' ] .'/">'.$row['code'].'</a>) '.$row['manufacturer'].'</span></div>
                <div class="resp_for_seller">Покупатель: '.$row['buyerName'].' '.$row['buyerSurName'].'</div>
                <div class="resp_rank">'.$row['rank'].'</div>
                <div class="resp_text">'.$row['response'].'</div>
                <div class="crl"></div>
                <div class="date">Дата отзыва '.date("d.m.Y",$row['timest']).'</div>
            </div>
            ';
            
        }
        
        $result_page.='</div>';
        
    }
    
 
    
?>
    
    
    
	<div class="vkldk_div vkldk_div_3 <?= ( $vkladka_active == 3 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			     <?=$result_page?>
			<div class="clr">&nbsp;</div>
		</div>
		<div class="clr">&nbsp;</div>
	</div>
	<div class="clr">&nbsp;</div>
	<!--========================TAB 3 END================================================-->
	
    
    
    
    
	
</div>

<?php
//<a href="'.$topage_url.'?confirmTestId='.$row['id'].'" class="greenButton">Подтвердить качество</a>



?>