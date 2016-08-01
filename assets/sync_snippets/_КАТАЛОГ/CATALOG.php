<?php

//============================================================================
	
	
$myid= $modx->documentIdentifier;
if( empty( $id ) ) $id= $myid;
$doc= $modx->runSnippet( 'GetDoc6', array( 'ids'=>$id, 'type'=>'this', 'fields'=>'pagetitle', 'tvfileds'=>'', 'isf'=>'all' ) );
$doc= $doc[ $id ];


$MaxItemsInPage= 99999;
$koren= 8;
$upFinders = false;

$modx->catalogItemId= intval( $modx->catalogItemId );


$mylvl= $modx->runSnippet( 'GetLvl', array( 'koren' => $koren, 'id' => $id ) );


$page= intval( $modx->catalogPageNum );
if( $page > 1 && $page < 100 ){}else{ $page= 1; }
$page_s= ( $page - 1 ) * $MaxItemsInPage;


if( $modx->catalogItemId )
{
	$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_catalog' )." WHERE parent={$id} AND id=". $modx->catalogItemId ." AND (enabled='y' OR enabled='n') LIMIT 1" );
	if( $rr && mysql_num_rows( $rr ) == 1 )
	{
		$print_pageitem= $modx->runSnippet( 'CAT_ITEM', array( 'type' => 'pageitem', 'row' => mysql_fetch_assoc( $rr ) ) );
		
	}elseif( $rr ){
		$modx->sendErrorPage();
	}
	
}else{
	$dopqq= $dopdopww= "";
	if( $modx->catalogFilterListX )
	{
		$upFinders = true;
		$xps= explode( "/", $modx->catalogFilterListX );
		$xps_vals= array();
		if( $xps )
		{
			foreach( $xps AS $xp )
			{
				if( $xp == 'p_y' ){ $dopqq .= " AND packaging='y'";
				}elseif( $xp == 'd_y' ){ $dopqq .= " AND documentation!=''";
				}elseif( $xp == 'dc_y' ){ $dopqq .= " AND discount!='0'";
				}elseif( $xp == 's1_y' ){ $dopdopww .= ( ! empty( $dopdopww ) ? " OR " : "" ) ."state='new'";
				}elseif( $xp == 's2_y' ){ $dopdopww .= ( ! empty( $dopdopww ) ? " OR " : "" ) ."state='bu'";
				}elseif( $xp == 's3_y' ){ $dopdopww .= ( ! empty( $dopdopww ) ? " OR " : "" ) ."state='zapch'";
				}else{
					$xp= explode( "_", $xp );
					if( $xp[ 0 ] == 'a' && ! empty( $xp[ 1 ] ) )
					{
						$dopqq .= " AND code LIKE '%{$xp[1]}%'";
						
					}elseif( $xp[ 0 ] == 'v' && ! empty( $xp[ 1 ] ) ){
						$dopqq .= " AND manufacturer LIKE '%{$xp[1]}%'";
						
					}elseif( $xp[ 0 ] == 'c' && ! empty( $xp[ 1 ] ) ){
						
					}elseif( $xp[ 0 ] == 't' && ! empty( $xp[ 1 ] ) ){
						$dopqq .= " AND ( code LIKE '%{$xp[1]}%' OR title LIKE '%{$xp[1]}%' OR manufacturer LIKE '%{$xp[1]}%' OR text LIKE '%{$xp[1]}%' )";
					}
				}
			}
		}
	}
	if( $dopdopww ) $dopqq .= " AND ( {$dopdopww} )";
	
	if( $mylvl == 1 && ! $modx->catalogFilterListX )
	{
		$print_categories .= $modx->runSnippet( 'Catalog_BigMenu' );
		
	}else{
		if( $id != $koren )
		{
			$childs= $modx->runSnippet( 'GetDoc6', array( 'ids'=>$id, 'type'=>'all', 'depth'=>'0', 'isf'=>'all' ) );
			if( $childs )
			{
				foreach( $childs AS $row ) $childsids .= ( ! empty( $childsids ) ? " OR " : "" ) ."parent=". $row[ 'id' ];
			}
		}else{
			$childsids= "1=1";
		}
		
		
		if( $id != $koren )
		{
			$subcat= $modx->runSnippet( 'GetDoc6', array( 'ids'=>$id, 'type'=>'childs', 'depth'=>'1', 'fields'=>'pagetitle', 'isf'=>'all' ) );
			if( $subcat )
			{
				$print_categories .= '<div class="catcatsmini">';
				foreach( $subcat AS $row )
				{
					$print_categories .= '<div class="catcatmin"><a class="as2" href="'. $modx->makeUrl( $row[ 'id' ] ) .'">'. $row[ 'pagetitle' ] .'</a></div>';
				}
				$print_categories .= '<div class="clr">&nbsp;</div></div>';
			}
		}
		
		
		$items= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_catalog' )." WHERE ( {$childsids} ) {$dopqq} AND enabled='y' ORDER BY id" );
		
		$print_items .= '<table class="catalog_table" cellpadding="0" cellspacing="0">';
		$print_items .= '<tr class="jqt_row_tit">';
		$print_items .= '<td class="jqtrt_col jqtrtc_code">Код</td>';
		$print_items .= '<td class="jqtrt_col jqtrtc_vend">Производитель</td>';
		$print_items .= '<td class="jqtrt_col jqtrtc_name">Описание</td>';
		$print_items .= '<td class="jqtrt_col jqtrtc_stock">В наличии</td>';
		$print_items .= '<td class="jqtrt_col jqtrtc_price">Цена, руб.</td>';
		$print_items .= '<td class="jqtrt_col jqtrtc_basket">Заказ</td>';
		$print_items .= '<td class="jqtrt_col jqtrtc_city">Продавец</td>';
		$print_items .= '</tr>';
		
		$ii= 0;
		while( $row= mysql_fetch_assoc( $items ) )
		{
			$ii++;
			$print_items .= $modx->runSnippet( 'CAT_ITEM', array( 'type' => 'item', 'row' => $row, 'upFinders'=>$upFinders, 'last' => ( $ii % 2 == 0 ? true : false ) ) );
		}
		
		$print_items .= '</table>';
	}
}


$print .= '<div class="catalog">';
if( ! empty( $print_categories ) )
{
	$print .= $print_categories;
	$print .= '<div class="clr">&nbsp;</div>';
}
if( ! empty( $print_items ) )
{
	$print .= $print_items;
	$print .= '<div class="clr">&nbsp;</div>';
}
if( ! empty( $print_pageitem ) )
{
	$print .= $print_pageitem;
	$print .= '<div class="clr">&nbsp;</div>';
}
$print .= '</div>';


return $print;





?>