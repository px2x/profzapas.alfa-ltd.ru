<?php

if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	header( 'location: '. $modx->makeUrl( $auth ) );
	exit();
}	

$topage_url= $modx->makeUrl( $modx->documentIdentifier );
	
$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];



if (isset($_POST['DeleteCheckedItemEnabled']) && is_array($_POST['deleteID'])){
	foreach ($_POST['deleteID'] AS $key => $value) {
		$result = mysql_query("DELETE FROM  ".$modx->getFullTableName( '_favorites' )." WHERE id_user = ".$webuserinfo['id']." AND id_item={$key}");
	}
			
		
}




$result = mysql_query("UPDATE  ".$modx->getFullTableName( '_favorites' )."
    SET new_notice = 0 WHERE id_user = ".$webuserinfo['id']);







$sql = "SELECT cat.id, cat.code, cat.parent, cat.title, cat.manufacturer, cat.price, cat.currency, cat.in_stock, cat.text, cat.state, cat.packaging, cat.guarantee, cat.discount, cat.shipment, cat.tender , fav.frezzePrice  , fav.frezzeCurrency  , fav.frezzeDiscount , fav.new_notice
	FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
	INNER JOIN  ".$modx->getFullTableName( '_favorites' )." AS fav ON fav.id_item = cat.id
	WHERE fav.id_user =  '".$webuserinfo['id']."'
	AND (cat.enabled =  'y' OR cat.enabled =  'n')
	GROUP BY cat.code";
$result = mysql_query($sql) or die ('Error Select noEnable items: '. mysql_error());



$noEnableItems .= '<form action="'.$topage_url.'?tab=1" name="EnabledItems" method="POST">';
$noEnableItems .= '<div class=lkItemtitle>
	<div class="checkboxsENA"><input type="checkbox" name="checkallENA" /></div>
	<div class="code" style="width:110px;">Код</div>
	<div class="vend" style="width:160px;">Производитель</div>
	<div class="name">Наименование</div>
	<div class="stock">Количество</div>
	<div class="price" style="width:140px;">Цена</div>
    <div class="price" style="text-align:center;">Изменения</div>
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
	
	

    if( $row[ 'currency' ] != 'rub' ) $row[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$row[ 'price' ], 'c'=>$row[ 'currency' ] ) );
    
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
    
    
	$printPrice = '<nobr><span class="price">'.$modx->runSnippet( 'Price', array( 'price' => round($row['price']), 'round' => 0 ) ).'</span> <span class="rubl">a</span></nobr>';
	//$noEnableItems.=$row['discount'];
	if ($row['discount'] > 0) {
		//$noEnableItems.=$row['discount'];
		$printPrice =  '<div class="discountprice">
							<div class="sale">-'.$row['discount'].'%</div>
                            
							<div class=oldprice>'.$modx->runSnippet( 'Price', array( 'price' => round($row['price']), 'round' => 0 ) ).' <span class="rubl">a</span></div>
                            
							<div class=newprice>'.$modx->runSnippet( 'Price', array( 'price' => round(($row['price'] - ($row['price'] / 100 * $row['discount']))), 'round' => 0 ) ).' <span class="rubl">a</span></div>
						</div>';
	}
	
    
    

     
    
    
    
    $rechPriceCurv = '';
    if ($row['new_notice'] == 1 || true) {
        
        $curvTextPL = '+';
        
        

        
        if( $row[ 'frezzeCurrency' ] != 'rub' ) $row[ 'frezzePrice' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$row[ 'frezzePrice' ], 'c'=>$row[ 'frezzeCurrency' ] ) );
        
        
        
        
        $frezzePriceWithDiscount =ceil(($row['frezzePrice'] - ($row['frezzePrice'] / 100 * $row['frezzeDiscount'])));
        
        $curv = ceil(($row['price'] - ($row['price'] / 100 * $row['discount'])));
        
        $curv = $curv - $frezzePriceWithDiscount;
            
        $rechPriceCurv .=($curv > 0 ?  $curvTextPL : "" ).$curv.'<span class="rubl">a</span>' ;
        $rechPriceCurv .=($curv > 0 ? '<span class="redColor">&#9650;</span>' : ($curv < 0 ? '<span class="greenColor">&#9650;</span>' : '<span class="greenColor">&#9660;</span><span class="redColor">&#9650;</span>' ) ) ;
        
    }
    
	
		$addNewCatText='';
	if ($flagBgColor > 0) {$addStyle = 'fillGrayBG';} else {$addStyle = '';}
	$noEnableItems .= '<div class="lkItem '.$addStyle.'">';
	$noEnableItems .= '<div class="checkboxsENA"><input type="checkbox" name="deleteID['.$row['id'].']" value="'.$row['id'].'" /></div>';
	$noEnableItems .= '<div class="code" style="width:110px;"><a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'id' ] .'/">'.$row['code'].'</a></div>';
	
	//$result .= '<td class="jqtri_col jqtric_code"><a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'id' ] .'/">'. $row[ 'code' ] .'</a></td>';
	
	
	$noEnableItems .= '<div class="vend" style="width:160px;">'.$row['manufacturer'].'</div>';
	$noEnableItems .= '<div class="name"><div class="type">'.$row['title'].'</div></div>';
	$noEnableItems .= '<div class="stock">'.$row['in_stock'].' шт.</div>';
	$noEnableItems .= '<div class="price" style="width:140px;">'.$printPrice.'</div>';	
	$noEnableItems .= '<div class="price" style="text-align:center;">'.$rechPriceCurv.'</div>';	
    
	//$noEnableItems .= '<div class="category"></div>';
	
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
									<div class="px_top_left">'.$row['text'].'</div>
								
						   		</div>
								<div class="px_middle">
									'.$warehouseList.'
										</div>
								<div class="px_bottom">
									'.$croppedImgs.'
								</div>
						   </div>
					   </div>';
	
	$noEnableItems .= '</div>';
	$flagBgColor = $flagBgColor * (-1);
}


$noEnableItems .= '<button class="buttonsubmit2 topMargin" name="DeleteCheckedItemEnabled" type="submit">Удалить отмеченные</button>';
$noEnableItems .= '</form>';



?>
	

				<?=$noEnableItems?>





?>