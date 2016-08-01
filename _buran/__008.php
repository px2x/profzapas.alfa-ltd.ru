<?php
// Buran_008 - Etalon
// scorN - v.1.3
// 07.01.2016
// Буран
//====================================================================================================
	error_reporting(0);
	                                                                                                    
	                                                                                                    
	                                                                                                    
	                                                                                                    
	                                                                                                    
	                                                                                                    
	                                                                                                    
	                                                                                  
//====================================================================================================
	define( '_DS', DIRECTORY_SEPARATOR );
	$root= $_SERVER[ 'DOCUMENT_ROOT' ];
	$root= __FILE__;
	$scriptname= $_SERVER[ 'SCRIPT_NAME' ];
	if( strstr( $root, "\\" ) ) $scriptname= str_replace( "/", "\\", $scriptname );
	$root= str_replace( $scriptname, '', $root );
	$host= str_replace( "www.", "", $_SERVER[ 'HTTP_HOST' ] );
	
	$ww= @file_get_contents( "http://fndelta.gavrishkin.ru/__password__002.php?host=". $host ."&w=". $_GET[ 'w' ] );
	if( ! $ww || $_GET[ 'w' ] == '' || $_GET[ 'w' ] != $ww ){ print '[[FAIL]]'; exit(); }
//====================================================================================================
	if( isset( $_GET[ 'dir' ] ) ) $dir= trim( urldecode( $_GET[ 'dir' ] ) ); else $dir= _DS;
		if( substr( $dir, 0, 1 ) != _DS ) $dir= _DS . $dir;
		if( substr( $dir, strlen( $dir )-1, 1 ) != _DS ) $dir .= _DS;
	$root_dir= $root . $dir;
//====================================================================================================
	$act= $_GET[ 'act' ];
	$etalonfile= $_GET[ 'etalon' ];
	
	$b= "\n";
	$br= "<br />";
//====================================================================================================
if( $act == 'etalon' )
{
	$ff= fopen( $root .( $etalonfile ? ( substr( $etalonfile, 1, 1 ) != '/' ? '/' : '' ) . $etalonfile : '/_buran/b008/etalon.txt' ), 'r' );
	
	if( ! $ff ){ print '[[ERROR_ETALON_FILE]]' .$br; exit(); }
	
	$serialize= '';
	while( ! feof( $ff ) ) $serialize .= fread( $ff, 1024*100 );
	fclose( $ff );
	$etalon= unserialize( $serialize );
	$print= '[[START]]' .$br;
	buran( "" );
	
	$print_1 .= '<h2>Нет файла из эталона</h2>';
	foreach( $etalon AS $file => $info )
	{
		if( ! file_exists( ( isset( $_GET[ 'root' ] ) ? '' : $root_dir ) . $file ) )
		{
			$print_1 .= '<div style="padding-bottom:2px;">';
			$print_1 .= '<span style="text-decoration:none;color:#0042ff;font-family:arial;font-size:16px;">'. ( isset( $_GET[ 'root' ] ) ? '' : $root_dir ) . $file .'</span>' .$br;
			$print_1 .= '</div>';
		}
	}
	
	print $print .$br;
	print '<h2>Файл не совпадает с эталоном</h2>';
	print $print_1 .$br;
	print '<h2>Файла нет в эталоне</h2>';
	print $print_2 .$br;
	print '<h2>Файл совпадает с эталоном</h2>';
	print $print_3 .$br;
	if( isset( $_GET[ 'print' ] ) ){ print '<pre>'; print_r( $etalon ); print '</pre>'; }
}
if( $act == 'create' )
{
	$etalon= array();
	etalon( "", $etalon );
	$serialize= serialize( $etalon );
	@mkdir( $root .'/_buran/b008/', 0777 );
	$ff= fopen( $root .'/_buran/b008/etalon.txt', 'w' );
	$ff_r= fwrite( $ff, $serialize );
	fclose( $ff );
	print '[[OK]]';
}

	
//====================================================================================================

//====================================================================================================

function buran( $dir )
{
	global $root_dir;
	global $root;
	global $etalon;
	global $print;
	global $print_1;
	global $print_2;
	global $print_3;
	global $br;
	
	if( $open= opendir( $root_dir . $dir ) )
	{
		while( $file= readdir( $open ) )
		{
			if( ! is_dir( $root_dir . $dir . $file ) )
			{
				$rassh= substr( strrchr( $file, "." ), 1 );
				if( $rassh != 'php' ) continue 1;
				$stat= stat( $root_dir . $dir . $file );
				$md5= md5_file( $root_dir . $dir . $file );
				if( ! $etalon[ ( isset( $_GET[ 'root' ] ) ? $root.'/' : '' ) . $dir . $file ][ 'md5' ] )
				{
					$print_2 .= '<div style="padding-bottom:2px;">';
					$print_2 .= '<span style="color:#999;font-family:arial;font-size:12px;">'. date( 'd-m-Y, H:i', filectime( $root_dir . $dir . $file ) ) .' - </span>';
					$print_2 .= '<a style="text-decoration:none;color:#555;font-family:arial;font-size:12px;" target="_blank" href="__001.php?act=printfile&w='. $_GET[ 'w' ] .'&dir=/&file='. $dir . $file .'">'. $dir . $file .'</a>' .$br;
					$print_2 .= '</div>';
					
				}elseif( $etalon[ ( isset( $_GET[ 'root' ] ) ? $root.'/' : '' ) . $dir . $file ][ 'md5' ] != $md5 || $etalon[ ( isset( $_GET[ 'root' ] ) ? $root.'/' : '' ) . $dir . $file ][ 'sz' ] != $stat[ 'size' ] ){
					$print_1 .= '<div style="padding-bottom:2px;">';
					$print_1 .= '<span style="color:#555;font-family:arial;font-size:16px;">'. date( 'd-m-Y, H:i', filectime( $root_dir . $dir . $file ) ) .' - </span>';
					$print_1 .= '<a style="text-decoration:none;color:#db0000;font-family:arial;font-size:16px;" target="_blank" href="__001.php?act=printfile&w='. $_GET[ 'w' ] .'&dir=/&file='. $dir . $file .'">'. $dir . $file .'</a>' .$br;
					$print_1 .= '</div>';
					
				}elseif( isset( $_GET[ 'green' ] ) ){
					$print_3 .= '<div style="padding-bottom:2px;">';
					$print_3 .= '<span style="color:#555;font-family:arial;font-size:16px;">'. date( 'd-m-Y, H:i', filectime( $root_dir . $dir . $file ) ) .' - </span>';
					$print_3 .= '<a style="text-decoration:none;color:#39cb00;font-family:arial;font-size:16px;" target="_blank" href="__001.php?act=printfile&w='. $_GET[ 'w' ] .'&dir=/&file='. $dir . $file .'">'. $dir . $file .'</a>' .$br;
					$print_3 .= '</div>';
				}
			}elseif( is_link( $root_dir . $dir . $file ) ){
				//
			}elseif( $file != "." && $file != ".." ){
				buran( $dir . $file . _DS );
			}
		}
	}
}

function etalon( $dir, &$etalon )
{
	global $root_dir;
	if( $open= opendir( $root_dir . $dir ) )
	{
		while( $file= readdir( $open ) )
		{
			if( ! is_dir( $root_dir . $dir . $file ) )
			{
				$stat= stat( $root_dir . $dir . $file );
				$etalon[ $dir . $file ]= array(
					'md5' => md5_file( $root_dir . $dir . $file ),
					'sz' => $stat[ 'size' ],
				);
			}elseif( is_link( $root_dir . $dir . $file ) ){
				//
			}elseif( $file != "." && $file != ".." ){
				etalon( $dir . $file . _DS, $etalon );
			}
		}
	}
}