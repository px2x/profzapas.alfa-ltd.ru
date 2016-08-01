<?php

$auth = 108;
$basket = 200;
$page_sellerInfo = 217; 

if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	header( 'location: '. $modx->makeUrl( $auth ) );
	exit();
}

$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];


$topage= ( $topage ? intval( $topage ) : $modx->documentIdentifier );
$topage_url= $modx->makeUrl( $topage );


$vkladka_active= 1;
if ($_GET['tab'] == 'user') $vkladka_active= 2;




if (is_numeric($_GET['acceptId'])  && is_numeric($_GET['buyer']) ){
	mysql_query("UPDATE  ".$modx->getFullTableName( '_request_price' )." SET response = 1 WHERE id = ".$_GET['acceptId']." AND id_user = ".$_GET['buyer']." ") or die ("ERR 6643");
}



if (is_numeric($_GET['abortId']) && is_numeric($_GET['buyer'])){
	mysql_query("UPDATE  ".$modx->getFullTableName( '_request_price' )." SET response = -1 WHERE id = ".$_GET['abortId']." AND id_user = ".$_GET['buyer']." ") or die ("ERR 4637");
}



if (is_numeric($_GET['deleteId'])  && is_numeric($_GET['buyer'])){
	mysql_query("DELETE FROM  ".$modx->getFullTableName( '_request_price' )."  WHERE id = ".$_GET['deleteId']." ") or die ("ERR 2467");
}



if (is_numeric($_GET['byuId'])){
	
	
	//is_numeric( $_GET['addToCartId'] ) && is_numeric( $_GET['addToCartCount'])

	
	$result = mysql_query("SELECT * FROM  ".$modx->getFullTableName( '_request_price' )."  WHERE id = ".$_GET['byuId']." AND id_user = ".$webuserinfo['id']) or die ("ERR 838458");
	if (mysql_num_rows($result) > 0){
		
		if ($itemRow = mysql_fetch_assoc($result)) {
			echo '!!!!!';
			$resToBasket = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'addToCartNotGET', 'addToCartId' => $itemRow['id_item'] , 'addToCartCount' => $itemRow['count_item'] ,'acc_price' => $itemRow['request_price'] , 'userId' => $webuserinfo['id']));
			if ($resToBasket){
				mysql_query("DELETE FROM  ".$modx->getFullTableName( '_request_price' )."  WHERE id = ".$_GET['byuId']." AND id_user = ".$webuserinfo['id']) or die ("ERR 436372");
				header( 'location: '. $modx->makeUrl( $basket ) );
				exit();
			}
		} 
	
	}
	
}


?>



<div class="vkladki_butts">
	<div class="vkldk_butt <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>" data-id="1">Для продаваемых товаров <?=$modx->runSnippet( '_LK_getCountEvent', array('event' => 'requestPriceS'))?></div>
	<div class="vkldk_butt <?= ( $vkladka_active == 2 ? 'active' : '' ) ?>" data-id="2">Для покупаемых товаров <?=$modx->runSnippet( '_LK_getCountEvent', array('event' => 'requestPriceU'))?></div>

	<div class="clr">&nbsp;</div>
</div>



<div class="vkladki_divs">

	
	<!--============================TAB1 START=======================================-->
	
	
	
	<?php
	
	
	$sql = "SELECT usr.firstname, usr.surname, usr.email, usr.id AS buyerID, cat.id, cat.code, cat.parent, cat.title, cat.manufacturer, cat.price, cat.currency, cat.in_stock, cat.text, cat.state, cat.packaging, cat.guarantee, cat.discount, cat.shipment, cat.tender  , rp.count_item , rp.request_price , rp.id AS rpid , rp.response
		FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
		INNER JOIN  ".$modx->getFullTableName( '_request_price' )." AS rp ON rp.id_item = cat.id
		INNER JOIN ".$modx->getFullTableName( '_user' )." AS usr ON rp.id_user = usr.id
		WHERE cat.enabled =  'y' 
		AND cat.seller =   '".$webuserinfo['id']."'
		GROUP BY cat.code LIMIT 200";
	$result = mysql_query($sql) or die ('Error Select noEnable items: '. mysql_error());
	


	$noEnableItems .= '<form action="'.$topage_url.'?tab=1" name="EnabledItems" method="POST">';
	$noEnableItems .= '<div class=lkItemtitle>
		<div class="checkboxsENA"><input type="checkbox" name="checkallENA" /></div>
		<div class="code">Код</div>
		<div class="vend">Производитель</div>
		<div class="name">Наименование</div>
		<div class="stock">Количество</div>
		<div class="price">Цена</div>
		<div class="category">Покупатель</div>
		<div class="more"></div>
	</div>';

	$flagBgColor = -1;
	while ($row = mysql_fetch_assoc($result)){

		//достаем склвдыколичество адреса -----START
		$sqlWarehouse = "SELECT cwh.quantity, uwh.num, uwh.address, uwh.city
			FROM  ".$modx->getFullTableName( '_catalog_warehouse' )." AS cwh
			LEFT JOIN  ".$modx->getFullTableName( '_user_warehouse' )." AS uwh ON cwh.warehouse = uwh.id
			WHERE cwh.item =  '".$row['id']."' ";
		$resultWarehouse = mysql_query($sqlWarehouse) or die ('Error Select sqlWarehouse: '. mysql_error());
		$warehouseList = 'Нет информации о складах';
		$tmp = '';
		while ($rowWh = mysql_fetch_assoc($resultWarehouse)){
			$tmp .= '<div>Склад №'.$rowWh['num'].' - <span>'.$rowWh['city'].' ('.$rowWh['address'].')</span> '.$rowWh['quantity'].' шт.</div>';
		}
		if ($tmp != ''){
			$warehouseList = $tmp;
		}
		//достаем склвдыколичество адреса -----END


		//достаем картинки -----START
		$sqlimg = "SELECT ci.link
			FROM  ".$modx->getFullTableName( '_catalog_images' )." AS ci
			WHERE ci.id_item =  '".$row['id']."' ";
		$resultimg = mysql_query($sqlimg) or die ('Error Select sqlimg: '. mysql_error());
		$croppedImgs = '';
		while ($rowimg = mysql_fetch_assoc($resultimg)){
			$croppedImgs .= '<a class="highslide sert" onclick="return hs.expand(this)" href="'. $modx->runSnippet( 'ImgCrop6', array( 'img' => $rowimg[ 'link' ], 'w' => 800, 'h' => 800, 'fill' => 0 , 'wm' =>1) ) .'">
							<img src="'. $modx->runSnippet( 'ImgCrop6', array( 'img' => $rowimg[ 'link' ], 'w' => 85, 'h' => 80, 'fill' => 0 , 'wm' =>1 ) ) .'" />
							</a>';
		}

		//достаем картинки -----END




		//достаем документы -----START
		$sqlimg = "SELECT cd.link , cd.originalname
			FROM  ".$modx->getFullTableName( '_catalog_docs' )." AS cd
			WHERE cd.id_item =  '".$row['id']."' ";
		$resultimg = mysql_query($sqlimg) or die ('Error Select sqldoc: '. mysql_error());
		$croppedDocs = '';
		while ($rowimg = mysql_fetch_assoc($resultimg)){

			$ext =  end(explode(".", $rowimg[ 'link' ]));
			$croppedDocs .= '<a class="docLinkDownload" href="../'.$rowimg[ 'link' ].'"><div class="documentPrint" style="background-image:url(../template/images/file_'.$ext.'.png);"><span>'.$rowimg[ 'originalname' ].'</span></div></a>';
		}
		//достаем документы -----END


		
		
	if( $row[ 'currency' ] != 'rub' ) $row[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$row[ 'price' ], 'c'=>$row[ 'currency' ] ) );
		
	$printPrice = '<nobr><span class="price">'.$modx->runSnippet( 'Price', array( 'price' => $row[ 'price' ], 'round' => 0 ) ).' <span class="rubl">a</span></span></nobr>';
	//$noEnableItems.=$row['discount'];
	if ($row['discount'] > 0) {
		//$noEnableItems.=$row['discount'];
		$printPrice =  '<div class="discountprice">
							<div class="sale">-'.$row['discount'].'%</div>
							<div class=oldprice>'.$modx->runSnippet( 'Price', array( 'price' => $row[ 'price' ], 'round' => 0 ) ).' <span class="rubl">a</span></div>
							<div class=newprice>'.$modx->runSnippet( 'Price', array( 'price' => round(($row['price'] - ($row['price'] / 100 * $row['discount']))), 'round' => 0 ) ).' <span class="rubl">a</span></div>
						</div>';
	}
	
	
		$addNewCatText='';
		if ($flagBgColor > 0) {$addStyle = 'fillGrayBG';} else {$addStyle = '';}
		$noEnableItems .= '<div class="lkItem '.$addStyle.'">';
		$noEnableItems .= '<div class="checkboxsENA"><input type="checkbox" name="deleteID['.$row['id'].']" value="'.$row['id'].'" /></div>';
		$noEnableItems .= '<div class="code"><a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'id' ] .'/">'.$row['code'].'</a></div>';

		//$result .= '<td class="jqtri_col jqtric_code"><a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'id' ] .'/">'. $row[ 'code' ] .'</a></td>';


		$noEnableItems .= '<div class="vend">'.$row['manufacturer'].'</div>';
		$noEnableItems .= '<div class="name"><div class="type">'.$row['title'].'</div></div>';
		$noEnableItems .= '<div class="stock">'.$row['in_stock'].' шт.</div>';
		$noEnableItems .= '<div class="price">'.$printPrice.'</div>';	

		$noEnableItems .= '<div class="categorySeller">'.$row['firstname'].' '.$row['surname'].'</div>';

		$noEnableItems .= '<div class="more"></div>';

		$noEnableItems .= '<div class="seeMoreInfo" style="height:0;">
							   <div class="px_left">
									<div class="px_top">
										<div>Состояние: <span>'.($row['state'] == 'new' ? 'новый' : ($row['state'] == 'bu' ? 'Б/У' : 'на запчасти') ).'</span></div>
										<div>Гарантия: <span>'.($row['guarantee'] > 0 ? $row['guarantee'].' мес.' : 'нет гарантии').'</span></div>
										<div>Наличие упаковки: <span>'.($row['packaging'] == 'y' ? 'да' : 'нет').'</span></div>
										<div>Распродажа: <span>'.($row['discount'] > 0 ? 'да (скидка '.$row['discount'].'%)' : 'нет').'</span></div>
										<div>Споособ доставки: <span>'.($row['shipment'] == 'human' ? 'курьером' : 'почтой').'</span></div>
										<div>Возможность торгов: <span>'.($row['tender'] == 1 ? 'да' : 'нет').'</span></div>
									</div>
									<div class="px_bottom">
										'.$croppedDocs.'
									</div>
							   </div>
							   <div class="px_right">
									<div class="px_top">
										<div class="px_top_left">'.$row['firstname'].' '.$row['surname'].' предлагает продать ему данный товар в количестве '.$row['count_item'].' шт. по '.$row['request_price'].'  руб. за штуку. Общая сумма - '.$row['count_item'] * $row['request_price'].' руб.</div>
										<div class="action">';
										
		
		if ($row['response'] == 1){
			$noEnableItems .='Вы согласились на данное предложение';
		}elseif($row['response'] == -1){
			$noEnableItems .='Вы отказались от данного предложения';
		}elseif($row['response'] == 0){
			$noEnableItems .='
			<a href="'.$topage_url.'?buyer='.$row['buyerID'].'&acceptId='.$row['rpid'].'" class="greenButton">Согласиться</a>
			<a href="'.$topage_url.'?buyer='.$row['buyerID'].'&abortId='.$row['rpid'].'" class="redButton">Отказаться</a>
			';
		}
											
												
												
								$noEnableItems .='
										</div>

									</div>
									<div class="px_middle">
										'.$warehouseList.'
											</div>
									<div class="px_bottom">
										'.$croppedImgs.'
									</div>
							   </div>
                               <span class="clr">&nbsp;</span>
						   </div>
                           <span class="clr">&nbsp;</span>
						   ';

		$noEnableItems .= '</div>
		<div class="clr">&nbsp;</div>';
		$flagBgColor = $flagBgColor * (-1);
	}


	$noEnableItems .= '<button class="buttonsubmit2 topMargin" name="DeleteCheckedItemEnabled" type="submit">Удалить отмеченные</button><div class="clr">&nbsp;</div>';
	$noEnableItems .= '</form>
	<div class="clr">&nbsp;</div>';


	?>
	<div class="vkldk_div vkldk_div_1 <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<?=$noEnableItems?>
			<div class="clr">&nbsp;</div>
		</div>
		<div class="clr">&nbsp;</div>
	</div>
	<div class="clr">&nbsp;</div>
	<!--============================TAB1 END=======================================-->
	
	
	
	
	
	
	
	
	<!--============================TAB2 START=======================================-->
	
	
		<?php
	
	$noEnableItems ='';
	$sql = "SELECT usr.firstname, usr.surname, usr.email, usr.id AS buyerID, cat.id, cat.code, cat.parent, cat.title, cat.manufacturer, cat.price, cat.currency, cat.in_stock, cat.text, cat.state, cat.packaging, cat.guarantee, cat.discount, cat.shipment, cat.tender  , rp.count_item , rp.request_price , rp.id AS rpid , rp.response
		FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
		INNER JOIN  ".$modx->getFullTableName( '_request_price' )." AS rp ON rp.id_item = cat.id
		INNER JOIN ".$modx->getFullTableName( '_user' )." AS usr ON cat.seller = usr.id
		WHERE rp.id_user =  '".$webuserinfo['id']."'
		AND cat.enabled =  'y' 
		GROUP BY cat.code LIMIT 200";
	$result = mysql_query($sql) or die ('Error Select noEnable items: '. mysql_error());
	


	$noEnableItems .= '<form action="'.$topage_url.'?tab=1" name="EnabledItems" method="POST">';
	$noEnableItems .= '<div class=lkItemtitle>
		<div class="checkboxsENA"><input type="checkbox" name="checkallENA" /></div>
		<div class="code">Код</div>
		<div class="vend">Производитель</div>
		<div class="name">Наименование</div>
		<div class="stock">Количество</div>
		<div class="price">Цена</div>
		<div class="category">Продавец</div>
		<div class="more"></div>
	</div>';

	$flagBgColor = -1;
	while ($row = mysql_fetch_assoc($result)){

		//достаем склвдыколичество адреса -----START
		$sqlWarehouse = "SELECT cwh.quantity, uwh.num, uwh.address, uwh.city
			FROM  ".$modx->getFullTableName( '_catalog_warehouse' )." AS cwh
			LEFT JOIN  ".$modx->getFullTableName( '_user_warehouse' )." AS uwh ON cwh.warehouse = uwh.id
			WHERE cwh.item =  '".$row['id']."' ";
		$resultWarehouse = mysql_query($sqlWarehouse) or die ('Error Select sqlWarehouse: '. mysql_error());
		$warehouseList = 'Нет информации о складах';
		$tmp = '';
		while ($rowWh = mysql_fetch_assoc($resultWarehouse)){
			$tmp .= '<div>Склад №'.$rowWh['num'].' - <span>'.$rowWh['city'].' ('.$rowWh['address'].')</span> '.$rowWh['quantity'].' шт.</div>';
		}
		if ($tmp != ''){
			$warehouseList = $tmp;
		}
		//достаем склвдыколичество адреса -----END


		//достаем картинки -----START
		$sqlimg = "SELECT ci.link
			FROM  ".$modx->getFullTableName( '_catalog_images' )." AS ci
			WHERE ci.id_item =  '".$row['id']."' ";
		$resultimg = mysql_query($sqlimg) or die ('Error Select sqlimg: '. mysql_error());
		$croppedImgs = '';
		while ($rowimg = mysql_fetch_assoc($resultimg)){
			$croppedImgs .= '<a class="highslide sert" onclick="return hs.expand(this)" href="'. $modx->runSnippet( 'ImgCrop6', array( 'img' => $rowimg[ 'link' ], 'w' => 800, 'h' => 800, 'fill' => 0 , 'wm' =>1) ) .'">
							<img src="'. $modx->runSnippet( 'ImgCrop6', array( 'img' => $rowimg[ 'link' ], 'w' => 85, 'h' => 80, 'fill' => 0 , 'wm' =>1 ) ) .'" />
							</a>';
		}

		//достаем картинки -----END
		
		
		//достаем документы -----START
		$sqlimg = "SELECT cd.link , cd.originalname
			FROM  ".$modx->getFullTableName( '_catalog_docs' )." AS cd
			WHERE cd.id_item =  '".$row['id']."' ";
		$resultimg = mysql_query($sqlimg) or die ('Error Select sqldoc: '. mysql_error());
		$croppedDocs = '';
		while ($rowimg = mysql_fetch_assoc($resultimg)){

			$ext =  end(explode(".", $rowimg[ 'link' ]));
			$croppedDocs .= '<a class="docLinkDownload" href="../'.$rowimg[ 'link' ].'"><div class="documentPrint" style="background-image:url(../template/images/file_'.$ext.'.png);"><span>'.$rowimg[ 'originalname' ].'</span></div></a>';
		}
		//достаем документы -----END


		
	if( $row[ 'currency' ] != 'rub' ) $row[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$row[ 'price' ], 'c'=>$row[ 'currency' ] ) );
		
	$printPrice = '<nobr><span class="price">'.$modx->runSnippet( 'Price', array( 'price' => $row[ 'price' ], 'round' => 0 ) ).' <span class="rubl">a</span></span></nobr>';
	//$noEnableItems.=$row['discount'];
	if ($row['discount'] > 0) {
		//$noEnableItems.=$row['discount'];
		$printPrice =  '<div class="discountprice">
							<div class="sale">-'.$row['discount'].'%</div>
							<div class=oldprice>'.$modx->runSnippet( 'Price', array( 'price' => $row[ 'price' ], 'round' => 0 ) ).' <span class="rubl">a</span></div>
							<div class=newprice>'.$modx->runSnippet( 'Price', array( 'price' => round(($row['price'] - ($row['price'] / 100 * $row['discount']))), 'round' => 0 ) ).' <span class="rubl">a</span></div>
						</div>';
	}
	
	
		$addNewCatText='';
		if ($flagBgColor > 0) {$addStyle = 'fillGrayBG';} else {$addStyle = '';}
		$noEnableItems .= '<div class="lkItem '.$addStyle.'">';
		$noEnableItems .= '<div class="checkboxsENA"><input type="checkbox" name="deleteID['.$row['id'].']" value="'.$row['id'].'" /></div>';
		$noEnableItems .= '<div class="code"><a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'id' ] .'/">'.$row['code'].'</a></div>';

		//$result .= '<td class="jqtri_col jqtric_code"><a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'id' ] .'/">'. $row[ 'code' ] .'</a></td>';


		$noEnableItems .= '<div class="vend">'.$row['manufacturer'].'</div>';
		$noEnableItems .= '<div class="name"><div class="type">'.$row['title'].'</div></div>';
		$noEnableItems .= '<div class="stock">'.$row['in_stock'].' шт.</div>';
		$noEnableItems .= '<div class="price">'.$printPrice.'</div>';	

		$noEnableItems .= '<div class="categorySeller"><a class="newMessage" href="'.$modx->makeUrl($page_sellerInfo).'?sellerID='.$row['buyerID'].'">'.$row['firstname'] . ' '.$row['surname'].'</a> </div>';

		$noEnableItems .= '<div class="more"></div>';

		$noEnableItems .= '<div class="seeMoreInfo" style="height:0;">
							   <div class="px_left">
									<div class="px_top">
										<div>Состояние: <span>'.($row['state'] == 'new' ? 'новый' : ($row['state'] == 'bu' ? 'Б/У' : 'на запчасти') ).'</span></div>
										<div>Гарантия: <span>'.($row['guarantee'] > 0 ? $row['guarantee'].' мес.' : 'нет гарантии').'</span></div>
										<div>Наличие упаковки: <span>'.($row['packaging'] == 'y' ? 'да' : 'нет').'</span></div>
										<div>Распродажа: <span>'.($row['discount'] > 0 ? 'да (скидка '.$row['discount'].'%)' : 'нет').'</span></div>
										<div>Споособ доставки: <span>'.($row['shipment'] == 'human' ? 'курьером' : 'почтой').'</span></div>
										<div>Возможность торгов: <span>'.($row['tender'] == 1 ? 'да' : 'нет').'</span></div>
									</div>
									<div class="px_bottom">
										'.$croppedDocs.'
									</div>
							   </div>
							   <div class="px_right">
									<div class="px_top">
										<div class="px_top_left">Вы предложили продавцу купить данный товар в количестве '.$row['count_item'].' шт. по '.$row['request_price'].'  руб. за штуку. Общая сумма - '.$row['count_item'] * $row['request_price'].' руб.</div>
										<div class="action">';
		
		
		
												
		
		if ($row['response'] == 1){
			$noEnableItems .='<span class="greenTX">Продавец согласился на данное предложение</span>
			<a href="'.$topage_url.'?byuId='.$row['rpid'].'" class="greenButton">Купить</a>
			<a href="'.$topage_url.'?buyer='.$row['buyerID'].'&deleteId='.$row['rpid'].'" class="redButton">Отменить</a>';
			
		}elseif($row['response'] == -1){
			$noEnableItems .='<span class="redTX">Продавец отказался от данного предложения</span>
			<a href="'.$topage_url.'?buyer='.$row['buyerID'].'&deleteId='.$row['rpid'].'" class="redButton">Отменить</a>';
		}elseif($row['response'] == 0){
			$noEnableItems .='
			
			<a href="'.$topage_url.'?buyer='.$row['buyerID'].'&deleteId='.$row['rpid'].'" class="redButton">Отменить</a>
			';
		}
											
												
												
								$noEnableItems .='
										</div>

									</div>
									<div class="px_middle">
										'.$warehouseList.'
											</div>
									<div class="px_bottom">
										'.$croppedImgs.'
									</div>
							   </div>
						   </div>
						   <span class="clr"></span>';

		$noEnableItems .= '</div>
		<div class="clr">&nbsp;</div>';
		$flagBgColor = $flagBgColor * (-1);
	}


	$noEnableItems .= '<button class="buttonsubmit2 topMargin" name="DeleteCheckedItemEnabled" type="submit">Удалить отмеченные</button><div class="clr">&nbsp;</div>';
	$noEnableItems .= '</form>
	<div class="clr">&nbsp;</div>';


	?>
	
	
	<div class="vkldk_div vkldk_div_2 <?= ( $vkladka_active == 2 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<?=$noEnableItems?>
		</div>
        <div class="clr">&nbsp;</div>
	</div>
	<div class="clr">&nbsp;</div>
	<!--============================TAB2 END=======================================-->
	

	
	
</div>
<div class="clr">&nbsp;</div>





?>