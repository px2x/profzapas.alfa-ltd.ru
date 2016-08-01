<?php

//define('ROOT', dirname(__FILE__).'/');
define('ROOT', $_SERVER['DOCUMENT_ROOT']);

$sc_site= 'profzapas.ru';
$sm_base= '../assets/modules/px_items/';


$module_url= MODX_MANAGER_URL .'?a='. $_GET[ 'a' ] .'&id='. $_GET[ 'id' ];
$module_url_orders= MODX_MANAGER_URL .'?a=112&id=3';

$webuser= intval( $_GET[ 'wu' ] );

if (isset( $_GET[ 'spg' ])){
	$subpage= $_GET[ 'spg' ];
}else {
	$subpage ='mainpage';
}

$act= $_GET[ 'act' ];



$result=mysql_query("SELECT id , parent, pagetitle FROM  ".$modx->getFullTableName( 'site_content' )." WHERE template = 4 AND id <>128") or die(mysql_error());
//Если в базе данных есть записи, формируем массив
if   (mysql_num_rows($result) > 0){
    $cats = array();
//В цикле формируем массив разделов, ключом будет id родительской категории, а также массив разделов, ключом будет id категории
    while($cat =  mysql_fetch_assoc($result)){
        $cats_ID[$cat['id']][] = $cat;
        $cats[$cat['parent']][$cat['id']] =  $cat;
    }
}

echo  '<div class="dark_bg"><div class="seeAnalogs"><div class="listAnalog"></div><div class="buttons"><button class="itemDisAnalogButton" value="Открепить">Открепить</button></div></div><div class="modal_tree">'.build_tree($cats,8).'</div><div class="modal_product"><div class="list"></div><div class="butt"><button class="itemSelectAnalogButton" value="Выбрать">Выбрать</button></div></div></div>';





//=================Удаление товара 
if (isset($_POST['DeleteCheckedItem'])){
	foreach ($_POST['deleteID'] AS $deleteID){
		$sql = "SELECT link FROM  ".$modx->getFullTableName( '_catalog_images' )." WHERE id_item = {$deleteID}";
		$result = mysql_query($sql) or die ('Error Select LInk Image');
		while ($link = mysql_fetch_row($result)[0]) {
			if (file_exists(ROOT.substr($link,1))){
				unlink(ROOT.substr($link,1));
			}else {
				//echo ROOT.substr($link,1);
			}
		}
		$sql = "SELECT link FROM  ".$modx->getFullTableName( '_catalog_docs' )." WHERE id_item = {$deleteID}";
		$result = mysql_query($sql) or die ('Error Select LInk Image');
		while ($link = mysql_fetch_row($result)[0]) {
			if (file_exists(ROOT.substr($link,1))){
				unlink(ROOT.substr($link,1));
			}else {
				//echo ROOT.substr($link,1);
			}
		}
		//каскадно удаляется из связных таблиц
		$sql="DELETE FROM ".$modx->getFullTableName( '_catalog' )." WHERE `id` = {$deleteID}";
		mysql_query($sql) or die ('Error Select noEnable items: '. mysql_error());
	}
	
	//================Одобрить товар
}elseif (isset($_POST['EnableCheckedItem'])){
	//echo 'Enable';
	$tmpArr = [];
	

	if (!is_array($_POST['deleteID'])){
		$tmpArr[] = $_POST['deleteID'];
	}else {
		$tmpArr = $_POST['deleteID'];
	}
		
	foreach ($tmpArr AS $deleteID){
		//$sql = "SELECT ".$modx->getFullTableName( '_catalog' )." SET enabled = 'y' WHERE id = {$deleteID}";
		
		$sql = "SELECT id FROM ".$modx->getFullTableName( '_request_new_path' )." WHERE id_item =  {$deleteID}";
		$result = mysql_query($sql) or die ('Error EnableCheckedItem '.mysql_error() );
		if($result && mysql_num_rows( $result ) == 0 ){
			$sql = "UPDATE  ".$modx->getFullTableName( '_catalog' )." SET  `enabled` =  'y' WHERE  `id` =  {$deleteID}";
			$result = mysql_query($sql) or die ('Error EnableCheckedItem '.mysql_error() );
		}
	}
}





//=================Установить скидку
if (isset($_POST['SetDiscountCheckedItem']) && is_numeric($_POST['setDiscountValue'])){
	$discountValue = $_POST['setDiscountValue'];
	foreach ($_POST['deleteID'] AS $deleteID){
		$sql = "UPDATE ".$modx->getFullTableName( '_catalog' )." SET discount = {$discountValue} WHERE id = {$deleteID}";
		$result = mysql_query($sql) or die ('Error UPDATE discount '.mysql_error() );
	}
	
	//================Установить скидку
}




/*======================Количество продовцов с новыми товарами =START===============*/
	$rr= mysql_query( "SELECT COUNT(DISTINCT seller) AS selReq, COUNT(seller) AS allItem FROM ".$modx->getFullTableName( '_catalog' )." WHERE enabled = 'n'" );
	if( $rr && mysql_num_rows( $rr ) > 0 )
	{
		$row = mysql_fetch_row( $rr ) ;
		$countRequest =  $row[0];
		$countRequestItem =  $row[1];
	}
	
/*======================Количество продовцов с новыми товарами=END===============*/



/*======================Количество продовцов с  товарами =START===============*/
	$rr= mysql_query( "SELECT COUNT(DISTINCT seller) AS selReq, COUNT(seller) AS allItem  FROM ".$modx->getFullTableName( '_catalog' )." WHERE enabled = 'y'" );
	if( $rr && mysql_num_rows( $rr ) > 0 )
	{
		$row = mysql_fetch_row( $rr ) ;
		$countSellers =  $row[0];
		$countSellersItem =  $row[1];
	}
	
/*======================Количество продовцов с  товарами=END===============*/



if( $result != '' ) $result .= '<br /><br />';
if( $result2 != '' ) $result2 .= '<br /><br />';

?><div class="modul_scorn_all">
<!-- -------------------------- -->
<link rel="stylesheet" type="text/css" href="<?php print $sm_base;?>_styles.css" />

<!--script type="text/javascript" src="//yandex.st/jquery/2.1.0/jquery.min.js"></script>
<script type="text/javascript" src="//yandex.st/jquery-ui/1.10.4/jquery-ui.min.js"></script-->


<script type="text/javascript" src="https://code.jquery.com/jquery-2.2.3.min.js"   integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo="   crossorigin="anonymous"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"   integrity="sha256-DI6NdAhhFRnO2k51mumYeDShet3I8AKCQf/tf7ARNhI="   crossorigin="anonymous"></script>


<script type="text/javascript" src="<?php print $sm_base;?>_script.js"></script>





<div class="topmenu">
	<ul>
		<li><a href="<?= $module_url ?>&ces=y">Все товары (<?php echo $countSellersItem?>)</a></li>
		<li><a href="<?= $module_url ?>&ces=n">Новые товары (+<?php echo $countRequestItem?>)</a></li>
		<li><a href="<?= $module_url ?>&spg=catTree">Категории</a></li>
	</ul>
	<div class="clr">&nbsp;</div>
</div>

<script type="text/javascript">
</script>



<?php


//===================================редактировать товар FORM  START===============================================================
if( $subpage == 'editItem' ) {
	
	/*----UPDATE START -------*/
	//event=dooupdate
	if (isset($_GET['event']) && $_GET['event']  == 'dooupdate' && is_numeric($_GET['requestsID'])) {
		
		$cat_id=addslashes($_GET['requestsID']);
	
		foreach ($_POST as $key => $value){
			$filteredPost[$key] = addslashes($value);
		}
		
		$sql = "SELECT cat.seller  
						FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
						WHERE cat.id =  '".$cat_id."'
						LIMIT 1";
		$result = mysql_query($sql) or die ('Error Select INS or UPD');
		if (! $sellerId = mysql_fetch_row($result)[0]) {
			//err
		}else {
		
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
		
		
			$instock_warehouses = $filteredPost['instock_warehouse1']+$filteredPost['instock_warehouse2']+$filteredPost['instock_warehouse3'];
		
			
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

				`documentation` ='file.pdf',  
				`image`='image.jpg',
				`tender`='".$save_item_tender."',
				`shipment`='".$filteredPost['save_dost']."',
				`sitelink`= '".$filteredPost['save_item_manuf_site']."'
				 WHERE `id` = ".$cat_id." LIMIT 1";
			mysql_query($sql) or die ('Error Insert');
			
				
				for ($y = 1; $y<4; $y++){
					$sql = "SELECT uwh.id
						FROM  ".$modx->getFullTableName( '_user_warehouse' )." AS uwh
						WHERE uwh.num =  '".$y."'
						AND uwh.seller =  '".$sellerId."'
						LIMIT 1";	
					$result = mysql_query($sql) or die ('Error Select');
					if ($currentWhId = mysql_fetch_row($result)[0]){
						$sql="UPDATE ".$modx->getFullTableName( '_catalog_warehouse' )."  SET
							`quantity` = '".$filteredPost['instock_warehouse'.$y]."'
							WHERE `item` = '".$cat_id."' AND `warehouse` = '".$currentWhId."' LIMIT 1";
						mysql_query($sql) or die ('Error Insert Warehouse on One Add');
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
				while (file_exists('.'.$filteredPost['fileImgHandler'.$fih])){
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
					if ($fih > 10) break;
				}
				
			
				
				$fdh = 0;
				while (file_exists('.'.$filteredPost['fileDocHandler'.$fdh])){
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
					if ($fdh > 10) break;
				}		
			}
		
		
		
		
		
		
		
		
		
	}

	
	/*----UPDATE END   -------*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	if (is_numeric($_GET['requestsID'])){
		$requestsID = addslashes($_GET['requestsID']);
		
		$catalogId=8;
		$catalogNewCatId=128; //сюда попадают товары с новой категорией
	//
		
		//Достаем всю инфу для вывода в форму
		
			//$idEditCat  = addslashes($_GET['idcat']);
			$idEditCat  = $requestsID;
			
			
			$sql = "SELECT cat.id, cat.seller, cat.code, cat.title, cat.parent, cat.manufacturer, cat.manufacturer_country, cat.price, cat.currency, cat.in_stock, cat.text, cat.state, cat.packaging, cat.guarantee, cat.discount, cat.shipment, cat.tender, cat.sitelink,
					   sc.pagetitle AS firstpagetitle,  sc2.pagetitle AS secondpagetitle, rnp.category AS path1,  rnp.subcategory AS path2
			FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
			LEFT JOIN  ".$modx->getFullTableName( '_request_new_path' )." AS rnp ON rnp.id_item = cat.id
			INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc ON sc.id = cat.parent
			INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc2 ON sc.parent = sc2.id
			WHERE  cat.id =  '{$idEditCat}' 
			GROUP BY cat.code LIMIT 1";
			$result = mysql_query($sql) or die ('Error Select noEnable items: '. mysql_error());
			
			if   (mysql_num_rows($result) > 0){
			
				$row = mysql_fetch_assoc($result);
				
				$webuserinfo[ 'item_id' ] = $row['id'];
				$webuserinfo[ 'seller_id' ] = $row['seller'];
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

			
				
			
	
			
		
		//Новая категория
		$result= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_request_new_path' )." WHERE id_item = ". $webuserinfo[ 'item_id' ]) or die ('Error 214: '. mysql_error());
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
			
			$result2= mysql_query( "SELECT parent FROM ".$modx->getFullTableName( 'site_content' )." WHERE id = ". $row[ 'parent' ])  or die ('Error Select 4881: '. mysql_error());
			$itemParentOneCat = mysql_fetch_assoc($result2)['parent'];
			

			
			$result2= mysql_query( "SELECT id, pagetitle FROM ".$modx->getFullTableName( 'site_content' )." WHERE parent = ".$itemParentOneCat) or die ('Error 994: '. mysql_error());
			while ($twoCatList = mysql_fetch_assoc($result2)){
				if ($twoCatList["id"] == $row[ 'parent' ]) {
					$attrSel = 'selected';
				}else {
					$attrSel = '';
				}
				$htmlcategoryTwolevel .='<option value="'.$twoCatList["id"].'" '.$attrSel.' >'.$twoCatList['pagetitle'].'</option>';
				
				$result3= mysql_query( "SELECT parent FROM ".$modx->getFullTableName( 'site_content' )." WHERE id = ". $twoCatList["id"]) or die ('Error 484: '. mysql_error());
				$itemParentMainCat = mysql_fetch_assoc($result3)['parent'];

			}

		}
		
			//достаем документы -----START
		$sqlimg = "SELECT cd.link , cd.originalname
			FROM  ".$modx->getFullTableName( '_catalog_docs' )." AS cd
			WHERE cd.id_item =  '".$webuserinfo[ 'item_id' ]."' ";
		$resultimg = mysql_query($sqlimg) or die ('Error 148: '. mysql_error());
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
		$resultimg = mysql_query($sqlimg) or die ('Error Select sqldoc: '. mysql_error()) or die ('Error 143: '. mysql_error());
		$croppedImgs = '';
		while ($rowimg = mysql_fetch_assoc($resultimg)){
			//$ext = explode('.' , $rowimg[ 'link' ])[1];
			
			$ext =  end(explode(".", $rowimg[ 'link' ]));

			$croppedImgs .= '<input type="hidden" class="linkToUplImgs" name="linkToUplImgs" value="'.$rowimg[ 'link' ].'" />';
		
		}
		//достаем картинки -----END
		
		
		
		
		

		$result= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user_warehouse' )." WHERE seller = ". $webuserinfo[ 'seller_id' ]) or die ('Error 232: '. mysql_error());
		$htmlOptWH = [];
		$tmp= [];
		while ($row = mysql_fetch_assoc($result)){
			$result2= mysql_query( "SELECT quantity FROM ".$modx->getFullTableName( '_catalog_warehouse' )." WHERE warehouse = ". $row[ 'id' ]." AND item = ".$webuserinfo[ 'item_id' ] ) or die ('Error 1145: '. mysql_error());
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




		$result= mysql_query( "SELECT id, pagetitle FROM ".$modx->getFullTableName( 'site_content' )." WHERE parent=". $catalogId ." AND id <> ".$catalogNewCatId."  ORDER BY menuindex ASC" )  or die ('Error 328: '. mysql_error());
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
		
			<form  action="<?=$module_url?>&spg=editItem&requestsID=<?=$idEditCat?>&event=dooupdate" method='POST' id="addMewItemForm">
				
				
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
								<input type="file" name="save_item_docfile" id="uploaded_fileEdit" multiple="multiple" accept=".doc,.docx,.xls,.pdf.txt,image/*"/>
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
								<input type="file"  name="save_item_imgfile" id="uploaded_imgEdit" multiple="multiple" accept="image/*"/>
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
		
		
		<?php
	//=============================================	
	}
}

//===================================редактировать товар FORM END===============================================================
















//=================================НОВЫЕ ТОВАРЫ  и не новые ...короче товары START==============================================================================
if( $subpage == 'mainpage' ) {
	$catEnabledStatus = addslashes($_GET['ces']);
	
	if ($catEnabledStatus != 'n') {
		$catEnabledStatus = 'y';
	}
	
	$result= mysql_query( "SELECT DISTINCT cat.seller AS selReq , user.firstname, user.surname, user.email, user.mobile 
							FROM ".$modx->getFullTableName( '_catalog' )." AS cat
							INNER JOIN ".$modx->getFullTableName( '_user' )." AS user ON cat.seller = user.id
							WHERE cat.enabled = '{$catEnabledStatus}'" );
	if( $result && mysql_num_rows( $result ) > 0 )
	{
		$print .= '<div class="requestsMainBlock"><table class="userstable">';
		while( $row= mysql_fetch_assoc( $result ) )
		{
	
			$requestsIDsel=$row['selReq'];
			$rr= mysql_query( "SELECT COUNT(seller) AS selReqCurUser FROM ".$modx->getFullTableName( '_catalog' )." WHERE enabled = '{$catEnabledStatus}' AND seller = '{$requestsIDsel}'" ) or die (mysql_error());
			$selReqCurUser  = mysql_result($rr,0);
			if ( $row[ 'firstname' ] == '' && $row[ 'surname' ] == ''){
				$printName = 'Имя не указано';
			}else {
				$printName = $row[ 'surname' ] .' '. $row[ 'firstname' ];
			}
			$print .= '
			
			<tr>
				<td class="sellerAdInfo">
					<a href="'.$module_url.'&ces='.$catEnabledStatus.'&type=selectItem&requestsID='.$requestsIDsel.'">'. $row[ 'email' ] .'</a><br/>
					( '.$printName.' : '. $row[ 'mobile' ].')
				</td>
				<td style="font-size:20px;">
					'.$selReqCurUser.'
				</td>
			</tr>';
		}
		$print .= '</table></div>';
	}
	
	if( $_GET['type'] == 'selectItem' && is_numeric($_GET['requestsID'])){
		//=======================

		$sql = "SELECT cat.id, cat.code, cat.title, cat.parent, cat.manufacturer, cat.price, cat.currency, cat.in_stock, cat.text, cat.state, cat.packaging, cat.guarantee, cat.discount, cat.shipment, cat.tender,
						   sc.pagetitle AS firstpagetitle,  sc2.pagetitle AS secondpagetitle, rnp.category AS path1,  rnp.subcategory AS path2
				FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
				LEFT JOIN  ".$modx->getFullTableName( '_request_new_path' )." AS rnp ON rnp.id_item = cat.id
				INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc ON sc.id = cat.parent
				INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc2 ON sc.parent = sc2.id
				WHERE cat.seller =  '".$_GET['requestsID']."'
				AND cat.enabled =  '{$catEnabledStatus}' 
				GROUP BY cat.code";
			$result = mysql_query($sql) or die ('Error Select noEnable items: '. mysql_error());
			$noEnableItems = '';
			$noEnableItems .= '<div class="requestsSeeItem">';
			$noEnableItems .= '<form action="'.$module_url.'&ces='.$catEnabledStatus.'&type=selectItem&requestsID='.$_GET['requestsID'].'" name="noEnabledItems" method="POST">';
			$noEnableItems .= '<div class=lkItemtitle>
				<div class="checkboxs"><input type="checkbox" name="checkall" /></div>
				<div class="code">Код</div>
				<div class="vend">Производитель</div>
				<div class="name">Наименование</div>
				<div class="stock">Количество</div>
				<div class="price">Цена</div>
				<div class="category">Категория</div>
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
				//$imgLink = '../.'.$rowimg[ 'link' ];
				$croppedImgs .= '<img src="'. $modx->runSnippet( 'ImgCrop6', array( 'img' => $rowimg[ 'link' ], 'w' => 85, 'h' => 80, 'fill' => 0, 'fullpath' => 1 ) ) .'" />';
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
				$croppedDocs .= '<a class="docLinkDownload" href="../'.$rowimg[ 'link' ].'"><div class="documentPrint" style="background-image:url(../template/images/file_'.$ext.'.png);"><span>'.$rowimg[ 'originalname' ].'</span></div></a>';
			}
			//достаем документы -----END
	
	
			
			$addNewCatText='';
			if ($flagBgColor > 0) {$addStyle = 'fillGrayBG';} else {$addStyle = '';}
			$noEnableItems .= '<div class="lkItem '.$addStyle.'">';
			$noEnableItems .= '<div class="checkboxs"><input type="checkbox" name="deleteID['.$row['id'].']" value="'.$row['id'].'" /></div>';
			
			$noEnableItems .= '<div class="code">'.$row['code'].'</div>';
			
			$noEnableItems .= '<div class="vend">'.$row['manufacturer'].'</div>';
			$noEnableItems .= '<div class="name"><div class="type">'.$row['title'].'</div></div>';
			$noEnableItems .= '<div class="stock">'.$row['in_stock'].' шт.</div>';
			$noEnableItems .= '<div class="price"><nobr><span class="price">'.$row['price'].'</span> <span class="">'.$row['currency'].'<span></nobr></div>';
			if ($row['path1'] !='' || $row['path2'] != '') {
				$addNewCatText = '<span>Продавец предлагает создать новый раздел:</span><br/>/'.$row['path1'].' / '.$row['path2'];
				$noEnableItems .= '<div class="category" id="category_'.$row['id'].'">'.$addNewCatText.'</div>';
				$newPath = '<div class="control addnewpath" id="addnewpath_'.$row['id'].'" data-id="'.$row['id'].'">Создать категорию</div>';
			}else {
				$noEnableItems .= '<div class="category"  id="category_'.$row['id'].'">'.$row['firstpagetitle'].'<br /> ('.$row['secondpagetitle'].')<br/> </div>';
			}
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
										<div class="px_bottom">'.$croppedDocs.'</div>
								   </div>
								   <div class="px_right">
										<div class="px_top">
											'.$row['text'].'
										</div>
										<div class="px_top_control">
											'.$newPath.'
											<div class="control setAnalogItem" data-id="'.$row['id'].'">Подобрать аналоги</div>
											<div class="control seeAnalogItem" data-id="'.$row['id'].'">Просмотреть аналоги</div>';
											

											
											if ($catEnabledStatus == 'n') {
												$noEnableItems .= '<div class="control enableOneItem" id="enableItem_'.$row['id'].'"  data-id="'.$row['id'].'">Одобрить</div>';
											}
											
											$noEnableItems .= ' 
											<div class="control" id="editItem_'.$row['id'].'"><a href='.$module_url.'&spg=editItem&requestsID='.$row['id'].'>Редактировать</a></div>
											<div class="control deleteOneItem" id="deleteItem_'.$row['id'].'"  data-id="'.$row['id'].'">Удалить</div>
										</div>
										<div class="clr"></div>
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



		$noEnableItems .= '<button class="buttonsubmit2" name="DeleteCheckedItem" type="submit">Удалить отмеченные</button>';
		$noEnableItems .= '<button class="buttonsubmit2" name="EnableCheckedItem" type="submit">Одобрить отмеченные</button>';
		$noEnableItems .= '<button class="buttonsubmit2" name="SetDiscountCheckedItem" type="submit">Установить скидку</button><input style="width: 20px;" type="text" name="setDiscountValue" value="0" />%';
		$noEnableItems .= '</form>';
		$noEnableItems .= '</div>';
		$print.=$noEnableItems;
			///==========
		}

	
}
//=================================НОВЫЕ ТОВАРЫ и не новые END==============================================================================	
	

	
	
	

	

	
	
	
	
	
	
	
//==================================дерево категорий = START ===================================================================//	
if( $subpage == 'catTree' ) {
	$print .= '<div class="requestsMainBlock treeList">';
		$print .= build_tree_link($cats,8,$module_url);
	$print .= '</div>';
	
	
	
	if (is_numeric($_GET['requestsID'])){
		
		$sql = "SELECT cat.id, cat.code, user.firstname, user.surname, user.email, user.id AS sellerid, cat.title, cat.parent, cat.manufacturer, cat.price, cat.currency, cat.in_stock, cat.text, cat.state, cat.packaging, cat.guarantee, cat.discount, cat.shipment, cat.tender,
						   sc.pagetitle AS firstpagetitle,  sc2.pagetitle AS secondpagetitle, rnp.category AS path1,  rnp.subcategory AS path2
				FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
				LEFT JOIN  ".$modx->getFullTableName( '_request_new_path' )." AS rnp ON rnp.id_item = cat.id
				INNER JOIN  ".$modx->getFullTableName( '_user' )." AS user ON user.id = cat.seller
				INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc ON sc.id = cat.parent
				INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc2 ON sc.parent = sc2.id
				WHERE cat.parent =  '".$_GET['requestsID']."'
				AND cat.enabled =  'y' 
				GROUP BY cat.code";
			$result = mysql_query($sql) or die ('Error Select noEnable items: '. mysql_error());
			$noEnableItems = '';
			$noEnableItems .= '<div class="requestsSeeItem">';
			$noEnableItems .= '<form action="'.$module_url.'&spg=catTree&requestsID='.$_GET['requestsID'].'" name="noEnabledItems" method="POST">';
			$noEnableItems .= '<div class=lkItemtitle>
				<div class="checkboxs"><input type="checkbox" name="checkall" /></div>
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
				//$imgLink = '../.'.$rowimg[ 'link' ];
				$croppedImgs .= '<img src="'. $modx->runSnippet( 'ImgCrop6', array( 'img' => $rowimg[ 'link' ], 'w' => 85, 'h' => 80, 'fill' => 0, 'fullpath' => 1 ) ) .'" />';
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
				$croppedDocs .= '<a class="docLinkDownload" href="../'.$rowimg[ 'link' ].'"><div class="documentPrint" style="background-image:url(../template/images/file_'.$ext.'.png);"><span>'.$rowimg[ 'originalname' ].'</span></div></a>';
			}
			//достаем документы -----END
	
	
			
			$addNewCatText='';
			if ($flagBgColor > 0) {$addStyle = 'fillGrayBG';} else {$addStyle = '';}
			$noEnableItems .= '<div class="lkItem '.$addStyle.'">';
			$noEnableItems .= '<div class="checkboxs"><input type="checkbox" name="deleteID['.$row['id'].']" value="'.$row['id'].'" /></div>';
			
			$noEnableItems .= '<div class="code">'.$row['code'].'</div>';
			
			$noEnableItems .= '<div class="vend">'.$row['manufacturer'].'</div>';
			$noEnableItems .= '<div class="name"><div class="type">'.$row['title'].'</div></div>';
			$noEnableItems .= '<div class="stock">'.$row['in_stock'].' шт.</div>';
			$noEnableItems .= '<div class="price"><nobr><span class="price">'.$row['price'].'</span> <span class="">'.$row['currency'].'<span></nobr></div>';
			if ($row['path1'] !='' || $row['path2'] != '') {
				$addNewCatText = '<span>Продавец предлагает создать новый раздел:</span><br/>/'.$row['path1'].' / '.$row['path2'];
				$noEnableItems .= '<div class="category" id="category_'.$row['id'].'">'.$addNewCatText.'</div>';
				$newPath = '<div class="control addnewpath" id="addnewpath_'.$row['id'].'" data-id="'.$row['id'].'">Создать категорию</div>';
			}else {
				$noEnableItems .= '<div class="category">'.$row['email'].' <br />('.$row['firstname'].' '.$row['surname'].')<br/> </div>';
			}
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
										<div class="px_bottom">'.$croppedDocs.'</div>
								   </div>
								   <div class="px_right">
										<div class="px_top">
											'.$row['text'].'
										</div>
										<div class="px_top_control">
											'.$newPath.'
											<div class="control setAnalogItem" data-id="'.$row['id'].'">Подобрать аналоги</div>
											<div class="control seeAnalogItem" data-id="'.$row['id'].'">Просмотреть аналоги</div>';
											

											
											if ($catEnabledStatus == 'n') {
												$noEnableItems .= '<div class="control enableOneItem" id="enableItem_'.$row['id'].'"  data-id="'.$row['id'].'">Одобрить</div>';
											}
											
											$noEnableItems .= ' 
											<div class="control" id="editItem_'.$row['id'].'"><a href='.$module_url.'&spg=editItem&requestsID='.$row['id'].'>Редактировать</a></div>
											<div class="control deleteOneItem" id="deleteItem_'.$row['id'].'"  data-id="'.$row['id'].'">Удалить</div>
										</div>
										<div class="clr"></div>
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



		$noEnableItems .= '<button class="buttonsubmit2" name="DeleteCheckedItem" type="submit">Удалить отмеченные</button>';
		//$noEnableItems .= '<button class="buttonsubmit2" name="EnableCheckedItem" type="submit">Одобрить отмеченные</button>';
		$noEnableItems .= '<button class="buttonsubmit2" name="SetDiscountCheckedItem" type="submit">Установить скидку</button><input style="width: 20px;" type="text" name="setDiscountValue" value="0" />%';
		$noEnableItems .= '</form>';
		$noEnableItems .= '</div>';
		$print.=$noEnableItems;
			///==========
	}
	$print .= '<div class="requestsSeeItem">';
	
	$print .= '</div>';
}
//=================================Категории END==============================================================================	




function build_tree($cats,$parent_id,$only_parent = false){
    if(is_array($cats) and isset($cats[$parent_id])){
        $tree = '<ul>';
        if($only_parent==false){
            foreach($cats[$parent_id] as $cat){
                $tree .= '<li id="'.$cat['id'].'">'.$cat['pagetitle']; 
                $tree .=  build_tree($cats,$cat['id']);
                $tree .= '</li>';
            }
        }elseif(is_numeric($only_parent)){
            $cat = $cats[$parent_id][$only_parent];
            $tree .= '<li id="'.$cat['id'].'">'.$cat['pagetitle']; 
            $tree .=  build_tree($cats,$cat['id']);
            $tree .= '</li>';
        }
        $tree .= '</ul>';
    }
    else return null;
    return $tree;
}


function build_tree_link($cats,$parent_id,$module_url,$only_parent = false){
    if(is_array($cats) and isset($cats[$parent_id])){
        $tree = '<ul>';
        if($only_parent==false){
            foreach($cats[$parent_id] as $cat){
                $tree .= '<li id="'.$cat['id'].'"><a href="'.$module_url.'&spg=catTree&requestsID='.$cat['id'].'">'.$cat['pagetitle']."</a>"; 
                $tree .=  build_tree_link($cats,$cat['id'],$module_url);
                $tree .= '</li>';
            }
        }elseif(is_numeric($only_parent)){
            $cat = $cats[$parent_id][$only_parent];
            $tree .= '<li id="'.$cat['id'].'"><a href="'.$module_url.'&spg=catTree&requestsID='.$cat['id'].'">'.$cat['pagetitle']."</a>"; 
            $tree .=  build_tree_link($cats,$cat['id'],$module_url);
            $tree .= '</li>';
        }
        $tree .= '</ul>';
    }
    else return null;
    return $tree;
}



print $print;
/*
*/
?>