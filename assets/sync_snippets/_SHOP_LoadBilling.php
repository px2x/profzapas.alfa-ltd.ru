<?php

//echo phpinfo();
header("Content-Type:text/html; charset=WIN-1251");

error_reporting(E_ALL);

require_once __DIR__ . '/PhpWord/Autoloader.php';

use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;


define('CLI', (PHP_SAPI == 'cli') ? true : false);
define('EOL', CLI ? PHP_EOL : '<br />');
define('SCRIPT_FILENAME', basename($_SERVER['SCRIPT_FILENAME'], '.php'));
define('IS_INDEX', SCRIPT_FILENAME == 'index');

echo SCRIPT_FILENAME.'<br>';
echo IS_INDEX.'<br>';
echo EOL.'<br>';
echo CLI.'<br>';

Autoloader::register();
Settings::loadConfig();


$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('template2.docx');

//require_once __DIR__ . '/PhpWord/IOFactory.php';

//$writer = PHPWord_IOFactory::createWriter($templateProcessor, 'Word2007');
//$writer->save('php://output');


//header("Content-Type:application/vnd.openxmlformats-officedocument.wordprocessingml.document");
//header("Content-Disposition:attachment;filename='schet_".date("d-m-Y H-i").".docx'");
$templateProcessor->saveAs('php://output');



/*
$templateProcessor->setValue('orderNumber', '111111');
$templateProcessor->setValue('orderDate' , '2222222');
$templateProcessor->setValue('nameBuyer' , 'ooo Roga  Kopita');
$templateProcessor->setValue('bankData' , 'rekvizitiy Roga Kopita');


$countitem = 3;


$templateProcessor->cloneRow('nameItem', $countitem);

for ($i = 1 ; $i<=$countitem ; $i++){
    $templateProcessor->setValue('positionNumber#'.$i , htmlspecialchars('p'.$i));
    $templateProcessor->setValue('nameItem#'.$i , htmlspecialchars('nameItem'.$i));
    $templateProcessor->setValue('unitsMetr#'.$i , htmlspecialchars('unitsMetr'.$i));
    $templateProcessor->setValue('counts#'.$i , htmlspecialchars('counts'.$i));
    $templateProcessor->setValue('oneItemPrice#'.$i , htmlspecialchars('oneItemPrice'.$i));
    $templateProcessor->setValue('sumItemPrice#'.$i , htmlspecialchars('sumItemPrice'.$i));   
}



*/



//header("Content-Type:application/vnd.openxmlformats-officedocument.wordprocessingml.document");
//header("Content-Disposition:attachment;filename='schet_".date("d-m-Y H-i").".docx'");


//$templateProcessor->saveAs('php://output');





?>