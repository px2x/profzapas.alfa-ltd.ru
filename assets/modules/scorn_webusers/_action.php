<?php


$sc_site= 'profzapas.ru';
$sm_base= '../assets/modules/scorn_webusers/';
$module_url= MODX_MANAGER_URL .'?a='. $_GET[ 'a' ] .'&id='. $_GET[ 'id' ];
$module_url_orders= MODX_MANAGER_URL .'?a=112&id=3';

$webuser= intval( $_GET[ 'wu' ] );
$subpage= $_GET[ 'spg' ];
$act= $_GET[ 'act' ];


/*======================Количество заявок=START===============*/
	$rr= mysql_query( "SELECT count(id) FROM ".$modx->getFullTableName( '_user' )." WHERE urlico = 'test' OR seller = 'test' ORDER BY enabled, id DESC" );
	if( $rr && mysql_num_rows( $rr ) > 0 )
	{
		$row = mysql_fetch_row( $rr ) ;
		$countRequest =  $row[0];
	}
	
/*======================Количество заявок=END===============*/


if( $result != '' ) $result .= '<br /><br />';
if( $result2 != '' ) $result2 .= '<br /><br />';

?><div class="modul_scorn_all">
<!-- -------------------------- -->
<link rel="stylesheet" type="text/css" href="<?php print $sm_base;?>_styles.css" />
<script type="text/javascript" src="//yandex.st/jquery/2.1.0/jquery.min.js"></script>
<script type="text/javascript" src="//yandex.st/jquery-ui/1.10.4/jquery-ui.min.js"></script>

<script type="text/javascript">

$(window).load(function(){
	
	
	blockMsgContent = $(".msgContent");
	//blockMsgContent.scrollTop('999999999') 
	blockMsgContent.animate({scrollTop:9999}, '2000', 'swing', function() { 
	   //alert("Finished animating");
	});
});

</script>
<div class="topmenu">
	<ul>
		<li><a href="<?= $module_url ?>">Физ.лица</a></li>
		<li><a href="<?= $module_url ?>&spg=urlica">Юр.лица</a></li>
		<li><a href="<?= $module_url ?>&spg=sellers">Продавцы</a></li>
		<li><a href="<?= $module_url ?>&spg=requests">Заявки (+<?php echo $countRequest?>)</a></li>
		<li><a href="<?= $module_url ?>&spg=search">Поиск</a></li>
	</ul>
	<div class="clr">&nbsp;</div>
</div>

<script type="text/javascript">
</script>



<?php

//===============================================================================================================
if( $subpage == 'urlica' )
{
	
	$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE urlico = 'y' ORDER BY enabled, id DESC" );
	if( $rr && mysql_num_rows( $rr ) > 0 )
	{
		$print .= '<table class="userstable">';
		while( $row= mysql_fetch_assoc( $rr ) )
		{
			$print .= '
			<tr>
				<td>#'. $row[ 'id' ] .' <a href='.$module_url.'&spg=seeUserInfo&userId='. $row[ 'id' ] .'>'. $row[ 'email' ] .'</a> </td><td> ('. $row[ 'firstname' ] .' '. $row[ 'surname' ].')  </td><td> '.$row['mobile'].'</td>
			</tr>';
		}
		$print .= '</table>';
	}
	
	
	
	
	
	
	
	
	
	
	
//===============================================================================================================
}elseif( $subpage == 'search' ){
	
	

	
	
	
	
	
	
	
	
	
	
	
	
//============================Cтраница продавца===START================================================================================
//
}elseif( $subpage == 'seeUserInfo' ){
	
	$print = '';
	
	
	
	//если необходимо зачислить аванс
	if (isset($_POST['prepaymentSubmit']) && is_numeric($_POST['prepayment']) && is_numeric($_GET['userId'])){
		
		
		$coins = addslashes($_POST['prepayment']);
		$userId = addslashes($_GET['userId']);
		$toDBdate = time();
		$toDBevent = $coins; // может быть со знаком минус
		$toDBtype = 'prepayment';
		
		//всего средств
		$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' LIMIT 1";
		$resultSumm = mysql_query($sqlSumm) or die ('Error всего средств');
		
		if ($coinsSummHH = mysql_fetch_row($resultSumm)){
			$coinsSummHH = $coinsSummHH[0]+$toDBevent;
		}else {
			$coinsSummHH = $toDBevent;
		}

		$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
        //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
	   //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
	   //TEMPORARY WITHOUT HASH
	   $hash = ($toDBdate.'_'.$toDBevent.'_'.$toDBtype.'_'.$userId.'_'.$coinsSummHH);
        
		$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins' )." (`id`, `id_user`, `hash`) VALUES (NULL, '{$userId}', '{$hash}') ON DUPLICATE KEY UPDATE `hash` = '".$hash."' ") or die ("Error 546: ".mysql_error());
		$result = mysql_query("SELECT id FROM ".$modx->getFullTableName( '_coins' )." WHERE  id_user = {$userId} LIMIT 1") or die ("Error 547: ".mysql_error());
		
		if (!$id_coins = mysql_fetch_assoc($result)['id']) {
			exit();
		}else{
			$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins_history' )." (`id`, `id_user`, `id_coins`, `event`, `date`, `type`) VALUES (NULL, {$userId}, {$id_coins}, {$toDBevent},'{$toDBdate}', '{$toDBtype}' )") or die ("Error 548: ".mysql_error());
			
			if ($result){
				echo 'ok';
                
                //fromRequest
                if (is_numeric($_POST['fromRequest'])) {
                    $reqPrepay = addslashes($_POST['fromRequest']);
                    
                    $sql="DELETE FROM ".$modx->getFullTableName( '_request_prepay' )." WHERE id = ".$reqPrepay."  LIMIT 1";
                    mysql_query($sql);
                    
                }
                
                
			}else exit();
			
		}
	}
	
	
	
	
	//если подтверидить вывод средств
	if (is_numeric($_GET['paydown']) && is_numeric($_GET['userId'])){
		$payDownId  = $_GET['paydown'];
		$userId = $_GET['userId'];
		$sqlPoints = "SELECT * FROM  ".$modx->getFullTableName( '_points_up' )." WHERE id_user =  '".$userId."' AND id = '".$payDownId."'  LIMIT 1";
		$resultPoints = mysql_query($sqlPoints) or die (mysql_error());
		
		if ($toDBeventArr = mysql_fetch_assoc($resultPoints)){
			//$print .='RRRRRRRRRRRRRR';
			$toDBevent = $toDBeventArr['sum'] * -1;
			$toDBdate = time();
			$toDBtype = 'payDown';
			$toDBtimest_fk = $toDBeventArr['timest'];
			//всего средств
			$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' LIMIT 1";
			$resultSumm = mysql_query($sqlSumm) or die ('Error всего средств');
			
			if ($coinsSummHH = mysql_fetch_row($resultSumm)){
				$coinsSummHH = $coinsSummHH[0]+$toDBevent;
				
				$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
                //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
               //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
               //TEMPORARY WITHOUT HASH
               $hash = ($toDBdate.'_'.$toDBevent.'_'.$toDBtype.'_'.$userId.'_'.$coinsSummHH);
				$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins' )." (`id`, `id_user`, `hash`) VALUES (NULL, '{$userId}', '{$hash}') ON DUPLICATE KEY UPDATE `hash` = '".$hash."' ") or die ("Error 546: ".mysql_error());
				$result = mysql_query("SELECT id FROM ".$modx->getFullTableName( '_coins' )." WHERE  id_user = {$userId} LIMIT 1") or die ("Error 547: ".mysql_error());
				
				if (!$id_coins = mysql_fetch_assoc($result)['id']) {
					exit();
				}else{
					$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins_history' )." (`id`, `id_user`, `id_coins`, `event`, `date`, `type`, `timest_fk`) VALUES (NULL, {$userId}, {$id_coins}, {$toDBevent},'{$toDBdate}', '{$toDBtype}' , '{$toDBtimest_fk}')") or die ("Error 548: ".mysql_error());
					
					if ($result){
						//$sqlPointsUpd = "UPDATE  ".$modx->getFullTableName( '_points_up' )." SET status = 'ok' WHERE id_user =  '".$userId."' AND id = '".$payDownId."'  LIMIT 1";
						$sqlPointsUpd = "UPDATE  ".$modx->getFullTableName( '_points_up' )." SET  `status` =  'ok' WHERE id_user =  '".$userId."' AND id = ".$payDownId;
						//UPDATE  `alfa-ltd-ru_profzapas`.`profzapas__points_up` SET  `status` =  'ok' WHERE  `profzapas__points_up`.`id` =5;
						$resultPoints = mysql_query($sqlPointsUpd) or die ('Error всего средств');
						//echo 'ok';
                        
                        $sqlEvent = "INSERT INTO  ".$modx->getFullTableName( '_finances_events' )."  
                        (`timestamp`, `id_user`, `see`) VALUES 
                        (UNIX_TIMESTAMP(), '".$userId."', 0)";
                        $resultEvent = mysql_query($sqlEvent);
                        
					}else exit();
				}	
			}	
		}
	}
	
	
	
	
	
	
	
	//если назначить персональную скидку
	if (is_numeric($_POST['personalDiscount']) && is_numeric($_GET['userId']) && isset($_POST['personalDiscountSubmit'])){
		$personalDiscount  = $_POST['personalDiscount'];
		$userId = $_GET['userId'];
		$timest = time();
		if ($personalDiscount > 0 && $personalDiscount < 91 ) {
			$sqlPDiscount = "INSERT INTO  ".$modx->getFullTableName( '_personal_discount' )." (
						`id`,
						`id_user`,
						`p_discount`,
						`timest`
						) VALUES (
						NULL,
						'".$userId."', 
						'".$personalDiscount."',
						'".$$timest."'
						) ON DUPLICATE KEY UPDATE `p_discount` = '".$personalDiscount."' ";
			$resultPDiscount = mysql_query($sqlPDiscount) or die ("Error 563: ".mysql_error());				
		}else {
			$sqlPDiscount = "DELETE FROM ".$modx->getFullTableName( '_personal_discount' )." WHERE `id_user` = {$userId} LIMIT 1";
			$resultPDiscount = mysql_query($sqlPDiscount) or die ("Error 599: ".mysql_error());			
		}
		

	
	}
	
	
	
	
	
	//
	
	//если подтверидить пополнение
	if (is_numeric($_GET['payup']) && is_numeric($_GET['userId'])){
		$payUpId  = $_GET['payup'];
		$userId = $_GET['userId'];
		$sqlPoints = "SELECT * FROM  ".$modx->getFullTableName( '_points_up' )." WHERE id_user =  '".$userId."' AND id = '".$payUpId."' AND status = 'wait'  LIMIT 1";
		$resultPoints = mysql_query($sqlPoints) or die (mysql_error());
		
		if ($toDBeventArr = mysql_fetch_assoc($resultPoints)){
			//$print .='RRRRRRRRRRRRRR';
			$toDBevent = $toDBeventArr['sum'];
			$toDBdate = time();
			$toDBtype = 'payUp';
			$toDBtimest_fk = $toDBeventArr['timest'];
			//всего средств
			$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' LIMIT 1";
			$resultSumm = mysql_query($sqlSumm) or die ('Error 550');
			
			if ($coinsSummHH = mysql_fetch_row($resultSumm)){
				$coinsSummHH = $coinsSummHH[0]+$toDBevent;
				
				$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
                //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
               //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
               //TEMPORARY WITHOUT HASH
               $hash = ($toDBdate.'_'.$toDBevent.'_'.$toDBtype.'_'.$userId.'_'.$coinsSummHH);
				$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins' )." (`id`, `id_user`, `hash`) VALUES (NULL, '{$userId}', '{$hash}') ON DUPLICATE KEY UPDATE `hash` = '".$hash."' ") or die ("Error 546: ".mysql_error());
				$result = mysql_query("SELECT id FROM ".$modx->getFullTableName( '_coins' )." WHERE  id_user = {$userId} LIMIT 1") or die ("Error 547: ".mysql_error());
				
				if (!$id_coins = mysql_fetch_assoc($result)['id']) {
					exit();
				}else{
					$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins_history' )." (`id`, `id_user`, `id_coins`, `event`, `date`, `type`, `timest_fk`) VALUES (NULL, {$userId}, {$id_coins}, {$toDBevent},'{$toDBdate}', '{$toDBtype}' , '{$toDBtimest_fk}')") or die ("Error 548: ".mysql_error());
					
					if ($result){
						//$sqlPointsUpd = "UPDATE  ".$modx->getFullTableName( '_points_up' )." SET status = 'ok' WHERE id_user =  '".$userId."' AND id = '".$payDownId."'  LIMIT 1";
						$sqlPointsUpd = "UPDATE  ".$modx->getFullTableName( '_points_up' )." SET  `status` =  'ok' WHERE id_user =  '".$userId."' AND id = ".$payUpId;
						//UPDATE  `alfa-ltd-ru_profzapas`.`profzapas__points_up` SET  `status` =  'ok' WHERE  `profzapas__points_up`.`id` =5;
						$resultPoints = mysql_query($sqlPointsUpd) or die ('Error 551');
						//echo 'ok';
                        
                        $sqlEvent = "INSERT INTO  ".$modx->getFullTableName( '_finances_events' )."  
                        (`timestamp`, `id_user`, `see`) VALUES 
                        (UNIX_TIMESTAMP(), '".$userId."', 0)";
                        $resultEvent = mysql_query($sqlEvent);
                        
					}else exit();
				}	
			}	
		}
	}
	
	
	
	
	//если подтверидить оплату аванса
	if (is_numeric($_GET['closePrepay']) && is_numeric($_GET['userId'])){
		$closePrepayId  = $_GET['closePrepay'];
		$userId = $_GET['userId'];
		$sqlPoints = "SELECT * FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' AND id = '".$closePrepayId."' AND type = 'prepayment'  LIMIT 1";
		$resultPoints = mysql_query($sqlPoints) or die (mysql_error());
		
		if ($toDBeventArr = mysql_fetch_assoc($resultPoints)){

			$toDBevent = $toDBeventArr['event'];
			$toDBdate = time();
			$toDBtype = 'prepaymentPAY';

			//всего средств
			$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' LIMIT 1";
			$resultSumm = mysql_query($sqlSumm) or die ('Error 550');
			
			if ($coinsSummHH = mysql_fetch_row($resultSumm)){
				$coinsSummHH = $coinsSummHH[0]+$toDBevent;
				
				$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
                //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
	   //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
	   //TEMPORARY WITHOUT HASH
	   $hash = ($toDBdate.'_'.$toDBevent.'_'.$toDBtype.'_'.$userId.'_'.$coinsSummHH);
				$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins' )." (`id`, `id_user`, `hash`) VALUES (NULL, '{$userId}', '{$hash}') ON DUPLICATE KEY UPDATE `hash` = '".$hash."' ") or die ("Error 546: ".mysql_error());
				$result = mysql_query("SELECT id FROM ".$modx->getFullTableName( '_coins' )." WHERE  id_user = {$userId} LIMIT 1") or die ("Error 547: ".mysql_error());
				
				if (!$id_coins = mysql_fetch_assoc($result)['id']) {
					exit();
				}else{
					$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins_history' )." (`id`, `id_user`, `id_coins`, `event`, `date`, `type` , `key`) VALUES (NULL, {$userId}, {$id_coins}, {$toDBevent}, '{$toDBdate}', '{$toDBtype}', {$closePrepayId} )") or die ("Error 548: ".mysql_error());
					
					if ($result){
						//$sqlPointsUpd = "UPDATE  ".$modx->getFullTableName( '_points_up' )." SET status = 'ok' WHERE id_user =  '".$userId."' AND id = '".$payDownId."'  LIMIT 1";
						$sqlCoinsUpd = "UPDATE  ".$modx->getFullTableName( '_coins_history' )." SET  `key` =  0 WHERE id_user =  '".$userId."' AND id = ".$closePrepayId;
						//UPDATE  `alfa-ltd-ru_profzapas`.`profzapas__points_up` SET  `status` =  'ok' WHERE  `profzapas__points_up`.`id` =5;
						$resultPoints = mysql_query($sqlCoinsUpd) or die ('Error 551');
						//echo 'ok';
					}else exit();
				}	
			}	
		}
	}
	
	
	
	
		
	//если закрыть часть аванса за счел ЛС
	if (is_numeric($_GET['deductPrepay']) && is_numeric($_GET['userId'])){
		$deductPrepayId  = $_GET['deductPrepay'];
		$userId = $_GET['userId'];
		$sqlPoints = "SELECT * FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' AND id = '".$deductPrepayId."' AND type = 'prepayment'  LIMIT 1";
		$resultPoints = mysql_query($sqlPoints) or die (mysql_error());
		
		if ($toDBeventArr = mysql_fetch_assoc($resultPoints)){

			$toDBevent = $toDBeventArr['event'];
			$toDBdate = time();
			$toDBtype = 'prepaymentPAY';

			
			//select summ wait output
			$sqlSummOutput = "SELECT SUM(sum) FROM  ".$modx->getFullTableName( '_points_up' )." WHERE id_user =  '".$userId."' AND type = 'output'  AND status = 'wait' LIMIT 1";
			$resultSummOutput = mysql_query($sqlSummOutput) or die ('Error select summ wait output');
			$coinsSummOutputInt = mysql_fetch_row($resultSummOutput)[0];
						

			// от продаж или пополнений
			$sqlSummPrivate = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' AND type <> 'prepayment'  AND type <> 'prepaymentPAY' LIMIT 1";
			$resultSummPrivate = mysql_query($sqlSummPrivate) or die ('Error от продаж или пополнений');
			$coinsSummPrivate = mysql_fetch_row($resultSummPrivate)[0] - $coinsSummOutputInt;
						
						
			if (abs($toDBevent) > $coinsSummPrivate) {
				echo "error";
			}else {
			
			
			
				//всего средств
				$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' LIMIT 1";
				$resultSumm = mysql_query($sqlSumm) or die ('Error 550');
				
				if ($coinsSummHH = mysql_fetch_row($resultSumm)){
					$coinsSummHH = $coinsSummHH[0]+$toDBevent;
					
					$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
                    //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
	   //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
	   //TEMPORARY WITHOUT HASH
	   $hash = ($toDBdate.'_'.$toDBevent.'_'.$toDBtype.'_'.$userId.'_'.$coinsSummHH);
					$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins' )." (`id`, `id_user`, `hash`) VALUES (NULL, '{$userId}', '{$hash}') ON DUPLICATE KEY UPDATE `hash` = '".$hash."' ") or die ("Error 546: ".mysql_error());
					$result = mysql_query("SELECT id FROM ".$modx->getFullTableName( '_coins' )." WHERE  id_user = {$userId} LIMIT 1") or die ("Error 547: ".mysql_error());
					
					if (!$id_coins = mysql_fetch_assoc($result)['id']) {
						exit();
					}else{
						$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins_history' )." (`id`, `id_user`, `id_coins`, `event`, `date`, `type` , `key`) VALUES (NULL, {$userId}, {$id_coins}, {$toDBevent}, '{$toDBdate}', '{$toDBtype}', {$deductPrepayId} )") or die ("Error 548: ".mysql_error());
						
						if ($result){
							//$sqlPointsUpd = "UPDATE  ".$modx->getFullTableName( '_points_up' )." SET status = 'ok' WHERE id_user =  '".$userId."' AND id = '".$payDownId."'  LIMIT 1";
							$sqlCoinsUpd = "UPDATE  ".$modx->getFullTableName( '_coins_history' )." SET  `key` =  0 WHERE id_user =  '".$userId."' AND id = ".$deductPrepayId;
							//UPDATE  `alfa-ltd-ru_profzapas`.`profzapas__points_up` SET  `status` =  'ok' WHERE  `profzapas__points_up`.`id` =5;
							$resultPoints = mysql_query($sqlCoinsUpd) or die ('Error 551');
							//echo 'ok';
							//тут списать со счета продавца
							
							//=================================
							
							
							//всего средств (для хеша)
							$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' LIMIT 1";
							$resultSumm = mysql_query($sqlSumm) or die ('Error 937');
							if ($coinsSummHH = mysql_fetch_row($resultSumm)){
								$coinsSummHH = $coinsSummHH[0]+$toDBevent;
							}else {
								$coinsSummHH = $toDBevent;
							}
							$toDBtype = 'payDown';
							
					
								$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
                            //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
                               //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
                               //TEMPORARY WITHOUT HASH
                               $hash = ($toDBdate.'_'.$toDBevent.'_'.$toDBtype.'_'.$userId.'_'.$coinsSummHH);
								$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins' )." (`id`, `id_user`, `hash`) VALUES (NULL, '{$userId}', '{$hash}') ON DUPLICATE KEY UPDATE `hash` = '".$hash."' ") or die ("Error 546: ".mysql_error());
								$result = mysql_query("SELECT id FROM ".$modx->getFullTableName( '_coins' )." WHERE  id_user = {$userId} LIMIT 1") or die ("Error 547: ".mysql_error());
								if (!$id_coins = mysql_fetch_assoc($result)['id']) {
									exit();
								}else{
									$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins_history' )." (`id`, `id_user`, `id_coins`, `event`, `date`, `type`, `shop_id`,`key` ) VALUES (NULL, {$userId}, {$id_coins}, {$toDBevent},'{$toDBdate}', '{$toDBtype}' , '{$toDBshopId}' , {$deductPrepayId} )") or die ("Error 548: ".mysql_error());
									if ($result){
										//echo 'Вы потратили '.$toDBevent.' руб. на товар № '.$toDBshopId.'<br>';
										//echo '<a href="http://www.profzapas.alfa-ltd.ru/lk/finasy.html?tab=1">Вернуться на сайт</a>';
									}else exit('Error 938');
								}
								$result = mysql_query("INSERT INTO ".$modx->getFullTableName( '_coins_history' )." (id, ) VALUES ()");
							
			
							//================================
							
						}else exit();
					}	
				}

			}
			
		}
	}
	
	
	
	
	
	
	
	
	//deductPrepay
	//closePrepay
	
	

	if (is_numeric($_GET['userId'])){
	$userId = addslashes($_GET['userId']);
		
		$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE id =  {$userId} LIMIT 1" );
		if( $rr && mysql_num_rows( $rr ) > 0 )
		{
			$print .= '<table class="userInfostable">';
			while( $row= mysql_fetch_assoc( $rr ) )
			{
				$print .= '
				<tr>
				<td>id#</td><td>'.$row['id'].'</td>
				</tr>
				<tr>
				<td>Имя</td><td>'.$row['firstname'].'</td>
				</tr>
				<tr>
				<td>Фамилия</td><td>'.$row['surname'].'</td>
				</tr>
				<tr>
				<td>Email</td><td>'.$row['email'].'</td>
				</tr>
				<tr>
				<td>Телефон</td><td>'.$row['mobile'].'</td>
				</tr>
				<tr>
				<td>Юридическое лицо</td><td>'.($row['seller'] == 'y' ? 'Да' : 'Нет').'</td>
				</tr>
				<tr>
				<td>Компания</td><td>'.$row['company'].'</td>
				</tr>
				<tr>
				<td>Телефон компании</td><td>'.$row['phone_company'].'</td>
				</tr>
				<tr>
				<td>Город</td><td>'.$row['maincity'].'</td>
				</tr>
				<tr>
				<td>Юридический адрес</td><td>'.$row['address_ur'].'</td>
				</tr>
				<tr>
				<td>Фактический адрес</td><td>'.$row['address_ft'].'</td>
				</tr>
				<tr>
				<td>ИНН</td><td>'.$row['inn'].'</td>
				</tr>
				<tr>
				<td>КПП</td><td>'.$row['kpp'].'</td>
				</tr>
				<tr>
				<td>БИК</td><td>'.$row['bik'].'</td>
				</tr>
				<tr>
				<td>ОГРН</td><td>'.$row['ogrn'].'</td>
				</tr>
				<tr>
				<td>БАНК</td><td>'.$row['bank'].'</td>
				</tr>
				<tr>
				<td>Расчетный счет</td><td>'.$row['rschet'].'</td>
				</tr>
				<tr>
				<td>Корр. счет</td><td>'.$row['kschet'].'</td>
				</tr>
				<tr>
				<td>Паспорт</td><td>'.$row['passport'].'</td>
				</tr>
				<tr>
				<td>Дата выдачи</td><td>'.$row['passport_dth'].'</td>
				</tr>
				<tr>
				<td>Выдан</td><td>'.$row['passport_who'].'</td>
				</tr>
				<tr>
				<td>Адрес прописки</td><td>'.$row['passport_address'].'</td>
				</tr>
				<tr>
				<td>Дата регистрации</td><td>'.date("d.m.Y H:i",$row['dt']).'</td>
				</tr>
				';
			}
			
			
			$sqlPDiscount = "SELECT p_discount FROM  ".$modx->getFullTableName( '_personal_discount' )." WHERE id_user =  '".$userId."'  LIMIT 1";
			$resultPDiscount = mysql_query($sqlPDiscount) or die ('Error Select coins history');
			if  ($row_pd = mysql_fetch_assoc($resultPDiscount)['p_discount']){
				$pagerow_pd = $row_pd;
			} else {
				$pagerow_pd = 0;
			}
			
			$print .= '
			
			<tr>
				<th colspan=2>
				
			<form class="persomaldiscount" action="'.$module_url.'&spg=seeUserInfo&userId='.$userId.'" method="POST">
				<span>Персональная скидка: '.$pagerow_pd.' %</span>
				<input type="text" name="personalDiscount" value="'.$pagerow_pd.'" placeholder="Введите размер скидки" required />
				<input type="submit" name="personalDiscountSubmit" value="Назначить" />
			</form>';
			$print .= '</th></tr></table>';
			
			
			$pageBufferHistory= '';
			//select all history
			//$sql = "SELECT * FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' AND event > 0 AND type <> 'prepayment' ORDER BY id DESC LIMIT 5";
			$sql = "SELECT * FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."'  AND type <> 'prepayment'  AND type <> 'prepaymentPAY' ORDER BY id DESC LIMIT 5";
			$result = mysql_query($sql) or die ('Error Select coins history');
			while ($row = mysql_fetch_assoc($result)){
				$pageBufferHistory.= '<div>'.($row['event'] > 0 ? '+'.$row['event'] : $row['event'] ).'<div>'.date("d.m.Y H:i:s",$row['date']).'</div></div>';
			}

			//select hash
			$sqlHash = "SELECT hash FROM  ".$modx->getFullTableName( '_coins' )." WHERE id_user =  '".$userId."' LIMIT 1";
			$resultHash = mysql_query($sqlHash) or die ('Error Select coins hash');
			$coinsHash = mysql_fetch_row($resultHash)[0];

			//select summ wait output
			$sqlSummOutput = "SELECT SUM(sum) FROM  ".$modx->getFullTableName( '_points_up' )." WHERE id_user =  '".$userId."' AND type = 'output'  AND status = 'wait' LIMIT 1";
			$resultSummOutput = mysql_query($sqlSummOutput) or die ('Error select summ wait output');
			$coinsSummOutputInt = mysql_fetch_row($resultSummOutput)[0];
			
			
			//select summ wait input
			$sqlSummInput = "SELECT SUM(sum) FROM  ".$modx->getFullTableName( '_points_up' )." WHERE id_user =  '".$userId."' AND (type = 'sber' OR type = 'schet')  AND status = 'wait' LIMIT 1";
			$resultSummInput = mysql_query($sqlSummInput) or die ('Error select summ wait input');
			$coinsSummInputInt = mysql_fetch_row($resultSummInput)[0];
			
			
			//select list wait output
			$sqlSummOutputList = "SELECT * FROM  ".$modx->getFullTableName( '_points_up' )." WHERE id_user =  '".$userId."' AND type = 'output'  AND status = 'wait'";
			$resultSummOutputList = mysql_query($sqlSummOutputList) or die ('Error select list wait output');
			$pageBufferWaitOut='';
			while ($row = mysql_fetch_assoc($resultSummOutputList)){
				$pageBufferWaitOut.= '<div>'.$row['sum'].'<div>'.date("d.m.Y H:i:s",$row['timest']).'</div><div class="clr"></div><div><a href="'.$module_url.'&spg=seeUserInfo&userId='.$userId.'&paydown='.$row['id'].'">подтвердить вывод</a></div></div>';
			}

			
			//select list wait input
			$sqlSummInputList = "SELECT * FROM  ".$modx->getFullTableName( '_points_up' )." WHERE id_user =  '".$userId."' AND (type = 'sber' OR type = 'schet')  AND status = 'wait'";
			$resultSummInputList = mysql_query($sqlSummInputList) or die ('Error select list wait input');
			$pageBufferWaitInput='';
			while ($row = mysql_fetch_assoc($resultSummInputList)){
				$pageBufferWaitInput.= '<div>'.$row['sum'].'<div>'.date("d.m.Y H:i:s",$row['timest']).'</div><div class="clr"></div><div><a href="'.$module_url.'&spg=seeUserInfo&userId='.$userId.'&payup='.$row['id'].'">подтвердить получение</a></div></div>';
			}
			
			
			//select list prepayment
			$sqlSummPrepayList = "SELECT * FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' AND (type = 'prepayment' OR type = 'prepaymentPAY' ) ORDER BY id DESC LIMIT 10";
			$resultSummPrepayList = mysql_query($sqlSummPrepayList) or die ('Error select list prepayment '.mysql_error());
			$pageBufferPrepayList='';
			while ($row = mysql_fetch_assoc($resultSummPrepayList)){
				
				if  ($row['event'] < 0  && $row['type'] == 'prepayment'){
					
					if ($row['key'] == -1) {
						$textToBuf = '<div class="clr"></div><div><a href="'.$module_url.'&spg=seeUserInfo&userId='.$userId.'&closePrepay='.$row['id'].'">подтвердить оплату</a></div><div class="clr"></div><div><a href="'.$module_url.'&spg=seeUserInfo&userId='.$userId.'&deductPrepay='.$row['id'].'">закрыть за счет личных средств продавца</a></div>'; 
					} else {
						$textToBuf = '<div class="clr"></div><span>Оплачено</span>';
						
					}
					
				}else {
					$textToBuf = '';
				}
				$pageBufferPrepayList.= '<div>'.($row['event'] < 0 ?  ($row['type'] != 'prepayment' ? ('+'.$row['event']*-1 ): $row['event']  ) : $row['event']  ).'<div>'.date("d.m.Y H:i:s",$row['date']).'</div>'.$textToBuf.'</div>';
			}
			
		
		
		
			//ДЕНЬГИ ЗА ПРОДАЖИ КОТОРЫЕ НА ТЕК. МОМЕНТ В ДОСТАВКЕ
			$sqlSummNotConfirm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." AS hist
								LEFT JOIN ".$modx->getFullTableName( '_orders' )." AS ord ON ord.id = hist.shop_id
								WHERE hist.id_user =  '".$userId."'   
								AND (ord.status <>  'ended'OR hist.key = -1)
								AND hist.shop_id >0
								AND hist.type =  'payUp'
								LIMIT 1";
			$resultNotConfirm = mysql_query($sqlSummNotConfirm) or die ('Error 634437 SUMMPrep coins history');
			$resultNotConfirmInt = mysql_fetch_row($resultNotConfirm)[0];


			//select last rec history
			$sqlLast = "SELECT * FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' ORDER BY id DESC LIMIT 1 ";
			$resultLast = mysql_query($sqlLast) or die ('Error Select coins history');
			$coinsLast = mysql_fetch_assoc($resultLast);
						
			//всего средств
			$sqlSumm = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' AND type <> 'prepaymentPAY' LIMIT 1";
			$resultSumm = mysql_query($sqlSumm) or die ('Error всего средств');
			$coinsSummHH = mysql_fetch_row($resultSumm)[0];
			$coinsSumm = $coinsSummHH - $coinsSummOutputInt;
			
			
			$hash = md5($coinsLast['date'].$coinsLast['event'].$coinsLast['type'].$coinsLast['id_user'].$coinsSummHH);
            //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
           //$hash = md5($toDBdate.$toDBevent.$toDBtype.$userId.$coinsSummHH);
           //TEMPORARY WITHOUT HASH
           $hash =($coinsLast['date'].'_'.$coinsLast['event'].'_'.$coinsLast['type'].'_'.$coinsLast['id_user'].'_'.$coinsSummHH);
			if ($coinsHash!=$hash) {
				$pageBuffer.= 'Ошибка внутреннего счета клиента';
			}
			
			

			

			
			//оплаченный аванс
			$sqlSummPrepayPay = "SELECT SUM(ABS(event)) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' AND type = 'prepaymentPAY' AND event < 0 LIMIT 1";
			$resultSummPrepayPay = mysql_query($sqlSummPrepayPay) or die ('Error в качестве аванса');
			$coinsSummPrepayPay = mysql_fetch_row($resultSummPrepayPay)[0];
			
			
			//в качестве аванса было получено
			$sqlSummPrepay = "SELECT SUM(ABS(event)) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' AND type = 'prepayment' AND event > 0 LIMIT 1";
			$resultSummPrepay = mysql_query($sqlSummPrepay) or die ('Error в качестве аванса');
			$coinsSummPrepay = mysql_fetch_row($resultSummPrepay)[0] - $coinsSummPrepayPay;

			
			
			//в качестве аванса потрачено
			$sqlSummPrepaySpent = "SELECT SUM(ABS(event)) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' AND type = 'prepayment' AND event < 0 LIMIT 1";
			$resultSummPrepaySpent = mysql_query($sqlSummPrepaySpent) or die ('Error 655 '. mysql_error());
			$coinsSummPrepaySpent = mysql_fetch_row($resultSummPrepaySpent)[0] - $coinsSummPrepayPay;

			
			//в качестве аванса остаток
			//$sqlSummPrepayResidue = "SELECT SUM(ABS(event)) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' AND type = 'prepayment' AND event > 0 LIMIT 1";
			//$resultSummPrepayResidue = mysql_query($sqlSummPrepayResidue) or die ('Error 656 '. mysql_error());
			//$coinsSummPrepayResidue = mysql_fetch_row($resultSummPrepayResidue)[0];
			$coinsSummPrepayResidue = $coinsSummPrepay - $coinsSummPrepaySpent;

			
			// от продаж или пополнений
			/*
			$sqlSummPrivate = "SELECT SUM(event) FROM  ".$modx->getFullTableName( '_coins_history' )." WHERE id_user =  '".$userId."' AND type <> 'prepayment'  AND type <> 'prepaymentPAY' LIMIT 1";
			$resultSummPrivate = mysql_query($sqlSummPrivate) or die ('Error от продаж или пополнений');
			$coinsSummPrivate = mysql_fetch_row($resultSummPrivate)[0] - $coinsSummOutputInt;
			*/
			$coinsSummPrivate = $coinsSumm -  $resultNotConfirmInt  - $coinsSummPrepayResidue;
			
            
            $sqlPRREQ = "SELECT * FROM ".$modx->getFullTableName( '_request_prepay' )." WHERE id_user = ".$userId." ORDER by timestamp DESC LIMIT 5 ";
            $resultPRREQ = mysql_query($sqlPRREQ);
            
            $tesxt_req_prepay = '';
            if ($resultPRREQ && mysql_num_rows($resultPRREQ) > 0){
                
  
                
                while ($rowPRREQ = mysql_fetch_assoc($resultPRREQ)) {
                    
           
                    $tesxt_req_prepay .= '<div>Запрошен аванс: '.$rowPRREQ['summ'].' руб.</div>
                    
                    <form action="'.$module_url.'&spg=seeUserInfo&userId='.$userId.'" method="POST">
						<input type="hidden" name="prepayment" value="'.$rowPRREQ['summ'].'" required />
                        <input type="hidden" name="fromRequest" value="'.$rowPRREQ['id'].'"/>
						<input type="submit" name="prepaymentSubmit" value="Начислить" />
				</form>
                <hr>
                        
                        ';
                    
                }
                
            }
			 
			$print .= '
			<div class="finances">
				<div class="title">Финансы</div>
				<div class="content">
					<div class="coins_sum">Всего средств:'.($coinsSumm-$resultNotConfirmInt).'</div>
                    
					<div class="coins_info">В качестве аванса: '.$coinsSummPrepay.'
                        <div>Должен: '.$coinsSummPrepaySpent.'</div>
                        <div>Осталось: '.$coinsSummPrepayResidue.'</div><hr>
                        '.$tesxt_req_prepay.'
                    </div>
					<div class="coins_info_prepay">'.$pageBufferPrepayList.'
						<form action="'.$module_url.'&spg=seeUserInfo&userId='.$userId.'" method="POST">
						<input type="text" name="prepayment" value="0" placeholder="Введите сумму аванса" required />
						<input type="submit" name="prepaymentSubmit" value="Начислить" />
						</form>
					</div>
					<div class="coins_info">Личных средств: '.$coinsSummPrivate.'</div>
					<div class="coins_info_lconis">'.$pageBufferHistory.'</div>
					<div class="coins_info">Ожадают вывода: '.$coinsSummOutputInt.'</div>
					<div class="coins_info_output">'.$pageBufferWaitOut.'</div>
					<div class="coins_info">Ожадают подтверждения пополнения: '.$coinsSummInputInt.'</div>
					<div class="coins_info_input">'.$pageBufferWaitInput.'</div>
					
				</div>
			</div>';
			
			
			if ($row['seller'] == 'y') {
				$print .= '<div class="sale"><div class="title">Продажи</div><div class="content"></div></div>';
			}
			
			
			$print .= '<div class="purchase"><div class="title">Покупки</div><div class="content"></div></div>';
			
			
			
			////////=======================================MESSSAGES START
			
			
			
			if (isset($_POST['messageToUserSubmit']) && is_numeric($_GET['userId']) && $_POST['messageToUser'] != '') {
				$messageToUser = addslashes($_POST['messageToUser']);
				$userId = $_GET['userId'];
				
				$from = -1;
				$date = time();
				
				$sql = "INSERT INTO ".$modx->getFullTableName( '_messages' )." (`from` , `to` , `text` , `date` , `readstatus` , `dispute` , `order_id`) VALUES (-1, {$userId} , '{$messageToUser}' , '{$date}' , 1, -1, -1) ";
				$result = mysql_query ($sql);
				
				
			}
			
			//$balance= $modx->runSnippet( 'UserBalance', array( 'type' => 'balance', 'user' => $wui[ 'id' ] ) );
			$msgList = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getMsgFrom', 'to' => -1, 'from' => $userId));
			
			$modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'setMsgReadStatus', 'to' => -1, 'from' => $userId));
			
            
            
            $unreadIcon = '<img src="/template/images/msg_unread.png" />';
            $readIcon = '<img src="/template/images/msg_read.png" />';

            
			$msgContent = '';
			if ($msgList) {

				foreach ($msgList AS $msg){
					$msgContent .= '
					<div class="'.($msg['to'] == $userId ? 'msgMe' : 'msgNotMe').' '.($msg['readstatus'] == 1 ? 'msgUnread' : '').'">'.$msg['text'].'<span>'.($msg['readstatus'] == 1 ?  $unreadIcon : $readIcon ).' '.date("d.m.Y H:i",$msg['date']).'</span></div>
					<div class="clr"></div>
					';
				}
				
			}
			
			$sendMessageForm = '<form action="'.$module_url.'&spg=seeUserInfo&userId='.$userId.'" method="POST">
								<input type="text" name="messageToUser">
								<input type="submit" name="messageToUserSubmit">
								</form>
			';
			$print .= ' <div class="messages">
							<div class="title">Сообщения</div>
							<div class="content">
								<div class="msgContent">
									'.$msgContent.'
								</div>
								'.$sendMessageForm.'
							</div>
						</div>'; 

			
			
			////////=======================================MESSSAGES END 
			
		}

	}
	

	
	
	
	
	
	
	
	
	
	
	
	
//============================Продавцы===START================================================================================

}elseif( $subpage == 'sellers' ){
	
	
	
	
	$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE seller = 'y' AND enabled= 'y' ORDER BY id DESC" );
	if( $rr && mysql_num_rows( $rr ) > 0 )
	{
		$print .= '<table class="userstable">';
		while( $row= mysql_fetch_assoc( $rr ) )
		{
			$print .= '
			<tr>
				<td>#'. $row[ 'id' ] .' <a href='.$module_url.'&spg=seeUserInfo&userId='. $row[ 'id' ] .'>'. $row[ 'email' ] .'</a> </td><td> ('. $row[ 'firstname' ] .' '. $row[ 'surname' ].')  </td><td> '.$row['mobile'].'</td>
			
			</tr>';
		}
		$print .= '</table>';
	}
	
	
	
	
	
	
	
	
	
	
	
//============================ЗАЯВКИ===START================================================================================
}elseif( $subpage == 'requests' ){
	
	//подтверждение статуса юрлица
	if (isset($_GET['requestsID']) && is_numeric($_GET['requestsID']) && ($_GET['type'] == 'urlico' || $_GET['type'] == 'seller') && $_GET['confirm'] == true) {
		$requestsID = $_GET['requestsID'];
		$type =  $_GET['type'];
		$result= mysql_query( "UPDATE ".$modx->getFullTableName( '_user' )." SET  `{$type}` =  'y' WHERE `{$type}` = 'test' AND id = {$requestsID}" );
	} else {
		
		$printMoreInfo = false;
		//подробная информация о заявке
		if (isset($_GET['requestsID']) && is_numeric($_GET['requestsID']) && isset($_GET['type'])) {
			
			$requestsID = $_GET['requestsID'];
			$requestsType = $_GET['type'];
			if ($requestsType == 'seller'){
				$typeRequestText = 'продавца';
			}elseif($requestsType == 'urlico'){
				$typeRequestText = 'юр. лица';
			}
			
	
			$result= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE id = {$requestsID} LIMIT 1" );
			if( $result && mysql_num_rows( $result ) > 0 )
			{
				$printMoreInfo .= '<table class="userstable">';
				while( $row= mysql_fetch_assoc( $result ) )
				{
					$printMoreInfo .= '
					<tr>
						<td>Email</td>
						<td>'. $row[ 'email' ] .'</td>
					</tr>	
					<tr>
						<td>Имя</td>
						<td>'. $row[ 'firstname' ] .'</td>
					</tr>	
					<tr>
						<td>Фамилия</td>
						<td>'. $row[ 'surname' ] .'</td>
					</tr>	
					<tr>
						<td>Наименование компании</td>
						<td>'. $row[ 'company' ] .'</td>
					</tr>	
					<tr>
						<td>Юридический адрес</td>
						<td>'. $row[ 'address_ur' ] .'</td>
					</tr>
					<tr>
						<td>Фактический адрес</td>
						<td>'. $row[ 'address_ft' ] .'</td>
					</tr>				
					<tr>
						<td>Телефон</td>
						<td>'. $row[ 'mobile' ] .'</td>
					</tr>	
					<tr>
						<td>ИНН</td>
						<td>'. $row[ 'inn' ] .'</td>
					</tr>	
					<tr>
						<td>Наименование банка</td>
						<td>'. $row[ 'bank' ] .'</td>
					</tr>	
					<tr>
						<td>КПП</td>
						<td>'. $row[ 'kpp' ] .'</td>
					</tr>				<tr>
						<td>БИК</td>
						<td>'. $row[ 'bik' ] .'</td>
					</tr>		
					<tr>
						<td>ОГРН</td>
						<td>'. $row[ 'ogrn' ] .'</td>
					</tr>				
					<tr>
						<td>Расчетный счет</td>
						<td>'. $row[ 'rschet' ] .'</td>
					</tr>
					<tr>
						<td>Корр. счет</td>
						<td>'. $row[ 'kschet' ] .'</td>
					</tr>	
					';
				}
				$printMoreInfo .= '</table>';
				$printMoreInfo .= '<a href="'.$module_url.'&spg=requests&type='.$requestsType.'&requestsID='.$requestsID.'&confirm=true">Подтвердить статус '.$typeRequestText.'</a>';
				
			}
		}
			
		
		
	}
	
	
	//выборка заявок на юрлицо
	$result= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE urlico = 'test' ORDER BY enabled, id DESC" );
	if( $result && mysql_num_rows( $result ) > 0 )
	{
		$printUrlico .= '<table class="userstable">';
		while( $row= mysql_fetch_assoc( $result ) )
		{
			$printUrlico .= '
			<tr>
				<td>'. $row[ 'email' ] .'('. $row[ 'firstname' ] .' '. $row[ 'surname' ].') <br/> 
					'.$row[ 'company' ].' ('.$row[ 'address_ur' ].')<br/>
					<a href="'.$module_url.'&spg=requests&type=urlico&requestsID='.$row[ 'id' ].'">Подробнее...</a>
				</td>
			</tr>';
		}
		$printUrlico .= '</table>';
	}
	
	
	//выборка заявок на продавца
	$result= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE seller = 'test' ORDER BY enabled, id DESC" );
	if( $result && mysql_num_rows( $result ) > 0 )
	{
		$printSeller .= '<table class="userstable">';
		while( $row= mysql_fetch_assoc( $result ) )
		{
			$printSeller .= '
			<tr>
				<td>'. $row[ 'email' ] .'('. $row[ 'firstname' ] .' '. $row[ 'surname' ].') <br/> 
					'.$row[ 'company' ].' ('.$row[ 'address_ur' ].')<br/>
					<a href="'.$module_url.'&spg=requests&type=seller&requestsID='.$row[ 'id' ].'">Подробнее...</a>
				</td>
			</tr>';
		}
		$printSeller .= '</table>';
	}
	

	
	
	
	
	
	
	
	echo '<div class="requestsMainBlock">Заявки на подтверждения статуса юридического лица'.$printUrlico.'</div>
		  <div class="requestsMainBlock">Заявки на подтверждения статуса продавца'.$printSeller.'</div>';
	if ($printMoreInfo){
		echo '<div class="requestsMainBlock">Подробно о пользователе'.$printMoreInfo.'</div>';
	} 
	
	
	
	
	
	
//============================Физ лица (по умолчанию)===================================================================================
}else{
	$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE urlico <> 'y' ORDER BY enabled, id DESC" );
	if( $rr && mysql_num_rows( $rr ) > 0 )
	{
		$print .= '<table class="userstable">';
		while( $row= mysql_fetch_assoc( $rr ) )
		{
			$print .= '<tr>
				<td>#'. $row[ 'id' ] .' <a href='.$module_url.'&spg=seeUserInfo&userId='. $row[ 'id' ] .'>'. $row[ 'email' ] .'</a> </td><td> ('. $row[ 'firstname' ] .' '. $row[ 'surname' ].')  </td><td> '.$row['mobile'].'</td>
			</tr>';
		}
		$print .= '</table>';
	}
}







//?????????че это
if( $_GET[ 'act' ] == 'seluser' && $webuser )
{
$rr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_user' )." WHERE id={$webuser} LIMIT 1" );
if( $rr && mysql_num_rows( $rr ) == 1 )
{
	$wui= mysql_fetch_assoc( $rr );
	
	$balance= $modx->runSnippet( 'UserBalance', array( 'type' => 'balance', 'user' => $wui[ 'id' ] ) );
	
	$rrr= mysql_query( "SELECT COUNT(id) AS cc FROM ".$modx->getFullTableName( '_shop_orders' )." WHERE iduser=". $wui[ 'id' ] ."" );
	$orderscc= ( $rrr && mysql_num_rows( $rrr ) == 1 ? mysql_result( $rrr, 0, 'cc' ) : 0 );
	
	$print .= '<table class="userinfoedit" cellpadding="0" cellspacing="0">';
	
	$print .= '<tr><td class="tit">ID</td>
	<td>'. $wui[ 'id' ] .'</td></tr>';
	
	$print .= '<tr><td class="tit">Имя, Фамилия</td>
	<td>'. $wui[ 'fio' ] .'</td></tr>';
	
	$print .= '<tr><td class="tit">E-mail</td>
	<td>'. $wui[ 'email' ] .'</td></tr>';
	
	$print .= '<tr><td class="tit">Телефон</td>
	<td>'. $wui[ 'phone' ] .'</td></tr>';
	
	$print .= '<tr><td class="tit">Почтовый индекс</td>
	<td>'. $wui[ 'zip' ] .'</td></tr>';
	
	$print .= '<tr><td class="tit">Страна</td>
	<td>'. $wui[ 'strana' ] .'</td></tr>';
	
	$print .= '<tr><td class="tit">Регион</td>
	<td>'. $wui[ 'region' ] .'</td></tr>';
	
	$print .= '<tr><td class="tit">Город</td>
	<td>'. $wui[ 'gorod' ] .'</td></tr>';
	
	$print .= '<tr><td class="tit">Адрес</td>
	<td>'. $wui[ 'adres' ] .'</td></tr>';
	
	$print .= '<tr><td class="tit">Дата регистрации</td>
	<td>'. date( 'd.m.Y, H:i', $wui[ 'dt' ] ) .'</td></tr>';
	
	$print .= '<tr><td class="tit">Кол-во заказов</td>
	<td>'. $orderscc .'<br /><br /><a class="link" href="'. $module_url_orders .'&wu='. $webuser .'">показать заказы этого покупателя</a></td></tr>';
	
	$print .= '<tr><td class="tit">Баллов на счету</td>
	<td>'. $balance .'</td></tr>';
	
	$print .= '</table><br /><br />';
	
	$rrr= mysql_query( "SELECT * FROM ".$modx->getFullTableName( '_balance' )." WHERE userid={$webuser} ORDER BY dt DESC" );
	if( $rrr && mysql_num_rows( $rrr ) > 0 )
	{
		$print .= '<table class="userinfoedit" cellpadding="0" cellspacing="0">';
			$print .= '<tr>
				<td class="tit">Описание</td>
				<td class="col_tit">Зачисление</td>
				<td class="col_tit">Списание</td>
				<td class="col_tit">Баланс</td>
				<td class="">Дата и время</td>
			</tr>';
		while( $row= mysql_fetch_assoc( $rrr ) )
		{
			$print .= '<tr>
				<td class="tit minpadding">'. $row[ 'descr' ] .'</td>
				<td class="plus minpadding">'.( $row[ 'plus' ] ? $row[ 'plus' ] : '' ).'</td>
				<td class="minus minpadding">'.( $row[ 'minus' ] ? $row[ 'minus' ] : '' ).'</td>
				<td class="balance minpadding">'. $row[ 'balance' ] .'</td>
				<td class="bldt minpadding">'. date( 'd.m.Y, H:i', $row[ 'dt' ] ) .'</td>
			</tr>';
		}
		$print .= '</table>';
	}
}
	
	
	
	
	
	
	
	

}else{
}

print $print;

/*
*/
?>