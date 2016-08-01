<?php

//v05
//==================================================================================
$lk= 106;
$reg= 107;
$auth= 108;
$restorepassword= 109;
$agreed= 110;
$finances = 120;
$DMTCaptcha= 111;
$DMTCaptcha_img= 112;
//==================================================================================
?>
<link rel="stylesheet" type="text/css" href="template/css/lk.css" />
<script type="text/javascript" src="template/js/lk.js"></script>
<script type="text/javascript" src="template/js/jquery-maskedinput.js"></script>
<?php
//==================================================================================
	
$topage= ( $topage ? intval( $topage ) : $modx->documentIdentifier );
$topage_url= $modx->makeUrl( $topage );
$topage_url= $modx->makeUrl( $topage );

///if( isset( $_GET[ 'exit' ] ) && $_SESSION[ 'webuserinfo' ][ 'auth' ] )
if( isset( $_GET[ 'exit' ] )  )
{
	$_SESSION[ 'webuserinfo' ]= array( 'auth' => '0' );
	//$_SESSION[ 'webuserinfo' ]= '';
	header( 'location: '. $modx->makeUrl( 1 ));
	exit();
}

//==================================================================================


if( $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." AND enabled='y' LIMIT 1" );
	if( $rr && mysql_num_rows( $rr ) == 1 )
	{
		$_SESSION[ 'webuserinfo' ][ 'info' ]= mysql_fetch_assoc( $rr );
		
		$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user_contact' )." WHERE user=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." ORDER BY num" );
		if( $rr && mysql_num_rows( $rr ) > 0 )
		{
			while( $row= mysql_fetch_assoc( $rr ) )
			{
				$_SESSION[ 'webuserinfo' ][ 'info' ][ 'contacts' ][ $row[ 'num' ] ]= $row;
			}
		}
		
		$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user_warehouse' )." WHERE seller=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." ORDER BY num" );
		if( $rr && mysql_num_rows( $rr ) > 0 )
		{
			while( $row= mysql_fetch_assoc( $rr ) )
			{
				$_SESSION[ 'webuserinfo' ][ 'info' ][ 'wh' ][ $row[ 'num' ] ]= $row;
			}
		}
		
	}else{
		$_SESSION[ 'webuserinfo' ]= array('auth'=>false);
		header( 'location: '. $topage_url );
		exit();
	}
}


$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];


print '<div class="_LK_topuserpanel">';


if( $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	


	
	print 'Здравствуйте, '. $webuserinfo[ 'firstname' ] .' '. $webuserinfo[ 'surname' ] .' &nbsp; | &nbsp; <a class="as2" href="[~'. $lk .'~]">Личный кабинет</a> &nbsp; | &nbsp; <a class="as2" href="[~[*id*]~]?exit">Выйти</span></a>';
}else{
	print '<a class="as2" href="[~'. $auth .'~]">Войдите</a> или <a class="as2" href="[~'. $reg .'~]">зарегистрируйтесь</a>';
}
print '</div>';





?>