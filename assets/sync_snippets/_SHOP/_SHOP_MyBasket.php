<?php

//_SHOP_DooOrder	page id
$pageid_dooOrder = 203;
	
$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];	
if (is_numeric ($webuserinfo['id'])) {
	$userId = $webuserinfo['id'];
	$sqlWhere = "`id_user` = ".$userId;
}else {
	$userId = session_id();
	$sqlWhere = "`sessid` = '".session_id()."'";
}	
	
$rows = $modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getMyBasketList', 'userId' => $userId));


$print_items_head .= '<table class="catalog_table" cellpadding="0" cellspacing="0">';
$print_items_head .= '<tr class="jqt_row_tit">';
$print_items_head .= '<td class="jqtrt_col jqtrtc_code">Код</td>';
$print_items_head .= '<td class="jqtrt_col jqtrtc_vend">Производитель</td>';
$print_items_head .= '<td class="jqtrt_col jqtrtc_name">Описание</td>';
$print_items_head .= '<td class="jqtrt_col jqtrtc_stock">В наличии</td>';
$print_items_head .= '<td class="jqtrt_col jqtrtc_price">Цена, руб.</td>';
$print_items_head .= '<td class="jqtrt_col jqtrtc_basket">Количество</td>';
$print_items_head .= '<td class="jqtrt_col jqtrtc_summ">Стоимость</td>';
$print_items_head .= '</tr>';

//return print_r($row);
if ($rows) {
	$last = 1;
	foreach ($rows AS $row){
		$last = $last*-1;
		if( $row[ 'currency' ] != 'rub' ) $row[ 'price' ]= $modx->runSnippet( 'ExchangeRates_Price', array( 'price'=>$row[ 'price' ], 'c'=>$row[ 'currency' ] ));
		$row[ 'text' ]= $modx->runSnippet( 'str_replace', array( 'from'=>"\n", 'to'=>'<br />', 'txt'=>$row[ 'text' ] ) );
		
		$result .= '<tr class="jqt_row_itm '.( $last >0 ? 'jqtri_chet' : '' ).'">';
			$result .= '<td class="jqtri_col jqtric_code"><a class="as1" href="'. $modx->makeUrl( $row[ 'parent' ] ) .'i/'. $row[ 'id' ] .'/">'. $row[ 'code' ] .'</a></td>';
			$result .= '<td class="jqtri_col jqtric_vend">'. $row[ 'manufacturer' ] .'</td>';
			$result .= '<td class="jqtri_col jqtric_name">
				<div class="descr">
					<div class="type">'. $row[ 'title' ] .'</div>
					<div>'. $row[ 'text' ] .'</div>
				</div>
				<div class="fulldescription">
					<div class="ugolok">&nbsp;</div>
					<div class="type">'. $row[ 'title' ] .'</div>
					<div>'. $row[ 'text' ] .'</div>
				</div>
			</td>';
			$result .= '<td class="jqtri_col jqtric_stock">'. $row[ 'in_stock' ] .' шт.</td>';
		
		
		//personalDiscount
		$sqlPDiscount = "SELECT p_discount FROM  ".$modx->getFullTableName( '_personal_discount' )." WHERE id_user =  '".$webuserinfo[ 'id' ]."'  LIMIT 1";
		$resultPDiscount = mysql_query($sqlPDiscount) or die ('Error 43432');
		if  ($row_pd = mysql_fetch_assoc($resultPDiscount)['p_discount']){
			$pagerow_pd = $row_pd;
		} else {
			$pagerow_pd = 0;
		}	

		if ($pagerow_pd > $row[ 'discount' ]){
			$row[ 'discount' ] = $pagerow_pd;
		}
		//
		$accepted_price= 0;
		if ($row['accepted_price'] > 0 &&  $row['accepted_price'] < $row['price']) {
			$accepted_price = $row['accepted_price'] ;
		}
		
		if ($row[ 'discount' ] > 0 ){
			if ($accepted_price >0 ) {
				$oneprice =  $accepted_price;
				$countContrors = 'disabled';
			}else {
				$oneprice =  round(($row['price'] - ($row['price'] / 100 * $row['discount'])));
			}
			
			$result .= '<td class="jqtri_col jqtric_price">
			<div class="discountpercent">- '.$row['discount'].' %</div>
			<div class="oldprice"><nobr><span class="throughprice">'. $modx->runSnippet( 'Price', array( 'price' => $row[ 'price' ], 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></div>
			<div class="newprice"><nobr><span class="newprice">'. $modx->runSnippet( 'Price', array( 'price' => $oneprice, 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></div>
			</td>';
		}else  {
			
			if ($accepted_price >0 ) {
				$oneprice =  $accepted_price;
				$countContrors = 'disabled';
			}else {
				$oneprice = $row[ 'price' ];
			}
			
			 
			$result .= '<td class="jqtri_col jqtric_price"><nobr><span class="price">'. $modx->runSnippet( 'Price', array( 'price' => $oneprice, 'round' => 0 ) ) .'</span> <span class="rubl">a</span></nobr></td>';
		}
		  
			
			$result .= '  
			<td class="jqtri_col jqtric_basket">
				<div class="cati_tobasket add_to_basket" id="ci_tobasket_'. $row[ 'id' ] .'" data-itemid="'. $row[ 'id' ] .'">
					<button class="button_minus" '.$countContrors.'>&#9668;</button>
					<input type="text" value="'.($row['count'] ).'" '.$countContrors.' id="ci_tobasket_count_'. $row[ 'id' ] .'"  data-id="'. $row[ 'id' ] .'" data-max="'. $row[ 'in_stock' ] .'" data-oneprice="'.$oneprice.'"  class="tobasket_item_count" pattern="[0-9]{4}" />
					<button class="button_plus"  data-max="'. $row[ 'in_stock' ] .'" '.$countContrors.'>&#9658;</button>
				</div>
			</td>';
			$result .= '<td class="jqtri_col jqtric_summ"  id="sumCountPrice_'.$row['id'].'"><span class="sumPrice">'. $modx->runSnippet( 'Price', array( 'price' => $oneprice * $row['count'] , 'round' => 0 ) ) .'</span> <span class="rubl">a</span><div class="delInput deleteFromBasket" data-id="'.$row['id'].'"></div></td>';
		$result .= '';
		$result .= '</tr>';
	}
	$result .= '<tr class="jqt_row_itm lasttrgray"><th class="tr_itogo" colspan=5>Итого: </th><th colspan="2" class="allSummMyOrder defaultNDS">'.$modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getSummMyOrder', 'nds' => 'none', 'userId' => $userId)).'</th></tr>';
	$result .= '<tr class="jqt_row_itm lasttrgray"><th class="tr_itogo" colspan=5>НДС 18%: </th><th colspan="2" class="allSummMyOrder onlyNDS">'.$modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getSummMyOrder', 'nds' => 'only',  'userId' => $userId)).'</th></tr>';
	$result .= '<tr class="jqt_row_itm lasttrgray"><th class="tr_itogo" colspan=5>Итого с НДС: </th><th colspan="2" class="allSummMyOrder withNDS">'.$modx->runSnippet( '_SHOP_actionBasket', array( 'type' => 'getSummMyOrder',  'nds' => 'with',  'userId' => $userId)).'</th></tr>';
$print_items_foot .= '</table>
	<a href="'. $modx->makeUrl( $pageid_dooOrder ).'"  class="linkDooOrder">Оформить заказ</a>';
	return $print_items_head.$result.$print_items_foot;
		
} else {


}





?>