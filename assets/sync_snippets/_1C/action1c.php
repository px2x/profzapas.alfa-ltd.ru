<?php

//v01
//==========================================================================

mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='go::'" ); // LOG

if( empty( $_SESSION[ '1c' ][ 'time' ] ) ) $_SESSION[ '1c' ][ 'time' ]= date( 'Y-m-d-H-i-s' );

$rr= mysql_query( "SELECT COUNT(id) AS cc FROM ". $modx->getFullTableName( '_1c_log' ) );
if( $rr && mysql_result( $rr, 0, 'cc' ) > 50000 )
{
	mysql_query( "TRUNCATE TABLE ". $modx->getFullTableName( '_1c_log' ) );
	mysql_query( "INSERT INTO ". $modx->getFullTableName( '_1c_log' ) ." SET dth='". date( 'Y-m-d-H-i-s' ) ."', text='clear_logs::'" ); // LOG
}


if( $_GET[ 'type' ] == 'catalog' )
{
	$print .= $modx->runSnippet( 'file_from_1c', array() );
	
}elseif( $_GET[ 'type' ] == 'sale' ){
	//$print .= $modx->runSnippet( 'file_to_1c', array() );
}

return $print;
exit();





?>