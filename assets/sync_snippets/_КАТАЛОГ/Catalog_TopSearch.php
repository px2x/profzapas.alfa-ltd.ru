<?php

$koren= 8;

$myid= $modx->documentIdentifier;
if( empty( $id ) ) $id= $myid;


if( isset( $_POST[ 'search' ] ) )
{
	$dop= ( $_POST[ 'dop' ] == 'y' ? 'y' : 'n' );
	
	$cat= intval( $_POST[ 'cat' ] );
	
	$txt= urlencode( trim( $_POST[ 'txt' ] ) );
	
	if( $dop == 'y' )
	{
		$art= urlencode( trim( $_POST[ 'art' ] ) );
		$vend= urlencode( trim( $_POST[ 'vend' ] ) );
		$cit= urlencode( trim( $_POST[ 'cit' ] ) );
		
		$stt1= ( $_POST[ 'stt1' ] == 'y' ? 'y' : 'n' );
		$stt2= ( $_POST[ 'stt2' ] == 'y' ? 'y' : 'n' );
		$stt3= ( $_POST[ 'stt3' ] == 'y' ? 'y' : 'n' );
		$pack= ( $_POST[ 'pack' ] == 'y' ? 'y' : 'n' );
		$disc= ( $_POST[ 'disc' ] == 'y' ? 'y' : 'n' );
		$doc= ( $_POST[ 'doc' ] == 'y' ? 'y' : 'n' );
	}
	
	$url= ( $cat ? $modx->makeUrl( $cat ) : $modx->makeUrl( $koren ) );
	$url_x= 'x';
	
	if( $dop == 'y' )
	{
		if( $dop == 'y' ) $url_x .= '/dop_y';
		
		if( $stt1 == 'y' ) $url_x .= '/s1_y';
		if( $stt2 == 'y' ) $url_x .= '/s2_y';
		if( $stt3 == 'y' ) $url_x .= '/s3_y';
		
		if( $pack == 'y' ) $url_x .= '/p_y';
		if( $disc == 'y' ) $url_x .= '/dc_y';
		if( $doc == 'y' ) $url_x .= '/d_y';
		
		if( $art ) $url_x .= '/a_'. $art;
		if( $vend ) $url_x .= '/v_'. $vend;
		if( $cit ) $url_x .= '/c_'. $cit;
	}
	
	if( $txt ) $url_x .= '/t_'. $txt;
	
	header( 'location: '. $url .( $url_x != 'x' ? $url_x .'/' : '' ) );
	exit();
}



if( $modx->catalogFilterListX )
{
	$xps= explode( "/", $modx->catalogFilterListX );
	$xps_vals= array();
	if( $xps )
	{
		foreach( $xps AS $xp )
		{
			$xp= explode( "_", $xp );
			$xps_vals[ $xp[ 0 ] ]= urldecode( $xp[ 1 ] );
		}
	}
}
if( $modx->catalogFilterListX ) $xps_flag_1= true;
if( $xps_vals[ 'dop' ] == 'y' ) $xps_flag_2= true;
if( $xps_flag_1 && $xps_flag_2 ) $xps_flag_3= true;



$select1= '';
$cats= $modx->runSnippet( 'GetDoc6', array( 'ids'=>$koren, 'type'=>'childs', 'depth'=>'1', 'fields'=>'pagetitle', 'isf'=>'all' ) );
if( $cats )
{
	foreach( $cats AS $row )
	{
		$podcats= $modx->runSnippet( 'GetDoc6', array( 'ids'=>$row[ 'id' ], 'type'=>'childs', 'depth'=>'1', 'fields'=>'pagetitle', 'isf'=>'all' ) );
		$select1 .= '<optgroup label="'. $row[ 'pagetitle' ] .'">';
		if( $podcats )
		{
			foreach( $podcats AS $row2 )
			{
				$select1 .= '<option '.( $row2[ 'id' ] == $id ? 'selected="selected"' : '' ).' value="'. $row2[ 'id' ] .'">'. $row2[ 'pagetitle' ] .'</option>';
			}
		}
		$select1 .= '</optgroup>';
	}
}
?>

<div class="topsearch <?= ( $xps_flag_3 ? 'topsearch_active' : '' ) ?>">
	<form action="[~8~]" method="post">
		<div class="tsch_block_1">
			<div class="tsch_pole">
				<input class="tsch_maininput default_value" type="text" name="txt" data-default="Артикул, производитель, поисковая фраза ..." value="<?= $xps_vals[ 't' ] ?>" />
				<div class="tsch_ico">&nbsp;</div>
			</div>
			<input class="tsch_dop_input_flag" type="hidden" name="dop" value="<?= ( $xps_flag_2 ? 'y' : 'n' ) ?>" />
			<div class="tsch_dop"><span class="txt">Расширенный поиск</span><span class="famicon">&nbsp;</span></div>
			<div class="tsch_submit"><button class="mainbutton" type="submit" name="search">Поиск</button></div>
			<div class="clr">&nbsp;</div>
		</div>
		
		<div class="clr">&nbsp;</div>
		
		<div class="tsch_block_2">
			<div class="tsch_ugolok">&nbsp;</div>
			
			<div class="tsch_submit2"><button class="mainbutton" type="submit" name="search">Найти</button></div>
			
			<div class="tsch_col tsch_col_2">
				<div class="tsch_input">
					<div class="tsch_lbl">Категория каталога</div>
					<div class="tsch_inp">
						<select name="cat" size="8">
							<option value="0" selected="selected">~ любая категория ~</option>
							<?= $select1 ?>
						</select>
					</div>
				</div>
			</div>
			
			<div class="tsch_col tsch_col_3">
				<div class="tsch_input">
					<div class="tsch_lbl">Артикул</div>
					<div class="tsch_inp"><input type="text" name="art" value="<?= $xps_vals[ 'a' ] ?>" /></div>
				</div>
				
				<div class="tsch_input">
					<div class="tsch_lbl">Производитель</div>
					<div class="tsch_inp"><input type="text" name="vend" value="<?= $xps_vals[ 'v' ] ?>" /></div>
				</div>
				
				<div class="tsch_input">
					<div class="tsch_lbl">Город</div>
					<div class="tsch_inp"><input type="text" name="cit" value="<?= $xps_vals[ 'c' ] ?>" /></div>
				</div>
			</div>
			
			<div class="tsch_col">
				<div class="tsch_input">
					<div class="tsch_lbl">Состояние</div>
					<div class="tsch_inp">
						<div><label><input type="checkbox" name="stt1" value="y" <?= ( $xps_vals[ 's1' ] == 'y' ? 'checked="checked"' : '' ) ?> /> Новое</label></div>
						<div><label><input type="checkbox" name="stt2" value="y" <?= ( $xps_vals[ 's2' ] == 'y' ? 'checked="checked"' : '' ) ?> /> Б/У</label></div>
						<div><label><input type="checkbox" name="stt3" value="y" <?= ( $xps_vals[ 's3' ] == 'y' ? 'checked="checked"' : '' ) ?> /> На запчасти</label></div>
					</div>
				</div>
			</div>
			
			<div class="tsch_col">
				<div class="tsch_input">
					<div class="tsch_inp"><label><input type="checkbox" name="pack" value="y" <?= ( $xps_vals[ 'p' ] == 'y' ? 'checked="checked"' : '' ) ?> /> Есть упаковка</label></div>
				</div>
				
				<div class="tsch_input">
					<div class="tsch_inp"><label><input type="checkbox" name="disc" value="y" <?= ( $xps_vals[ 'dc' ] == 'y' ? 'checked="checked"' : '' ) ?> /> Распродажа</label></div>
				</div>
				
				<div class="tsch_input">
					<div class="tsch_inp"><label><input type="checkbox" name="doc" value="y" <?= ( $xps_vals[ 'd' ] == 'y' ? 'checked="checked"' : '' ) ?> /> Документация</label></div>
				</div>
			</div>
			<div class="clr">&nbsp;</div>
		</div>
	</form>
</div>

<?php
	//





?>