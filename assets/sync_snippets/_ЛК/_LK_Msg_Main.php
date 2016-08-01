<?php

$lk= 106;
$reg= 107;
$auth= 108;
$topage_url= $modx->makeUrl( $modx->documentIdentifier );

if( ! $_SESSION[ 'webuserinfo' ][ 'auth' ] )
{
	header( 'location: '. $modx->makeUrl( $auth ) );
	exit();
}

$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];


$topage= ( $topage ? intval( $topage ) : $modx->documentIdentifier );
$topage_url= $modx->makeUrl( $topage );


$vkladka_active= 1;
if ($_GET['dispute'] == true) $vkladka_active= 2;
if ($_GET['tab'] == 3) $vkladka_active= 3;

$countMsgLk = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getCountMsgDisp', 'to' => $webuserinfo['id'] , 'dispute' => false));
$countMsgDi = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getCountMsgDisp', 'to' => $webuserinfo['id'] , 'dispute' => true));
?>



<div class="vkladki_butts">
	<div class="vkldk_butt <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>" data-id="1">Личные сообщения <?=($countMsgLk ? '(+'.$countMsgLk .')':'')?></div>
	<div class="vkldk_butt <?= ( $vkladka_active == 2 ? 'active' : '' ) ?>" data-id="2">Споры <?=($countMsgDi ? '(+'.$countMsgDi .')':'')?></div>
	<!--div class="vkldk_butt <?= ( $vkladka_active == 3 ? 'active' : '' ) ?>" data-id="3">Вывести средства</div-->
	<div class="clr">&nbsp;</div>
</div>



<div class="vkladki_divs">

<!--==============================TAB1=START================================-->	
<?

	//sendTextSubmit
if (isset($_POST['sendTextSubmit']) && $_POST['sendText'] != '' && is_numeric($_GET['getMsgFrom'])){
	$text = addslashes($_POST['sendText']);
	$from = $_GET['getMsgFrom'];
	
	
	if ($_GET['dispute'] == true && is_numeric($_GET['order'])){
		$dispute = true;
		$order = $_GET['order'];
	}else {
		$dispute = false;
		$order = -1;
	}
	
	//echo $dispute.$order.'!!!!';
	$modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'sendNewMessage', 
												 'to' => $from, 
												 'text' => $text, 
												 'from' => $webuserinfo['id'],
											     'dispute'=>$dispute,
											     'order'=>$order));
	
}



	
	
	
?>	


	<div class="vkldk_div vkldk_div_1 <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<div class="userList">
				
				
				<?php
				$writersListIn = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getUserWritingListInbox', 'to' => $webuserinfo['id']));
				$writersListOut = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getUserWritingListOutbox', 'to' => $webuserinfo['id']));
				if (is_array($writersListIn) && is_array($writersListOut)) {
					//$writersList = array_merge($writersListIn , $writersListOut);///cvjnhtnnm 
					//$writersList = array_unique($writersList);
					$finded = false;
					$writersList = $writersListIn;
					foreach ($writersListOut AS $tOuntArrOut) {
						foreach ($writersListIn AS $tOuntArrIn) {
							if ($tOuntArrOut['user'] == $tOuntArrIn['user']){
								$finded = true;
								break;
							}
						}
						if (!$finded){
							$writersList[]=$tOuntArrOut;
						}
					
					}
	
					//$writersList = $writersListNew;
				}elseif (is_array($writersListIn)) {
					$writersList = $writersListIn;
				}elseif (is_array($writersListOut)) {
					$writersList = $writersListOut;
				}else {
					$writersList=false;
				}
				

				$countMsgFromWriter='';
				$countMsgFromWriter = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getCountMsgFromWriter', 'to' => $webuserinfo['id'] , 'from' => -1));
				$chechMsgFromAdmin = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'chechMsgFromAdmin', 'to' => $webuserinfo['id'] ));
				
					echo '
						<a class="'.(($_GET['getMsgFrom'] == -1 && $_GET['dispute'] != true) ? 'grayBg': '').'" href="'.$topage_url.'?getMsgFrom=-1">
							<div class="oneUser">
								<div class="nameUser">Администратор</span></div>
								<div class="unreadMsg">'.($countMsgFromWriter > 0 ? '<span>'.$countMsgFromWriter.'</span>' : '').'</div>
							</div>
						</a>
				';


				
				
				//print_r($writersList);
				if ($writersList) {
					foreach ($writersList AS $writer) {
						if ($writer['user'] == -1) {
							continue;
							$writer['firstname'] = 'Aдминистратор';
							$writer['surname'] = '';
							$writer['email'] = '';
						}
						
						if ($writer['firstname'] == '' && $writer['surname'] == ''){
							$writer['firstname'] = 'Имя не указано';
						}
					
						
						//getCountMsgFromWriter
						$countMsgFromWriter='';
						$countMsgFromWriter = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getCountMsgFromWriter', 'to' => $webuserinfo['id'] , 'from' => $writer['user']));
						echo '
						
						<a class="'.(($_GET['getMsgFrom'] == $writer['user'] && $_GET['dispute'] != true) ? 'grayBg': '').'" href="'.$topage_url.'?getMsgFrom='.$writer['user'].'">
							<div class="oneUser">
								<div class="nameUser">'.$writer['firstname'].' '.$writer['surname'].'<span>'.$writer['email'].'</span></div>
								<div class="unreadMsg">'.($countMsgFromWriter > 0 ? '<span>'.$countMsgFromWriter.'</span>' : '').'</div>
							</div>
						</a>
						';
					}
				}

				//getMsgFrom
				if (is_numeric($_GET['getMsgFrom'])   && $_GET['dispute'] != true) {
					$msgList = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getMsgFrom', 'to' => $webuserinfo['id'], 'from' => $_GET['getMsgFrom']));
					$modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'setMsgReadStatus', 'to' => $webuserinfo['id'], 'from' => $_GET['getMsgFrom']));
				}
				
				//print_r($msgList);
				$msgContent = '';
				if ($msgList) {

					foreach ($msgList AS $msg){
						$msgContent .= '
						<div class="'.($msg['from'] == $webuserinfo['id'] ? 'msgMe' : 'msgNotMe').' '.($msg['readstatus'] == 1 ? 'msgUnread' : '').'">'.$msg['text'].'<span>'.date("d.m.Y H:i",$msg['date']).'</span></div>
						<div class="clr"></div>
						';
					}
					
				}
				

					//getUserInfo
				if ($_GET['getMsgFrom'] == -1 ){
					$userInfo = 'Администратор';
				}elseif ($_GET['getMsgFrom'] > 0) {
					$userInfoArr = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getUserInfo',  'from' => $_GET['getMsgFrom']));
					//print_r($userInfoArr);
					$userInfo = $userInfoArr['email'];
				}else {
					$userInfo = '';
				}
				
				
				//print_r($userInfo);
				?>

				
			</div>
			
			<?php
			if ( $_GET['dispute'] != true && is_numeric($_GET['getMsgFrom'])){
			
			?>
			<div class="messages">
				
				<div class="writersInfo">
						<div class="writerFrom"><?=$userInfo?></div>
						<div class="writerMe">Вы</div>
				</div>
				
				<div class="msgContent">
					
					<?=$msgContent?>
					
				</div>
				<div class="sendForm">
					<form action="<?=$topage_url?><?=is_numeric($_GET['getMsgFrom']) ? '?getMsgFrom='.$_GET['getMsgFrom'] : ''?>" method="POST">
						<textarea name="sendText" placeholder="Введите текст" required></textarea>
						<input type="submit" class='sendMessageButton' value="Отправить" name="sendTextSubmit">
						
					</form>
				</div>
			</div>
			<div class="clr"></div>
			
			<?php
			}
			?>
		</div>
		<div class="clr"></div>
	</div>
<!--==============================TAB1==END================================-->	
	
	
	
	
<!--==============================TAB2=START================================-->	
	
	<div class="vkldk_div vkldk_div_2 <?= ( $vkladka_active == 2 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">	
			<div class="userList">
				<?php
				$msgContent = '';	
					
				if ($_GET['dispute'] == true && is_numeric($_GET['order']) && is_numeric($_GET['getMsgFrom'])) {
				
					
					$sqlCheckDisp = "SELECT count(id) FROM ".$modx->getFullTableName( '_messages' )." WHERE dispute = 0 AND order_id = ".$_GET['order'];
					$resultCheckDisp = mysql_query($sqlCheckDisp) or die("ERROR 954962 ".mysql_error());
					$ttt = mysql_fetch_row($resultCheckDisp)[0];
					if ($ttt < 1) {
					
						$sqlGetOrderNumber = "SELECT order_number FROM ".$modx->getFullTableName( '_orders' )." WHERE id = ".$_GET['order'];
						$resultOrdNumb = mysql_query($sqlGetOrderNumber) or die("ERROR 954962 ".mysql_error());


						if (mysql_num_rows($resultOrdNumb) > 0) {
							if ($orderNumber = mysql_fetch_row($resultOrdNumb)[0]) {

								$valueToInput = 'Открыт спор касательно заказа № <a href="'.$modx->makeUrl( 117 ).'">'.$orderNumber.'</a> '.date("d.m.Y", time()).' в '.date("H:i:s", time());
								$from = $_GET['getMsgFrom'];
								$order = $_GET['order'];
								$modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'sendNewMessage', 'to' => $from, 'text' => $valueToInput, 'from' => $webuserinfo['id'] , 'dispute' => true , 'order' => $order));

							}

						}
					
					}
					
					
					
				}


				$writersListIn = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getUserWritingListInbox', 'to' => $webuserinfo['id'], 'dispute' => true));
				$writersListOut = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getUserWritingListOutbox', 'to' => $webuserinfo['id'], 'dispute' => true));
				if (is_array($writersListIn) && is_array($writersListOut)) {
					//$writersList = array_merge($writersListIn , $writersListOut);///cvjnhtnnm 
					//$writersList = array_unique($writersList);
					$finded = false;
					$writersList = $writersListIn;
					foreach ($writersListOut AS $tOuntArrOut) {
						foreach ($writersListIn AS $tOuntArrIn) {
							if ($tOuntArrOut['user'] == $tOuntArrIn['user']){
								$finded = true;
								break;
							}
						}
						if (!$finded){
							$writersList[]=$tOuntArrOut;
						}
					
					}
	
					//$writersList = $writersListNew;
				}elseif (is_array($writersListIn)) {
					$writersList = $writersListIn;
				}elseif (is_array($writersListOut)) {
					$writersList = $writersListOut;
				}else {
					$writersList=false;
				}
				
				//print_r($writersList);
				if ($writersList) {
					foreach ($writersList AS $writer) {
						if ($writer['user'] == -1) {
							$writer['firstname'] = 'Aдминистратор';
							$writer['surname'] = '';
							$writer['email'] = '';
						}
						
						if ($writer['firstname'] == '' && $writer['surname'] == ''){
							$writer['firstname'] = 'Имя не указано';
						}
					
						
						//getCountMsgFromWriter
						$countMsgFromWriter='';
						$countMsgFromWriter = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getCountMsgFromWriter', 'to' => $webuserinfo['id'] , 'from' => $writer['user'], 'dispute' => true));
						echo '
							<a class="'.(($_GET['getMsgFrom'] == $writer['user'] && $_GET['dispute'] == true) ? 'grayBg': '').'" href="'.$topage_url.'?getMsgFrom='.$writer['user'].'&dispute=true&order='.$writer['order_id'].'">
							<div class="oneUser">
								<div class="nameUser">'.$writer['firstname'].' '.$writer['surname'].'<span>'.$writer['email'].'</span></div>
								<div class="unreadMsg">'.($countMsgFromWriter > 0 ? '<span>'.$countMsgFromWriter.'</span>' : '').'</div>
							</div>
						</a>
						';
					}
				} else {
					echo 'Нет открытых споров';
				}

				//getMsgFrom
				if (is_numeric($_GET['getMsgFrom']) && $_GET['dispute'] == true) {
					$msgList = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getMsgFrom', 'to' => $webuserinfo['id'], 'from' => $_GET['getMsgFrom'], 'dispute' => true));
					$modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'setMsgReadStatus', 'to' => $webuserinfo['id'], 'from' => $_GET['getMsgFrom'], 'dispute' => true));
				}
				
				//print_r($msgList);
				$msgContent = '';
				if ($msgList  && $_GET['dispute'] == true) {

					foreach ($msgList AS $msg){
						$msgContent .= '
						<div class="'.($msg['from'] == $webuserinfo['id'] ? 'msgMe' : 'msgNotMe').' '.($msg['readstatus'] == 1 ? 'msgUnread' : '').'">'.$msg['text'].'<span>'.date("d.m.Y H:i",$msg['date']).'</span></div>
						<div class="clr"></div>
						';
					}
					
				}
				

				

					//getUserInfo
				if ($_GET['getMsgFrom'] == -1 ){
					$userInfo = 'Администратор';
				}elseif ($_GET['getMsgFrom'] > 0) {
					$userInfoArr = $modx->runSnippet( '_LK_Msg_actions', array( 'event' => 'getUserInfo',  'from' => $_GET['getMsgFrom']));
					//print_r($userInfoArr);
					$userInfo = $userInfoArr['email'];
				}else {
					$userInfo = '';
				}
				
				
				//print_r($userInfo);
				?>

			</div>
			
			
			
			<?php
			if ( $_GET['dispute'] == true && is_numeric($_GET['getMsgFrom']) && is_numeric($_GET['order'])){
			
			?>
			
			
			<div class="messages">
				
				<div class="writersInfo">
						<div class="writerFrom"><?=$userInfo?></div>
						<div class="writerMe">Вы</div>
				</div>
				
				<div class="msgContent">
					
					<?=$msgContent?>
					
				</div>
				<div class="sendForm">
					<form action="<?=$topage_url?><?=is_numeric($_GET['getMsgFrom']) ? '?getMsgFrom='.$_GET['getMsgFrom'] : ''?>&dispute=true&order=<?=$_GET['order']?>" method="POST">
						<textarea name="sendText" placeholder="Введите текст" required></textarea>
						<input type="submit" class='sendMessageButton' value="Отправить" name="sendTextSubmit">
						
					</form>
				</div>
			</div>
			<div class="clr"></div>
			<?php
			}
			?>
			
			
		</div>
		<div class="clr"></div>
	</div>
<!--==============================TAB2==END================================-->	
	
	
	
	
<!--==============================TAB3=START================================-->	
	


	<div class="vkldk_div vkldk_div_3 <?= ( $vkladka_active == 3 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">	
		
			
			
		</div>
	</div>
<!--==============================TAB3==END================================-->	
	
</div>





?>