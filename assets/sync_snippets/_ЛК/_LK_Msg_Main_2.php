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
//$countMsgLk = $modx->runSnippet( '_LK_Msg_actions_2', array( 'event' => 'getCountMsgDisp', 'to' => $webuserinfo['id'] , 'dispute' => false));
//$countMsgDi = $modx->runSnippet( '_LK_Msg_actions_2', array( 'event' => 'getCountMsgDisp', 'to' => $webuserinfo['id'] , 'dispute' => true));
?>



<div class="vkladki_butts">
	<div class="vkldk_butt <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>" data-id="1">Личные сообщения <?=($countMsgLk ? '(+'.$countMsgLk .')':'')?></div>
	<div class="clr">&nbsp;</div>
</div>



<div class="vkladki_divs">

<!--==============================TAB1=START================================-->	
<?

	//sendTextSubmit
if (isset($_POST['sendTextSubmit']) && $_POST['sendText'] != '' ){
	$text = addslashes($_POST['sendText']);


	$modx->runSnippet( '_LK_Msg_actions_2', array( 'event' => 'sendNewMessage', 
												 'text' => $text, 
												 'from' => $webuserinfo['id']
											   ));
	
}



	
	
	
?>	


	<div class="vkldk_div vkldk_div_1 <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<!--div class="userList">
		
				<?php
		
				
				$msgList = $modx->runSnippet( '_LK_Msg_actions_2', array( 'event' => 'getMsgFrom', 'to' => $webuserinfo['id']));
				$modx->runSnippet( '_LK_Msg_actions_2', array( 'event' => 'setMsgReadStatus', 'to' => $webuserinfo['id']));
				
				$unreadIcon = '<img src="/template/images/msg_unread.png" />';
				$readIcon = '<img src="/template/images/msg_read.png" />';

				
				//print_r($msgList);
				$msgContent = '';
				if ($msgList) {

					foreach ($msgList AS $msg){
						$msgContent .= '
						<div class="'.($msg['from'] == $webuserinfo['id'] ? 'msgMe' : 'msgNotMe').' '.($msg['readstatus'] == 1 ? 'msgUnread' : '').'">'.$msg['text'].'<span> '.($msg['readstatus'] == 1 ? $unreadIcon : $readIcon).' '.date("d.m.Y H:i",$msg['date']).'</span></div>
						<div class="clr"></div>
						';
					}
					
				}
				



				?>

				
			</div-->
			
			<div class="messages">
				
				<div class="sendForm">
					<form action="<?=$topage_url?>" method="POST">
						<textarea name="sendText" placeholder="Введите текст" required></textarea>
						<input type="submit" class='sendMessageButton' value="Отправить" name="sendTextSubmit">
						
					</form>
				</div>
				
				
				<div class="writersInfo">
						<div class="writerFrom">Администратор</div>
						<div class="writerMe">Вы</div>
				</div>
				
				<div class="msgContent">
					
					<?=$msgContent?>
					
				</div>
							
				
			</div>
			<div class="clr"></div>
			

		</div>
		<div class="clr"></div>
	</div>
<!--==============================TAB1==END================================-->	
	
	
	
	

	
	

	
</div>





?>