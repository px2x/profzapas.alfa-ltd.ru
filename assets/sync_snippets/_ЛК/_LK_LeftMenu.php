<?php

print '<div class="_LK_leftblock">';
print '<div class="_LK_leftmenu"><ul>';

$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];

$menu= $modx->runSnippet( 'GetDoc6', array( 'ids'=>115, 'type'=>'childs', 'depth'=>1, 'fields'=>'pagetitle', 'sort'=>'menuindex', 'isf'=>'all' ) );
if( $menu )
{
	foreach( $menu AS $row )
	{
		$notice='';
		
		if ($row[ 'id' ] == 122) {
			$notice .= '<span class="menuNotice">';
			$notice .= $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getCountMsgAll', 'to' => $webuserinfo['id'] ));
			$notice .= '</span>';
		}
		
		//$modx->runSnippet( '_LK_getCountEvent', array('event' => 'inShip'))
		

		if ($row[ 'id' ] == 117) {
			$notice .= $modx->runSnippet( '_LK_getCountEvent', array('event' => 'allOrders'));
		}
		

		if ($row[ 'id' ] == 118) {
			$notice .= $modx->runSnippet( '_LK_getCountEvent', array('event' => 'allSellerOrd'));
		}
        
        if ($row[ 'id' ] == 119) {
			$notice .= $modx->runSnippet( '_LK_getCountEvent', array('event' => 'newFavourPrice'));
		}
        
        
        if ($row[ 'id' ] == 120) {
			$notice .= $modx->runSnippet( '_LK_getCountEvent', array('event' => 'newFinancesEvents'));
		}
        
        
		
		if ($row[ 'id' ] == 124) {
			$notice .= $modx->runSnippet( '_LK_getCountEvent', array('event' => 'waitResponse'));
		}
        
        if ($row[ 'id' ] == 125) {
			$notice .= $modx->runSnippet( '_LK_getCountEvent', array('event' => 'requestPriceA'));
		}
        
        //$modx->runSnippet( '_LK_getCountEvent', array('event' => 'waitResponse'))

        
		
		print '<li class="'.( $row[ 'id' ] == $modx->documentIdentifier ? 'active' : '' ).'"><a href="'. $modx->makeUrl( $row[ 'id' ] ) .'"><span class="famicon">&nbsp;&nbsp;&nbsp;</span>'. $row[ 'pagetitle' ] .' '.$notice.'</a></li>';
		
	}
}

print '</ul></div>';
print '</div>';





?>