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

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$active_sheet = $objPHPExcel->getActiveSheet();
$objPHPExcel->createSheet();

$active_sheet->getPageSetup()
		->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$active_sheet->getPageSetup()
			->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$active_sheet->setTitle("Прайс-лист"); 

$active_sheet->setCellValue('A1','Код');
$active_sheet->setCellValue('B1','Производитель');
$active_sheet->setCellValue('C1','Категория 1го уровня');
$active_sheet->setCellValue('D1','Категория 2го уровня');
$active_sheet->setCellValue('E1','Наименование');
$active_sheet->setCellValue('F1','Описание');
$active_sheet->setCellValue('G1','Количество');
$active_sheet->setCellValue('H1','Цена');
$active_sheet->setCellValue('I1','Валюта');
$active_sheet->setCellValue('J1','Склад');
$active_sheet->setCellValue('K1','Адрес склада');
$active_sheet->setCellValue('L1','Наличие');
$active_sheet->setCellValue('M1','Состояние');
$active_sheet->setCellValue('N1','Гарантия');
$active_sheet->setCellValue('O1','Сайт производителя');
$active_sheet->setCellValue('P1','Возможность торгов');
$active_sheet->setCellValue('Q1','Фотография 1');
$active_sheet->setCellValue('R1','Фотография 2');
$active_sheet->setCellValue('S1','Фотография 3');
$active_sheet->setCellValue('T1','Фотография 4');
$active_sheet->setCellValue('U1','Фотография 5');
$active_sheet->setCellValue('V1','Фотография 6');

for ($i = 'A'; $i<='U'; $i++){
	$active_sheet->getColumnDimension($i)->setAutoSize(true);
}


header("Content-Type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename='Profzapas_template.xls'");
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

exit();





?>