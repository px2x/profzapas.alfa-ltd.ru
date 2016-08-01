<?php


//$sellerId
//$itemId
//

 
$page_sellerInfo = 217; 

$webuserinfo= $_SESSION[ 'webuserinfo' ][ 'info' ];

$sqlseller = "SELECT firstname , surname
			FROM  ".$modx->getFullTableName( '_user' )." 
			WHERE id =  '".$sellerId."'  LIMIT 1";

if ($resultseller = mysql_query($sqlseller)){
    if (!$tmp = mysql_fetch_assoc($resultseller)){
        $rowseller = 'Неизвестно';
    }else {
        $rowseller = $tmp['firstname']." ".$tmp['surname'];
    }
}


    $sqlAVG = "SELECT  AVG(resp.`rank`) AS avgRank , resp.id_user , COUNT(resp.id) AS sumRespons 
            FROM ".$modx->getFullTableName( '_responses' )."  AS resp
            INNER JOIN ".$modx->getFullTableName( '_catalog' )."  AS cat ON resp.id_cat_item = cat.id
            WHERE cat.seller = ".$sellerId."
            AND resp.visible = '1' "; 
    
        $resultAVG = mysql_query($sqlAVG) or die (mysql_error());
        if ($resultAVG && mysql_num_rows($resultAVG) > 0) {
            if ($avgUser = mysql_fetch_assoc($resultAVG)) {
            
            }
        }
    



$content = '<div class="sellerNameRank"><a href="'.$modx->makeUrl( $page_sellerInfo ).'?sellerID='.$sellerId.'" style="display:initial;">'.$rowseller.'</a> <span class="rnk_satrs">'.(round($avgUser['avgRank'],1)).'</span></div>';




if (is_numeric($webuserinfo[ 'id' ])){
    $sql2 = "SELECT * FROM  ".$modx->getFullTableName( '_favorites' )." WHERE id_user = ".$webuserinfo[ 'id' ]." AND id_item=".$itemId;
    $result2 = mysql_query($sql2);

    if (mysql_num_rows($result2)>0) {
        $tobasketButton .= '<span  data-itemid="'. $itemId.'"  data-userid="'. $webuserinfo[ 'id' ].'" class="addOrDel_tofavour" >Удалить из избранного</span>';
    }else {
        $tobasketButton .= '<span  data-itemid="'. $itemId.'"  data-userid="'. $webuserinfo[ 'id' ].'" class="addOrDel_tofavour " >Добавить в избранное</span>';
    }
}
	

$content .= '<div>'.$tobasketButton.'</div>';





























//$sellerId
//$itemId
//$tender

$pageId_aukc= 125;

	if ($tender ==1) {
        
        
    
	
	/*
        $requestNewPrice = '<div class="requestPriceForm">
	
							<form class="requestNewPriceForm">
								<div class="reqText">По какой цене Вы готовы приобрести этот товар?</div>
								<div class="delInputReqPrice"></div>
								<input type="hidden" name="newPriceID" value="'.$row[ 'id' ].'">
								<input class="inputText" type="text" name="newPrice" placeholder="Введите свою цену" pattern="^[ 0-9]+$" required>
								<input class="inputText textReqCount"  data-max="'.$row[ 'in_stock' ].'" type="text" name="newPriceCount" placeholder="Введите количество" pattern="^[ 0-9]+$" required>
								<input class="inputbutton" data-itemid="'.$row[ 'id' ].'" data-userid="'.$webuserinfo[ 'id' ].'" type="submit" name="newPriceSubmit" value="Запросить">
							</form>	
                            
 
						</div>';
        */
        
        
        $requestNewPrice = '<div class="requestPriceForm_px">
                                <div class="px_pp_title">По какой цене Вы готовы преобрести данный товар?</div>
                                <div class="px_pp_title_itm">'.$row[ 'title' ].'</div>
                 
                                <div class="clr"></div>


                                <input class="inputText" id="newPrice_req" type="text" name="newPrice" placeholder="Введите цену в рублях" pattern="^[ 0-9]+$" required>
                                
                                <input class="inputText textReqCount"  id="newCount_req" data-max="'.$row[ 'in_stock' ].'" type="text" name="newPriceCount" placeholder="Введите количество" pattern="^[ 0-9]+$" required>
                                
                                <button class="px_pp_inp_request_send"  data-item_id="'.$row[ 'id' ].'">Отправить</button>
                                <div class="waitCheckRes">
                                    <div class="checkResult"></div>
                                    <div class="wrap">
                                        <div class="dot"></div>
                                    </div>
                                </div>
                            </div>';
	
        
        
		
		$dopClass='';
		$titleText = 'Предложить свою цену';
		$acceptReqNewPrice= false;
		$reqPriseAccepted ='';
		$reqCountAccepted = '';
			
		
		///
		//echo $webuserinfo[ 'id' ];
		if (is_numeric($webuserinfo[ 'id' ])){
			$sql="SELECT * FROM ".$modx->getFullTableName( '_request_price' )."  WHERE id_user = ".$webuserinfo[ 'id' ]." AND id_item = ".$itemId." ORDER BY date_req DESC LIMIT 1 ";
			$resultStat = mysql_query($sql) or die ('ERR 26626 '.mysql_error());
			if (mysql_num_rows($resultStat) > 0) {
				if ($resVal = mysql_fetch_assoc($resultStat)){
                    
                    
                    

                    
                    
                    
					if ($resVal['response'] == 1){
			
						
						$acceptReqNewPrice= true;
						$reqPriseAccepted = $resVal['request_price'];
						$reqCountAccepted = $resVal['count_item'];
                        
                        $titleText = 'Купить за '.$reqPriseAccepted;
                        
						$tobasketButtonRnp .= '<a class="buttonsubmit_toreqprice2" href="'. $modx->makeUrl( $pageId_aukc ).'?tab=user" >'.$titleText.'<span class="rubl">a</span></a>';
					}elseif ($resVal['response'] == -1) {
				
                        
                        $requestNewPrice = '<div class="requestPriceForm_px">
                                <div class="px_pp_title">Вы предложили купить данный товар<br/> в количестве '.$resVal['count_item'].' шт. по '.$resVal['request_price'].' <span class="rubl">a</span></div>
                                <div class="px_pp_title_itm">'.$row[ 'title' ].'</div>
                 
                                <div class="clr"></div>
                                <br/>
                                <br/>
                                <br/>
                                <div class="px_pp_title_itm">Продавец отказался от Вашего предложения</div>
                 
                                <div class="clr"></div>


                                <div class="waitCheckRes" style="opacity:1;">
                                    <div class="checkResult cRTD"  style="background-color:#ee9595;font-size: 14px;">Отказано</div>
                                    <div class="wrap animRing"  style="opacity:1;">
                                        <div class="dot"></div>
                                    </div>
                                </div>
                            </div>';
                        
                        
						$titleText = '<span class="renewpricPxR">Ваша цена не одобрена</span>';
						$tobasketButtonRnp .= '<div class="buttonsubmit_toreqprice2" >'.$requestNewPrice.$titleText.'</div>';
					}else {
			             
                        
                            $requestNewPrice = '<div class="requestPriceForm_px">
                                <div class="px_pp_title">Вы предложили купить данный товар<br/> в количестве '.$resVal['count_item'].' шт. по '.$resVal['request_price'].' <span class="rubl">a</span></div>
                                <div class="px_pp_title_itm">'.$row[ 'title' ].'</div>
                 
                                <div class="clr"></div>
                                <br/>
                                <br/>
                                <br/>
                                <div class="px_pp_title_itm">Продавец рассматривает Ваше предложение</div>
                                
                 
                                <div class="clr"></div>


                                <div class="waitCheckRes" style="opacity:1;">
                                    <div class="checkResult"></div>
                                    <div class="wrap animRing"  style="opacity:1;">
                                        <div class="dot"></div>
                                    </div>
                                </div>
                            </div>';
                        
                        
						$titleText = '<span class="renewpricPxR">Цена на рассмотрении</span>';
						$tobasketButtonRnp .= '<div class="buttonsubmit_toreqprice2" >'.$requestNewPrice.$titleText.'</div>';
					}
				}

                
			}else {
				$titleText = '<span class="renewpricPxR">Предложить свою цену</span>';
				$tobasketButtonRnp .= '<div class="buttonsubmit_toreqprice2" >'.$requestNewPrice.$titleText.'</div>';
			}
			
		}
		
	}
    




return $content.$tobasketButtonRnp;
?>