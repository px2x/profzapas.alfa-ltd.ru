<?php

$pageId_basket = 200;
$pageId_messages= 122;
$pageId_aukc= 125;

$states= array(
	'new' => 'Новое', 'bu' => 'Б/У', 'zapch' => 'На запчасти',
);


	$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];




if( $type == 'category_1' )
{
	$result .= '<div class="catcat_big '.( $last ? 'catcat_big_last' : '' ).'">
		<div class="catcatb_img"><img src="'. $modx->runSnippet( 'ImgCrop6', array( 'img'=>$row[ 'image' ], 'w'=>86, 'h'=>86, 'backgr'=>true ) ) .'" /></div>
		<div class="catcatb_rght">
			<div class="catcatb_tit"><a class="as1" href="'. $modx->makeUrl( $row[ 'id' ] ) .'">'. $row[ 'pagetitle' ] .'</a></div>
			<div class="catcatb_podcat">';
	
	$podcat= $modx->runSnippet( 'GetDoc6', array( 'ids'=>$row[ 'id' ], 'type'=>'childs', 'depth'=>'1', 'fields'=>'pagetitle', 'isf'=>'all' ) );
	if( $podcat )
	{
		foreach( $podcat AS $row2 )
		{
			$result .= '<div class="catcatb_podcat_itm"><span class="famicon">&nbsp;&nbsp;</span><a class="as2" href="'. $modx->makeUrl( $row2[ 'id' ] ) .'">'. $row2[ 'pagetitle' ] .'</a></div>';
			//$result .= ( $flag ? ', ' : '' ) .'<a class="as5" href="'. $modx->makeUrl( $row2[ 'id' ] ) .'">'. $row2[ 'pagetitle' ] .'</a>';
			//$flag= true;
		}
	}
	
	$result .= '</div>
		</div>
		<div class="clr">&nbsp;</div>
	</div>';
	
	if( $last ? 'catcat_big_last' : '' ) $result .= '<div class="clr">&nbsp;</div>';
}








if( $type == 'category_2' )
{
	$result .= '<div class="catcat '.( $last ? 'catcat_last' : '' ).'"><a href="'. $modx->makeUrl( $row[ 'id' ] ) .'">
					<div class="catc_img"><img src="'. $modx->runSnippet( 'ImgCrop6', array( 'img'=>$row[ 'image' ], 'w'=>180, 'h'=>180, 'fill'=>1 ) ) .'" /></div>
					<div class="catc_a"><span class="as2">'. $row[ 'pagetitle' ] .'</span></div>
				</a></div>';
	
	if( $last ? 'catcat_last' : '' ) $result .= '<div class="clr">&nbsp;</div>';
}








if( $type == 'item' )
{
	
	//upFinders
	if ($upFinders) {
		$modx->runSnippet( 'seeCounterItem', array( 'idItem'=>$row[ 'id' ] , 'type'=>'search' ) );
	}
	$item_state= $modx->runSnippet( 'BasketAction', array( 'act' => 'stat', 'id' => $row[ 'id' ] ) );
	
	if( $row[ 'currency' ] != 'rub' ) $row[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$row[ 'price' ], 'c'=>$row[ 'currency' ] ) );
	
	$row[ 'text' ]= $modx->runSnippet( 'str_replace', array( 'from'=>"\n", 'to'=>'<br />', 'txt'=>$row[ 'text' ] ) );
	
	$result .= '<tr class="jqt_row_itm '.( $last ? 'jqtri_chet' : '' ).'">';
		$result .= '<td class="jqtri_col jqtric_code"><a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'id' ] .'/">'. $row[ 'code' ] .'</a></td>';
		$result .= '<td class="jqtri_col jqtric_vend">'. $row[ 'manufacturer' ] .'</td>';
	
		$result .= '<td class="jqtri_col jqtric_name">
			<div class="descr">
				<div class="type">'. $row[ 'title' ] .'</div>
				<div>'. $row[ 'text' ] .'</div>
			</div>
			<div class="fulldescription">
				<div class="ugolok">&nbsp;</div>
				<div class="type">'. $row[ 'title' ] .'</div>
				<div>'. $row[ 'text' ] .'</div>
			</div>
		</td>';
		
		$sqlseller = "SELECT firstname , surname
			FROM  ".$modx->getFullTableName( '_user' )." 
			WHERE id =  '".$row['seller']."'  LIMIT 1";
		if ($resultseller = mysql_query($sqlseller)){
			if (!$tmp = mysql_fetch_assoc($resultseller)){
				$rowseller = 'Неизвестно';
			}else {
				$rowseller = $tmp['firstname']." ".$tmp['surname'];
			}
		}
	

		/*
		*
		*Отнимает купленный товар из общего кол -ва
		*
		*
		
	$sql = "SELECT oi.count AS broned FROM `profzapas__catalog` AS cat
		INNER JOIN `profzapas__order_items` AS oi ON cat.id = oi.id_item
		INNER JOIN `profzapas__orders` AS ord ON oi.id_order = ord.id
		WHERE (ord.status = 'waitShipment' OR ord.status = 'waitPayment' OR ord.status = 'calculateShipment') AND oi.id_item = ".$row['id'] ;
	$resultBroned = mysql_query($sql) or die ('Error Select broned');	
	if  ($bronedCount = mysql_fetch_assoc($resultBroned)['broned']){
		$row['in_stock'] = $row['in_stock'] -  $bronedCount;
	}
	
	if ($row['in_stock'] < 0) {
		$row['in_stock']=0;
	}
	*/	
	
	$tobasketButton ='';
	
	
/*	$requestNewPrice = '<div class="requestPriceForm">
	
							<form class="requestNewPriceForm">
								<div class="reqText">По какой цене Вы готовы приобрести этот товар?</div>
								<div class="delInputReqPrice"></div>
								<input type="hidden" name="newPriceID" value="'.$row[ 'id' ].'">
								<input class="inputText" type="text" name="newPrice" placeholder="Введите свою цену" pattern="^[ 0-9]+$" required>
								<input class="inputText textReqCount"  data-max="'.$row[ 'in_stock' ].'" type="text" name="newPriceCount" placeholder="Введите количество" pattern="^[ 0-9]+$" required>
								<input class="inputbutton" data-itemid="'.$row[ 'id' ].'" data-userid="'.$webuserinfo[ 'id' ].'" type="submit" name="newPriceSubmit" value="Запросить">
							</form>	
						</div>';*/
	
/*	if ($row['tender'] ==1) {
		
		$dopClass='';
		$titleText = 'Предложить свою цену';
		$acceptReqNewPrice= false;
		$reqPriseAccepted ='';
		$reqCountAccepted = '';
			
		
		///
		//echo $webuserinfo[ 'id' ];
		if (is_numeric($webuserinfo[ 'id' ])){
			$sql="SELECT * FROM ".$modx->getFullTableName( '_request_price' )."  WHERE id_user = ".$webuserinfo[ 'id' ]." AND id_item = ".$row[ 'id' ]." ORDER BY date_req DESC LIMIT 1 ";
			$resultStat = mysql_query($sql) or die ('ERR 26626 '.mysql_error());
			if (mysql_num_rows($resultStat) > 0) {
				if ($resVal = mysql_fetch_assoc($resultStat)){
					if ($resVal['response'] == 1){
						$dopClass = " greenBGbutton";
						$titleText = 'Продавец согласился на Ваше предложение';
						$acceptReqNewPrice= true;
						$reqPriseAccepted = $resVal['request_price'];
						$reqCountAccepted = $resVal['count_item'];
						$tobasketButton .= '<a class="buttonsubmit_toreqprice '.$dopClass.'" href="'. $modx->makeUrl( $pageId_aukc ).'?tab=user" >'.$requestNewPrice.'<img src="template/images/button_req_price.png" title="'.$titleText.'"></a>';
					}elseif ($resVal['response'] == -1) {
						$dopClass = " noAccess";
						$titleText = 'Продавец отказался от Вашего предложения';
						$tobasketButton .= '<a class="buttonsubmit_toreqprice '.$dopClass.'" href="'. $modx->makeUrl( $pageId_aukc ).'?tab=user" >'.$requestNewPrice.'<img src="template/images/button_req_price.png" title="'.$titleText.'"></a>';
					}else {
						$dopClass = " yellowBGbutton";
						$titleText = 'Продавец рассматривает Ваше предложение';
						$tobasketButton .= '<button class="buttonsubmit_toreqprice '.$dopClass.'" >'.$requestNewPrice.'<img src="template/images/button_req_price.png" title="'.$titleText.'"></button>';
					}
				}else {
					$tobasketButton .= '<button class="buttonsubmit_toreqprice '.$dopClass.'" >'.$requestNewPrice.'<img src="template/images/button_req_price.png" title="'.$titleText.'"></button>';
				}

			}else {
				$titleText = 'Предложить свою цену';
				$tobasketButton .= '<button class="buttonsubmit_toreqprice '.$dopClass.'" >'.$requestNewPrice.'<img src="template/images/button_req_price.png" title="'.$titleText.'"></button>';
			}
			//
		}else {
			
		}
		
	}
	*/
	
/*	if (is_numeric($webuserinfo[ 'id' ])){
		$sql2 = "SELECT * FROM  ".$modx->getFullTableName( '_favorites' )." WHERE id_user = ".$webuserinfo[ 'id' ]." AND id_item=".$row[ 'id' ];
		$result2 = mysql_query($sql2);
		//echo mysql_num_rows($result2);
		if (mysql_num_rows($result2)>0) {
			$tobasketButton .= '<button  data-itemid="'. $row[ 'id' ].'"  data-userid="'. $webuserinfo[ 'id' ].'" class="buttonsubmit_tofavour greenBGbutton" ><img src="template/images/button_to_favorite.png" title="Добавить в избранное"></button>';
		}else {
			$tobasketButton .= '<button  data-itemid="'. $row[ 'id' ].'"  data-userid="'. $webuserinfo[ 'id' ].'" class="buttonsubmit_tofavour " ><img src="template/images/button_to_favorite.png" title="Добавить в избранное"></button>';
		}
	}
	*/

	
		
	$itemStatus =  $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'checkItem', 'userId' => $webuserinfo[ 'id' ] , 'itemId' =>$row[ 'id' ]  ));
	
	if (!$acceptReqNewPrice){
		if ($itemStatus == 'finded') {
            
            $tobasketButton .= '
            <div class="wrapButtonCart">
                <div class="picCart">
                    <img src="template/images/cart_px.png" />
                </div> 
                <div class="button2border activeCartB">
                    <a href="'.$modx->makeUrl( $pageId_basket ).'" class="buttonsubmit_tobasket activeCartB" >
                        В корзине
                    </a>
                </div>
            </div>';
            
            
		}elseif ($row['in_stock'] < 1) {
//			$tobasketButton .= '<button class="buttonsubmit_tobasket noAccess" disabled><img src="template/images/button_basket_bg.png" title="Нет в наличии"></button>';			
            
            
            $tobasketButton .= '
            <div class="wrapButtonCart">
                <div class="picCart">
                    <img src="template/images/cart_px.png" />
                </div> 
                <div class="button2border disabledCartB">
                    <button class="buttonsubmit_tobasket disabledCartB" disabled>
                        Нет в наличии
                    </button>
                </div>
            </div>
            ';
		}else {
//			$tobasketButton .= '<button class="buttonsubmit_tobasket" data-itemid="'. $row[ 'id' ].'"  data-userid="'. $webuserinfo[ 'id' ].'"><img src="template/images/button_basket_bg.png" title="В корзину"></button>';
            
            $tobasketButton .= '
            <div class="wrapButtonCart">
                <div class="picCart">
                    <img src="template/images/cart_px.png" />
                </div> 
                <div class="button2border">
                    <button class="buttonsubmit_tobasket" data-itemid="'. $row[ 'id' ].'"  data-userid="'. $webuserinfo[ 'id' ].'">
                        В корзину
                    </button>
                </div>
            </div>';
		}

	}
	
		$result .= '<td class="jqtri_col jqtric_stock">'. $row[ 'in_stock' ] .' шт.</td>';
	
		//personalDiscount
		$sqlPDiscount = "SELECT p_discount FROM  ".$modx->getFullTableName( '_personal_discount' )." WHERE id_user =  '".$webuserinfo[ 'id' ]."'  LIMIT 1";
		$resultPDiscount = mysql_query($sqlPDiscount) or die ('Error Select coins history');
		if  ($row_pd = mysql_fetch_assoc($resultPDiscount)['p_discount']){
			$pagerow_pd = $row_pd;
		} else {
			$pagerow_pd = 0;
		}	
		
		if ($pagerow_pd > $row[ 'discount' ]){
			$row[ 'discount' ] = $pagerow_pd;
		}
		//
	
		if ($row[ 'discount' ] > 0 ){
			$result .= '<td class="jqtri_col jqtric_price">
			<div class="discountpercent">- '.$row['discount'].' %</div>
			<div class="oldprice"><nobr><span class="throughprice">'. $modx->runSnippet( 'Price', array( 'price' => $row[ 'price' ], 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></div>
			<div class="newprice"><nobr><span class="newpricetext">'. $modx->runSnippet( 'Price', array( 'price' => round(($row['price'] - ($row['price'] / 100 * $row['discount']))), 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></div>
			</td>';
		}else  {
			if ($acceptReqNewPrice){
				$result .= '
							<td class="jqtri_col jqtric_price">
							<div class="oldprice"><nobr><span class="throughprice">'. $modx->runSnippet( 'Price', array( 'price' => $row[ 'price' ], 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></div>
							<div class="newprice"><nobr><span class="newpricetext">'. $modx->runSnippet( 'Price', array( 'price' => $reqPriseAccepted , 'round' => 0 ) ) .'</span> <span class="rubl">a</span> x '.$reqCountAccepted.' шт.</nobr></div>
							</td>';
			}else {
				$result .= '<td class="jqtri_col jqtric_price"><nobr><span class="price">'. $modx->runSnippet( 'Price', array( 'price' => $row[ 'price' ], 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></td>';
			}
			
		}
	
		
 
		
		$result .= '
		<td class="jqtri_col jqtric_basket">
			<div class="cati_tobasket add_to_basket" id="ci_tobasket_'. $row[ 'id' ] .'" data-itemid="'. $row[ 'id' ] .'">
				<button class="button_minus" '.(($itemStatus == 'finded' || $row['in_stock'] < 1 ) ? "disabled" : "" ).'>&#9668;</button>
				<input type="text" value="'.($row['in_stock'] > 0 ? "1":"0" ).'"  '.(($itemStatus == 'finded' || $row['in_stock'] < 1 ) ? "disabled" : "" ).'  id="ci_tobasket_count_'. $row[ 'id' ] .'"  data-max="'. $row[ 'in_stock' ] .'"  class="tobasket_item_count" pattern="[0-9]{4}" />
				<button class="button_plus"  '.(($itemStatus == 'finded' || $row['in_stock'] < 1 )  ? "disabled" : "" ).' data-max="'. $row[ 'in_stock' ] .'">&#9658;</button>
				<div class="clr"></div>'.$tobasketButton.'
			</div>
		</td>';
		$result .= '<td class="jqtri_col jqtric_city"><div class="newBlWrap">'.$modx->runSnippet( '_CAT_sellersBlock', array('sellerId' => $row['seller'] , 'itemId' => $row['id'] , 'tender' =>$row['tender'] , 'row' =>  $row ) ).'</div></td>';
	$result .= '</tr>';
}








if( $type == 'pageitem' )
{
	
	$modx->runSnippet( 'seeCounterItem', array( 'idItem'=>$row[ 'id' ] ) );
	
	$superPuperMegaPageID = $row[ 'id' ] ;
    $superPuperMegaSellerID = $row[ 'seller' ] ;
    
	if( $row[ 'currency' ] != 'rub' ) $row[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$row[ 'price' ], 'c'=>$row[ 'currency' ] ) );
	
	$row[ 'text' ]= $modx->runSnippet( 'str_replace', array( 'from'=>"\n", 'to'=>'<br />', 'txt'=>$row[ 'text' ] ) );

	//=========px2x========
	//достаем картинки -----START
	$sqlimg = "SELECT ci.link
		FROM  ".$modx->getFullTableName( '_catalog_images' )." AS ci
		WHERE ci.id_item =  '".$row['id']."' ";
	$resultimg = mysql_query($sqlimg) or die ('Error Select sqlimg: '. mysql_error());
	$croppedImgs = '';
	$mainImg = '';
	while ($rowimg = mysql_fetch_assoc($resultimg)){
		if ($mainImg == '') {
			$mainImg = '<div class="previevBimg" style="background-image:url('. $modx->runSnippet( 'ImgCrop6', array( 'img' => $rowimg[ 'link' ], 'w' => 440, 'h' => 400, 'fill' => 0 , 'wm' =>1 ) ) .');" /> </div>';
		}
		$croppedImgs .= '<div class="previevimg" style="background-image:url('. $modx->runSnippet( 'ImgCrop6', array( 'img' => $rowimg[ 'link' ], 'w' => 770, 'h' => 700, 'fill' => 0 , 'wm' =>1 ) ) .');" /> </div>';
	}
	if ($mainImg == '') {
		$mainImg = '<div class="previevBimg" style="background-image:url('. $modx->runSnippet( 'ImgCrop6', array( 'img' => 'template/images/notphoto.png', 'w' => 340, 'h' => 300, 'fill' => 0 , 'wm' =>1 ) ) .');" /> </div>';

	}

	//достаем картинки -----END
	
	

	//достаем документы -----START
	$sqlimg = "SELECT cd.link , cd.originalname
		FROM  ".$modx->getFullTableName( '_catalog_docs' )." AS cd
		WHERE cd.id_item =  '".$row['id']."' ";
	$resultimg = mysql_query($sqlimg) or die ('Error Select sqldoc: '. mysql_error());
	$croppedDocs = '';
	while ($rowimg = mysql_fetch_assoc($resultimg)){
		//$ext = explode('.' , $rowimg[ 'link' ])[1];
		$ext =  end(explode(".", $rowimg[ 'link' ]));
		$croppedDocs .= '<a class="docLinkDownload" href="'.$rowimg[ 'link' ].'"><div class="documentPrintOnItemP" style="background-image:url(template/images/file_'.$ext.'.png);"><span>'.$rowimg[ 'originalname' ].'</span></div></a>';
	}
	//достаем документы -----END
	
	
	
	
	$thisOldPrice = '';
	
	//personalDiscount
	$sqlPDiscount = "SELECT p_discount FROM  ".$modx->getFullTableName( '_personal_discount' )." WHERE id_user =  '".$webuserinfo[ 'id' ]."'  LIMIT 1";
	$resultPDiscount = mysql_query($sqlPDiscount) or die ('Error Select coins history');
	if  ($row_pd = mysql_fetch_assoc($resultPDiscount)['p_discount']){
		$pagerow_pd = $row_pd;
	} else {
		$pagerow_pd = 0;
	}	
	
	if ($pagerow_pd > $row[ 'discount' ]){
		$row[ 'discount' ] = $pagerow_pd;
	}
		//
	
	
	if ($row['discount'] > 0 ) {
		$discount = '<div class="sale">-'.$row['discount'].'%</div>';
		$thisOldPrice = 'thisOldPrice';
		$tmp = round(($row['price'] - ($row['price'] / 100 * $row['discount'])));
		$newPrice = '<div class="param">Новая цена:</div><span class="newPrice"><nobr><span class="price">'. $modx->runSnippet( 'Price', array( 'price' => $tmp, 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></span><div class="clr">&nbsp;</div>';
	}
	
	if ($row['state'] == 'new' ) {
		$state =  'Новый';
	}elseif ($row['state'] == 'bu'){
		$state = 'Б/У';
	}else {
		$state = 'На запчасти';
	}
	
	
	if ($row['shipment'] == 'postal' ) {
		$shipment =  'Почта';
	}else {
		$shipment = 'Курьер';
	}
	
	if ($row['tender'] == '1' ) {
		$tender =  'Да';
	}else {
		$tender = 'Нет';
	}
	
	
	//=========px2x========
	$result .= '<div class="catpgitm_pagetitle"><h2>'. $row[ 'manufacturer' ] .' '. $row[ 'title' ] .'</h2></div>
	<div class="clr">&nbsp;</div>';
	
	$result .= '<div class="catpgitm_imgs"><div class="mainimg">'.$discount.$mainImg.'</div><div class="subimg">'.$croppedImgs.'</div></div>';
	
	$result .= '<div class="catpgitm_params">
		<div class="param">Производитель:</div><span>'. $row[ 'manufacturer' ] . ($row[ 'manufacturer_country' ] != '' ?  ' ('.$row[ 'manufacturer_country' ].') ' : '') .'</span><div class="clr">&nbsp;</div>
		<div class="param">Артикул:</div><span>'. $row[ 'code' ] .'</span><div class="clr">&nbsp;</div>
		<div class="param">Гарантия:</div><span>'. $row[ 'guarantee' ] .' мес.</span><div class="clr">&nbsp;</div>
		
		<div class="param">Доставка:</div><span>'. $shipment .'</span><div class="clr">&nbsp;</div>
		<div class="param">Возможность торгов:</div><span>'. $tender .'</span><div class="clr">&nbsp;</div>

		
		<div class="param">Состояние:</div><span>'. $state .'</span><div class="clr">&nbsp;</div>
		<div class="param">Упаковка:</div><span>'.( $row[ 'packaging' ] == 'y' ? 'Есть' : 'Нет' ).'</span><div class="clr">&nbsp;</div>
		<div class="param">В наличии:</div><span>'. $row[ 'in_stock' ] .'</span><div class="clr">&nbsp;</div>
		<div class="param">Цена:</div><span class="'.$thisOldPrice.'"><nobr><span class="price">'. $modx->runSnippet( 'Price', array( 'price' => $row[ 'price' ], 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></span><div class="clr">&nbsp;</div>
		'.$newPrice.'<div class="clr">&nbsp;</div>';
		
	
    if ($row['enabled'] == 'y'){
        
	
		$itemStatus =  $modx->runSnippet( '_SHOP_checkBasket', array( 'type' => 'checkItem', 'userId' => $webuserinfo[ 'id' ] , 'itemId' =>$row[ 'id' ]  ));
		if ($itemStatus == 'finded') {
			$tobasketButton = '<a class="buttonsubmit_tobasket buttonsubmit_inbasket" href="'. $modx->makeUrl( $pageId_basket ).'">В корзине</a>';
		}else {
			$tobasketButton = '<button class="buttonsubmit_tobasket" data-itemid="'. $row[ 'id' ].'"  data-userid="'. $webuserinfo[ 'id' ].'">В корзину</button>';
		}
	$result .='	
			<div class="cati_tobasket add_to_basket itemPage" id="ci_tobasket_'. $row[ 'id' ] .'" data-itemid="'. $row[ 'id' ] .'">
				<button class="button_minus" '.($itemStatus == 'finded' ? "disabled" : "" ).'>-</button>
				<input type="text" value="1"  '.($itemStatus == 'finded' ? "disabled" : "" ).'  id="ci_tobasket_count_'. $row[ 'id' ] .'"  data-max="'. $row[ 'in_stock' ] .'"  class="tobasket_item_count" pattern="[0-9]{4}" />
				<button class="button_plus"  '.($itemStatus == 'finded' ? "disabled" : "" ).' data-max="'. $row[ 'in_stock' ] .'">+</button>
				'.$tobasketButton.'
			</div>';
    
    }else {
        
        $result .='	
			<div class="notEnabledItem">
                Товар временно не доступен для заказа.
			</div>';
    }
	
	
	
	$result .='	
	</div>';
	
	$result .= '<div class="catpgitm_descr">
		<div class="catpgitm_tit">Описание и характеристики</div>
		<div class="catpgitm_txt">'. $row[ 'text' ] .'</div>
		<div class="catpgitm_tit">Файлы для загрузки</div>
		'.$croppedDocs.'
	</div>';
	$result .= '<div class="clr">&nbsp;</div>';
	
	//====аналоги + отзывы START === 
	$print_items ='';
	$items= mysql_query( "SELECT cata.id_analog, catb.seller, catb.id, catb.parent, catb.title, catb.code, catb.manufacturer, catb.manufacturer_country, catb.price, catb.currency, catb.in_stock, catb.text
							FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
							INNER JOIN  ".$modx->getFullTableName( '_catalog_analogs' )." AS cata ON cat.id = cata.id_item
							INNER JOIN  ".$modx->getFullTableName( '_catalog' )." AS catb ON catb.id = cata.id_analog
							WHERE catb.enabled =  'y' AND cata.id_item = '".$row['id']."'
							ORDER BY catb.id
							LIMIT 0 , 30" );
    
    
    $sqlAVG = "SELECT  AVG(resp.`rank`) AS avgRank , resp.id_user , COUNT(resp.id) AS sumRespons 
            FROM ".$modx->getFullTableName( '_responses' )."  AS resp
            INNER JOIN ".$modx->getFullTableName( '_catalog' )."  AS cat ON resp.id_cat_item = cat.id
            WHERE cat.seller = ".$superPuperMegaSellerID."
            AND resp.visible = '1'
            AND cat.id  = '".$row['id']."' ";
    
        $resultAVG = mysql_query($sqlAVG) or die (mysql_error());
        if ($resultAVG && mysql_num_rows($resultAVG) > 0) {
            if ($avgUser = mysql_fetch_assoc($resultAVG)) {
            
            }
        }
    
    
    $sqlAVGI = "SELECT  AVG(resp.`rank`) AS avgRankI 
            FROM ".$modx->getFullTableName( '_responses' )."  AS resp
            INNER JOIN ".$modx->getFullTableName( '_catalog' )."  AS cat ON resp.id_cat_item = cat.id
            WHERE cat.id = ".$superPuperMegaPageID."
            AND visible = '1'";
    
        $resultAVGI = mysql_query($sqlAVGI) or die (mysql_error());
        if ($resultAVGI && mysql_num_rows($resultAVGI) > 0) {
            if ($avgitem = mysql_fetch_assoc($resultAVGI)) {
            
            }
        }
    
    
    
    $print_items_head  .= '<div class="subItemWrapper">';
    $print_items_head .= '
        <div class="analogHeads">
            <div class="catItrmLine_respons '.($avgUser['sumRespons'] > 0 ? "active" : "").' ">Отзывы: '.$avgUser['sumRespons'].'</div>
            <div class="catItrmLine_analogs '.($avgUser['sumRespons'] == 0 ? "active" : "").'">Аналоги</div>
            
        </div>';
    
    
	if (mysql_num_rows($items)>0){
		
        
		$print_items .= '<div class="catItrmBlock_analogs" style="display: '.($avgUser['sumRespons'] > 0 ? "none" : "block").';">';
        
		$print_items .= '<table class="catalog_table" cellpadding="0" cellspacing="0">';
		$print_items .= '<tr class="jqt_row_tit">';
		$print_items .= '<td class="jqtrt_col jqtrtc_code">Код</td>';
		$print_items .= '<td class="jqtrt_col jqtrtc_vend">Производитель</td>';
		$print_items .= '<td class="jqtrt_col jqtrtc_name">Описание</td>';
		$print_items .= '<td class="jqtrt_col jqtrtc_stock">В наличии</td>';
		$print_items .= '<td class="jqtrt_col jqtrtc_price">Цена, руб.</td>';
		$print_items .= '<td class="jqtrt_col jqtrtc_basket">Заказ</td>';
		$print_items .= '<td class="jqtrt_col jqtrtc_city">Продавец</td>';
		$print_items .= '</tr>';

		$ii= 0;
		while( $row= mysql_fetch_assoc( $items ) )
		{
			$ii++;
			$print_items .= $modx->runSnippet( 'CAT_ITEM', array( 'type' => 'item', 'row' => $row, 'last' => ( $ii % 2 == 0 ? true : false ) ) );
            
            //$print_items .= $row['title'];
		} 
		$print_items .= '</table><div class="clr">&nbsp;</div>';
        $print_items .= '</div><div class="clr">&nbsp;</div>';
	}
    
    

    

    
        if ($resp_row = $modx->runSnippet( 'responseToCatItem', array( 'id' => $superPuperMegaPageID)) ){
            $print_items .= '<div class="catItrmBlock_respons" style="display: '.($avgUser['sumRespons'] > 0 ? "block" : "none").';">';
          
            $print_items .= '<div class="avg_respons">Средняя оценка продавца: '.(round($avgUser['avgRank'],1)).'</div>';
            
            $print_items .= '<div class="avg_respons">Средняя оценка этого товара: '.(round($avgitem['avgRankI'],1)).'</div>';
            
                foreach ($resp_row AS $row) {
                     $print_items.= '
                        <div class="oneResponse">
                            <div class="resp_for_seller">От: <span class="bigestText">'.$row['buyerName'].' '.$row['buyerSurName'].'</span></div>
                            <div class="resp_rank">'.$row['rank'].'</div>
                            <div class="resp_text">"'.$row['response'].'"</div>
                            <div class="crl"></div>
                            <div class="date small12px">Дата отзыва '.date("d.m.Y",$row['timest']).'</div>
                        </div>
                        <div class="clr">&nbsp;</div>
                    ';

                }
            
            $print_items .= '</div><div class="clr">&nbsp;</div>';
            
            
        } else {
            $print_items .= '<div class="avg_respons">Средняя оценка продавца: '.(round($avgUser['avgRank'],1)).'</div>';
            
        }
    
    $print_items .= '</div><div class="clr">&nbsp;</div>';
    
	$result .=$print_items_head.$print_items;
	//====аналоги END === 
	
}



return $result;





?>