<?php

//v03
//===========================================================================================
	
$session_name= session_name();
$session_id= session_id();


if( ! file_exists( MODX_BASE_PATH .'mybox/1c/'. date( 'Y-m' ) .'/' ) ) mkdir( MODX_BASE_PATH .'mybox/1c/'. date( 'Y-m' ) .'/', 0777, true );


$rr= mysql_query( "SELECT * FROM ". $modx->getFullTableName( '_1c_' ) ." WHERE `check`='import' LIMIT 1" );
if( ! $rr || mysql_num_rows( $rr ) > 0 ) return "failure\n";


mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::000:: {$session_id}'" ); // LOG

if( $_GET[ 'type' ] == 'catalog' )
{
	if( $_GET[ 'mode' ] == 'checkauth' )
	{
		print "success\n";
		print $session_name ."\n";
		print $session_id ."\n";
		
		mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::checkauth::'" ); // LOG
		
		
	}elseif( $_GET[ 'mode' ] == 'init' ){
		print "zip=no\n";
		print "file_limit=".( 1024*1024*5 )."\n";
		
		mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::init::'" ); // LOG
		
		
	}elseif( $_GET[ 'mode' ] == 'file' && ! empty( $_GET[ 'filename' ] ) ){
		$filename= trim( $_GET[ 'filename' ] );
		
		mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::001:: {$filename}'" ); // LOG
		
		$rassh= explode( ".", $filename );
		$rassh= $rassh[ count( $rassh ) - 1 ];
		
		if( $rassh == 'xml' )
			$filename= $_SESSION[ '1c' ][ 'time' ] .'__'. $filename;
		
		$file= '';
		if( $_SESSION[ '1c' ][ 'catalog_file' ][ $filename ][ 'step' ] != 2 )
		{
			if( $rassh == 'xml' )
			{
				$file= MODX_BASE_PATH .'mybox/1c/'. date( 'Y-m' ) .'/'. $filename;
				
				mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::002:: {$file}'" ); // LOG
				
				if( ! file_exists( $file ) )
				{
					mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::003:: {$file}'" ); // LOG
					
					$_SESSION[ '1c' ][ 'catalog_file' ][ $filename ][ 'step' ]= 2;
					$_SESSION[ '1c' ][ 'catalog_file' ][ $filename ][ 'filepath' ]= $file;
					
					mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_' ) ." SET filename='". date( 'Y-m' ) ."/{$filename}',
						session='{$session_id}', `check`='progress', dth='". date( 'Y-m-d-H-i-s' ) ."', dt=". time() );
				}else{
					$file= '';
				}
				
			}elseif( true ){
				$filename2= md5( $filename ) .'.'. $rassh;
				$fldr= 'x'.substr( $filename2, 0, 2 );
				$file= MODX_BASE_PATH .'assets/images/1c/'. $fldr .'/';
				if( ! file_exists( $file ) ) mkdir( $file, 0777, true );
				$file .= $filename2;
				if( file_exists( $file ) ) unlink( $file );
				mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::001-2:: {$file}'" ); // LOG
				$_SESSION[ '1c' ][ 'catalog_file' ][ $filename ][ 'step' ]= 2;
				$_SESSION[ '1c' ][ 'catalog_file' ][ $filename ][ 'filepath' ]= $file;
			}
			
		}else{
			$file= $_SESSION[ '1c' ][ 'catalog_file' ][ $filename ][ 'filepath' ];
			
			mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::004:: {$file}'" ); // LOG
		}
		
		if( ! empty( $file ) )
		{
			mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::005:: {$file}'" ); // LOG
			
			if( $fp= fopen( $file, "ab" ) )
			{
				$post_data= file_get_contents( "php://input" );
				
				if( $post_data !== false && ! empty( $post_data ) )
				{
					$byte= fwrite( $fp, $post_data );
					
					if( $byte )
					{
						mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::006:: {$file}'" ); // LOG
						print "success\n";
					}
				}else{
					mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::007:: {$file}'" ); // LOG
					print "failure\n";
				}
			}else{
				mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::008:: {$file}'" ); // LOG
				print "failure\n";
			}
		}else{
			mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::009:: {$file}'" ); // LOG
			print "failure\n";
		}
		
		
	}elseif( $_GET[ 'mode' ] == 'import' ){
		if( $_SESSION[ '1c' ][ 'step' ] != 3 )
		{
			mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='catalog::import::'" ); // LOG
			
			$_SESSION[ '1c' ][ 'step' ]= 3;
			
			mysql_query( "UPDATE ". $modx->getFullTableName( '_1c_' ) ." SET `check`='new' WHERE session='{$session_id}'" );
			
			file_get_contents( $modx->makeUrl( 29, '', '', 'full' ) );
		}
		
		print "success\n";
	}else{
		print "failure\n";
	}
}else{
	print "failure\n";
}





?>