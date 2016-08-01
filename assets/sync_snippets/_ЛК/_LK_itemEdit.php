<?php

$topage_url= $modx->makeUrl( $modx->documentIdentifier );
$notFoundId = 128;
$auth = 108;
$sellerPageId = 106;
$catalogId=8;
$catalogNewCatId=128; //сюда попадают товары с новой категорией
$vkladka_active= 1;


$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];
$sellerDescrInfo= $_SESSION[ 'webuserinfo' ][ 'seller_descr' ];
$sellerWarehouses= $_SESSION[ 'webuserinfo' ][ 'seller_warehouses' ];
$userContactFaces= $_SESSION[ 'webuserinfo' ][ 'contact_faces' ];



if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	header( 'location: '. $modx->makeUrl( $auth ) );
	exit();
}


if(  $webuserinfo['seller'] !='y')
{
	header( 'location: '. $modx->makeUrl( $sellerPageId ) );
	exit();
}



///Обновляем

/*=================================Обновление товара SATRT==============================================*/
if (isset($_POST['updateItem'])){
	if (is_numeric($_GET['idcat'])){
		$cat_id=addslashes($_GET['idcat']);
	
	foreach ($_POST as $key => $value){
		$filteredPost[$key] = addslashes($value);
	}
	
	
	$sql = "SELECT cat.id
					FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
					WHERE cat.seller =  '".$webuserinfo['id']."'
					AND cat.id =  '".$cat_id."'
					LIMIT 1";
	$result = mysql_query($sql) or die ('Error Select INS or UPD');
	if (!mysql_fetch_row($result)[0]) {
		//err
	}else {
		//echo 'EERRER';
		if ($filteredPost['save_guarant'] == 'yes'){
			$save_guarant = $filteredPost['save_guarant_text'];
		}else {
			$save_guarant = 0;
		}
		
		if (!isset($filteredPost['save_item_catTwo'])) {
			$filteredPost['save_item_catTwo'] = $notFoundId;
		}
		
	
		if ($filteredPost['save_item_pack'] == 'on'){
			$save_item_pack = 'y';
		}else {
			$save_item_pack = 'n';
		}
		
		if ($filteredPost['save_item_discount'] == 'on' && is_numeric($filteredPost['save_item_discount_text'])){
			$save_item_discount_text = $filteredPost['save_item_discount_text'];
		}else {
			$save_item_discount_text = 0;
		}

				
		if ($filteredPost['save_item_tender'] == 'on'){
			$save_item_tender = 1;
		}else {
			$save_item_tender = 0;
		}
		
		
		$rese =  mysql_query("SELECT id FROM ".$modx->getFullTableName( '_catalog_warehouse' )." WHERE `item` = '".$cat_id."' AND `warehouse` = '".$currentWhId."' LIMIT 1");
			if ($rese) {	
				if (mysql_num_rows($rese)> 0) {
					$instock_warehouses = $filteredPost['instock_warehouse1']+$filteredPost['instock_warehouse2']+$filteredPost['instock_warehouse3'];
				}else {
					$instock_warehouses = $filteredPost['save_item_count'];
				}
			}
							
							
		$sql="UPDATE ".$modx->getFullTableName( '_catalog' )." AS cat SET 
	
			`parent` ='".$filteredPost['save_item_catTwo']."',  
	
			`title` ='".$filteredPost['save_item_name']."',  
			`code`= '".$filteredPost['save_item_code']."',  
			`manufacturer` ='".$filteredPost['save_item_manuf']."',
			`manufacturer_country` ='".$filteredPost['save_item_manuf_с']."',
			`price` ='".$filteredPost['save_item_price']."',  
			`currency` ='".$filteredPost['save_item_valute']."', 
			`in_stock` ='".$instock_warehouses."',  
			`guarantee` ='".$save_guarant."',  
			`state` ='".$filteredPost['save_sost']."',   
			`packaging` ='".$save_item_pack."',  
			`discount` ='".$save_item_discount_text."',   
			`text` ='".$filteredPost['save_item_descr']."', 
			`enabled`= 'n',
			`documentation` ='file.pdf',  
			`image`='image.jpg',
			`tender`='".$save_item_tender."',
			`shipment`='".$filteredPost['save_dost']."',
			`sitelink`= '".$filteredPost['save_item_manuf_site']."'
			 WHERE `id` = ".$cat_id." LIMIT 1";
		mysql_query($sql) or die ('Error Insert '.mysql_error());
		
			
			for ($y = 1; $y<4; $y++){
				$sql = "SELECT uwh.id
					FROM  ".$modx->getFullTableName( '_user_warehouse' )." AS uwh
					WHERE uwh.num =  '".$y."'
					AND uwh.seller =  '".$webuserinfo['id']."'
					LIMIT 1";	
				$result = mysql_query($sql) or die ('Error Select');
				if ($currentWhId = mysql_fetch_row($result)[0]){
					
					
					
					$rese =  mysql_query("SELECT id FROM ".$modx->getFullTableName( '_catalog_warehouse' )." WHERE `item` = '".$cat_id."' AND `warehouse` = '".$currentWhId."' LIMIT 1");
					if ($rese) {
					
						if (mysql_num_rows($rese)> 0) {
							if ($ididid = mysql_fetch_row($rese)[0]){
								$sql="UPDATE ".$modx->getFullTableName( '_catalog_warehouse' )."  SET
									`quantity` = '".$filteredPost['instock_warehouse'.$y]."'
									WHERE `item` = '".$cat_id."' AND `warehouse` = '".$currentWhId."' LIMIT 1";
								mysql_query($sql) or die ('Error Insert Warehouse on One Add');
							}
						
						
						}else {
							$sql="INSERT INTO ".$modx->getFullTableName( '_catalog_warehouse' )."  (`quantity` , `item` , `warehouse`) VALUES ('".$filteredPost['instock_warehouse'.$y]."' , '".$cat_id."' , '".$currentWhId."')";
							mysql_query($sql) or die ('Error Insert Warehouse on One Add');
						}
						
					}
					
	
					
				}
			}
			
					
			//предложил создать новую категрию
			if ($filteredPost['save_catType'] == 'new'){
				$sql = "INSERT INTO  ".$modx->getFullTableName( '_request_new_path' )." (
						`id`,
						`id_item`,
						`category`,
						`subcategory`
						) VALUES (
						NULL,
						'".$cat_id."', 
						'".$filteredPost['save_item_catOne_alter']."',
						'".$filteredPost['save_item_catTwo_alter']."'
						) ON DUPLICATE KEY UPDATE `category` = '".$filteredPost['save_item_catOne_alter']."' , `subcategory` = '".$filteredPost['save_item_catTwo_alter']."'";
					$result = mysql_query($sql) or die ('Error  ADD new Cat REQ: '.mysql_error());
				
				
				$sql = "UPDATE ".$modx->getFullTableName( '_catalog' )." SET parent = '128'  WHERE `id` = '".$cat_id."' AND seller = '".$webuserinfo['id']."' LIMIT 1";
				$result = mysql_query($sql) or die ('Error UPD CAT Cat REQ: '.mysql_error());
				
				$catListRequired = '';
				$catAlterRequired = 'required';
			}else {
				$catListRequired = 'required';
				$catAlterRequired = '';
				if ($filteredPost['save_item_catOne_alter'] == '' || $filteredPost['save_item_catTwo_alter'] == '') {
				
					$sql = "DELETE FROM  ".$modx->getFullTableName( '_request_new_path' )." WHERE `id_item` = '{$cat_id}' LIMIT 1";
					$result = mysql_query($sql) or die ('Error  DEL  new Cat REQ: '.mysql_error());
				//	$sql = "UPDATE ".$modx->getFullTableName( '_request_new_path' )." SET parent = '".$filteredPost['save_item_catTwo_alter']."'  WHERE `id_item` = '{$cat_id}' AND seller = '".$webuserinfo['id']."' LIMIT 1";
				//	$result = mysql_query($sql) or die ('Error  DEL  new Cat REQ: '.mysql_error());
				}
			}
			
				

			$fih = 0;
			while (file_exists($filteredPost['fileImgHandler'.$fih])){
				$sql = "INSERT INTO  ".$modx->getFullTableName( '_catalog_images' )." (
						`id`,
						`id_item`,
						`link`
						) VALUES (
						NULL,
						'".$cat_id."', 
						'".$filteredPost['fileImgHandler'.$fih]."'
						) ON DUPLICATE KEY UPDATE `link` = '".$filteredPost['fileImgHandler'.$fih]."' ";
				$result = mysql_query($sql) or die ('Error  ADD new Cat IMG: '.mysql_error());
				$fih++;
			}
			
		
			
			$fdh = 0;
			while (file_exists($filteredPost['fileDocHandler'.$fdh])){
				$sql = "INSERT INTO  ".$modx->getFullTableName( '_catalog_docs' )." (
						`id`,
						`id_item`,
						`link`,
						`originalname`
						) VALUES (
						NULL,
						'".$cat_id."', 
						'".$filteredPost['fileDocHandler'.$fdh]."',
						'".$filteredPost['fileDocHandlername'.$fdh]."'
						) ON DUPLICATE KEY UPDATE `link` = '".$filteredPost['fileDocHandler'.$fdh]."' ";
				$result = mysql_query($sql) or die ('Error  ADD new Cat DOC: '.mysql_error());
				$fdh++;
			}		
		}
	}
}

/*=================================Обновление товара END==============================================*/








//Достаем всю инфу для вывода в форму
if (is_numeric($_GET['idcat'])){
	$idEditCat  = addslashes($_GET['idcat']);
	
	
	$sql = "SELECT cat.id, cat.seller, cat.code, cat.title, cat.parent, cat.manufacturer, cat.manufacturer_country, cat.price, cat.currency, cat.in_stock, cat.text, cat.state, cat.packaging, cat.guarantee, cat.discount, cat.shipment, cat.tender, cat.sitelink,
			   sc.pagetitle AS firstpagetitle,  sc2.pagetitle AS secondpagetitle, rnp.category AS path1,  rnp.subcategory AS path2
	FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
	LEFT JOIN  ".$modx->getFullTableName( '_request_new_path' )." AS rnp ON rnp.id_item = cat.id
	INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc ON sc.id = cat.parent
	INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc2 ON sc.parent = sc2.id
	WHERE cat.seller =  '".$webuserinfo['id']."'
	AND cat.id =  '{$idEditCat}' 
	GROUP BY cat.code LIMIT 1";
	$result = mysql_query($sql) or die ('Error Select noEnable items: '. mysql_error());
	
	if   (mysql_num_rows($result) > 0){
		$row = mysql_fetch_assoc($result);
		
		$webuserinfo[ 'item_id' ] = $row['id'];
		$webuserinfo[ 'item_code' ] = $row['code'];
		$webuserinfo[ 'item_name' ] = $row['title'];
		$webuserinfo[ 'item_manuf' ] = $row['manufacturer'];
		$webuserinfo[ 'item_manuf_с' ] = $row['manufacturer_country'];
		$webuserinfo[ 'item_descr' ] = $row['text'];
		$webuserinfo[ 'item_price' ] = $row['price'];
		$webuserinfo[ 'item_manuf_site' ] = $row['sitelink'];
		$webuserinfo[ 'item_in_stock' ] = $row['in_stock'];
		
		$webuserinfo[ 'item_in_stock_state' ] = '';
			
		$webuserinfo[ 'item_pack' ] = '';
		if ($row['packaging'] == 'y') $webuserinfo[ 'item_pack' ] = 'checked=checked';

		
		$webuserinfo[ 'item_state1' ] = '';
		$webuserinfo[ 'item_state2' ] = '';
		$webuserinfo[ 'item_state3' ] = '';
		if ($row['state'] == 'new'){ $webuserinfo[ 'item_state1' ] = 'checked=checked';}
		elseif ($row['state'] == 'bu'){ $webuserinfo[ 'item_state2' ] = 'checked=checked';}
		else { $webuserinfo[ 'item_state3' ] = 'checked=checked';}
			

		$webuserinfo[ 'item_currency1' ] = '';
		$webuserinfo[ 'item_currency2' ] = '';
		$webuserinfo[ 'item_currency3' ] = '';
		if ($row['currency'] == 'rub'){ $webuserinfo[ 'item_currency1' ] = 'selected';}
		elseif ($row['currency'] == 'usd'){ $webuserinfo[ 'item_currency2' ] = 'selected';}
		else { $webuserinfo[ 'item_currency3' ] = 'selected';}
	
		
		$webuserinfo[ 'item_guarantee1' ] = '';
		$webuserinfo[ 'item_guarantee2' ] = '';
		$webuserinfo[ 'item_guaranteeT' ] = '';
		if ($row['guarantee'] > 0){ 
			$webuserinfo[ 'item_guarantee1' ] = 'checked=checked';
			$webuserinfo[ 'item_guaranteeT' ] = $row['guarantee'];
			$guaranteRequired = "required";
			$guaranteeDispNone ='';
		}else {
			$webuserinfo[ 'item_guarantee2' ] = 'checked=checked';
			$guaranteeDispNone ='style="display:none;"';
			$guaranteRequired = "";
		}
		
		
		$webuserinfo[ 'item_shipment1' ] = '';
		$webuserinfo[ 'item_shipment2' ] = '';
		if ($row['shipment']  == 'postal'){ 
			$webuserinfo[ 'item_shipment1' ] = 'checked=checked';
		}else {
			$webuserinfo[ 'item_shipment2' ] = 'checked=checked';
		}
		
		$webuserinfo[ 'item_tender' ] = '';
		if ($row['tender'] == 1) $webuserinfo[ 'item_tender' ] = 'checked=checked';
		
		
			
		$webuserinfo[ 'item_discount' ] = '';
		$webuserinfo[ 'item_discount_text' ] = '';
		$webuserinfo[ 'item_discount_st' ] = 'style="display:none;"';
		if ($row['discount'] > 0){
			$webuserinfo[ 'item_discount' ] = 'checked=checked';
			$webuserinfo[ 'item_discount_text' ] = $row['discount'];
			$webuserinfo[ 'item_discount_st' ] = 'style="display:;"';
		}

	}

	
		
	
} else {
	return "Произошла ошибка";
}
	
//Новая категория
$result= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_request_new_path' )." WHERE id_item = ". $webuserinfo[ 'item_id' ]);
$checkRequestNewPath1 = '';
$checkRequestNewPath2 = '';
if   (mysql_num_rows($result) > 0){
	$row = mysql_fetch_assoc($result);
	$userRequestMainCat = $row ['category'];
	$userRequestSubCat = $row ['subcategory'];
	$urnpDefaultList = 'style="display:none;"';
	$urnpAlter = 'style="display:;"';
	$checkRequestNewPath2 = 'checked=checked';
}else {
	$urnpDefaultList = 'style="display:;"';
	$urnpAlter = 'style="display:none;"';
	$checkRequestNewPath1 = 'checked=checked';
	
	$result2= mysql_query( "SELECT parent FROM ".$modx->getFullTableName( 'site_content' )." WHERE id = ". $row[ 'parent' ]);
	$itemParentOneCat = mysql_fetch_assoc($result2)['parent'];
	

	
	$result2= mysql_query( "SELECT id, pagetitle FROM ".$modx->getFullTableName( 'site_content' )." WHERE parent = ".$itemParentOneCat);
	while ($twoCatList = mysql_fetch_assoc($result2)){
		if ($twoCatList["id"] == $row[ 'parent' ]) {
			$attrSel = 'selected';
		}else {
			$attrSel = '';
		}
		$htmlcategoryTwolevel .='<option value="'.$twoCatList["id"].'" '.$attrSel.' >'.$twoCatList['pagetitle'].'</option>';
		
		$result3= mysql_query( "SELECT parent FROM ".$modx->getFullTableName( 'site_content' )." WHERE id = ". $twoCatList["id"]);
		$itemParentMainCat = mysql_fetch_assoc($result3)['parent'];

	}

}



	
	//достаем документы -----START
	$sqlimg = "SELECT cd.link , cd.originalname
		FROM  ".$modx->getFullTableName( '_catalog_docs' )." AS cd
		WHERE cd.id_item =  '".$webuserinfo[ 'item_id' ]."' ";
	$resultimg = mysql_query($sqlimg) or die ('Error Select sqldoc: '. mysql_error());
	$croppedDocs = '';
	while ($rowimg = mysql_fetch_assoc($resultimg)){
		//$ext = explode('.' , $rowimg[ 'link' ])[1];
		
		$ext =  end(explode(".", $rowimg[ 'link' ]));

		$croppedDocs .= '<input type="hidden" class="linkToUplDocs" name="linkToUplDocs" value="'.$rowimg[ 'link' ].'" />';
		$croppedDocs .= '<input type="hidden" class="originalnameToUplDocs"  name="originalnameToUplDocs" value="'.$rowimg[ 'originalname' ].'" />';
	}
	//достаем документы -----END



	
	//достаем картинки -----START
	$sqlimg = "SELECT cd.link
		FROM  ".$modx->getFullTableName( '_catalog_images' )." AS cd
		WHERE cd.id_item =  '".$webuserinfo[ 'item_id' ]."' ";
	$resultimg = mysql_query($sqlimg) or die ('Error Select sqldoc: '. mysql_error());
	$croppedImgs = '';
	while ($rowimg = mysql_fetch_assoc($resultimg)){
		//$ext = explode('.' , $rowimg[ 'link' ])[1];
		
		$ext =  end(explode(".", $rowimg[ 'link' ]));

		$croppedImgs .= '<input type="hidden" class="linkToUplImgs" name="linkToUplImgs" value="'.$rowimg[ 'link' ].'" />';
	
	}
	//достаем картинки -----END



$result= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user_warehouse' )." WHERE seller = ". $webuserinfo[ 'id' ]);
$htmlOptWH = [];
$tmp= [];
while ($row = mysql_fetch_assoc($result)){
	$result2= mysql_query( "SELECT quantity FROM ".$modx->getFullTableName( '_catalog_warehouse' )." WHERE warehouse = ". $row[ 'id' ]." AND item = ".$webuserinfo[ 'item_id' ] );
	while ($row2 = mysql_fetch_assoc($result2)){
		$webuserinfo[ 'item_in_stock_state' ] = 'readonly';
		$tmp[$row["num"]] = $row2;
	}
	$htmlOptWH[$row["num"]] ='
	<input  class="smallINSTCK miniinput" type="text" name="name_warehouse'.$row["num"].'" value="'.$row['city'].' ('.$row['address'].')  " id="instock_warehouse'.$row["num"].'" pattern="^[ 0-9]+$" placeholder="" readonly />
	<input  class="smallINSTCK miniinput" type="text" name="instock_warehouse'.$row["num"].'"  value="'.$tmp[$row["num"]]['quantity'].'" id="instock_warehouse'.$row["num"].'" pattern="^[ 0-9]+$" placeholder=""  />';
	
	if ($row['address'] == '') {
		$styleHiddenWH[$row["num"]] ='style="display:none;"';
	}else {
		$styleHiddenWH[$row["num"]] ='style="display:;"';
	}
	
}




$result= mysql_query( "SELECT id, pagetitle FROM ".$modx->getFullTableName( 'site_content' )." WHERE parent=". $catalogId ." AND id <> ".$catalogNewCatId."  ORDER BY menuindex ASC" ) or die(mysql_error());
$htmlcategoryOnelevel .='<option value="-1">Категория не выбрана</option>';
while ($row = mysql_fetch_assoc($result)){

	if ($itemParentMainCat == $row[ 'id' ]) {
		$attrSel = 'selected';
	}else {
		$attrSel = '';
	}
	$htmlcategoryOnelevel .='<option value="'.$row["id"].'" '.$attrSel.' >'.$row['pagetitle'].'</option>';
}


?>






<div class="_LK_wrapper _LK_wrapper_big">
			<div class="_LK_form">
			<?=$parseXlsError?>
			<form  action="<?=$topage_url?>?idcat=<?=$idEditCat?>" method='POST' id="addMewItemForm">
				
				
			<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Код товара</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_code' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="text" class="miniinput" name="save_item_code" value="<?= $webuserinfo[ 'item_code' ] ?>" required autofocus/>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Наименование</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_name' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="text" class="miniinput" name="save_item_name" value="<?= $webuserinfo[ 'item_name' ] ?>" required/>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Производитель</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_manuf' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="text" class="miniinput" name="save_item_manuf" value="<?= $webuserinfo[ 'item_manuf' ] ?>" required/>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Страна</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_manuf_с' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="text" class="miniinput" name="save_item_manuf_с" value="<?= $webuserinfo[ 'item_manuf_с' ] ?>" />
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Описание</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_descr' ] ? '_LK_form_inp_error' : '' ) ?>">
							<textarea class="miniinput" name="save_item_descr"><?= $webuserinfo[ 'item_descr' ] ?></textarea>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				
					<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Стоимость</div>
						<div class="_LK_form_inp small<?= ( $save_err_flag[ 'save_item_price' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="text" class="miniinput" name="save_item_price" value="<?= $webuserinfo[ 'item_price' ] ?>" />
							<select name="save_item_valute" class="select_valute miniinput">
								 <option <?=$webuserinfo[ 'item_currency1' ]?> >RUB</option>
								 <option <?=$webuserinfo[ 'item_currency2' ]?> >USD</option>
								 <option <?=$webuserinfo[ 'item_currency3' ]?> >EUR</option>
							</select>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Ссылка на сайт производителя</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_manuf_site' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="text" class="miniinput" name="save_item_manuf_site" value="<?= $webuserinfo[ 'item_manuf_site' ] ?>"/>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Документация</div>
						<div class="_LK_form_inp  <?= ( $save_err_flag[ 'save_item_docfile' ] ? '_LK_form_inp_error' : '' ) ?>">
							<div class="input_file noneborder">
								<input type="file" name="save_item_docfile" id="uploaded_fileEdit" multiple="multiple" accept=".doc,.docx,.xls,.pdf.txt,image/*" onchange="file_selected();"/>
								<!--progress id="progressbar" value="0" max="100"></progress-->
								<div class="progress-bar orange shine">
									<span id="docprogress" style="width: 0%"></span>
								</div>
								<div class="notice_px">* Вы можете загрузить файлы инструкций,<br/> опиисаний в форматах: <br/>
								".docx", ".xlsx", ".doc", ".xls" , ".pdf", ".jpg", ".png", ".gif"</div>
								<div class="ajax-respond"></div>
							
							</div>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Наличие упаковки</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_pack' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input class="checkbox" type="checkbox" name="save_item_pack" <?= $webuserinfo[ 'item_pack' ] ?> />
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Состояние</div>
						<div class="_LK_form_inp dashedGroup <?= ( $save_err_flag[ 'save_item_count' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="radio" class="radio" value="new" <?= $webuserinfo[ 'item_state1' ] ?> name="save_sost" id="sost1"/><label class="labforradio"  for="sost1">Новое</label> <div class="clr">&nbsp;</div>
							<input type="radio" class="radio" value="bu"  <?= $webuserinfo[ 'item_state2' ] ?> name="save_sost" id="sost2"/><label class="labforradio" for="sost2">Б/У</label><div class="clr">&nbsp;</div>
							<input type="radio" class="radio" value="zapch" <?= $webuserinfo[ 'item_state3' ] ?> name="save_sost" id="sost3"/><label class="labforradio" for="sost3">На запчасти</label><div class="clr">&nbsp;</div>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Гарантия</div>
						<div class="_LK_form_inp dashedGroup <?= ( $save_err_flag[ 'save_item_count' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="radio" class="radio" value="yes"  <?= $webuserinfo[ 'item_guarantee1' ] ?> name="save_guarant" id="guarant1"/><label class="labforradio"  for="guarant1">С гарантией</label> <div class="clr">&nbsp;</div>
							<input type="radio" class="radio" value="no" <?= $webuserinfo[ 'item_guarantee2' ] ?> name="save_guarant" id="guarant2"/><label class="labforradio" for="guarant2">Без гарантии</label><div class="clr">&nbsp;</div>
							<label class="labfordateGuarant" <?=$guaranteeDispNone?> for="dateGuarant">Срок гарантии (мес.)<input type="text" class="miniinput" id="dateGuarant" name="save_guarant_text" value="<?= $webuserinfo[ 'item_guaranteeT' ] ?>" <?=$guaranteRequired?> /></label>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Количество</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_count' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="text"  class="miniinput" name="save_item_count" value="<?= $webuserinfo[ 'item_in_stock' ] ?>" id="save_item_count"  pattern="^[ 0-9]+$" <?=$webuserinfo[ 'item_in_stock_state' ]?> />
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line" id="save_item_warehouse1" <?=$styleHiddenWH[1]?> >
					<div class="_LK_form_lbl_mini">Месторасположение товара</div>
					
						<div class="_LK_form_inp">
							
								<?=$htmlOptWH[1]?>
							
						</div>
						<div class="clr">&nbsp;</div>
				</div>	
				
				<div class="_LK_form_line" id="save_item_warehouse2" <?=$styleHiddenWH[2]?> >	
					<div class="_LK_form_lbl"></div>
						<div class="_LK_form_inp">
								<?=$htmlOptWH[2]?>
							
						</div>
						<div class="clr">&nbsp;</div>
				</div>	
				
				<div class="_LK_form_line" id="save_item_warehouse3" <?=$styleHiddenWH[3]?> >	
					<div class="_LK_form_lbl"></div>
						<div class="_LK_form_inp ">
								<?=$htmlOptWH[3]?>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Способ доставки </div>
						<div class="_LK_form_inp dashedGroup <?= ( $save_err_flag[ 'save_item_count' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="radio" class="radio" value="postal"   <?= $webuserinfo[ 'item_shipment1' ] ?>  name="save_dost" id="dost1"/><label class="labforradio"  for="dost1">Отправка почтой</label> <div class="clr">&nbsp;</div>
							<input type="radio" class="radio" value="human"  <?= $webuserinfo[ 'item_shipment2' ] ?>  name="save_dost" id="dost2"/><label class="labforradio" for="dost2">Отправка курьером</label><div class="clr">&nbsp;</div>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Возможность торгов</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_tender' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input class="checkbox" type="checkbox" <?=$webuserinfo[ 'item_tender' ]?> name="save_item_tender"/>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				
				
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Распродажа</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_discount' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input class="checkbox" type="checkbox" <?= $webuserinfo[ 'item_discount' ] ?> name="save_item_discount" id="checkDiscount"/>
							<label <?= $webuserinfo[ 'item_discount_st' ] ?> class="labforpercentDiscount" for="percentDiscount">Скидка (%)<input type="text" class="miniinput" id="percentDiscount" name="save_item_discount_text" value="<?= $webuserinfo[ 'item_discount_text' ] ?>" /></label>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Категория товара</div>
						<div class="_LK_form_inp dashedGroup <?= ( $save_err_flag[ 'save_item_count' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="radio" class="radio" value="sles"  <?=$checkRequestNewPath1?> name="save_catType" id="catType1"/><label class="labforradio"  for="catType1">Выбор из существующих</label> <div class="clr">&nbsp;</div>
							<input type="radio" class="radio" value="new" <?=$checkRequestNewPath2?> name="save_catType" id="catType2"/><label class="labforradio" for="catType2">Предложить новую категорию</label><div class="clr">&nbsp;</div>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Категория товара 1го уровня</div>
					
						<div class="_LK_form_inp">
							<select name="save_item_catOne" <?= $urnpDefaultList ?>   class="miniinput" id="save_item_catOne"  >
								<?=$htmlcategoryOnelevel?>
							</select>
							<input type="text"  class="miniinput" name="save_item_catOne_alter" placeholder="Введите желаемую категорию товара" id="save_item_catOne_alter" value="<?= $userRequestMainCat ?>"  <?= $urnpAlter ?> <?=$catAlterRequired?> />
						</div>
						<div class="clr">&nbsp;</div>
				</div>	
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Категория товара 2го уровня</div>
					
						<div class="_LK_form_inp">
							<select name="save_item_catTwo" <?= $urnpDefaultList ?> class="miniinput" id="save_item_catTwo" <?=$catListRequired?> >
								<?=$htmlcategoryTwolevel?>
							</select>
							<input type="text"  class="miniinput" name="save_item_catTwo_alter" placeholder="Введите желаемую категорию товара" id="save_item_catTwo_alter" value="<?= $userRequestSubCat ?>"  <?= $urnpAlter ?>  <?=$catAlterRequired?> />
						</div>
						<div class="clr">&nbsp;</div>
				</div>	
				
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Фото</div>
						<div class="_LK_form_inp  <?= ( $save_err_flag[ 'save_item_docfile' ] ? '_LK_form_inp_error' : '' ) ?>">
							<div class="input_file noneborder">
								<input type="file"  name="save_item_imgfile" id="uploaded_imgEdit" multiple="multiple" accept="image/*" onchange="file_selected();"/>
								<!--progress id="progressbar_img" value="0" max="100"></progress-->
								<div class="progress-bar orange shine">
									<span id="photoprogress" style="width: 0%"></span>
								</div>
								<div class="notice_px">* Вы можете загрузить фотографии товара в форматах: <br/>
								".jpg", ".png", ".gif"</div>
								<div class="ajax-respond_img"></div>
							
							</div>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				<div class="_LK_form_line _LK_form_line_butt">
						<div class="_LK_form_lbl"> </div>
						<div class="_LK_form_inp"><button id="submitAddItem" class="mainbutton buttonsubmit" name="updateItem" type="submit">Сохранить</button></div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				

			</form>
				<?=$croppedDocs?>
				<?=$croppedImgs?>
			</div>


		</div>

?>