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

$vkladka_active= 1;
if ($_GET['tab'] == 2) $vkladka_active= 2;
if ($_GET['tab'] == 3) $vkladka_active= 3;
?>



<div class="vkladki_butts">
	

	<div class="vkldk_butt <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>" data-id="1">Заказы в обработке <?=$modx->runSnippet( '_LK_getCountEvent', array('event' => 'sellerOrdProcess'))?></div>
	<div class="vkldk_butt <?= ( $vkladka_active == 2 ? 'active' : '' ) ?>" data-id="2">Статистика</div>
	<div class="vkldk_butt <?= ( $vkladka_active == 3 ? 'active' : '' ) ?>" data-id="3">Архив <?=$modx->runSnippet( '_LK_getCountEvent', array('event' => 'allended'))?></div>
	<div class="clr">&nbsp;</div>
</div>



<script type="text/javascript">
	google.charts.load('current', {'packages':['corechart']});
</script>


<div class="vkladki_divs">
	
	
	
	
	
	
		
	
	<!--========================TAB 1 START================================================-->
	<?php
	
	//==========OPENED DISPUTE BLOCK START
	$inDispute = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getDisputeItems', 'sellerID' => $webuserinfo['id']));

//print_r ($inDispute);
	
	$cointInDispute = 0;
	
	$inOpenedDisputeBlock = '';
	if (is_array($inDispute)) {
		$cointInDispute = count ($inDispute);
		foreach ($inDispute as $dispRow) {

			if ($dispRow['status'] == 'aborted'){
				$cellStatusContent = 'Отменен';
			}elseif ($dispRow['status'] == 'calculateShipment'){
				$cellStatusContent = 'Расчет доставки';
				$dateToDie = 'Доставка будет расчитана до '.date("d.m.Y H:i", $row['date_w_check'] + 60*60*24);
			}elseif ($dispRow['status'] == 'waitPayment'){
				$cellStatusContent = 'Ожидание оплаты';
				$dateToDie = 'Необходимо оплатить до '.date("d.m.Y H:i", $row['date_w_coins'] + 60*60*24*5);	
			}elseif ($dispRow['status'] == 'waitShipment'){
				$cellStatusContent = 'Ожидается отправка';	
				$dateToDie = 'Бедет отправлено до '.date("d.m.Y H:i", $row['date_w_ship'] + 60*60*24*5);	
			}elseif ($dispRow['status'] == 'inShipment'){
				$cellStatusContent = 'Товар отправлен';
				$dateToDie = 'Будет доставлено до '.date("d.m.Y H:i", $row['date_w_ship'] + 60*60*24*45);
			}elseif ($dispRow['status'] == 'waitTesting'){
				$cellStatusContent = 'Тестирование';	
				$dateToDie = 'Необходимо протестировать до '.date("d.m.Y H:i", $row['date_w_test'] + 60*60*24*5);	
			}elseif ($dispRow['status'] == 'ended' || $row['status'] == 'waitEnd'){
				$cellStatusContent = 'Завершен';
				$dateToDie = 'Завершен '.date("d.m.Y H:i", $row['date_end']);
			}

			$inOpenedDisputeBlock .= '
			<div class="openedDisputeLine">
				<div class="stat_orederNumber">#'.$dispRow['order_number'].'</div>
				<div class="stat_buyer">'.$dispRow['email'].'</div>
				<div class="stat_dateOrder">'.date("d.m.Y H:i", $dispRow['date_w_check']).'</div>
				<div class="stat_dateOpenDispute">'.date("d.m.Y H:i" , $dispRow['date_open']).'</div>
				<div class="stat_status">'.$cellStatusContent.'</div>
				<a class="stat_messages">подробнее</a>
			</div>
			<div class="clr"></div>';
		}
	}

	$openedDisputeBlock = '
	<div class="statisticMainLine openedDispute">
		<span>Открытые споры по продажам : '.$cointInDispute.'</span>
		<div class="seeMoreStat"><img src="/template/images/dropDownList.png"></div>
		<div class="clr"></div>
		<div class="seeMoreStatBlock" id="smstas_dispute">
			'.$inOpenedDisputeBlock.'
		</div>
		<div class="clr"></div>
	</div>
	<div class="clr"></div>';
	//==========OPENED DISPUTE BLOCK END



	//==========CALCULATE  BLOCK START
	$calculateShipBlock = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getHTML', 'type' => 'calculateShipment', 'sellerID' => $webuserinfo['id']));



	$waitPaymentBlock = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getHTML', 'type' => 'waitPayment', 'sellerID' => $webuserinfo['id']));


	$waitShipmentBlock = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getHTML', 'type' => 'waitShipment', 'sellerID' => $webuserinfo['id']));


	$inShipmentBlock = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getHTML', 'type' => 'inShipment', 'sellerID' => $webuserinfo['id']));


	$waitTestingBlock = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getHTML', 'type' => 'waitTesting', 'sellerID' => $webuserinfo['id']));

	$waitENDBlock = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getHTML', 'type' => 'waitEnd', 'sellerID' => $webuserinfo['id']));
	//===========CALCULATE  BLOCK END







	?>
	<div class="vkldk_div vkldk_div_1 <?= ( $vkladka_active == 1 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			
			<?=($cointInDispute > 0 ? $openedDisputeBlock : '')?>
			<?=$calculateShipBlock.$waitPaymentBlock.$waitShipmentBlock.$inShipmentBlock.$waitTestingBlock.$waitENDBlock?>
			<div class="clr">&nbsp;</div>
		</div>
		<div class="clr">&nbsp;</div>
	</div>
	<div class="clr">&nbsp;</div>
	<!--========================TAB 1 END================================================-->
	
	
	
	
	<!--========================TAB 2 START================================================-->
<?php

$allEndedOrders = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getAllEnded', 'sellerID' => $webuserinfo['id']));

$bigestSelB = $modx->runSnippet( '_LK_getStatisticHTML', array( 'event' => 'getTopTeenHTML', 'arr' => $allEndedOrders, 'type' => 'buy'));
$allEndOrdCount = $modx->runSnippet( '_LK_getStatisticHTML', array( 'event' => 'allEndOrdCount', 'arr' => $allEndedOrders));
$toJSdata = $modx->runSnippet( '_LK_getStatisticHTML', array( 'event' => 'toJSChartData', 'arr' => $allEndedOrders, 'type' => 'buy'));



$bigestSeeItem = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getTopSeeItem', 'sellerID' => $webuserinfo['id'] , 'type' => 'see'));

$seeStatistocTopTeen = $modx->runSnippet( '_LK_getStatisticHTML', array( 'event' => 'getTopTeenHTML', 'arr' => $bigestSeeItem, 'type' => 'see'));
$seeStatistocSummary = $modx->runSnippet( '_LK_getStatisticHTML', array( 'event' => 'seeStatistocSummary', 'arr' => $bigestSeeItem, 'type' => 'see'));
$seeStatistocToJSdata = $modx->runSnippet( '_LK_getStatisticHTML', array( 'event' => 'toJSChartData', 'arr' => $bigestSeeItem, 'type' => 'see'));



$bigestSearchItem = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getTopSeeItem', 'sellerID' => $webuserinfo['id'] , 'type' => 'find'));


$searchStatistocTopTeen = $modx->runSnippet( '_LK_getStatisticHTML', array( 'event' => 'getTopTeenHTML', 'arr' => $bigestSearchItem, 'type' => 'search'));
$searchStatistocSummary = $modx->runSnippet( '_LK_getStatisticHTML', array( 'event' => 'seeStatistocSummary', 'arr' => $bigestSearchItem, 'type' => 'search'));
$searchStatistocToJSdata = $modx->runSnippet( '_LK_getStatisticHTML', array( 'event' => 'toJSChartData', 'arr' => $bigestSearchItem, 'type' => 'search'));




?>
	<div class="vkldk_div vkldk_div_2 <?= ( $vkladka_active == 2 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			
			<?=$allEndOrdCount?>
			
			<?php
				if ($toJSdata) {
					
					echo <<<FF
						<script type="text/javascript">
							//google.charts.load('current', {'packages':['corechart']});
							google.charts.setOnLoadCallback(drawChart);

							   function drawChart() {
								var data = google.visualization.arrayToDataTable([
								  ['Дата', 'Сумма (руб.)'],
								  $toJSdata

								]);

								var options = {
									title: 'Статистика выполненых заказов за месяц',
									curveType: '',
									legend: { position: 'bottom' },					
									width: 810,
									height: 400,
									chartArea:{left:60,top:40,width:'90%',height:'75%'},
									hAxis: {gridlines:	{color: '#333', count: 10}}
								};

								var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

								chart.draw(data, options);
							  }
						</script>
FF;

				}
			?>
			
			<?=$bigestSelB?>
			
			
			
			
			<?=$seeStatistocSummary?>
			<div id="curve_chart_see"></div>
			<div class="clr">&nbsp;</div>
			
			<?php
				if ($seeStatistocToJSdata) {
					
					echo <<<FF
						<script type="text/javascript">
							//google.charts.load('current', {'packages':['corechart']});
							google.charts.setOnLoadCallback(drawChart);

							   function drawChart() {
								var data = google.visualization.arrayToDataTable([
								  ['Дата', 'Кол-во просмотров'],
								  $seeStatistocToJSdata

								]);

								var options = {
									title: 'Статистика просмотров Ваших товаров',
									curveType: '',
									legend: { position: 'bottom' },					
									width: 810,
									height: 400,
									chartArea:{left:60,top:40,width:'90%',height:'75%'},
									hAxis: {gridlines:	{color: '#333', count: 10}}
								};

								var chart = new google.visualization.LineChart(document.getElementById('curve_chart_see'));

								chart.draw(data, options);
							  }
						</script>
FF;

				}
			?>
			
			<?=$seeStatistocTopTeen?>
			
			
			
			<?=$searchStatistocSummary?>
			
			
			<div id="curve_chart_search"></div>
			<div class="clr">&nbsp;</div>
			
			<?php
				if ($searchStatistocToJSdata) {
					
					echo <<<FF
						<script type="text/javascript">
							//google.charts.load('current', {'packages':['corechart']});
							google.charts.setOnLoadCallback(drawChart);

							   function drawChart() {
								var data = google.visualization.arrayToDataTable([
								  ['Дата', 'Кол-во'],
								  $searchStatistocToJSdata

								]);

								var options = {
									title: 'Статистика участия в поиске Ваших товаров',
									curveType: '',
									legend: { position: 'bottom' },					
									width: 810,
									height: 400,
									chartArea:{left:60,top:40,width:'90%',height:'75%'},
									hAxis: {gridlines:	{color: '#333', count: 10}}
								};

								var chart = new google.visualization.LineChart(document.getElementById('curve_chart_search'));

								chart.draw(data, options);
							  }
						</script>
FF;

				}	
			
			?>
			
			
			
			<?=$searchStatistocTopTeen?>
			
			<div class="clr">&nbsp;</div>
		</div>
		<div class="clr">&nbsp;</div>
	</div>
	<div class="clr">&nbsp;</div>
	<!--========================TAB 2 END================================================-->
	
	
	

	
	
	

	
	
	<!--========================TAB 3 START================================================-->
	
	<?php 
		$endedBlock = $modx->runSnippet( '_LK_getStatistic', array( 'event' => 'getHTML', 'type' => 'ended', 'sellerID' => $webuserinfo['id']));
		
		
	?>
	<div class="vkldk_div vkldk_div_3 <?= ( $vkladka_active == 3 ? 'active' : '' ) ?>">
		<div class="_LK_wrapper _LK_wrapper_big">
			<?=$endedBlock?>
			<div class="clr">&nbsp;</div>
		</div>
		<div class="clr">&nbsp;</div>
	</div>
	<div class="clr">&nbsp;</div>
	<!--========================TAB 3 END================================================-->
	
	
	
</div>





?>