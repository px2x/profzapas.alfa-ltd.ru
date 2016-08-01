<?php

/* v.6 */
//function ImgCrop( $img, $w=0, $h=0, $backgr=false, $fill=false, $x='center', $y='center', $rgba='255,255,255,127', $wm=false, $filter=-1, $png=false, $refresh=false )
/*
	$img=
	$w=
	$h=
	$backgr=
	$fill=
	$x=
	$y=
	$rgba=
	$wm=
	$filter=
	$png=
	$r=
	$ellipse=
	$dopimg=
	$toimg=
	$quality=
    ####pxpx
*/
//===============================================================================================================================
	
	$img= urldecode( $img );
	$w= ( empty( $w ) ? 0 : $w );
	$h= ( empty( $h ) ? 0 : $h );
	$backgr= ( empty( $backgr ) ? false : $backgr );
	$fill= ( empty( $fill ) ? false : $fill );
	$x= ( empty( $x ) ? 'center' : $x );
	$y= ( empty( $y ) ? 'center' : $y );
	$rgba= ( empty( $rgba ) ? '255,255,255,127' : $rgba );
	$wm= ( empty( $wm ) ? false : $wm );
	$png= ( empty( $png ) ? false : $png );
	$filter= ( empty( $filter ) ? -1 : $filter );
	$refresh= ( empty( $r ) ? false : true );
	$slash= ( substr( $img, 0, 1 ) != "/" ? true : false );
	$root= rtrim( MODX_BASE_PATH, "/\\" ) . ( $slash ? '/' : '' );
	$img= trim( $img );

	$quality= intval( $quality );
	$quality= ( empty( $quality ) || $quality < 0 || $quality > 100 ? 100 : $quality );

	$ellipse= ( $ellipse == 'max' ? 'max' : intval( $ellipse ) );
	if( $dopimg ) $dopimg= $root . ( $slash ? '' : '/' ) . urldecode( $dopimg );
	
	$toimg= urldecode( trim( $toimg ) );
	if( $toimg )
	{
		if( substr( $toimg, 0, 1 ) == "/" && $slash ) $toimg= substr( $toimg, 1 );
		if( substr( $toimg, 0, 1 ) != "/" && ! $slash ) $toimg= "/". $toimg;
	}
	
	$ipathnotphoto= ( $slash ? '' : '/' ) . 'template/images/notphoto.png';
	$ipathwatermark= ( $slash ? '' : '/' ) . 'template/images/profzapas_wm2.png'; // ТОЛЬКО .PNG
	
	if( ! file_exists( $root . $img ) || ! is_file( $root . $img ) )
	{
		$img= $ipathnotphoto;
		if( $fill ){ $fill= false; $backgr= true; $rgba= '1:1'; }
	}
	if( ! file_exists( $root . $img ) || ! is_file( $root . $img ) ) return false;
	if( $wm && ( ! file_exists( $root . $ipathwatermark ) || ! is_file( $root . $ipathwatermark ) ) )
	{
		$wm= false;
		$img= $ipathnotphoto;
	}

	if( ! $toimg )
	{
		$imgrassh= explode( ".", $img );
		$imgrassh= $imgrassh[ count( $imgrassh )-1 ];
		$newimg= '_th'. md5( $img . $w . $h . $backgr . $fill . $x . $y . $rgba . $wm . $filter . $ellipse . $dopimg . $quality ) . ( $png ? '.png' : '.'. $imgrassh );
		
		$imgarr= explode( "/", $img );
		unset( $imgarr[ count( $imgarr )-1 ] );
		foreach( $imgarr AS $val )
		{
			$newimg_dir .= $val ."/";
		}
		$newimg_dir .= '.th/';
		if( ! file_exists( $root . $newimg_dir ) )
			@mkdir( $root . $newimg_dir, 0777 );
		
		$newimg_path= $root . $newimg_dir . $newimg;
		$newimg_path_return= ( $fullpath ? MODX_SITE_URL : '' ) . $newimg_dir . $newimg;
		
	}else{
		$newimg_path= $toimg;
		$newimg_path_return= ( $fullpath ? MODX_SITE_URL : '' ) . $toimg;
	}
	
	if( ! file_exists( $newimg_path ) || filemtime( $root . $img ) > filemtime( $newimg_path ) ) $refresh= true;
	if( filesize( $root . $img ) > 1024*1024*10 ) return $img;
// ======================================================

	if( $refresh )
	{
		$img1_info= @getimagesize( $root . $img );
		if( ! $img1_info[ 1 ] ) return false;
		$ot= $img1_info[ 0 ] / $img1_info[ 1 ];
		$dstW= ( $w > 0 ? $w : $img1_info[ 0 ] );
		$dstH= ( $h > 0 ? $h : $img1_info[ 1 ] );
		$dstX= 0;
		$dstY= 0;
		$srcW= $img1_info[ 0 ];
		$srcH= $img1_info[ 1 ];
		$srcX= 0;
		$srcY= 0;
		if( $fill )
		{
			$srcW= $img1_info[ 0 ];
			$srcH= round( $img1_info[ 0 ] / ( $dstW / $dstH ) );
			if( $srcH > $img1_info[ 1 ] )
			{
				$srcW= round( $img1_info[ 1 ] / ( $dstH / $dstW ) );
				$srcH= $img1_info[ 1 ];
			}
			if( $x == 'center' ) $srcX= round( ( $img1_info[ 0 ] - $srcW ) / 2 );
			if( $x == 'right' ) $srcX= $img1_info[ 0 ] - $srcW;
			if( $y == 'center' ) $srcY= round( ( $img1_info[ 1 ] - $srcH ) / 2 );
			if( $y == 'bottom' ) $srcY= $img1_info[ 1 ] - $srcH;
		}else{
			if( ( $img1_info[ 0 ] > $w && $w > 0 ) || ( $img1_info[ 1 ] > $h && $h > 0 ) )
			{
				$dstH= round( $dstW / $ot );
				
				if( $dstH > $h && $h > 0 )
				{
					$dstH= $h;
					$dstW= round( $dstH * $ot );
				}
			}else{
				$dstW= $img1_info[ 0 ];
				$dstH= $img1_info[ 1 ];
			}
			if( $backgr )
			{
				if( $dstW < $w )
				{
					if( $x == 'center' ) $dstX= round( ( $w - $dstW ) / 2 );
					if( $x == 'right' ) $dstX= $w - $dstW;
				}
				if( $dstH < $h )
				{
					if( $y == 'center' ) $dstY= round( ( $h - $dstH ) / 2 );
					if( $y == 'bottom' ) $dstY= $h - $dstH;
				}
			}
		}
		$crW= ( $backgr && $w > 0 ? $w : $dstW );
		$crH= ( $backgr && $h > 0 ? $h : $dstH );
		if( strstr( $rgba, "," ) )
		{
			$rgba_arr= explode( ",", $rgba );
			for( $kk=0; $kk<=3; $kk++ )
			{
				$rgba_arr[ $kk ]= intval( $rgba_arr[ $kk ] );
				if( $kk <= 2 && ( $rgba_arr[ $kk ] < 0 || $rgba_arr[ $kk ] > 255 ) ) $rgba_arr[ $kk ]= 255;
				if( $kk == 3 && ( $rgba_arr[ $kk ] < 0 || $rgba_arr[ $kk ] > 127 ) ) $rgba_arr[ $kk ]= 127;
			}
			$rgba= 'rgba';
		}else{
			$coord_arr= explode( ":", $rgba );
			$rgba= 'coord';
		}
//========================================================================================
		
		if( $img1_info[ 2 ] == 1 ) $img1= @imagecreatefromgif( $root . $img );
			
		elseif( $img1_info[ 2 ] == 2 ) $img1= @imagecreatefromjpeg( $root . $img );
			
		elseif( $img1_info[ 2 ] == 3 ){
			$img1= @imagecreatefrompng( $root . $img );
			$png= true;
		}
		
		if( $rgba == 'coord' )
		{
			$col= imagecolorat( $img1, $coord_arr[ 0 ], $coord_arr[ 1 ] );
			$rgba= imagecolorsforindex( $img1, $col );
			$rgba_arr[ 0 ]= $rgba[ 'red' ];
			$rgba_arr[ 1 ]= $rgba[ 'green' ];
			$rgba_arr[ 2 ]= $rgba[ 'blue' ];
			$rgba_arr[ 3 ]= $rgba[ 'alpha' ];
		}
		
		$img2= @ImageCreateTrueColor( $crW, $crH );
		
		if( $png )
		{
			@imagealphablending( $img2, true );
			@imagesavealpha( $img2, true );
			$col= @imagecolorallocatealpha( $img2, $rgba_arr[ 0 ], $rgba_arr[ 1 ], $rgba_arr[ 2 ], $rgba_arr[ 3 ] );
		}else{
			$col= @imagecolorallocate( $img2, $rgba_arr[ 0 ], $rgba_arr[ 1 ], $rgba_arr[ 2 ] );
		}
		
		@imagefill( $img2, 0,0, $col );
		@imagecopyresampled( $img2, $img1, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH );
		
		if( $wm )
		{
			$wm_info= @getimagesize( $root . $ipathwatermark );
			$img3= @imagecreatefrompng( $root . $ipathwatermark );
			$wm_ot= $wm_info[ 0 ] / $wm_info[ 1 ];
			$wmW= $wm_info[ 0 ];
			$wmH= $wm_info[ 1 ];
			if( $crW < $wm_info[ 0 ] )
			{
				$wmW= $crW - round( $crW / 30 );
				$wmH= round( $wmW / $wm_ot );
			}
			if( $crH < $wmH )
			{
				$wmH= $crH - round( $crH / 30 );
				$wmW= round( $wmH * $wm_ot );
			}
			$wmX= round( ( $crW - $wmW ) / 2 );
			$wmY= round( ( $crH - $wmH ) / 2 );
			@imagecopyresampled( $img2, $img3, $wmX, $wmY, 0, 0, $wmW, $wmH, $wm_info[ 0 ], $wm_info[ 1 ] );
			@imagedestroy( $img3 );
		}
		
		$filter= explode( '|', $filter );
		if( ! empty( $filter ) )
		{
			foreach( $filter AS $row )
			{
				$tmp= explode( ';', $row );
				if( $tmp[ 0 ] == 2 || $tmp[ 0 ] == 3 || $tmp[ 0 ] == 10 ) imagefilter( $img2, $tmp[ 0 ], $tmp[ 1 ] );
				elseif( $tmp[ 0 ] == 4 ) imagefilter( $img2, $tmp[ 0 ], $tmp[ 1 ], $tmp[ 2 ], $tmp[ 3 ], $tmp[ 4 ] );
				elseif( $tmp[ 0 ] == 11 ) imagefilter( $img2, $tmp[ 0 ], $tmp[ 1 ], $tmp[ 2 ] );
				else imagefilter( $img2, $tmp[ 0 ] );
			}
		}
		
		if( $ellipse )
		{
			$degstep= ( $degstep ? intval( $degstep ) : 5 );
			$w= ( $crW > $crH ? $crH : $crW );
			$cntr= ($w/2);
			$coord= array();
			$opacitycolor= imagecolorallocatealpha( $img2, 255, 255, 255, 127 );
			if( $ellipse == 'max' ) $ellipse_r= $cntr-1; else $ellipse_r= $ellipse;
			for( $part=1; $part<=4; $part++ )
			{
				for( $deg=0; $deg<90; $deg+=$degstep )
				{
					$mydeg= $deg;
					if( $part == 2 || $part == 4 ) $mydeg= 90 - $deg;
					if( ! $coord[ $mydeg ][ 'x' ] ) $coord[ $mydeg ][ 'x' ]= round( $ellipse_r * cos( deg2rad( $mydeg ) ) );
					if( ! $coord[ $mydeg ][ 'y' ] ) $coord[ $mydeg ][ 'y' ]= round( $ellipse_r * sin( deg2rad( $mydeg ) ) );
					$x= $coord[ $mydeg ][ 'x' ];
					$y= $coord[ $mydeg ][ 'y' ];
					if( $part == 4 ){ $y *= -1; }
					if( $part == 3 ){ $x *= -1; $y *= -1; }
					if( $part == 2 ){ $x *= -1; }
					$points[]= $cntr + $x;
					$points[]= $cntr + $y;
				}
			}
			$points[]= $cntr + $ellipse_r; $points[]= $cntr;
			$points[]= $w; $points[]= $cntr;
			$points[]= $w; $points[]= $w;
			$points[]= 0; $points[]= $w;
			$points[]= 0; $points[]= 0;
			$points[]= $w; $points[]= 0;
			$points[]= $w; $points[]= $cntr;
			$png= true;
			imagealphablending( $img2, false );
			imagesavealpha( $img2, true );
			imagefilledpolygon( $img2, $points, count($points)/2, $opacitycolor );
			//$autrum= imagecolorallocate( $img2, 216, 181, 85 );
			//imageellipse( $img2, $cntr, $cntr, $ellipse_r*2, $ellipse_r*2, $autrum );
		}
		
		if( $dopimg )
		{
			if( $dopimg_xy )
			{
				$dopimg_xy= explode( ":", $dopimg_xy );	
			}
			imagealphablending( $img2, true );
			imagesavealpha( $img2, true );
			$dopimg_info= @getimagesize( $dopimg );
			$img3= @imagecreatefrompng( $dopimg );
			$diX= round( ( $crW - $dopimg_info[ 0 ] ) / 2 ) + ( $dopimg_xy[ 0 ] ? intval( $dopimg_xy[ 0 ] ) : 0 );
			$diY= round( ( $crH - $dopimg_info[ 1 ] ) / 2 ) + ( $dopimg_xy[ 1 ] ? intval( $dopimg_xy[ 1 ] ) : 0 );
			@imagecopyresampled( $img2, $img3, $diX, $diY, 0, 0, $dopimg_info[ 0 ], $dopimg_info[ 1 ], $dopimg_info[ 0 ], $dopimg_info[ 1 ] );
			@imagedestroy( $img3 );
		}
		
		if( $png ){
			@imagepng( $img2, $newimg_path );
		}elseif( $img1_info[ 2 ] == 1 ){
			@imagegif( $img2, $newimg_path, $quality );
		}elseif( $img1_info[ 2 ] == 2 ){
			@imagejpeg( $img2, $newimg_path, $quality );
		}
		@chmod( $newimg_path, 0777 );
		@imagedestroy( $img1 );
		@imagedestroy( $img2 );
	}

	return $newimg_path_return;





?>