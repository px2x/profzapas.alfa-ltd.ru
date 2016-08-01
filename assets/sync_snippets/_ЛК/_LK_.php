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
	
$topage_url= $modx->makeUrl( $modx->documentIdentifier );

if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	header( 'location: '. $modx->makeUrl( $auth ) );
	exit();
}
//==================================================================================





?>