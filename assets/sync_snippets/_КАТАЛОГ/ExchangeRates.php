<?php

//v01
//=============================================================

$file= MODX_BASE_PATH .'mybox/ExchangeRates.txt';

$currency_num= array(
	'eur' => 2,
	'usd' => 3,
);

$c= $currency_num[ $c ];

$currency= array(
	2 => array( 40, 'R01239', 978 ),
	3 => array( 30, 'R01235', 840 ),
);


$_SESSION[ 'ExchangeRates' ]= array();

if( ! $_SESSION[ 'ExchangeRates' ][ 2 ] || ! $_SESSION[ 'ExchangeRates' ][ 3 ] || ! $_SESSION[ 'ExchangeRates' ][ 0 ] || time() - $_SESSION[ 'ExchangeRates' ][ 1 ] >= 60*60*24 )
{
	
	
	$currency_from_file= array();
	if( file_exists( $file ) )
	{
		$fp= fopen( $file, 'r' );
		if( $fp )
		{
			while( ! feof( $fp ) )
			{ 
				$currency_from_file[]= fgets( $fp ); 
			}
			fclose( $fp ); 
		}
	}
	if( ! $currency_from_file[ 0 ] || time() - $currency_from_file[ 1 ] >= 60*60*24 )
	{
		$info= @simplexml_load_file( 'http://cbr.ru/scripts/XML_daily.asp' ); //http://cbr.ru/scripts/XML_daily.asp?date_req=23/02/2016'
		if( $info )
		{
			foreach( $info->{Valute} AS $row )
			{
				foreach( $currency AS $key2 => $row2 )
				{
					if( $row[ 'ID' ] == $row2[ 1 ] )
					{
						$val= $row->{Value};
						$val= str_replace( ",", ".", $val );
						$val= round( $val, 2 );
						$currency[ $key2 ][ 0 ]= $val;
						$currency[ $key2 ][ 3 ]= $row->{CharCode};
						$currency[ $key2 ][ 4 ]= $row->{Name};
					}
				}
			}
		}
		
		$fp= fopen( $file, 'w' );
		if( $fp )
		{
			$tmp= time() ."\n";
			$tmp .= mktime( 0,0,0, date( 'm' ), date( 'd' ), date( 'Y' ) ) ."\n";
			$tmp .= $currency[ 2 ][ 0 ] ."\n";
			$tmp .= $currency[ 3 ][ 0 ];
			fwrite( $fp, $tmp );
			fclose( $fp );	
		}
		
	}else{
		$currency[ 2 ][ 0 ]= $currency_from_file[ 1 ];
		$currency[ 3 ][ 0 ]= $currency_from_file[ 2 ];
		foreach( $currency AS $key2 => $row2 )
		{
			$currency[ $key2 ][ 0 ]= $currency_from_file[ $key2 ];
		}
	}
	$_SESSION[ 'ExchangeRates' ][ 0 ]= time();
	$_SESSION[ 'ExchangeRates' ][ 1 ]= mktime( 0,0,0, date( 'm' ), date( 'd' ), date( 'Y' ) );
	foreach( $currency AS $key2 => $row2 )
	{
		$_SESSION[ 'ExchangeRates' ][ $key2 ]= $row2[ 0 ];
	}
}
//print_r( $_SESSION[ 'ExchangeRates' ] );
return $_SESSION[ 'ExchangeRates' ][ $c ];

?>