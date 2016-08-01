<?php

$sc_site= 'oboi-rnd.ru';
$sm_base= '../assets/modules/scorn_orders/';
$module_url= MODX_MANAGER_URL .'?a='. $_GET[ 'a' ] .'&id='. $_GET[ 'id' ];
$module_url_webusers= MODX_MANAGER_URL .'?a=112&id=4';




//===============================================================================
$sc_site= 'rosagro-rostov.ru';
   $mailto= 'rosagro@aaanet.ru';
   $mailto_cc= false;
//$mailto= 'sergey.it7@gmail.com';
$mailfrom= 'rosagro-rostov.ru@yandex.ru';
$mailtype= 'smtp';
$mailpassw= 'm_y642onB5pr';
//Любимый киногерой: mWdqjMJ28geu_lSJZ7_0
$smtp= 'smtp.yandex.ru';
$smtpport= 465;
include_once( MODX_MANAGER_PATH .'includes/controls/class.phpmailer.php' );
//SNIPPET PopupForm, SNIPPET FeedBack, SNIPPET ZakazZvonka, SNIPPET BasketOrder, SNIPPET ScornCallBackHunter, SNIPPET LK_Restore_Password, MODULE scorn_orders
//===============================================================================
//===============================================================================



/*
СТАТУСЫ ЗАКАЗОВ:
5 _ заказ изменен и покупатель его не видел
10 _ заказ оформлен покупателем
20 _ заказ изменен, но покупатель его видел
50 _ заказ завершен, баллы начислены
100 _ заказ отменен админом, баллы возвращены
101 _ заказ отменен покупателем, баллы возвращены
*/



$order= intval( $_GET[ 'ord' ] );
$wu= intval( $_GET[ 'wu' ] );



// AJAX =======================================================================
if( isset( $_GET[ 'ajax' ] ) )
{
	
	exit();
}
// AJAX =======================================================================



if( $_GET[ 'act' ] == 'edit' )
{
	
}else{
}



if( $result != '' ) $result .= '<br /><br />';
if( $result2 != '' ) $result2 .= '<br /><br />';

?><div class="modul_scorn_all">
<!-- -------------------------- -->
<link rel="stylesheet" type="text/css" href="<?php print $sm_base;?>_styles.css" />
<script type="text/javascript" src="//yandex.st/jquery/2.1.0/jquery.min.js"></script>
<script type="text/javascript" src="//yandex.st/jquery-ui/1.10.4/jquery-ui.min.js"></script>


<div class="topmenu">
	<ul>
		<li><a href="<?= $module_url ?>">Заказы</a></li>
		<li><a href="<?= $module_url_webusers ?>">Покупатели</a></li>
	</ul>
	<div class="clr">&nbsp;</div>
</div>

<script type="text/javascript">
</script>


<?php
if( $_GET[ 'act' ] == 'full' )
{
	$orderinfo= mysql_query( "SELECT ord.id, ord.email, ord.dt, ord.iduser, ml.mail, ml.editmail, ml.editdt FROM ". $modx->getFullTableName( '_shop_orders' ) ." AS ord
		INNER JOIN ". $modx->getFullTableName( '_shop_order_mail' ) ." AS ml ON ml.`order`=ord.`order`
			WHERE ord.`order`='{$order}' LIMIT 1" );
	if( $orderinfo && mysql_num_rows( $orderinfo ) == 1 )
	{
		$info= mysql_fetch_assoc( $orderinfo );
		$сс= md5( $info[ 'id' ] . $order . $info[ 'email' ] . $info[ 'dt' ] . $info[ 'iduser' ] );
		$print .= mysql_result( $orderinfo, 0, 'mail' );
	}
//===============================================================================================================







	
}elseif( $_GET[ 'act' ] == 'sett' ){
//===============================================================================================================









	
}else{
	if( $_GET[ 'act2' ] == 'cancelorder' )
	{
		$rr= mysql_query( "SELECT * FROM ". $modx->getFullTableName( '_shop_orders' ) ." WHERE `order`='{$order}' LIMIT 1" );
		if( $rr && mysql_num_rows( $rr ) == 1 )
		{
			mysql_query( "UPDATE ". $modx->getFullTableName( '_shop_orders' ) ." SET status=100 WHERE `order`='{$order}' LIMIT 1" );
		}
		header( 'location: '. $module_url );
		exit();
	}
	
	if( $_GET[ 'act2' ] == 'sendmail' )
	{
		$rr= mysql_query( "SELECT ml.mail, ml.editmail, ml.editdt, ord.email, ord.status FROM ". $modx->getFullTableName( '_shop_orders' ) ." AS ord
			INNER JOIN ". $modx->getFullTableName( '_shop_order_mail' ) ." AS ml ON ml.`order`=ord.`order`
				WHERE ord.`order`={$order} LIMIT 1" );
		if( $rr && mysql_num_rows( $rr ) == 1 )
		{
			if( mysql_result( $rr, 0, 'status' ) == 5 )
			{
				mysql_query( "UPDATE ". $modx->getFullTableName( '_shop_orders' ) ." SET status=20 WHERE `order`={$order} LIMIT 1" );
			}
			
			$order_email= mysql_result( $rr, 0, 'email' );
			$order_mail= mysql_result( $rr, 0, 'mail' );
			if( mysql_result( $rr, 0, 'editdt' ) )
			{
				$order_mail= mysql_result( $rr, 0, 'editmail' );
			}
// ============================================================================
			if( $mailtype == 'smtp' )
			{
				$phpmailer= new PHPMailer();
				$phpmailer->isSMTP();
				if( false )
				{
					$phpmailer->SMTPDebug= 2;
					$phpmailer->Debugoutput = 'html';
				}
				$phpmailer->Host= $smtp;
				$phpmailer->Port= $smtpport;
				$phpmailer->SMTPAuth= true;
				$phpmailer->SMTPSecure= 'ssl';
				$phpmailer->Username= $mailfrom;
				$phpmailer->Password= $mailpassw;
				$phpmailer->CharSet= 'utf-8';
				$phpmailer->From= $mailfrom;
				$phpmailer->FromName= "";
				$phpmailer->addBCC( $mailto );
				if( $mailto_cc ) $phpmailer->addBCC( $mailto_cc );
				$phpmailer->isHTML( true );
				$phpmailer->Subject= "Оформленный заказ с сайта www.". $sc_site;
				$phpmailer->Body= $order_mail;
				$phpmailer->addAddress( $order_email );
				$phpmailer->send();
				
			}else{
				$to_client= "<". $order_email .">";
				$subject= "Оформленный заказ с сайта ". $sc_site;
				$headers= "Content-type: text/html; charset=utf-8 \n";
				$headers .= "From: <". $mailfrom .">\n";
				mail( $to_client, $subject, $order_mail, $headers );
			}
// ============================================================================
		}
		header( 'location: '. $module_url );
		exit();
	}
	
	
	
	
	
	$orders= mysql_query( "SELECT * FROM ". $modx->getFullTableName( '_shop_orders' ) ." ".( $wu ? "WHERE iduser={$wu}" : "" )." ORDER BY dt DESC ".( $wu ? "" : "LIMIT 100" )."" );
	if( $orders && mysql_num_rows( $orders ) > 0 )
	{
		if( ! $wu ) $print .= '<p>Последних 100 заказов:</p>';
		if( $wu ) $print .= '<p>Заказы покупателя #'. $wu .'</p>';
		
		$print .= '
			<table class="orders_table" cellpadding="0" cellspacing="0">
				<tr class="tit">
					<td class="podrobn" valign="center">&nbsp;</td>
					<td class="num" valign="center">Номер<br />заказа</td>
					<td class="summa" valign="center">Сумма, руб.</td>
					<td class="fio" valign="center">Имя<br />Фамилия</td>
					<td class="email" valign="center">E-mail</td>
					<td class="phone" valign="center">Телефон</td>
					<td class="gorod" valign="center">Город</td>
					<td class="status" valign="center">Статус</td>
					<td class="status" valign="center">&nbsp;</td>
					<td class="date" valign="center">Дата<br />время</td>
				</tr>';
		$ii= 0;	
		while( $row= mysql_fetch_assoc( $orders ) )
		{
			$status= '';
			if( $row[ 'status' ] == 5 )
			{
				$status= '<p>Заказ отредактирован администратором.</p><p>Покупатель изменения НЕ видел.</p>';
				
			}elseif( $row[ 'status' ] == 10 ){
				$status= '<p>Заказ оформлен покупателем.</p>';
				
			}elseif( $row[ 'status' ] == 20 ){
				$status= '<p>Заказ отредактирован администратором.</p><p>Покупатель уведомлен.</p>';
				
			}elseif( $row[ 'status' ] == 50 ){
				$status= '<p>Заказ завершен.</p>';
				
			}elseif( $row[ 'status' ] == 100 ){
				$status= '<p>Заказ отменен администратором.</p>';
				
			}elseif( $row[ 'status' ] == 101 ){
				$status= '<p>Заказ отменен покупателем.</p>';
			}
			
			$ii++;
			$print .= '<tr class="item '.( $ii % 2 == 0 ? 'item_chet' : '' ).' status_'. $row[ 'status' ] .'">
					<td class="podrobn" valign="center">
						<a href="'. $module_url .'&act=full&ord='. $row[ 'order' ] .'">Подробнее о заказе</a>';
			
			if( $row[ 'iduser' ] )
				$print .= '<br /><br /><a href="'. $module_url_webusers .'&act=seluser&wu='. $row[ 'iduser' ] .'">Аккаунт покупателя</a>';
						
			$print .= '</td>
					
					<td class="num" valign="center">'. $row[ 'order' ] .'</td>
					
					<td class="summa" valign="center">'. price( $row[ 'itogo' ] ) .'</td>
					
					<td class="fio" valign="center">'. $row[ 'fio' ] .'</td>
					
					<td class="email" valign="center"><a target="_blank" href="mailto:'. $row[ 'email' ] .'">'. $row[ 'email' ] .'</a></td>
					
					<td class="phone" valign="center">'. $row[ 'phone' ] .'</td>
					
					<td class="gorod" valign="center">'. $row[ 'gorod' ] .'</td>
					
					<td class="status" valign="center">'. $status;
					
			if( $row[ 'status' ] <= 20 )
			{
				$print .= '<p><a href="'. $module_url .'&act2=cancelorder&ord='. $row[ 'order' ] .'" style="color:#d00;">Отменить заказ</a></p>';
			}
			
			$print .= '</td>
					
					<td class="action" valign="center">';
			
			if( $row[ 'status' ] == 5 )
			{
				$print .= '<a href="'. $module_url .'&act2=sendmail&ord='. $row[ 'order' ] .'">Уведомить покупателя<br />об изменениях в заказе</a>';
			}elseif( $row[ 'status' ] == 10 ){
				$print .= '<a href="'. $module_url .'&act2=sendmail&ord='. $row[ 'order' ] .'">Отправить заказ покупателю<br />еще раз</a>';
			}elseif( $row[ 'status' ] == 20 ){
				$print .= '<a href="'. $module_url .'&act2=sendmail&ord='. $row[ 'order' ] .'">Отправить заказ покупателю<br />еще раз</a>';
			}elseif( $row[ 'status' ] == 50 ){
			}elseif( $row[ 'status' ] == 100 ){
			}elseif( $row[ 'status' ] == 101 ){
			}
			
			$print .= '</td>
					
					<td class="date" valign="center">'. date( "d.m.Y", $row[ 'dt' ] ) .'<br />'. date( "H:i", $row[ 'dt' ] ) .'</td>';
			$print .= '</tr>';
		}
	}
}

print $print;
?>


<?php




// ВЫВОД ЦЕНЫ
function price( $price )
{
	if( empty( $delimiter ) ) $delimiter= '&thinsp;';
	if( empty( $round ) ) $round= 2;

	$price= mysql_escape_string( trim( $price ) );

	$price= str_replace( ",", ".", $price );

	$price= round( $price, $round );

	if( $price <= 0 || $price == '' ) return "&mdash;";
	
	$tmp= explode( ".", $price );

	$itogo_price= '';
	$ii= 0;
	for( $kk=strlen( $tmp[ 0 ] )-1; $kk >= 0; $kk-- )
	{
		$ii++;
		$itogo_price= substr( $tmp[ 0 ], $kk, 1 ) . $itogo_price;
		if( $ii % 3 == 0 )
		{
			$itogo_price= $delimiter . $itogo_price;
		}
	}
	if( $tmp[ 1 ] > 0 ) $itogo_price .= ','. $tmp[ 1 ];
	
	return $itogo_price;
}
// ВЫВОД ЦЕНЫ


/*
*/
?>