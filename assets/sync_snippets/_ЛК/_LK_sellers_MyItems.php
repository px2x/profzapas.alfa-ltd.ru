<?php

/*для каскадного удаления (Каталог и связных таблиц)
	/необходимо наличие триггера
	DROP TRIGGER IF EXISTS `auto_delete`;
	CREATE DEFINER=`alfa-ltd-ru`@`%` TRIGGER `auto_delete` 
	BEFORE DELETE ON `profzapas__catalog` 
	FOR EACH ROW 
	BEGIN
	DELETE FROM profzapas__catalog_warehouse WHERE OLD.id = item;
	DELETE FROM profzapas__request_new_path WHERE OLD.id = id_item;
	DELETE FROM profzapas__catalog_docs WHERE OLD.id = id_item;
	DELETE FROM profzapas__catalog_images WHERE OLD.id = id_item;
	DELETE FROM profzapas__catalog_analogs WHERE OLD.id = id_item;
	END
	*/
/*
	что бы работад\ло  ON DUPLICATE KEY UPDATE
	_request_new_path`.`id_item` должен быть UNIQUE KEY
	
	
	
ALTER TABLE  `profzapas__catalog_images` ADD UNIQUE (
`link`
);


ALTER TABLE  `profzapas__catalog_docs` ADD UNIQUE (
`link`
);
	
*/
$topage_url= $modx->makeUrl( $modx->documentIdentifier );

$notFoundId = 128;
$auth = 108;
$sellerPageId = 106;
$catalogId=8;
$catalogNewCatId=128; //сюда попадают товары с новой категорией
$vkladka_active= 1;

if ($_GET['tab'] == 2) $vkladka_active= 2;
if ($_GET['tab'] == 3) $vkladka_active= 3;
if ($_GET['tab'] == 4) $vkladka_active= 4;
if ($_GET['tab'] == 5) $vkladka_active= 5;
$error_report = '';
$parseXlsError ='';
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


/*==============================ЕСЛИ НАДО УДАЛИТЬ==START====================*/
if (isset($_POST['DeleteCheckedItemNoEnabled']) || isset($_POST['DeleteCheckedItemEnabled'])){

	foreach ($_POST['deleteID'] AS $deleteID){
		
		$sql = "SELECT link FROM  ".$modx->getFullTableName( '_catalog_images' )." WHERE id_item = {$deleteID}";
		$result = mysql_query($sql) or die ('Error Select LInk Image');
		while ($link = mysql_fetch_row($result)[0]) {
			if (file_exists($link)){
				unlink($link);
			}
		}
		
		$sql = "SELECT link FROM  ".$modx->getFullTableName( '_catalog_docs' )." WHERE id_item = {$deleteID}";
		$result = mysql_query($sql) or die ('Error Select LInk Image');
		while ($link = mysql_fetch_row($result)[0]) {
			if (file_exists($link)){
				unlink($link);
			}
		}
		
		//каскадно удаляется из связных таблиц
		$sql="DELETE FROM ".$modx->getFullTableName( '_catalog' )." WHERE `id` = {$deleteID}";
		mysql_query($sql) or die ('Error Select noEnable items: '. mysql_error());
		
	}
}
/*==============================ЕСЛИ НАДО УДАЛИТЬ==END====================*/

//return print_r($webuserinfo);
$uploaddir = 'xlsimport/';
$uploadfile = $uploaddir.session_id().'_'.time().'.xls';

/*==============================ЕСЛИ ПРИШЕЛ ФАЙЛ==START====================*/
if (isset($_POST['excellFileSubmit'])){
	if (move_uploaded_file($_FILES['excellFile']['tmp_name'], $uploadfile)) {
		$readerResult = array();
		$readerColIterator = array();
   		include_once( MODX_MANAGER_PATH .'includes/controls/PHPExcel.php' );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel = PHPExcel_IOFactory::load($uploadfile);
		$objPHPExcel->setActiveSheetIndex(0);
		$active_sheet = $objPHPExcel->getActiveSheet();
		
		/*
		for ($row = 2; $row <= $active_sheet->getHighestRow(); $row++) {  
		$nColumn = PHPExcel_Cell::columnIndexFromString($active_sheet->getHighestColumn());
			for ($col = 0; $col < $nColumn; $col++) {
				array_push($readerColIterator,  addslashes($active_sheet->getCellByColumnAndRow($col, $row)->getValue()));
			}
			array_push($readerResult, $readerColIterator);
			$readerColIterator = array();
		}
		unlink($uploadfile);
*/
		
		
		$breakFlag[0] = false; 
		$breakFlag[1] = false;
		
		$row=2;
		while (!$breakFlag[0] && !$breakFlag[1]) { 
		$nColumn = PHPExcel_Cell::columnIndexFromString($active_sheet->getHighestColumn());
			for ($col = 0; $col < $nColumn; $col++) {
				$tmp = addslashes($active_sheet->getCellByColumnAndRow($col, $row)->getValue());
				array_push($readerColIterator,  $tmp);
				if (($tmp == '' || $tmp == ' ') && $col == 0) {
					$breakFlag[0] = true;
					$errr = true;
				}
				
				if (($tmp == '' || $tmp == ' ') && ($col == 5 || $col == 10)) {
					$breakFlag[1] = true;
					$errr = true;
				}
				
				
			}
			$row++;
			if (!$errr) {
				array_push($readerResult, $readerColIterator);
			}
			
			$readerColIterator = array();
		}
		unlink($uploadfile);
		//$testdata = 'rererrrer';
		
		//return print_r($readerResult);
		$hookRow++;
		$hookRow = 2;
		foreach ($readerResult AS $elem){
			if ($elem[0] == '' || $elem[5] == '' || $elem[10] == '' || $elem[11] == '' ) {
				$parseXlsError .= '<div class="_LK_error">Ошибка в строке '.$hookRow.'. Прверьте "Код","Наименование", "Цена", "Валюта".</div>';
				continue;
			}
			
			
			
			//echo setlocale(LC_CTYPE, '');
			 //$elem[12] = iconv("UTF-8", "CP1251", $elem[12]);
			//$testdata .= mb_strtolower($elem[12],'UTF-8')."-новый //// - ".strtolower($elem[15])." - да  /// - ".$elem[16]." - ".$elem[17]. '<br />';
			//состояние state
		
			if (mb_strtolower($elem[12],'UTF-8') == 'новый'){
				$elem[12] = 'new';
			}elseif (mb_strtolower($elem[12],'UTF-8') == 'на запчасти') {
				$elem[12] = 'zapch';
			} else {
				$elem[12] = 'bu';
			}
			
			
			//вазможность торгов tender
			if (mb_strtolower($elem[15],'UTF-8') == 'да'){
				$elem[15] = '1';
			}else {
				$elem[15] = '0';
			}
			
			//нал упак
			if (mb_strtolower($elem[16],'UTF-8') == 'да'){
				$elem[16] = 'y';
			}else {
				$elem[16] = 'n';
			}
			
			
			//доставка
			if (mb_strtolower($elem[17],'UTF-8') == 'почта'){
				$elem[17] = 'postal';
			}else {
				$elem[17] = 'human';
			}
			
			//echo 'worehOk';
			
			
			$addNewCatRequest = false;
			//ищем ИД категорий по PAGETITLE
			//если не найжена то добавляем новую категорию на одобрение
			$sql = "SELECT t1.id AS cat_first , t2.id AS cat_second 
					FROM  ".$modx->getFullTableName( 'site_content' )." AS t1
					INNER JOIN  ".$modx->getFullTableName( 'site_content' )." AS t2 ON t2.parent = t1.id
					WHERE t2.pagetitle =  '".$elem[4]."'
					AND t1.pagetitle =  '".$elem[3]."'
					LIMIT 1";
			$result = mysql_query($sql) or die ('Error Select ИД');
			if (!$subcategory = mysql_fetch_row($result)[1]) {
				$addNewCatRequest = true;
				$subcategory=$notFoundId;
			}	
			
			//ищем склад
			$excellelem = 7; //начальная позиция столбца со складами
			for ($i = 1; $i<4; $i++){
				if (! $elem[$excellelem] == '' ||  ! $elem[$excellelem] == 0){
					$sql = "SELECT uwh.id
							FROM  ".$modx->getFullTableName( '_user_warehouse' )." AS uwh
							WHERE uwh.num =  '{$i}'
							AND uwh.seller =  '".$webuserinfo['id']."'
							AND uwh.city <>  ''
							LIMIT 1";
					$result = mysql_query($sql) or die ('Error Select Warehouse');
					if (!$findedUWH[$i] = mysql_fetch_row($result)[0]) $findedUWH[$i]=false;
					
				} else $findedUWH[$i]=false;
				$excellelem++;
			}
			
			
			//return print_r($findedUWH);
			//обновлять или добавлять
			$inStockSumm = (int)$elem[7]+(int)$elem[8]+(int)$elem[9];
			$sql = "SELECT cat.id
					FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
					WHERE cat.seller =  '".$webuserinfo['id']."'
					AND cat.code =  '".$elem[0]."'
					LIMIT 1";
			$result = mysql_query($sql) or die ('Error Select INS or UPD');
			
			
			
			if ($findedRowId = mysql_fetch_row($result)[0]) {
				$sql="UPDATE ".$modx->getFullTableName( '_catalog' )." AS cat SET 
					`parent` =  '".$subcategory."',
					`title`= '".$elem[5]."' ,
					`manufacturer` ='".$elem[1]."' ,
					`manufacturer_country` ='".$elem[2]."' ,
					`price` = '".$elem[10]."' ,
					`currency` = '".$elem[11]."' ,
					`in_stock` ='".$inStockSumm."' ,
					`guarantee` = '".$elem[13]."'  ,
					`state` = '".$elem[12]."' ,
					`packaging` = '".$elem[16]."',
					`discount` = '".$elem[18]."',
					`text` = '".$elem[6]."',
					`enabled` = 'n' ,
					`documentation` ='file.pdf',
					`image` = 'image.jpg',
					`tender` = '".$elem[15]."',
					`shipment` = '".$elem[17]."',
					`sitelink` = '".$elem[14]."'
					 WHERE cat.id ='{$findedRowId}'";
				mysql_query($sql) or die ('Error Update'. mysql_error());
				$flagNewCatReq = $findedRowId;
				
				$sql="UPDATE ".$modx->getFullTableName( '_catalog_warehouse' )." AS cwh SET 
					`quantity`= '".$elem[7]."' 
					WHERE cwh.item ='{$findedRowId}' AND warehouse = '".$findedUWH[1]."' ";
				mysql_query($sql) or die ('Error Update Warehouse');
				
				$sql="UPDATE ".$modx->getFullTableName( '_catalog_warehouse' )." AS cwh SET 
					`quantity`= '".$elem[8]."' 
					 WHERE cwh.item ='{$findedRowId}' AND warehouse = '".$findedUWH[2]."' ";
				mysql_query($sql) or die ('Error Update Warehouse');
				
				$sql="UPDATE ".$modx->getFullTableName( '_catalog_warehouse' )." AS cwh SET 
					`quantity`= '".$elem[9]."' 
					 WHERE cwh.item ='{$findedRowId}' AND warehouse = '".$findedUWH[3]."' ";
				mysql_query($sql) or die ('Error Update Warehouse');
				//return print_r($findedUWH);
				
			}else {
				$sql="INSERT INTO ".$modx->getFullTableName( '_catalog' )." (
					`id` ,
					`parent` ,
					`seller` ,
					`title` ,
					`code` ,
					`manufacturer` ,
					`manufacturer_country` ,
					`price` ,
					`currency` ,
					`in_stock` ,
					`guarantee` ,
					`state` ,
					`packaging` ,
					`discount` ,
					`text` ,
					`enabled` ,
					`documentation` ,
					`image`,
					`tender`,
					`shipment`,
					`sitelink`
					)
					VALUES (
					NULL , 
					'".$subcategory."',  
					'".$webuserinfo['id']."',  
					'".$elem[5]."',  
					'".$elem[0]."',  
					'".$elem[1]."',
					'".$elem[2]."',  
					'".$elem[10]."', 
					'".$elem[11]."',   
					'".$inStockSumm."',  
					'".$elem[13]."',  
					'".$elem[12]."', 
					'".$elem[16]."',   
					'".$elem[18]."',  
					'".$elem[6]."', 
					'n',  
					'file.pdf',  
					'image.jpg',
					'".$elem[15]."',
					'".$elem[17]."',
					'".$elem[14]."'
					)";
				mysql_query($sql) or die ('Error Insert');
				
				$sql = "SELECT cat.id
					FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
					WHERE cat.code =  '".$elem[0]."'
					AND cat.seller =  '".$webuserinfo['id']."'
					LIMIT 1";
				$result = mysql_query($sql) or die ('Error Select');
				if ($lastInsertId = mysql_fetch_row($result)[0]){
				
					
					for ($g = 1; $g<4; $g++){
						if ($findedUWH[$g]){
							$sql="INSERT INTO ".$modx->getFullTableName( '_catalog_warehouse' )." (
								`id`,
								`item` ,
								`warehouse`,
								`quantity`)
								VALUES (
								NULL , 
								'".$lastInsertId."',  
								'".$findedUWH[$g]."',
								'".$elem[$g+6]."'
								)";
							mysql_query($sql) or die ('Error Insert Warehouse'.$g);	
						
						}
					}
					
					
					$flagNewCatReq = $lastInsertId;
				}
			}
			///если продавец предлагает новую категорию
			if ($findedRowId) {
				mysql_query("DELETE FROM ".$modx->getFullTableName( '_request_new_path' )." WHERE id_item = ".$findedRowId." LIMIT 1 ") or die (mysql_error());
			}
			
			if ($addNewCatRequest){
				$sql = "INSERT INTO  ".$modx->getFullTableName( '_request_new_path' )." (
					`id`,
					`id_item`,
					`category`,
					`subcategory`
					) VALUES (
					NULL,
					'".$flagNewCatReq."', 
					'".$elem[3]."',
					'".$elem[4]."'
					) ON DUPLICATE KEY UPDATE `category` = '".$elem[3]."' , `subcategory` = '".$elem[4]."'";
				$result = mysql_query($sql) or die ('Error Select ADD new Cat REQ: '.mysql_error());
			}
		}
		
		
				header( 'location: '. $topage_url.'?tab=4&ok44' );
				exit();
	} else {
		$error_report = 'Не удалось загрузить файл. Попробуйте снова';
	}
}
//return $testdata;
/*==============================ЕСЛИ ПРИШЕЛ ФАЙЛ==END====================*/


/*=================================Добавление нового товара SATRT==============================================*/
if (isset($_POST['save_3'])){
	//print_r($_POST);
	foreach ($_POST as $key => $value){
		$filteredPost[$key] = addslashes($value);
	}
	
	
	$sql = "SELECT cat.id
					FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
					WHERE cat.seller =  '".$webuserinfo['id']."'
					AND cat.code =  '".$filteredPost['save_item_code']."'
					LIMIT 1";
	$result = mysql_query($sql) or die ('Error Select INS or UPD');
	if (mysql_fetch_row($result)[0]) {
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
		
		$sql="INSERT INTO ".$modx->getFullTableName( '_catalog' )." (
			`id` ,
			`parent` ,
			`seller` ,
			`title` ,
			`code` ,
			`manufacturer` ,
			`manufacturer_country` ,
			`price` ,
			`currency` ,
			`in_stock` ,
			`guarantee` ,
			`state` ,
			`packaging` ,
			`discount` ,
			`text` ,
			`enabled` ,
			`documentation` ,
			`image`,
			`tender`,
			`shipment`,
			`sitelink`
			)
			VALUES (
			NULL , 
			'".$filteredPost['save_item_catTwo']."',  
			'".$webuserinfo['id']."',  
			'".$filteredPost['save_item_name']."',  
			'".$filteredPost['save_item_code']."',  
			'".$filteredPost['save_item_manuf']."',
			'".$filteredPost['save_item_manuf_с']."',
			'".$filteredPost['save_item_price']."',  
			'".$filteredPost['save_item_valute']."', 
			'".$instock_warehouses."',   
			'".$save_guarant."',  
			'".$filteredPost['save_sost']."',   
			'".$save_item_pack."',   
			'".$save_item_discount_text."',   
			'".$filteredPost['save_item_descr']."', 
			'n',  
			'file.pdf',  
			'image.jpg',
			'".$save_item_tender."',
			'".$filteredPost['save_dost']."',
			'".$filteredPost['save_item_manuf_site']."'
			)";
		mysql_query($sql) or die ('Error Insert');
			
		$sql = "SELECT cat.id
				FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
				WHERE cat.code =  '".$filteredPost['save_item_code']."'
				AND cat.seller =  '".$webuserinfo['id']."'
				LIMIT 1";
		
		

		//последний вставленный ИД 		
		$result = mysql_query($sql) or die ('Error Select');
		if ($lastInsertId = mysql_fetch_row($result)[0]){
			for ($y = 1; $y<4; $y++){
				$currentWh=$filteredPost['save_item_warehouse'.$y];
				//склады Ищем ИД Склада
				$sql = "SELECT uwh.id
					FROM  ".$modx->getFullTableName( '_user_warehouse' )." AS uwh
					WHERE uwh.num =  '".$currentWh."'
					AND uwh.seller =  '".$webuserinfo['id']."'
					LIMIT 1";	
				$result = mysql_query($sql) or die ('Error Select');
				if ($currentWhId = mysql_fetch_row($result)[0]){
					$sql="INSERT INTO ".$modx->getFullTableName( '_catalog_warehouse' )." (
						`id`,
						`item` ,
						`warehouse`,
						`quantity`)
						VALUES (
						NULL , 
						'".$lastInsertId."',  
						'".$currentWhId."',
						'".$filteredPost['instock_warehouse'.$y]."'
						)";
					mysql_query($sql) or die ('Error Insert Warehouse on One Add');
				}
			}
			
			$flagNewCatReq = $lastInsertId;
			//предложил создать новую категрию
			if ($filteredPost['save_catType'] == 'new'){
				$sql = "INSERT INTO  ".$modx->getFullTableName( '_request_new_path' )." (
						`id`,
						`id_item`,
						`category`,
						`subcategory`
						) VALUES (
						NULL,
						'".$flagNewCatReq."', 
						'".$filteredPost['save_item_catOne_alter']."',
						'".$filteredPost['save_item_catTwo_alter']."'
						) ON DUPLICATE KEY UPDATE `category` = '".$filteredPost['save_item_catOne_alter']."' , `subcategory` = '".$filteredPost['save_item_catTwo_alter']."'";
					$result = mysql_query($sql) or die ('Error  ADD new Cat REQ: '.mysql_error());
			}
			
			
			
			///rfhnbyrb
			//fileImgHandler0
			//$tmpbuf =  file_exists($filteredPost['fileImgHandler'.$fih]).'!!!!!!!!!!!!!!!!!!!!!!!!!!!';
			$fih = 0;
			while (file_exists($filteredPost['fileImgHandler'.$fih])){
				$sql = "INSERT INTO  ".$modx->getFullTableName( '_catalog_images' )." (
						`id`,
						`id_item`,
						`link`
						) VALUES (
						NULL,
						'".$flagNewCatReq."', 
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
						'".$flagNewCatReq."', 
						'".$filteredPost['fileDocHandler'.$fdh]."',
						'".$filteredPost['fileDocHandlername'.$fdh]."'
						) ON DUPLICATE KEY UPDATE `link` = '".$filteredPost['fileDocHandler'.$fdh]."' ";
				$result = mysql_query($sql) or die ('Error  ADD new Cat DOC: '.mysql_error());
				$fdh++;
			}
			
			
		}
		
		
				
	}
}

/*=================================Добавление нового товара END==============================================*/


$sql = "SELECT COUNT(cat.id)
	FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
	WHERE cat.enabled =  'n'
	AND cat.seller =  '".$webuserinfo['id']."'
	LIMIT 1";
$result = mysql_query($sql) or die ('Error Select Count NoEnabled');
if ($itemsNoEnabled = mysql_fetch_row($result)[0]){
	$itemsNoEnabled = '('.$itemsNoEnabled.')';
} else {
	$itemsNoEnabled = '';
}

$sql = "SELECT COUNT(cat.id)
	FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
	WHERE cat.enabled =  'y'
	AND cat.seller =  '".$webuserinfo['id']."'
	LIMIT 1";
$result = mysql_query($sql) or die ('Error Select Count NoEnabled');
if ($itemsEnabled = mysql_fetch_row($result)[0]){
	$itemsEnabled = '('.$itemsEnabled.')';
} else {
	$itemsEnabled = '';
}

?>

<div class="vkladki_butts">
	<div class="vkldk_butt <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>" data-id="1">Список товаров <?=$itemsEnabled?></div>
	<div class="vkldk_butt <?= ( $vkladka_active == 2 ? 'active' : '' ) ?>" data-id="2">На модерации <?=$itemsNoEnabled?></div>
	<div class="vkldk_butt <?= ( $vkladka_active == 3 ? 'active' : '' ) ?>" data-id="3">Добавить новый товар</div>
	<div class="vkldk_butt <?= ( $vkladka_active == 4 ? 'active' : '' ) ?>" data-id="4">Импорт товаров</div>
	<div class="clr">&nbsp;</div>
</div>



	
	
<div class="vkladki_divs">

	
	
<!--=====================TAB1============================================================-->
<?php 
$sql = "SELECT cat.id, cat.code, cat.parent, cat.title, cat.manufacturer, cat.price, cat.currency, cat.in_stock, cat.text, cat.state, cat.packaging, cat.guarantee, cat.discount, cat.shipment, cat.tender,
			   sc.pagetitle AS firstpagetitle,  sc2.pagetitle AS secondpagetitle, rnp.category AS path1,  rnp.subcategory AS path2
	FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
	LEFT JOIN  ".$modx->getFullTableName( '_request_new_path' )." AS rnp ON rnp.id_item = cat.id
	INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc ON sc.id = cat.parent
	INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc2 ON sc.parent = sc2.id
	WHERE cat.seller =  '".$webuserinfo['id']."'
	AND cat.enabled =  'y' 
	GROUP BY cat.code";
$result = mysql_query($sql) or die ('Error Select noEnable items: '. mysql_error());


$noEnableItems = '<a href="/generatemyexcell.html" target="_blank" class="linkGetMyExcell">Скачать в формате Excel (.xls)</a>';
$noEnableItems .= '<form action="'.$topage_url.'?tab=1" name="EnabledItems" method="POST">';
$noEnableItems .= '<div class=lkItemtitle>
	<div class="checkboxsENA"><input type="checkbox" name="checkallENA" /></div>
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


	
	$printPrice = '<nobr><span class="price">'.$row['price'].'</span> <span class="">'.$row['currency'].'</span></nobr>';
	//$noEnableItems.=$row['discount'];
	if ($row['discount'] > 0) {
		//$noEnableItems.=$row['discount'];
		$printPrice =  '<div class="discountprice">
							<div class="sale">-'.$row['discount'].'%</div>
							<div class=oldprice>'.$row['price'].' '.$row['currency'].'</div>
							<div class=newprice>'.round(($row['price'] - ($row['price'] / 100 * $row['discount']))).' <span class="">'.$row['currency'].'</span></div>
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
	if ($row['path1'] !='' || $row['path2'] != '') {
		$addNewCatText = 'Вы предложили создать новый раздел:<br/>/'.$row['path1'].' / '.$row['path2'];
		$noEnableItems .= '<div class="category">'.$addNewCatText.'</div>';
	}else {
		$noEnableItems .= '<div class="category">'.$row['firstpagetitle'].'<br /> ('.$row['secondpagetitle'].')<br/> </div>';
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
								<div class="px_bottom">
									'.$croppedDocs.'
								</div>
						   </div>
						   <div class="px_right">
						   		<div class="px_top">
									<div class="px_top_left">'.$row['text'].'</div>
									<div class="px_top_right">
										<a href="'. $modx->makeUrl( 183 ).'?idcat='.$row['id'].'">Редактировать</a>
									</div>
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
	
	<div class="vkldk_div vkldk_div_1 <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
				<?=$noEnableItems?>
		</div>
	</div>
	
	
	
	
	
	
	
	
	
	
	
	<!--=====================TAB2============================================================-->
<?php 
	



$sql = "SELECT cat.id, cat.code, cat.title, cat.parent, cat.manufacturer, cat.price, cat.currency, cat.in_stock, cat.text, cat.state, cat.packaging, cat.guarantee, cat.discount, cat.shipment, cat.tender,
			   sc.pagetitle AS firstpagetitle,  sc2.pagetitle AS secondpagetitle, rnp.category AS path1,  rnp.subcategory AS path2
	FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
	LEFT JOIN  ".$modx->getFullTableName( '_request_new_path' )." AS rnp ON rnp.id_item = cat.id
	INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc ON sc.id = cat.parent
	INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc2 ON sc.parent = sc2.id
	WHERE cat.seller =  '".$webuserinfo['id']."'
	AND cat.enabled =  'n' 
	GROUP BY cat.code";
$result = mysql_query($sql) or die ('Error Select noEnable items: '. mysql_error());
$noEnableItems = '<a href="/generatemyexcell.html" target="_blank" class="linkGetMyExcell">Скачать в формате Excel (.xls)</a>';
$noEnableItems .= '<form action="'.$topage_url.'?tab=2" name="noEnabledItems" method="POST">';
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
		$croppedImgs .= '<a class="highslide sert" onclick="return hs.expand(this)" href="'. $modx->runSnippet( 'ImgCrop6', array( 'img' => $rowimg[ 'link' ], 'w' => 800, 'h' => 800, 'fill' => 0 , 'wm' =>1) ) .'">
							<img src="'. $modx->runSnippet( 'ImgCrop6', array( 'img' => $rowimg[ 'link' ], 'w' => 85, 'h' => 80, 'fill' => 0 , 'wm' =>1) ) .'" />
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
	
	
	$printPrice = '<nobr><span class="price">'.$row['price'].'</span> <span class="">'.$row['currency'].'</span></nobr>';
	//$noEnableItems.=$row['discount'];
	if ($row['discount'] > 0) {
		//$noEnableItems.=$row['discount'];
		$printPrice =  '<div class="discountprice">
							<div class="sale">-'.$row['discount'].'%</div>
							<div class=oldprice>'.$row['price'].' '.$row['currency'].'</div>
							<div class=newprice>'.round(($row['price'] - ($row['price'] / 100 * $row['discount']))).' <span class="">'.$row['currency'].'</span></div>
						</div>';
	}
	
	$addNewCatText='';
	if ($flagBgColor > 0) {$addStyle = 'fillGrayBG';} else {$addStyle = '';}
	$noEnableItems .= '<div class="lkItem '.$addStyle.'">';
	$noEnableItems .= '<div class="checkboxs"><input type="checkbox" name="deleteID['.$row['id'].']" value="'.$row['id'].'" /></div>';
	$noEnableItems .= '<div class="code">'.$row['code'].'</div>';
	$noEnableItems .= '<div class="vend">'.$row['manufacturer'].'</div>';
	$noEnableItems .= '<div class="name"><div class="type">'.$row['title'].'</div></div>';
	$noEnableItems .= '<div class="stock">'.$row['in_stock'].' шт.</div>';
	$noEnableItems .= '<div class="price">'.$printPrice.'</div>';
	if ($row['path1'] !='' || $row['path2'] != '') {
		$addNewCatText = 'Вы предложили создать новый раздел:<br/>/'.$row['path1'].' / '.$row['path2'];
		$noEnableItems .= '<div class="category">'.$addNewCatText.'</div>';
	}else {
		$noEnableItems .= '<div class="category">'.$row['firstpagetitle'].'<br /> ('.$row['secondpagetitle'].')<br/> </div>';
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
								<div class="px_bottom">
									'.$croppedDocs.'
								</div>
						   </div>
						   <div class="px_right">
						   		<div class="px_top">
						   			<div class="px_top_left">'.$row['text'].'</div>
									<div class="px_top_right">
										<a href="'. $modx->makeUrl( 183 ).'?idcat='.$row['id'].'">Редактировать</a>
									</div>
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


$noEnableItems .= '<button class="buttonsubmit2 topMargin" name="DeleteCheckedItemNoEnabled" type="submit">Удалить отмеченные</button>';
$noEnableItems .= '</form>';



?>
	<div class="vkldk_div vkldk_div_2 <?= ( $vkladka_active == 2 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<?=$noEnableItems?>
		</div>
	</div>
	
	
	
	
	
	
	
	
	
<?php 
$result= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user_warehouse' )." WHERE seller=". $webuserinfo[ 'id' ] ." AND city<>'' ORDER BY num ASC LIMIT 3" );
					//   SELECT * FROM `profzapas__user_warehouse`        			  WHERE seller=5  					   		AND city <> '' ORDER BY num ASC LIMIT 3
$htmlOptWH = '<option value="0">Нет склада</option>';
while ($row = mysql_fetch_assoc($result)){
	$htmlOptWH .='<option id="addwhid_'.$row["num"].'" value="'.$row["num"].'">'.$row['city'].' ('.$row['address'].')</option>';
}

$result= mysql_query( "SELECT id, pagetitle FROM ".$modx->getFullTableName( 'site_content' )." WHERE parent=". $catalogId ." AND id <> ".$catalogNewCatId."  ORDER BY menuindex ASC" ) or die(mysql_error());
$htmlcategoryOnelevel .='<option value="-1">Категория не выбрана</option>';
while ($row = mysql_fetch_assoc($result)){
	$htmlcategoryOnelevel .='<option value="'.$row["id"].'">'.$row['pagetitle'].'</option>';
}



?>	
		<!--=====================TAB3============================================================-->
	<div class="vkldk_div vkldk_div_3 <?= ( $vkladka_active == 3 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<div class="_LK_form">
			<?=$parseXlsError?>
			<form  action="<?=$topage_url?>?tab=3" method='POST' id="addMewItemForm">
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
								 <option>RUB</option>
								 <option>USD</option>
								 <option>EUR</option>
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
								<input type="file" name="save_item_docfile" id="uploaded_file" multiple="multiple" accept=".doc,.docx,.xls,.pdf.txt,image/*" onchange="file_selected();"/>
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
							<input class="checkbox" type="checkbox" name="save_item_pack" checked="<?= $webuserinfo[ 'item_pack' ] ?>"/>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
	
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Состояние</div>
						<div class="_LK_form_inp dashedGroup <?= ( $save_err_flag[ 'save_item_count' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="radio" class="radio" value="new" checked="checked" name="save_sost" id="sost1"/><label class="labforradio"  for="sost1">Новое</label> <div class="clr">&nbsp;</div>
							<input type="radio" class="radio" value="bu" name="save_sost" id="sost2"/><label class="labforradio" for="sost2">Б/У</label><div class="clr">&nbsp;</div>
							<input type="radio" class="radio" value="zapch" name="save_sost" id="sost3"/><label class="labforradio" for="sost3">На запчасти</label><div class="clr">&nbsp;</div>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Гарантия</div>
						<div class="_LK_form_inp dashedGroup <?= ( $save_err_flag[ 'save_item_count' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="radio" class="radio" value="yes"  checked="checked" name="save_guarant" id="guarant1"/><label class="labforradio"  for="guarant1">С гарантией</label> <div class="clr">&nbsp;</div>
							<input type="radio" class="radio" value="no" name="save_guarant" id="guarant2"/><label class="labforradio" for="guarant2">Без гарантии</label><div class="clr">&nbsp;</div>
							<label class="labfordateGuarant" for="dateGuarant">Срок гарантии (мес.)<input type="text" class="miniinput" id="dateGuarant" name="save_guarant_text" value="<?= $webuserinfo[ 'item_count' ] ?>" required/></label>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Количество</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_count' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="text"  class="miniinput" name="save_item_count" id="save_item_count"  pattern="^[ 0-9]+$" />
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				
				<div class="_LK_form_line" id="save_item_warehouse1">
						<div class="_LK_form_lbl_mini">Месторасположение товара</div>
					
						<div class="_LK_form_inp">
							<select class="smallWH miniinput" name="save_item_warehouse1"  id="select_item_warehouse1">
								<?=$htmlOptWH?>
							</select>
							<input  class="smallINSTCK miniinput" type="text" name="instock_warehouse1"  id="instock_warehouse1" pattern="^[ 0-9]+$" placeholder="" readonly  />
						</div>
						<div class="clr">&nbsp;</div>
				</div>	
				<div class="_LK_form_line" id="save_item_warehouse2" style="display:none;">	
					<div class="_LK_form_lbl"></div>
						<div class="_LK_form_inp">
							<select class="smallWH miniinput" name="save_item_warehouse2"   id="select_item_warehouse2">
								<?=$htmlOptWH?>
							</select>
							<input  class="smallINSTCK miniinput" type="text" name="instock_warehouse2"  id="instock_warehouse2" pattern="^[ 0-9]+$" placeholder="" readonly />
							
						</div>
						<div class="clr">&nbsp;</div>
				</div>	
				<div class="_LK_form_line" id="save_item_warehouse3" style="display:none;">	
					<div class="_LK_form_lbl"></div>
						<div class="_LK_form_inp ">
							<select class="smallWH miniinput" name="save_item_warehouse3"   id="select_item_warehouse3">
								<?=$htmlOptWH?>
							</select>
							<input  class="smallINSTCK miniinput" type="text" name="instock_warehouse3"  id="instock_warehouse3" pattern="^[ 0-9]+$" placeholder="" readonly />
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Способ доставки </div>
						<div class="_LK_form_inp dashedGroup <?= ( $save_err_flag[ 'save_item_count' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="radio" class="radio" value="postal"  checked="checked" name="save_dost" id="dost1"/><label class="labforradio"  for="dost1">Отправка почтой</label> <div class="clr">&nbsp;</div>
							<input type="radio" class="radio" value="human"  name="save_dost" id="dost2"/><label class="labforradio" for="dost2">Отправка курьером</label><div class="clr">&nbsp;</div>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Возможность торгов</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_tender' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input class="checkbox" type="checkbox" name="save_item_tender"/>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Распродажа</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_item_discount' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input class="checkbox" type="checkbox" name="save_item_discount" id="checkDiscount"/>
							<label style="display:none;" class="labforpercentDiscount" for="percentDiscount">Скидка (%)<input type="text" class="miniinput" id="percentDiscount" name="save_item_discount_text" value="<?= $webuserinfo[ 'item_discount_text' ] ?>" /></label>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Категория товара</div>
						<div class="_LK_form_inp dashedGroup <?= ( $save_err_flag[ 'save_item_count' ] ? '_LK_form_inp_error' : '' ) ?>">
							<input type="radio" class="radio" value="sles"  checked="checked" name="save_catType" id="catType1"/><label class="labforradio"  for="catType1">Выбор из существующих</label> <div class="clr">&nbsp;</div>
							<input type="radio" class="radio" value="new"  name="save_catType" id="catType2"/><label class="labforradio" for="catType2">Предложить новую категорию</label><div class="clr">&nbsp;</div>
						</div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Категория товара 1го уровня</div>
					
						<div class="_LK_form_inp">
							<select name="save_item_catOne"  class="miniinput" id="save_item_catOne">
								<?=$htmlcategoryOnelevel?>
							</select>
							<input type="text"  class="miniinput" name="save_item_catOne_alter" placeholder="Введите желаемую категорию товара" id="save_item_catOne_alter" value="<?= $webuserinfo[ 'item_catOne_alter' ] ?>" style="display:none;"/>
						</div>
						<div class="clr">&nbsp;</div>
				</div>	
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Категория товара 2го уровня</div>
					
						<div class="_LK_form_inp">
							<select name="save_item_catTwo"  class="miniinput" id="save_item_catTwo" required>
								
							</select>
							<input type="text"  class="miniinput" name="save_item_catTwo_alter" placeholder="Введите желаемую категорию товара" id="save_item_catTwo_alter" value="<?= $webuserinfo[ 'item_catTwo_alter' ] ?>" style="display:none;"/>
						</div>
						<div class="clr">&nbsp;</div>
				</div>	
				
				
				<div class="_LK_form_line">
						<div class="_LK_form_lbl_mini">Фото</div>
						<div class="_LK_form_inp  <?= ( $save_err_flag[ 'save_item_docfile' ] ? '_LK_form_inp_error' : '' ) ?>">
							<div class="input_file noneborder">
								<input type="file"  name="save_item_imgfile" id="uploaded_img" multiple="multiple" accept="image/*" onchange="file_selected();"/>
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
						<div class="_LK_form_inp"><button id="submitAddItem" class="mainbutton buttonsubmit" name="save_3" type="submit">Добавить</button></div>
						<div class="clr">&nbsp;</div>
				</div>
				
				
				

			</form>
			</div>


		</div>
	</div>
	
	
	
	<!--=====================TAB4============================================================-->
	<div class="vkldk_div vkldk_div_4 <?= ( $vkladka_active == 4 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<div class="_LK_form">
				<?php if( isset( $_GET[ 'ok44' ] ) ){ ?>
					<div class="_LK_ok">
						<p>Ваш файл отправлен на проверку администратору. Срок проверки - 1 рабочий день<br/>
						Скоро ваши товары появяться на сайте.</p>
					</div>
				<?php } ?>
			</div>
			<div style="width: 500px;float: left;">
				<div class="_LK_form">
					
					<?=$parseXlsError?>
					<form enctype="multipart/form-data" action="<?=$topage_url?>?tab=4" method='POST' >
						Для ипорта товаров загрузите Ваш прайс<br/> в формате Excell (*.xls или *.xlsx).<br/>
						<div class="inputtupefile"><span>Выбирите файл</span>
							<input type="file"  name="excellFile" onchange="file_selected();"/>
						</div>
						<input type="submit" name="excellFileSubmit" value="Отправить" class="mainbutton butInpForm"/>
					</form>
					<div class="clr">&nbsp;</div>
					<span class="notice">*обращием внимание, что прайс должен соответствовать указанному шаблону.</span> 
				</div>
			</div>
			<div style="width: 310px;float: left;">
				<div class="downloadTemplate">
					<div class="downloadTemplate_Tit">Докумменты для загрузки и инструкции</div>
					<a class="linkInstructions" href="#">Инструкция по заполеннию прайса</a>
					<a class="linkInstructions" href="/generateblankexcell.html" target="_blank">Скачать пустой шаблон прайса</a>
					<a class="linkInstructions" href="#">Пример заполненного прайса</a>
				</div>
			</div>
	
		</div>
	</div>
	
	<!--=====================TAB5============================================================-->
	<div class="vkldk_div vkldk_div_5 <?= ( $vkladka_active == 5 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			Экспорт
		</div>
	</div>
	
</div>
<?php
	//return $tmpbuf;
 	//return print_r($readerResult);





?>