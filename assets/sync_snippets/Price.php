<?php

if( empty( $delimiter ) ) $delimiter= '&thinsp;';
	if( empty( $round ) ) $round= 2;
	
$price= mysql_escape_string( trim( $price ) );
	
	$price= str_replace( ",", ".", $price );
	
	$price= ceil( $price );

	if( $price <= 0 || $price == '' ) return "&mdash;";
	
	$tmp= explode( ".", $price );
	
	$itogo_price= '';
	$ii= 0;
	for( $kk=strlen( $tmp[ 0 ] )-1; $kk >= 0; $kk-- )
	{
		$ii++;
		$itogo_price= substr( $tmp[ 0 ], $kk, 1 ) . $itogo_price;
		if( $ii % 3 == 0 && $kk > 0 )
		{
			$itogo_price= $delimiter . $itogo_price;
		}
	}
	if( $tmp[ 1 ] > 0 ) $itogo_price .= ','. $tmp[ 1 ];

//$price= @number_format( $price, ( strstr( $price, "." ) ? 2 : 0 ), ',', "&thinsp;" );
//$price= @money_format( $price, ( strstr( $price, "." ) ? 2 : 0 ), ',', "&thinsp;" );
	
//$pr .= '<span class="pr_mini">,00</span>';
	
	return $itogo_price;





?>