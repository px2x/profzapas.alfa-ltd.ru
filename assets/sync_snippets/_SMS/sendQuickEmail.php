<?php

if( $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	header( 'location: '. $modx->makeUrl( $lk ) );
	exit();
}

$sc_site= 'profzapas.ru';
$mailto= 'korneva.ola@gmail.com';
$mailto_bcc= false;
$mailto= 'sasha.it6@gmail.com';
$mailfrom= 'profzapas-noreply@yandex.ru';
$mailtype= 'smtp';
$mailpassw= 'm_y642onB5pr';
//Любимый киногерой: mWdqjMJ28geu_lSJZ7_0
$smtp= 'smtp.yandex.ru';
$smtpport= 465;
include_once( MODX_MANAGER_PATH .'includes/controls/class.phpmailer.php' );


/*

$reg_email= $email;
$subject = $text;
$emailtext = 'fff';
*/


if( $mailtype == 'smtp' || $mailtype == 'mail' ){
    $phpmailer= new PHPMailer();
    if( false ){ 
        $phpmailer->SMTPDebug= 2;
        $phpmailer->Debugoutput = 'html';
    }

    if( $mailtype == 'smtp' ) {
        $phpmailer->isSMTP();
        $phpmailer->Host= $smtp;
        $phpmailer->Port= $smtpport;
        $phpmailer->SMTPAuth= true;
        $phpmailer->SMTPSecure= 'ssl';
        $phpmailer->Username= $mailfrom;
        $phpmailer->Password= $mailpassw;
    }
    $phpmailer->CharSet= 'utf-8';
    $phpmailer->From= $mailfrom;
    $phpmailer->FromName= "";
    if( $mailto_bcc ) $phpmailer->addBCC( $mailto_bcc );
    $phpmailer->isHTML( true );
    $phpmailer->Subject= $subject;
    $phpmailer->Body= $emailtext;
    $phpmailer->addAddress( $reg_email );
    $phpmailer->send();
}else{
    $mailto= "<". $reg_email .">";
    $headers= "Content-type: text/html; charset=utf-8\n";
    $headers .= "From: <". $mailfrom .">\n";
    mail( $mailto, $subject, $emailtext, $headers );
}

return true;

?>