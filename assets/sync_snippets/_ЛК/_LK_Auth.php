<?php

//==================================================================================
$lk= 106;
$reg= 107;
$auth= 108;
$restorepassword= 109;
$agreed= 110;
$DMTCaptcha= 111;
$DMTCaptcha_img= 112;
//==================================================================================

$topage= ( $topage ? intval( $topage ) : $modx->documentIdentifier );
$topage_url= $modx->makeUrl( $topage );


if( $_SESSION[ 'webuserinfo' ][ 'auth' ] === true){
	header( 'location: '. $modx->makeUrl( $lk ) );
	exit();
}

// =======================================================================================================


if( isset( $_POST[ 'auth_auth' ] ) && $_SESSION[ 'webuserinfo' ][ 'auth' ] !==true )
{! 
	$auth_email= addslashes( trim( $_POST[ 'auth_email' ] ) );
	$auth_passw= md5( $_POST[ 'auth_passw' ] );
	
	$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE email='{$auth_email}' AND password='{$auth_passw}' LIMIT 1" );
	if( $rr && mysql_num_rows( $rr ) == 1 )
	{
		
		
		
		$_SESSION[ 'webuserinfo' ][ 'info' ]= mysql_fetch_assoc( $rr );
		$_SESSION[ 'webuserinfo' ][ 'id' ]= $_SESSION[ 'webuserinfo' ][ 'info' ][ 'id' ];
		$_SESSION[ 'webuserinfo' ][ 'auth' ]= true;
		$_SESSION[ 'webuserinfo' ][ 'login' ]= time();
		
		
		// конвертировать SESSID в userID (КОРЗИНА) START
		$sessid = session_id();
		$userId = $_SESSION[ 'webuserinfo' ][ 'id' ];
		$sql = "UPDATE ".$modx->getFullTableName( '_shop_basket' )." SET id_user = {$userId} WHERE sessid = '{$sessid}' ";
		$result = mysql_query($sql);
		
		
		// конвертировать SESSID в userID (КОРЗИНА) END
		
		

		$result =  mysql_query( "SELECT description FROM ".$modx->getFullTableName( '_user_seller_descr' )." WHERE id_seller='".$_SESSION[ 'webuserinfo' ][ 'id' ]."' LIMIT 1" );
		if( $result && mysql_num_rows( $result ) == 1 ){
			$temp =  mysql_fetch_assoc( $result );
			$_SESSION[ 'webuserinfo' ][ 'seller_descr' ] = $temp['description'];
		}
		
		
		$result =  mysql_query( "SELECT address FROM ".$modx->getFullTableName( '_user_warehouse' )." WHERE seller='".$_SESSION[ 'webuserinfo' ][ 'id' ]."' LIMIT 3" );
		if( $result && mysql_num_rows( $result ) > 0 ){
			$_SESSION[ 'webuserinfo' ][ 'seller_warehouses' ] = mysql_fetch_assoc( $result );
		}
		
		
		$result =  mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user_contact' )." WHERE firstname <> '' AND surname <> '' AND user='".$_SESSION[ 'webuserinfo' ][ 'id' ]."' LIMIT 5" );
		if( $result && mysql_num_rows( $result ) > 0 ){
			$j=1;
			while ($assoc_row = mysql_fetch_assoc( $result )){
				$_SESSION[ 'webuserinfo' ][ 'contact_faces' ][$j] = $assoc_row;
				$j++;
			}
		}
		
		
		header( 'location: '. $topage_url );
		exit();
		
	}elseif( $rr ){
		$auth_err .= '<p>- Неверно введены Логин или пароль</p>';
	}else{
		$auth_err .= '<p>- Ошибка базы данных (003)</p>';
	}
}


?>
<div class="_LK_wrapper">
	<div class="_LK_form _LK_form_auth _LK_form_left">
		<div class="_LK_form_tit">Вход в систему</div>
		
		<?php if( ! empty( $auth_err ) ) print '<div class="_LK_error">'. $auth_err .'</div>'; ?>
		
		<form action="<?= $topage_url ?>" method="post">
			<div class="_LK_form_line">
				<div class="_LK_form_lbl">Адрес электронной почты</div>
				<div class="_LK_form_inp"><input type="text" name="auth_email" value="<?= $auth_email ?>" /></div>
				<div class="clr">&nbsp;</div>
			</div>
			
			<div class="_LK_form_line _LK_form_line_br">
				<div class="_LK_form_lbl">Пароль</div>
				<div class="_LK_form_inp"><input type="password" name="auth_passw" /></div>
				<div class="clr">&nbsp;</div>
			</div>
			
			<div class="_LK_form_line">
				<div class="_LK_form_lbl"></div>
				<div class="_LK_form_inp"><a class="as1" href="<?= $modx->makeUrl( $restorepassword ) ?>">Забыли пароль?</a></div>
				<div class="clr">&nbsp;</div>
			</div>
			
			<div class="_LK_form_line _LK_form_line_butt">
				<div class="_LK_form_lbl"> </div>
				<div class="_LK_form_inp"><button class="mainbutton buttonsubmit" name="auth_auth" type="submit">Войти</button></div>
				<div class="clr">&nbsp;</div>
			</div>
		</form>
	</div>
	
	<div class="_LK_form _LK_form_info _LK_form_right">
		<div class="_LK_form_tit">Впервые на нашем портале?</div>
		
		<p>Зарегистрированные пользователи получают следующие дополнительные возможности:</p>
		<ul>
			<li>участие в аукционах;</li>
			<li>добавление товара в избранное;</li>
			<li>прямое общение с администрацией портала;</li>
			<li>отправка претензий;</li>
		</ul>
		
		<p><a class="as1" href="[~<?= $reg ?>~]">Регистрация в системе</a></p>
	</div>
	
	<div class="clr">&nbsp;</div>
</div>
<?php
	//





?>