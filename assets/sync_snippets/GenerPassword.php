<?php

//v01
//==============================================================
if( ! $length ) $length= 12;
$simbols= array(
	'a','b','s','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
	'A','B','S','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
	'_','_','_','_',
	'0','1','2','3','4','5','6','7','8','9',	'0','1','2','3','4','5','6','7','8','9',
);
for( $o= 1; $o <= $length; $o++ )
{
	$rand_tmp= rand( 0, count( $simbols )-1 );
	$simbol_tmp= $simbols[ $rand_tmp ];
	$password .= $simbol_tmp;
}
return $password;





?>