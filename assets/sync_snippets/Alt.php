<?php

//v002
//============================================================================
global $altcounter;
if( $plus ) $altcounter[ $name ] += $plus;
if( $print == 'ii' ) return $altcounter[ $name ];
	elseif( ! empty( $print ) && $kk && $altcounter[ $name ] % $kk == 0 ) return $print;





?>