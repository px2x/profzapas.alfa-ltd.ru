<?php

//v05
//==================================================================================
$lk= 106;
$reg= 107;
$auth= 108;
$restorepassword= 109;
$agreed= 110;
$DMTCaptcha= 111;
$DMTCaptcha_img= 112;
//==================================================================================

$topage_url= $modx->makeUrl( $modx->documentIdentifier );

if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	header( 'location: '. $modx->makeUrl( $auth ) );
	exit();
}

$_SESSION[ 'webuserinfo' ][ 'contact_faces' ]=false;
$result =  mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user_contact' )." WHERE firstname <> '' AND surname <> '' AND user='".$_SESSION[ 'webuserinfo' ][ 'id' ]."' LIMIT 5" );
if( $result && mysql_num_rows( $result ) > 0 ){
	$j=1;
	while ($assoc_row = mysql_fetch_assoc( $result )){
		$_SESSION[ 'webuserinfo' ][ 'contact_faces' ][$j] = $assoc_row;
		$j++;
	}
}

$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];
$sellerDescrInfo= $_SESSION[ 'webuserinfo' ][ 'seller_descr' ];
$sellerWarehouses= $_SESSION[ 'webuserinfo' ][ 'seller_warehouses' ];
$userContactFaces= $_SESSION[ 'webuserinfo' ][ 'contact_faces' ];
$post[ 'urlico' ]= $webuserinfo[ 'urlico' ];
$post[ 'seller' ]= $webuserinfo[ 'seller' ];

	//return print_r($userContactFaces);
// =======================================================================================================


$vkladka_active= 1;
if( isset( $_POST[ 'save_2' ] ) ||isset( $_GET[ 'ok21' ] ) || isset( $_GET[ 'ok22' ] ) ) $vkladka_active= 2;
if( isset( $_POST[ 'save_3' ] ) || isset( $_GET[ 'ok3' ] ) ) $vkladka_active= 3;
if( isset( $_POST[ 'save_4' ] ) || isset( $_GET[ 'ok4' ] ) ) $vkladka_active= 4;
if( isset( $_POST[ 'save_5' ] ) || isset( $_GET[ 'ok5' ] ) ) $vkladka_active= 1;
if( isset( $_POST[ 'save_51' ] ) || isset( $_GET[ 'ok51' ] ) ) $vkladka_active= 2;
if( isset( $_POST[ 'save_6' ] ) || isset( $_GET[ 'ok6' ] ) ) $vkladka_active= 6;
	





if( isset( $_POST[ 'save_1' ] ) )
{
	$save_surname= addslashes( trim( $_POST[ 'save_surname' ] ) );
	$save_firstname= addslashes( trim( $_POST[ 'save_firstname' ] ) );
	
	$save_email= addslashes( trim( $_POST[ 'save_email' ] ) );
	$save_mobile= addslashes( trim( $_POST[ 'save_mobile' ] ) );
	
	$save_passport= addslashes( trim( $_POST[ 'save_passport' ] ) );
	$save_passport_dth= addslashes( trim( $_POST[ 'save_passport_dth' ] ) );
	$save_passport_who= addslashes( trim( $_POST[ 'save_passport_who' ] ) );
	$save_passport_address= addslashes( trim( $_POST[ 'save_passport_address' ] ) );
	
	$qq= '';
	
	if( $save_firstname && $save_firstname != $webuserinfo[ 'firstname' ] ) $qq .= ( ! empty( $qq ) ? ", " : "" ) ."firstname='{$save_firstname}'";
	if( $save_surname && $save_surname != $webuserinfo[ 'surname' ] ) $qq .= ( ! empty( $qq ) ? ", " : "" ) ."surname='{$save_surname}'";
	
	if( $save_passport && $save_passport != $webuserinfo[ 'passport' ] ) $qq .= ( ! empty( $qq ) ? ", " : "" ) ."passport='{$save_passport}'";
	if( $save_passport_dth && $save_passport_dth != $webuserinfo[ 'passport_dth' ] ) $qq .= ( ! empty( $qq ) ? ", " : "" ) ."passport_dth='{$save_passport_dth}'";
	if( $save_passport_who && $save_passport_who != $webuserinfo[ 'passport_who' ] ) $qq .= ( ! empty( $qq ) ? ", " : "" ) ."passport_who='{$save_passport_who}'";
	if( $save_passport_address && $save_passport_address != $webuserinfo[ 'passport_address' ] ) $qq .= ( ! empty( $qq ) ? ", " : "" ) ."passport_address='{$save_passport_address}'";
	
	if( ! preg_match( "/^[a-z0-9-_\.]{1,}@[a-z0-9-\.]{1,}\.[a-z]{2,10}$/i", $save_email ) )
	{
		$save_err[1] .= '<p>- Адрес электронной почты неверного формата</p>';
		$save_err_flag[ 'save_email' ]= true;
	}elseif( $save_email != $webuserinfo[ 'email' ] ){
		//$qq .= ( ! empty( $qq ) ? ", " : "" ) ."email='{$save_email}'";
	}
	
	if( ! preg_match( "/^\+7 [0-9]{3} [0-9]{3}-[0-9]{4}$/i", $save_mobile ) )
	{
		$save_err[1] .= '<p>- Номер мобильного телефона неверного формата</p>';
		$save_err_flag[ 'save_mobile' ]= true;
	}elseif( $save_mobile != $webuserinfo[ 'mobile' ] ){
		//$qq .= ( ! empty( $qq ) ? ", " : "" ) ."mobile='{$save_mobile}'";
	}
	
	if( $qq )
	{
		mysql_query( "INSERT INTO ".$modx->getFullTableName( '_user_h' )." SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
		mysql_query( "UPDATE ".$modx->getFullTableName( '_user' )." SET fizlico='test', {$qq} WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
		
		header( 'location: '. $topage_url .'?ok1' );
		exit();
	}
}
?>






<div class="vkladki_butts">
	<div class="vkldk_butt <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>" data-id="1">Данные физ.лица</div>
	<div class="vkldk_butt <?= ( $vkladka_active == 2 ? 'active' : '' ) ?>" data-id="2">Данные юр.лица</div>
	<div class="vkldk_butt <?= ( $vkladka_active == 3 ? 'active' : '' ) ?>" data-id="3">Склады</div>
	<div class="vkldk_butt <?= ( $vkladka_active == 4 ? 'active' : '' ) ?>" data-id="4">Контакты</div>
	<!--div class="vkldk_butt <?= ( $vkladka_active == 5 ? 'active' : '' ) ?>" data-id="5">Получить статус Продавца</div-->
	<div class="vkldk_butt <?= ( $vkladka_active == 6 ? 'active' : '' ) ?>" data-id="6">Пароль</div>
	<div class="clr">&nbsp;</div>
</div>

<div class="vkladki_divs">
	
	
	
	
	<?php
if( isset( $_POST[ 'save_5' ] ) )
{
	$vkladka_active= 1; 
	$save_dopinfo= addslashes( trim( $_POST[ 'save_dopinfo' ] ) );
	

	$save_agreed= ( $_POST[ 'save_agreed' ] == 'y' ? 'y' : 'n' );
	$save_seller= ( $_POST[ 'save_seller' ] == 'y' ? 'test' : 'n' );
	
	if( $webuserinfo[ 'seller' ] == 'y' && $save_agreed != 'y' )
	{
		$save_err[5] .= '<p>- Подтвердите изменение данных</p>';
	}
	
	if( $save_seller != 'n' )
	{
		if( ! $save_dopinfo  )
		{
			$save_err[5] .= '<p>- Заполните краткое описание Вашего магазина</p>';
			$save_dopinfo[ 'save_dopinfo' ]= true;
		}

		if( ! $save_err[5] )
		{
			
			
			$_SESSION[ 'webuserinfo' ][ 'seller_descr' ] = $temp['description'] = $save_dopinfo;
		
			mysql_query( "INSERT INTO ".$modx->getFullTableName( '_user_h' )." SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
			mysql_query( "UPDATE ".$modx->getFullTableName( '_user' )." SET
						seller='{$save_seller}'
						WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
						

			$tmp = mysql_query("SELECT `id` FROM ".$modx->getFullTableName( '_user_seller_descr' )." 
						WHERE id_seller='". $_SESSION[ 'webuserinfo' ][ 'id' ] ."' LIMIT 1" );	
			
			if(mysql_num_rows( $tmp ) > 0 ){
			
				 mysql_query("UPDATE  ".$modx->getFullTableName( '_user_seller_descr' )." SET 
							description='{$save_dopinfo}'
							WHERE id_seller=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
							
			}else {
				
				// mysql_query("INSERT INTO  ".$modx->getFullTableName( '_user_seller_descr' )." (`seller`, `description`) VALUES (". $_SESSION[ 'webuserinfo' ][ 'id' ] ." ,'{$save_dopinfo}')" );
				mysql_query("INSERT INTO ".$modx->getFullTableName( '_user_seller_descr' )." (
									`id` ,
									`id_seller` ,
									`description`
									)
									VALUES (
									'',  '". $_SESSION[ 'webuserinfo' ][ 'id' ] ."',  '{$save_dopinfo}'
									)" );
			}

			///	
			header( 'location: '. $topage_url .'?ok5' );
			exit();
			
		}else{
			$post[ 'seller' ]= $save_seller;
			$webuserinfo[ 'dopinfo' ]= $save_dopinfo;
		}
	}
	
	if( $save_seller == 'n' )
	{
		if( ! $save_err[5] )
		{
			mysql_query( "UPDATE ".$modx->getFullTableName( '_user' )." SET
						seller='{$save_seller}'
							WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
			
			header( 'location: '. $topage_url .'?ok5' );
			exit();
			
		}else{
			$post[ 'seller' ]= $save_seller;
		}
	}
}



?>
	<div class="vkldk_div vkldk_div_1 <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<div class="_LK_form">
				<?php if( isset( $_GET[ 'ok1' ] ) ){ ?>
				<div class="_LK_ok">
					<p>Данные успешно сохранены!</p>
				</div>
				<?php } ?>
				
				<?php if( ! empty( $save_err[1] ) ) print '<div class="_LK_error">'. $save_err[1] .'</div>'; ?>
				<?php if( ! empty( $save_err[5] ) ) print '<div class="_LK_error">'. $save_err[5] .'</div>'; ?>
				<form action="<?= $topage_url ?>?px" method="post" class="borderdashed">
					<div class="_LK_form_line">
						<div class="_LK_form_lbl">Фамилия</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_surname' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_surname" value="<?= $webuserinfo[ 'surname' ] ?>" /></div>
						<div class="clr">&nbsp;</div>
					</div>
					<div class="_LK_form_line _LK_form_line_br">
						<div class="_LK_form_lbl">Имя</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_firstname' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_firstname" value="<?= $webuserinfo[ 'firstname' ] ?>" /></div>
						<div class="clr">&nbsp;</div>
					</div>
					
					<div class="_LK_form_line">
						<div class="_LK_form_lbl">Адрес электронной почты</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_email' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_email" value="<?= $webuserinfo[ 'email' ] ?>" /></div>
						<div class="clr">&nbsp;</div>
					</div>
					<div class="_LK_form_line _LK_form_line_br">
						<div class="_LK_form_lbl">Номер мобильного телефона</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_mobile' ] ? '_LK_form_inp_error' : '' ) ?>"><input class="input_mask_mobile" type="text" name="save_mobile" value="<?= $webuserinfo[ 'mobile' ] ?>" /></div>
						<div class="clr">&nbsp;</div>
					</div>
					
					<div class="_LK_form_line">
						<div class="_LK_form_lbl">Серия и номер паспорта</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_passport' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_passport" value="<?= $webuserinfo[ 'passport' ] ?>" /></div>
						<div class="clr">&nbsp;</div>
					</div>
					<div class="_LK_form_line">
						<div class="_LK_form_lbl">Дата выдачи</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_passport_dth' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_passport_dth" value="<?= $webuserinfo[ 'passport_dth' ] ?>" /></div>
						<div class="clr">&nbsp;</div>
					</div>
					<div class="_LK_form_line">
						<div class="_LK_form_lbl">Кем выдан</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_passport_who' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_passport_who" value="<?= $webuserinfo[ 'passport_who' ] ?>" /></div>
						<div class="clr">&nbsp;</div>
					</div>
					<div class="_LK_form_line _LK_form_line_br">
						<div class="_LK_form_lbl">Адрес регистрации (прописки)</div>
						<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_passport_address' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_passport_address" value="<?= $webuserinfo[ 'passport_address' ] ?>" /></div>
						<div class="clr">&nbsp;</div>
					</div>
					
					<div class="_LK_form_line _LK_form_line_butt">
						<div class="_LK_form_lbl"> </div>
						<div class="_LK_form_inp"><div class="mainbutton buttonsubmit genSMS" name="save_1" >Сохранить</div></div>
						<div class="clr">&nbsp;</div>
					</div>
					
					<input type="hidden"   name="save_1"/>
					
					<!--div class="darkSMSCode">
						<div class="popupEnterSMS"> 
							<div class="popupHtext">Введите код из СМС</div>
							<div class="popupHdescr">На Ваш мобильный телефон было отправлено сообщение с кодом подтверждения для изменения данных</div>
							<div class="popupDublicSMS"></div>
							<input type="text" class="verifySMScode" maxlength="5"/>
							<div class="waitCheckSMS"><div class="checkResult">Повторно СМС можно выслать через 120 сек.</div><div class="wrap"><div class="dot"></div></div></div>
							
							
							
						</div>
						<div class="closeAll"></div>
					</div-->
					
					
				</form>
			</div>
		</div>
		
		
		
		
		
		
		
		<div class="_LK_wrapper _LK_wrapper_big">
			<div class="_LK_form">
				<?php if( isset( $_GET[ 'ok5' ] )  && $_POST['save_seller'] == 'y'){ ?>
				<div class="_LK_ok">
					<p>Данные отправлены на проверку!</p>
					<p>Статус продавца пока еще не был присвоен!</p>
				</div>
				<?php } ?>

				<?php if( ! isset( $_GET[ 'ok5' ] ) && $webuserinfo[ 'seller' ] == 'test' ){ ?>
				<div class="_LK_info">
					<p>Данные проходят проверку!</p>
					<p>Статус продавца не присвоен!</p>
				</div>
				<?php } ?>
				
				
				
				<form action="<?= $topage_url ?>" method="post"  class="borderdashed">
					
					<?php if( $webuserinfo[ 'seller' ] != 'n' ){ ?>
					<div class="_LK_form_line _LK_form_line_br">
						<div class="_LK_form_lbl"><b>Статус</b></div>
						<div class="_LK_form_inp _LK_form_inp_txt"><b>
						<?php
							if( $webuserinfo[ 'seller' ] == 'y' ) print 'Продавец. Данные подтверждены!';
							elseif( $webuserinfo[ 'seller' ] == 'test' ) print 'Данные продавца на проверке!';
						?>
						</b></div>
						<div class="clr">&nbsp;</div>
					</div>
					<br />
					<?php } ?>
					
					<div class="_LK_form_line _LK_form_line_br">
						<div class="_LK_form_lbl"><input class="LK_form_urlico_checkbox" type="checkbox" name="save_seller" value="y" <?=( $post[ 'seller' ] != 'n' ? 'checked="checked"' : '' )?> /></div>
						<div class="_LK_form_inp"><?= ( $post[ 'seller' ] == 'y' ? 'Продавец' : 'Отправить заявку<br />на получение статуса продавца' ) ?></div>
						<div class="clr">&nbsp;</div>
					</div>
					
					<div class="LK_form_urlico <?=( $post[ 'seller' ] != 'n' ? 'LK_form_urlico_active' : '' )?>">
					
						<?php
						if ($webuserinfo[ 'urlico' ] == 'y') {
							$urlicoChecked = 'checked="checked"';?>
							
						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl"></div>
							<div class="_LK_form_inp"><input type="hidden" name="type_seller" value="urlico" <?php echo $urlicoChecked?>/>Юридическое лицо</div>
							<!--input type="radio" name="type_seller" value="fizlico" <?php echo $fizlicoChecked?>/>Физическое лицо-->
							<div class="clr">&nbsp;</div>
						</div>
						
						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl">Наименование компании</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_company' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_company" readonly value="<?= $webuserinfo[ 'company' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Телефон компании</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_phone_company' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_phone_company" readonly value="<?= $webuserinfo[ 'phone_company' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						

						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl">Юридический адрес</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_address_ur' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_address_ur" readonly value="<?= $webuserinfo[ 'address_ur' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
							
						<?php						
						} else {
							$fizlicoChecked = 'checked="checked"';?>
						   
						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl"></div>
							<div class="_LK_form_inp"><input type="hidden" name="type_seller" value="fizlico" <?php echo $fizlicoChecked?>/>Физическое лицо</div>
							<div class="clr">&nbsp;</div>
						</div>
						<?php	
						}
						
						?>

						
						
						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl">Краткое описание Вашего магазина</div>
							<div class="_LK_form_inp"><textarea name="save_dopinfo"><?= $sellerDescrInfo ?></textarea></div>
							<div class="clr">&nbsp;</div>
						</div>
						
					</div>
						
					<?php if( $webuserinfo[ 'seller' ] == 'y' ){ ?>
					<div class="_LK_form_line">
						<div class="_LK_form_lbl"><input type="checkbox" name="save_agreed" value="y" /></div>
						<div class="_LK_form_inp">Подтверждаю изменение данных</div>
						<div class="clr">&nbsp;</div>
					</div>
					<div class="_LK_form_line">
						<div class="_LK_form_lbl"> </div>
						<div class="_LK_form_inp">
							<div class="_LK_info">
								<p>Измененные данные подлежат проверке администратором.</p>
							</div>
						</div>
						<div class="clr">&nbsp;</div>
					</div>
					<?php } ?>
					
					<div class="_LK_form_line _LK_form_line_butt">
						<div class="_LK_form_lbl"> </div>
						<div class="_LK_form_inp"><button class="mainbutton buttonsubmit" name="save_5" type="submit">Сохранить</button></div>
						<div class="clr">&nbsp;</div>
					</div>
				</form>
			</div>
		</div>
		
		
		
		
	</div>
	
	

	
	<!-- ===================================================================================== -->
	
	
	
	
	
	
<?php
if( isset( $_POST[ 'save_2' ] ) )
{
	$vkladka_active= 2;
	
	$save_company= addslashes( trim( $_POST[ 'save_company' ] ) );
	$save_address_ur= addslashes( trim( $_POST[ 'save_address_ur' ] ) );
	$save_address_ft= addslashes( trim( $_POST[ 'save_address_ft' ] ) );
	$save_inn= addslashes( trim( $_POST[ 'save_inn' ] ) );
	$save_kpp= addslashes( trim( $_POST[ 'save_kpp' ] ) );
	$save_bik= addslashes( trim( $_POST[ 'save_bik' ] ) );
	$save_ogrn= addslashes( trim( $_POST[ 'save_ogrn' ] ) );
	$save_bank= addslashes( trim( $_POST[ 'save_bank' ] ) );
	$save_rschet= addslashes( trim( $_POST[ 'save_rschet' ] ) );
	$save_kschet= addslashes( trim( $_POST[ 'save_kschet' ] ) );
	$phone_company= addslashes( trim( $_POST[ 'save_phone_company' ] ) );
	
	
	$save_agreed= ( $_POST[ 'save_agreed' ] == 'y' ? 'y' : 'n' );
	$save_urlico= ( $_POST[ 'save_urlico' ] == 'y' ? 'test' : 'n' );
	
	if( $webuserinfo[ 'urlico' ] == 'y' && $save_agreed != 'y' )
	{
		$save_err[2] .= '<p>- Подтвердите изменение данных</p>';
	}
	
	if( $save_urlico != 'n' )
	{
		if( ! $save_company || ! $save_address_ur || ! $save_address_ft || ! $save_bank )
		{
			$save_err[2] .= '<p>- Заполните все реквизиты</p>';
			$save_err_flag[ 'save_company' ]= true;
			$save_err_flag[ 'save_address_ur' ]= true;
			$save_err_flag[ 'save_address_ft' ]= true;
			$save_err_flag[ 'save_bank' ]= true;
		}
		if( ! preg_match( "/^[0-9]{10}$/", $save_inn ) )
		{
			$save_err[2] .= '<p>- ИНН неверного формата</p>';
			$save_err_flag[ 'save_inn' ]= true;
		}
		if( ! preg_match( "/^[A-Za-z0-9]{9}$/", $save_kpp ) )
		{
			$save_err[2] .= '<p>- КПП неверного формата</p>';
			$save_err_flag[ 'save_kpp' ]= true;
		}
		if( ! preg_match( "/^[0-9]{9}$/", $save_bik ) )
		{
			$save_err[2] .= '<p>- БИК неверного формата</p>';
			$save_err_flag[ 'save_bik' ]= true;
		}
		if( ! preg_match( "/^[A-Za-z0-9]{13,15}$/", $save_ogrn ) )
		{
			$save_err[2] .= '<p>- ОГРН (ОГРНИП) неверного формата</p>';
			$save_err_flag[ 'save_ogrn' ]= true;
		}
		if( ! preg_match( "/^[0-9]{20,25}$/", $save_rschet ) )
		{
			$save_err[2] .= '<p>- Рассчетный счет неверного формата</p>';
			$save_err_flag[ 'save_rschet' ]= true;
		}
		if( ! preg_match( "/^[0-9]{20}$/", $save_kschet ) )
		{
			$save_err[2] .= '<p>- Корреспондентский счёт неверного формата</p>';
			$save_err_flag[ 'save_kschet' ]= true;
		}
		if( ! $save_err[2] )
		{
			mysql_query( "INSERT INTO ".$modx->getFullTableName( '_user_h' )." SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
			
			mysql_query( "UPDATE ".$modx->getFullTableName( '_user' )." SET
						urlico='{$save_urlico}',
						company='{$save_company}',
						phone_company='{$phone_company}',
						address_ur='{$save_address_ur}',
						address_ft='{$save_address_ft}',
						inn='{$save_inn}',
						kpp='{$save_kpp}',
						bik='{$save_bik}',
						ogrn='{$save_ogrn}',
						bank='{$save_bank}',
						rschet='{$save_rschet}',
						kschet='{$save_kschet}'
							WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
			
			header( 'location: '. $topage_url .'?ok21' );
			exit();
			
		}else{
			$post[ 'urlico' ]= $save_urlico;
			
			$webuserinfo[ 'company' ]= $save_company;
			$webuserinfo[ 'address_ur' ]= $save_address_ur;
			$webuserinfo[ 'address_ft' ]= $save_address_ft;
			$webuserinfo[ 'inn' ]= $save_inn;
			$webuserinfo[ 'kpp' ]= $save_kpp;
			$webuserinfo[ 'bik' ]= $save_bik;
			$webuserinfo[ 'ogrn' ]= $save_ogrn;
			$webuserinfo[ 'bank' ]= $save_bank;
			$webuserinfo[ 'rschet' ]= $save_rschet;
			$webuserinfo[ 'kschet' ]= $save_kschet;
			$webuserinfo[ 'phone_company' ]= $phone_company;
		}
	}
	
	if( $save_urlico == 'n' )
	{
		if( ! $save_err[2] )
		{
			mysql_query( "UPDATE ".$modx->getFullTableName( '_user' )." SET
						urlico='{$save_urlico}'
							WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
			
			header( 'location: '. $topage_url .'?ok22' );
			exit();
			
		}else{
			$post[ 'urlico' ]= $save_urlico;
		}
	}
}
?>
	
	
	
	
	
<?php
if( isset( $_POST[ 'save_51' ] ) )
{
	$vkladka_active= 2; 
	$save_dopinfo= addslashes( trim( $_POST[ 'save_dopinfo' ] ) );
	

	$save_agreed= ( $_POST[ 'save_agreed' ] == 'y' ? 'y' : 'n' );
	$save_seller= ( $_POST[ 'save_seller' ] == 'y' ? 'test' : 'n' );
	
	if( $webuserinfo[ 'seller' ] == 'y' && $save_agreed != 'y' )
	{
		$save_err[51] .= '<p>- Подтвердите изменение данных</p>';
	}
	
	if( $save_seller != 'n' )
	{
		if( ! $save_dopinfo  )
		{
			$save_err[51] .= '<p>- Заполните краткое описание Вашего магазина</p>';
			$save_dopinfo[ 'save_dopinfo' ]= true;
		}

		if( ! $save_err[51] )
		{
			$_SESSION[ 'webuserinfo' ][ 'seller_descr' ] = $temp['description'] = $save_dopinfo;
			mysql_query( "INSERT INTO ".$modx->getFullTableName( '_user_h' )." SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
			mysql_query( "UPDATE ".$modx->getFullTableName( '_user' )." SET
						seller='{$save_seller}'
						WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
						

			$tmp = mysql_query("SELECT `id` FROM ".$modx->getFullTableName( '_user_seller_descr' )." 
						WHERE id_seller='". $_SESSION[ 'webuserinfo' ][ 'id' ] ."' LIMIT 1" );	
			
			if(mysql_num_rows( $tmp ) > 0 ){
			
				 mysql_query("UPDATE  ".$modx->getFullTableName( '_user_seller_descr' )." SET 
							description='{$save_dopinfo}'
							WHERE id_seller=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
							
			}else {
				
				// mysql_query("INSERT INTO  ".$modx->getFullTableName( '_user_seller_descr' )." (`seller`, `description`) VALUES (". $_SESSION[ 'webuserinfo' ][ 'id' ] ." ,'{$save_dopinfo}')" );
				mysql_query("INSERT INTO ".$modx->getFullTableName( '_user_seller_descr' )." (
									`id` ,
									`id_seller` ,
									`description`
									)
									VALUES (
									'',  '". $_SESSION[ 'webuserinfo' ][ 'id' ] ."',  '{$save_dopinfo}'
									)" );
			}

			///	
			header( 'location: '. $topage_url .'?ok51' );
			exit();
			
		}else{
			$post[ 'seller' ]= $save_seller;
			$webuserinfo[ 'dopinfo' ]= $save_dopinfo;
		}
	}
	
	if( $save_seller == 'n' )
	{
		if( ! $save_err[51] )
		{
			mysql_query( "UPDATE ".$modx->getFullTableName( '_user' )." SET
						seller='{$save_seller}'
							WHERE id=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" );
			
			header( 'location: '. $topage_url .'?ok51' );
			exit();
			
		}else{
			$post[ 'seller' ]= $save_seller;
		}
	}
}



?>	
	
	

	
	<div class="vkldk_div vkldk_div_2 <?= ( $vkladka_active == 2 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<div class="_LK_form">
				<?php if( isset( $_GET[ 'ok21' ] ) ){ ?>
				<div class="_LK_ok">
					<p>Данные отправлены на проверку!</p>
					<p>Статус юридического лица пока еще не был присвоен!</p>
				</div>
				<?php } ?>
				
				<?php if( isset( $_GET[ 'ok22' ] ) ){ ?>
				<div class="_LK_ok">
					<p>Сохранено!</p>
				</div>
				<?php } ?>
				
				<?php if( ! isset( $_GET[ 'ok21' ] ) && $webuserinfo[ 'urlico' ] == 'test' ){ ?>
				<div class="_LK_info">
					<p>Данные проходят проверку!</p>
					<p>Статус юридического лица не присвоен!</p>
				</div>
				<?php } ?>
				
				<?php if( ! empty( $save_err[2] ) ) print '<div class="_LK_error">'. $save_err[2] .'</div>'; ?>
				<?php if( ! empty( $save_err[51] ) ) print '<div class="_LK_error">'. $save_err[51] .'</div>'; ?>
				<form action="<?= $topage_url ?>" method="post" class="borderdashed">
					
					<?php if( $webuserinfo[ 'urlico' ] != 'n' ){ ?>
					<div class="_LK_form_line _LK_form_line_br">
						<div class="_LK_form_lbl"><b>Статус</b></div>
						<div class="_LK_form_inp _LK_form_inp_txt"><b>
						<?php
							if( $webuserinfo[ 'urlico' ] == 'y' ) print 'Юридическое лицо. Данные подтверждены!';
							elseif( $webuserinfo[ 'urlico' ] == 'test' ) print 'Данные юридического лица на проверке!';
						?>
						</b></div>
						<div class="clr">&nbsp;</div>
					</div>
					<br />
					<?php } ?>
					
					<div class="_LK_form_line _LK_form_line_br">
						<div class="_LK_form_lbl"><input class="LK_form_urlico_checkbox" type="checkbox" name="save_urlico" value="y" <?=( $post[ 'urlico' ] != 'n' ? 'checked="checked"' : 'checked="checked"' )?> /></div>
						<div class="_LK_form_inp"><?= ( $post[ 'urlico' ] == 'y' ? 'Юридическое лицо' : 'Отправить заявку<br />на получение статуса юридического лица' ) ?></div>
						<div class="clr">&nbsp;</div>
					</div>
					
					<div class="LK_form_urlico <?=( $post[ 'urlico' ] != 'n' ? 'LK_form_urlico_active' : '' )?>">
						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl">Наименование компании</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_company' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_company" value="<?= $webuserinfo[ 'company' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Юридический адрес</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_address_ur' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_address_ur" value="<?= $webuserinfo[ 'address_ur' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl">Фактический адрес</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_address_ft' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_address_ft" value="<?= $webuserinfo[ 'address_ft' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						
						
						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl">Телефон компании</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_phone_company' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_phone_company" value="<?= $webuserinfo[ 'phone_company' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">ИНН</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_inn' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_inn" value="<?= $webuserinfo[ 'inn' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">КПП</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_kpp' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_kpp" value="<?= $webuserinfo[ 'kpp' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">БИК</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_bik' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_bik" value="<?= $webuserinfo[ 'bik' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">ОГРН (ОГРНИП)</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_ogrn' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_ogrn" value="<?= $webuserinfo[ 'ogrn' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Рассчетный счет</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_rschet' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_rschet" value="<?= $webuserinfo[ 'rschet' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Наименование банка</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_bank' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_bank" value="<?= $webuserinfo[ 'bank' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl">Корр. счет</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_kschet' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_kschet" value="<?= $webuserinfo[ 'kschet' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
					</div>
						
					<?php if( $webuserinfo[ 'urlico' ] == 'y' ){ ?>
					<div class="_LK_form_line">
						<div class="_LK_form_lbl"><input type="checkbox" name="save_agreed" value="y" required/></div>
						<div class="_LK_form_inp">Подтверждаю изменение данных</div>
						<div class="clr">&nbsp;</div>
					</div>
					<div class="_LK_form_line">
						<div class="_LK_form_lbl"> </div>
						<div class="_LK_form_inp">
							<div class="_LK_info">
								<p>Измененные данные подлежат проверке администратором.</p>
							</div>
						</div>
						<div class="clr">&nbsp;</div>
					</div>
					<?php } ?>
					
					<div class="_LK_form_line _LK_form_line_butt">
						<div class="_LK_form_lbl"> </div>
						<div class="_LK_form_inp"><div class="mainbutton buttonsubmit genSMS" name="save_2" >Сохранить</div></div>
						<div class="clr">&nbsp;</div>
					</div>
					
					<input type="hidden"   name="save_2"/>
					
					<!--div class="darkSMSCode">
						<div class="popupEnterSMS"> 
							<div class="popupHtext">Введите код из СМС</div>
							<div class="popupHdescr">На Ваш мобильный телефон было отправлено сообщение с кодом подтверждения для изменения данных</div>
							<div class="popupDublicSMS"></div>
							<input type="text" class="verifySMScode" maxlength="5"/>
							<div class="waitCheckSMS"><div class="checkResult">Повторно СМС можно выслать через 120 сек.</div><div class="wrap"><div class="dot"></div></div></div>
				
							
						</div>
						<div class="closeAll"></div>
					</div-->
					
					
				</form>
			</div>
		</div>
		
		
		
				
		
		<div class="_LK_wrapper _LK_wrapper_big">
			<div class="_LK_form">
				<?php if( isset( $_GET[ 'ok51' ] )    && $_POST['save_seller'] == 'y' ){ ?>
				<div class="_LK_ok">
					<p>Данные отправлены на проверку!</p>
					<p>Статус продавца пока еще не был присвоен!</p>
				</div>
				<?php } ?>

				<?php if( ! isset( $_GET[ 'ok51' ] ) && $webuserinfo[ 'seller' ] == 'test' ){ ?>
				<div class="_LK_info">
					<p>Данные проходят проверку!</p>
					<p>Статус продавца не присвоен!</p>
				</div>
				<?php } ?>
				
				
				
				<form action="<?= $topage_url ?>" method="post"  class="borderdashed">
					
					<?php if( $webuserinfo[ 'seller' ] != 'n' ){ ?>
					<div class="_LK_form_line _LK_form_line_br">
						<div class="_LK_form_lbl"><b>Статус</b></div>
						<div class="_LK_form_inp _LK_form_inp_txt"><b>
						<?php
							if( $webuserinfo[ 'seller' ] == 'y' ) print 'Продавец. Данные подтверждены!';
							elseif( $webuserinfo[ 'seller' ] == 'test' ) print 'Данные продавца на проверке!';
						?>
						</b></div>
						<div class="clr">&nbsp;</div>
					</div>
					<br />
					<?php } ?>
					
					<div class="_LK_form_line _LK_form_line_br">
						<div class="_LK_form_lbl"><input class="LK_form_urlico_checkbox" type="checkbox" name="save_seller" value="y" <?=( $post[ 'seller' ] != 'n' ? 'checked="checked"' : '' )?> /></div>
						<div class="_LK_form_inp"><?= ( $post[ 'seller' ] == 'y' ? 'Продавец' : 'Отправить заявку<br />на получение статуса продавца' ) ?></div>
						<div class="clr">&nbsp;</div>
					</div>
					
					<div class="LK_form_urlico <?=( $post[ 'seller' ] != 'n' ? 'LK_form_urlico_active' : '' )?>">
					
						<?php
						if ($webuserinfo[ 'urlico' ] == 'y') {
							$urlicoChecked = 'checked="checked"';?>
							
						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl"></div>
							<div class="_LK_form_inp"><input type="hidden" name="type_seller" value="urlico" <?php echo $urlicoChecked?>/>Юридическое лицо</div>
							<!--input type="radio" name="type_seller" value="fizlico" <?php echo $fizlicoChecked?>/>Физическое лицо-->
							<div class="clr">&nbsp;</div>
						</div>
						
						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl">Наименование компании</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_company' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_company" readonly value="<?= $webuserinfo[ 'company' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Телефон компании</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_phone_company' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_phone_company" readonly value="<?= $webuserinfo[ 'phone_company' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						

						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl">Юридический адрес</div>
							<div class="_LK_form_inp <?= ( $save_err_flag[ 'save_address_ur' ] ? '_LK_form_inp_error' : '' ) ?>"><input type="text" name="save_address_ur" readonly value="<?= $webuserinfo[ 'address_ur' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
							
						<?php						
						} else {
							$fizlicoChecked = 'checked="checked"';?>
						   
						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl"></div>
							<div class="_LK_form_inp"><input type="hidden" name="type_seller" value="fizlico" <?php echo $fizlicoChecked?>/>Физическое лицо</div>
							<div class="clr">&nbsp;</div>
						</div>
						<?php	
						}
						
						?>

						
						
						<div class="_LK_form_line _LK_form_line_br">
							<div class="_LK_form_lbl">Краткое описание Вашего магазина</div>
							<div class="_LK_form_inp"><textarea name="save_dopinfo"><?= $sellerDescrInfo ?></textarea></div>
							<div class="clr">&nbsp;</div>
						</div>
						
					</div>
						
					<?php if( $webuserinfo[ 'seller' ] == 'y' ){ ?>
					<div class="_LK_form_line">
						<div class="_LK_form_lbl"><input type="checkbox" name="save_agreed" value="y" /></div>
						<div class="_LK_form_inp">Подтверждаю изменение данных</div>
						<div class="clr">&nbsp;</div>
					</div>
					<div class="_LK_form_line">
						<div class="_LK_form_lbl"> </div>
						<div class="_LK_form_inp">
							<div class="_LK_info">
								<p>Измененные данные подлежат проверке администратором.</p>
							</div>
						</div>
						<div class="clr">&nbsp;</div>
					</div>
					<?php } ?>
					
					<div class="_LK_form_line _LK_form_line_butt">
						<div class="_LK_form_lbl"> </div>
						<div class="_LK_form_inp"><button class="mainbutton buttonsubmit" name="save_51" type="submit">Сохранить</button></div>
						<div class="clr">&nbsp;</div>
					</div>
				</form>
			</div>
		</div>
		
		
		
		
		
		
	</div>
	<!-- ===================================================================================== -->
	
	
	
	
	
	
	
<?php
if( isset( $_POST[ 'save_3' ] ) )
{
	$vkladka_active= 3;
	
	foreach( $_POST[ 'save_sklad' ] AS $num => $address )
	{
		$address= addslashes( trim( $address ) );
		$city= addslashes( trim( $_POST[ 'save_city' ][$num] ) );
		
		$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user_warehouse' )." WHERE seller=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." AND num={$num} LIMIT 1" );
		if( $rr && mysql_num_rows( $rr ) == 1 )
		{
			mysql_query( "UPDATE ".$modx->getFullTableName( '_user_warehouse' )." SET address='{$address}' ,  city='{$city}' WHERE seller=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." AND num={$num} LIMIT 1" );
		}elseif( $rr ){
			mysql_query( "INSERT INTO ".$modx->getFullTableName( '_user_warehouse' )." SET seller=". $_SESSION[ 'webuserinfo' ][ 'id' ] .", num={$num}, address='{$address}' ,  city='{$city}' " );
		}
	}
	
	header( 'location: '. $topage_url .'?ok3' );
	exit();
}
?>
	
	
	
	
	
	
	
	<div class="vkldk_div vkldk_div_3 <?= ( $vkladka_active == 3 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<div class="_LK_form">
				<?php if( isset( $_GET[ 'ok3' ] ) ){ ?>
				<div class="_LK_ok">
					<p>Данные успешно сохранены!</p>
				</div>
				<?php } ?>
				
				<?php if( ! empty( $save_err[3] ) ) print '<div class="_LK_error">'. $save_err[3] .'</div>'; ?>
				
				<form action="<?= $topage_url ?>" method="post">
					
					<div class="_LK_form_line _LK_form_line_br seeWarehouse1">
						<div class="_LK_form_line grayBG">
							<div class="_LK_form_lbl thinFont">Склад №1</div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="clr">&nbsp;</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Город</div>
							<div class="_LK_form_inp"><input type="text" id="search_box1" autocomplete="off" name="save_city[1]" value="<?= $webuserinfo[ 'wh' ][ 1 ][ 'city' ] ?>" /></div>
							<div id="search_advice_wrapper1"></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Адрес</div>
							<div class="_LK_form_inp"><input type="text" name="save_sklad[1]" value="<?= $webuserinfo[ 'wh' ][ 1 ][ 'address' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						
					</div>
					
					<? $wH2style =' style="display:none"';
						if ($webuserinfo[ 'wh' ][ 2 ][ 'city' ] != '') $wH2style =' style="display:block"' ?>
					<div class="_LK_form_line _LK_form_line_br seeWarehouse2"  <?=$wH2style?>>
						
						
						
						<div class="_LK_form_line grayBG">
							<div class="_LK_form_lbl thinFont">Склад №2</div>
							<div class="delInput"  onclick="deletewarehouse(2)"></div>
							<div class="clr">&nbsp;</div>
						</div>
						
						<div class="clr">&nbsp;</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Город</div>
							<div class="_LK_form_inp"><input type="text" id="search_box2" autocomplete="off" name="save_city[2]" value="<?= $webuserinfo[ 'wh' ][ 2 ][ 'city' ] ?>" /></div>
							<div id="search_advice_wrapper2"></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Адрес</div>
							<div class="_LK_form_inp"><input type="text" name="save_sklad[2]" value="<?= $webuserinfo[ 'wh' ][ 2 ][ 'address' ] ?>" /></div>
							
							<div class="clr">&nbsp;</div>
						</div>
						
					</div>
					
					
					
					<? $wH3style =' style="display:none"';
						if ($webuserinfo[ 'wh' ][ 3 ][ 'city' ] != '') $wH3style =' style="display:block"' ?>
					<div class="_LK_form_line _LK_form_line_br seeWarehouse3"  <?=$wH3style?>>
						<div class="_LK_form_line grayBG">
							<div class="_LK_form_lbl thinFont">Склад №3</div>
							<div class="delInput"  onclick="deletewarehouse(3)"></div>
							<div class="clr">&nbsp;</div>
						</div>
						
						<div class="clr">&nbsp;</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Город</div>
							<div class="_LK_form_inp"><input type="text" id="search_box3" autocomplete="off" name="save_city[3]" value="<?= $webuserinfo[ 'wh' ][ 3 ][ 'city' ] ?>" /></div>
							<div id="search_advice_wrapper3"></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Адрес</div>
							<div class="_LK_form_inp"><input type="text" name="save_sklad[3]" value="<?= $webuserinfo[ 'wh' ][ 3 ][ 'address' ] ?>" /></div>
							
							<div class="clr">&nbsp;</div>
						</div>
						
					</div>
					
				
					<div class="addInput"  onclick="addwarehouse()">Добавить еще адрес склада</div>
					<div class="clr">&nbsp;</div>
					
					<div class="_LK_form_line _LK_form_line_butt">
						<div class="_LK_form_lbl"> </div>
						<div class="_LK_form_inp"><button class="mainbutton buttonsubmit" name="save_3" type="submit">Сохранить</button></div>
						<div class="clr">&nbsp;</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- ===================================================================================== -->
	
	
	
	
	
	
	
	<!-- ==========================CONTACTS===START======================================================== -->
<?php
if( isset( $_POST[ 'save_4' ] ) )
{
	$vkladka_active= 4;
	

	$save_contacts_surname =$_POST[ 'save_contacts_surname' ];				
	$save_contacts_email =$_POST[ 'save_contacts_email' ];
	$save_contacts_mobile =$_POST[ 'save_contacts_mobile' ];
	$save_contacts_num =$_POST[ 'save_contacts_num' ];
	$num = 1;
	$count = 0;
	foreach( $_POST[ 'save_contacts_firstname' ] AS $contacts_firstname )
	{
		
		$firstname= addslashes( trim( $contacts_firstname ) );
		$surname= addslashes( trim( $save_contacts_surname[$num] ) );
		$email= addslashes( trim( $save_contacts_email[$num] ) );
		$mobile= addslashes( trim( $save_contacts_mobile[$num] ) );
		$fcurrentnum= addslashes( trim( $save_contacts_num[$num] ) );
		$num++;
		if ($firstname == "" && $surname == "" && $email == "" && $mobile == "") {
			mysql_query( "UPDATE ".$modx->getFullTableName( '_user_contact' )." SET firstname='',surname='',email='',mobile='' WHERE num=".$fcurrentnum."  LIMIT 1" );
			continue;
		}
		$count++; 

		if (($email != '' || $mobile != '') && ($firstname != '' && $surname != '')){
			
			$result = mysql_query( "SELECT id FROM ".$modx->getFullTableName( '_user_contact' )." 
									WHERE user=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." AND num = '{$fcurrentnum}' LIMIT 1" ) or die ('err');
			
			if( $result && mysql_num_rows( $result ) == 1 )
			{
				mysql_query( "UPDATE ".$modx->getFullTableName( '_user_contact' )." SET firstname='{$firstname}',surname='{$surname}',email='{$email}',mobile='{$mobile}' WHERE num=".$fcurrentnum."  LIMIT 1" );
			}elseif( $result ){
				$resultMax = mysql_query( "SELECT MAX(num) FROM ".$modx->getFullTableName( '_user_contact' )." 
									WHERE user=". $_SESSION[ 'webuserinfo' ][ 'id' ] ." LIMIT 1" ) or die ('errMax');
				$maxNum = mysql_result($resultMax,0);
				if ($fcurrentnum > $maxNum) $fcurrentnum = $maxNum+1;
				mysql_query( "INSERT INTO ".$modx->getFullTableName( '_user_contact' )." (id, user, num, firstname, surname, email, mobile) 
								VALUES ('',
										". $_SESSION[ 'webuserinfo' ][ 'id' ] .",
										'{$fcurrentnum}',
										'{$firstname}',
										'{$surname}',
										'{$email}',
										'{$mobile}')" );
			}
		}
			
	}
							
	//return $count.$num;						
	header( 'location: '. $topage_url .'?ok4' );
	exit();
}
?>	
	<div class="vkldk_div vkldk_div_4 <?= ( $vkladka_active == 4 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<div class="_LK_form">
				<?php if( isset( $_GET[ 'ok4' ] ) ){ ?>
				<div class="_LK_ok">
					<p>Данные успешно сохранены!</p>
				</div>
				<?php } ?>
				
				<?php if( ! empty( $save_err[4] ) ) print '<div class="_LK_error">'. $save_err[4] .'</div>'; ?>
				
				<form action="<?= $topage_url ?>" method="post">
					
					
					
					<div class="_LK_form_line _LK_form_line_br seeContacts1">
						<div class="_LK_form_line grayBG">
							<div class="_LK_form_lbl thinFont">Контактное лицо 1</div>
							<input type="hidden" name="save_contacts_num[1]" value="1" />
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Имя</div>
							<div class="_LK_form_inp"><input type="text" name="save_contacts_firstname[1]" value="<?= $userContactFaces[ 1 ][ 'firstname' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Фамилия</div>
							<div class="_LK_form_inp"><input type="text" name="save_contacts_surname[1]" value="<?= $userContactFaces[ 1 ][ 'surname' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">	
							<div class="_LK_form_lbl">Email</div>
							<div class="_LK_form_inp"><input type="text" name="save_contacts_email[1]" value="<?= $userContactFaces[ 1 ][ 'email' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Телефон</div>
							<div class="_LK_form_inp"><input type="text" name="save_contacts_mobile[1]" value="<?= $userContactFaces[ 1 ][ 'mobile' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>	
					</div>
					
					
					<? 
					for ($i = 2;$i<6;$i++){
					$contact2style =' style="display:none"';
					if ($userContactFaces[ $i ][ 'firstname' ] != '') $contact2style =' style="display:block"' ?>
					<div class="_LK_form_line _LK_form_line_br seeContacts<?=$i?>" <?=$contact2style?>>
						
						<div class="_LK_form_line grayBG">
							<div class="_LK_form_lbl thinFont" >Контактное лицо <?=$i?></div>
							<div class="delInput"  onclick="deleteContactFace(<?=$i?>)"></div>
							<input type="hidden" name="save_contacts_num[<?=$i?>]" value="<?=$i?>" />
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Имя</div>
							<div class="_LK_form_inp"><input type="text" name="save_contacts_firstname[<?=$i?>]" value="<?= $userContactFaces[ $i ][ 'firstname' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Фамилия</div>
							<div class="_LK_form_inp"><input type="text" name="save_contacts_surname[<?=$i?>]" value="<?= $userContactFaces[ $i ][ 'surname' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">	
							<div class="_LK_form_lbl">Email</div>
							<div class="_LK_form_inp"><input type="text" name="save_contacts_email[<?=$i?>]" value="<?= $userContactFaces[ $i ][ 'email' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Телефон</div>
							<div class="_LK_form_inp"><input type="text" name="save_contacts_mobile[<?=$i?>]" value="<?= $userContactFaces[ $i ][ 'mobile' ] ?>" /></div>
							<div class="clr">&nbsp;</div>
						</div>
						
					</div>
					<?
					}
					?>
					
					
					<div class="addInput"  onclick="addContactFace()">Добавить еще контактное лицо</div>
					<div class="clr">&nbsp;</div>
					<div class="_LK_form_line _LK_form_line_butt">
						<div class="_LK_form_lbl"> </div>
						<div class="_LK_form_inp"><button class="mainbutton buttonsubmit" name="save_4" type="submit">Сохранить</button></div>
						<div class="clr">&nbsp;</div>
					</div>
					
				</form>
			</div>
		</div>
	</div>
	<!-- ==================CONTACTS==END================================================================= -->
	
	
	
	
	
	
	
	<!-- ================ВКЛАДКА ПРОДАВЕЦ START===================================================================== -->	



	<!-- ==================ВКЛАДКА ПРОДАВЕЦ END====================================================================== -->
	
	
	
	
	
<?php
	
if (isset($_POST['save_6'])) {
	

	
	$userID = $webuserinfo['id'];
	$oldPasswd = addslashes($_POST['oldPass']);
	$newPasswd = addslashes($_POST['newPass']);
	

	if (strlen($newPasswd) >= 6) {
			
			$newPasswd_md = md5($newPasswd);
		
			$sql = "SELECT password, mobile FROM  ".$modx->getFullTableName( '_user' )." WHERE id = {$userID}";
			$result = mysql_query($sql) or die ("ERR 74451 ". mysql_error());
			if ($result && mysql_num_rows($result)>0) {
				if ($tmp = mysql_fetch_assoc($result)){
					$passs = $tmp['password'];
					$mobile = $tmp['mobile'];
					
					//echo $passs;
					//echo '<br/>';
					
		
					$LKok = false;
					
					if (md5($oldPasswd) == $passs){		
						
						//
						$sql = "UPDATE ".$modx->getFullTableName( '_user' )." SET password = '{$newPasswd_md}'   WHERE id = {$userID}";
						$result = mysql_query($sql);
						if ($result) {
							//$mobile = preg_replace('/[^0-9]/', '', $mobile);
							
							//echo $mobile;
							//echo '<br/>';
							$LKok = 'Ваш новый пароль: '.$newPasswd.'. Никому не сообщайте его.';
							$modx->runSnippet( 'sendQuickSMS', array( 'phone' => $mobile, 'text' => 'Ваш новый пароль: '.$newPasswd.'. Никому не сообщайте его.' , 'toStack' => 'true'));
						}
						
					}
				}
			}
	
	
	} 
	
				
	//$password_new= $modx->runSnippet( 'GenerPassword' );
	//$password_new_md5= md5( $password_new );
	
	//$password_new
}

	

	
	
?>
	
	
	<div class="vkldk_div vkldk_div_6 <?= ( $vkladka_active == 6 ? 'active' : '' ) ?>">
	
		<div class="_LK_wrapper _LK_wrapper_big">
			<div class="_LK_form">
				
				<?php if($LKok){ ?>
				<div class="_LK_ok">
					<p><?= $LKok ?></p>
				</div>
				<?php } ?>
				
				
				<!--div class="changePasswdForm"-->
			
			
					<form action="<?= $topage_url ?>" method="post" class="borderdashed">

						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Ваш старый пароль<span class="pxNtc pxNtc_oldPass"></span></div>
							<div class="_LK_form_inp"><input type="password" name="oldPass"  class="oldPass"/></div>
							<div class="clr">&nbsp;</div>
						</div>

						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Новый пароль<span class="pxNtc pxNtc_newPass"></span></div>
							<div class="_LK_form_inp"><input type="password" name="newPass" class="newPass"/></div>
							<div class="clr">&nbsp;</div>
						</div>


						<div class="_LK_form_line">
							<div class="_LK_form_lbl">Повторите новый пароль<span class="pxNtc pxNtc_newPassConfirm"></span></div>
							<div class="_LK_form_inp"><input type="password" name="newPassConfirm"  class="newPassConfirm"/></div>
							<div class="clr">&nbsp;</div>
						</div>


						<div class="_LK_form_line _LK_form_line_butt">
								<div class="_LK_form_lbl"> </div>
								<div class="_LK_form_inp"><div class="mainbutton buttonsubmit genSMS" name="save_6" >Изменить</div></div>
								<div class="clr">&nbsp;</div>
						</div>

						
						<input type="hidden"   name="save_6"/>

						<!--div class="darkSMSCode">
								<div class="popupEnterSMS"> 
									<div class="popupHtext">Введите код из СМС</div>
									<div class="popupHdescr">На Ваш мобильный телефон было отправлено сообщение с кодом подтверждения для изменения данных</div>
									<div class="popupDublicSMS"></div>
									<input type="text" class="verifySMScode" maxlength="5"/>
									<div class="waitCheckSMS"><div class="checkResult">Повторно СМС можно выслать через 120 сек.</div><div class="wrap"><div class="dot"></div></div></div>


								</div>
								<div class="closeAll"></div>
						</div-->

					</form>
			
			
				<!--/div-->
				</div>
			</div>
	
	</div>
	
	<div class="clr">&nbsp;</div>
</div>

<?php
	//





?>