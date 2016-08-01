<?php

//v05
//==================================================================================
$lk= 106;
$reg= 107;
$auth= 108;
$restorepassword= 109;
$agreed= 110;
$DMTCaptcha= 111;
$DMTCaptcha_img= 112;
//==================================================================================

if(! $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	header( 'location: '. $modx->makeUrl( $lk ) );
	exit();
}


$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];
$sellerDescrInfo= $_SESSION[ 'webuserinfo' ][ 'seller_descr' ];
$sellerWarehouses= $_SESSION[ 'webuserinfo' ][ 'seller_warehouses' ];
$userContactFaces= $_SESSION[ 'webuserinfo' ][ 'contact_faces' ];


include_once( MODX_MANAGER_PATH .'includes/controls/PHPExcel.php' );
include_once( MODX_MANAGER_PATH .'includes/controls/PHPExcel/IOFactory.php' );

 

$objPHPExcel = PHPExcel_IOFactory::load("template.xls");
 

$objPHPExcel->setActiveSheetIndex(0);
$active_sheet = $objPHPExcel->getActiveSheet();
 
$cellOfset = 2;




$sql = "SELECT cat.id, cat.code, cat.parent, cat.title, cat.manufacturer, cat.manufacturer_country, cat.price, cat.currency, cat.in_stock, cat.text, cat.state, cat.packaging, cat.guarantee, cat.discount, cat.shipment, cat.tender,
			   sc.pagetitle AS firstpagetitle,  sc2.pagetitle AS secondpagetitle, rnp.category AS path1,  rnp.subcategory AS path2
	FROM  ".$modx->getFullTableName( '_catalog' )." AS cat
	LEFT JOIN  ".$modx->getFullTableName( '_request_new_path' )." AS rnp ON rnp.id_item = cat.id
	INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc ON sc.id = cat.parent
	INNER JOIN ".$modx->getFullTableName( 'site_content' )." AS sc2 ON sc.parent = sc2.id
	WHERE cat.seller =  '".$webuserinfo['id']."'
	GROUP BY cat.code";
$result = mysql_query($sql) or die ('Error Select noEnable items: '. mysql_error());

while ($row = mysql_fetch_assoc($result)){

	
	if ($row['state'] == 'new'){
		$row['state'] = 'Новый';
	} elseif($row['state'] == 'bu'){
		$row['state'] = 'Б/У';
	}else {
		$row['state'] = 'На запчасти';
	}
	
	if ($row['tender'] == '1'){
		$row['tender'] = 'Да';
	} else {
		$row['tender'] = 'Нет';
	}
	
	
	if ($row['packaging'] == 'y'){
		$row['packaging'] = 'Да';
	} else {
		$row['packaging'] = 'Нет';
	}
	
	
	if ($row['shipment'] == 'postal'){
		$row['shipment'] = 'Почта';
	} else {
		$row['shipment'] = 'Курьер';
	}
	
	
	//достаем склвдыколичество адреса -----START
	$sqlWarehouse = "SELECT cwh.quantity, uwh.num
		FROM  ".$modx->getFullTableName( '_catalog_warehouse' )." AS cwh
		LEFT JOIN  ".$modx->getFullTableName( '_user_warehouse' )." AS uwh ON cwh.warehouse = uwh.id
		WHERE cwh.item =  '".$row['id']."' ";
	$resultWarehouse = mysql_query($sqlWarehouse) or die ('Error Select sqlWarehouse: '. mysql_error());
	$warehouseList = 'Нет информации о складах';
	$tmp = array( '1' => 0, '2' => 0, '3' => 0 );
	while ($rowWh = mysql_fetch_assoc($resultWarehouse)){
		$tmp [$rowWh['num']] = $rowWh['quantity'];
	}
	//достаем склвдыколичество адреса -----END
	
	
	$active_sheet->setCellValue('A'.$cellOfset, $row['code']);
	$active_sheet->setCellValue('B'.$cellOfset, $row['manufacturer']);
	$active_sheet->setCellValue('C'.$cellOfset, $row['manufacturer_country']);
	$active_sheet->setCellValue('D'.$cellOfset, $row['secondpagetitle']);
	$active_sheet->setCellValue('E'.$cellOfset, $row['firstpagetitle']);
	$active_sheet->setCellValue('F'.$cellOfset, $row['title']);
	$active_sheet->setCellValue('G'.$cellOfset, $row['text']);
	$active_sheet->setCellValue('H'.$cellOfset, $tmp[1]);
	$active_sheet->setCellValue('I'.$cellOfset, $tmp[2]);
	$active_sheet->setCellValue('J'.$cellOfset, $tmp[3]);
	$active_sheet->setCellValue('K'.$cellOfset, $row['price']);
	$active_sheet->setCellValue('L'.$cellOfset, $row['currency']);
	$active_sheet->setCellValue('M'.$cellOfset, $row['state']);
	$active_sheet->setCellValue('N'.$cellOfset, $row['guarantee']);
	//$active_sheet->setCellValue('O'.$cellOfset, $row['site']);
	$active_sheet->setCellValue('P'.$cellOfset, $row['tender']);
	$active_sheet->setCellValue('Q'.$cellOfset, $row['packaging']);
	$active_sheet->setCellValue('R'.$cellOfset, $row['shipment']);
	$active_sheet->setCellValue('S'.$cellOfset, $row['discount']);
	
	
	$cellOfset ++;

}	





header("Content-Type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename='Profzapas_".date("d-m-Y H-i").".xls'");
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

exit();





?>