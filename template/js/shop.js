

var $= jQuery.noConflict();



$(document).ready(function(){
	//------------------------------------------------------------------

	//------------------------------------------------------------------
	
	$(".button_plus").click(function(){
		itemid = $(this).parent().data('itemid');
		input = $("#ci_tobasket_count_"+itemid);
		max = $(this).data("max");
		cirrentInp = input.val();
		prevVal = cirrentInp;
		console.log(input);
		cirrentInp++;
		if (cirrentInp > max )cirrentInp = max;
		input.val(cirrentInp);
		rechangeCountItem(input.data("id"), cirrentInp, input, prevVal);
		setTimeout(function(){
			getSummMyOrder();
            recountItem();
		},500);
		
	});
	
	
	
	
	$(".button_minus").click(function(){
		itemid = $(this).parent().data('itemid');
		input = $("#ci_tobasket_count_"+itemid);
		cirrentInp = input.val();
		prevVal = cirrentInp;
		console.log(input);
		cirrentInp--;
		if (cirrentInp < 1 )cirrentInp = 1;
		input.val(cirrentInp);
		rechangeCountItem(input.data("id"), cirrentInp, input, prevVal);
		setTimeout(function(){
			getSummMyOrder();
            recountItem();
		},500);
	});
	
	
	$(".tobasket_item_count").change(function(){
		max = $(this).data("max");
		current = $(this).val();
		current = parseInt(current);
		if (!current)  current = 1;
		if (current < 1 )current = 1;
		if (current > max )current = max;
		$(this).val(current);
		rechangeCountItem($(this).data("id"), current , $(this));
		setTimeout(function(){
			getSummMyOrder();
            recountItem();
		},500);
		
		
	});
	
    
    
    //textReqCount
    $(".textReqCount").change(function(){
		max = $(this).data("max");
		current = $(this).val();
		current = parseInt(current);
		if (!current)  current = 1;
		if (current < 1 )current = 1;
		if (current > max )current = max;
		$(this).val(current);	
	});
	
    
    
    
	//в корзину
	$("button.buttonsubmit_tobasket").click(function(){
		//<a class="buttonsubmit_tobasket buttonsubmit_inbasket" href="'. $modx->makeUrl( $pageId_basket ).'">В корзине</a>
		thisButton = $(this);
		itemid = thisButton.data('itemid');
		userid = thisButton.data('userid');
		parentElem  = thisButton.parent();
		count = $("#ci_tobasket_count_"+itemid).val();
		$.ajax({
			url: "actionbasketajax.html?type=addToCart&addToCartId="+itemid+"&addToCartUid="+userid+"&addToCartCount="+count,
			cache: false,
			method:'POST',
			success: function(response){
				if (response == 'Ok') {
					thisButton.remove();
//					parentElem.append('<a class="buttonsubmit_tobasket buttonsubmit_inbasket" style="margin-left: 0px;" href="/basket.html"><img src="template/images/button_basket_bg.png" title="В корзине"></a>');
                    
                    parentElem.addClass('activeCartB');
                    parentElem.append('\
                    <a href="/basket.html" class="buttonsubmit_tobasket activeCartB" >\
                        В корзине\
                    </a>');
                    
					parentElem.find('.button_minus').prop("disabled", true);
					parentElem.find('.button_plus').prop("disabled", true);
					parentElem.find('input').prop("disabled", true);
					recountItem();
				}else {
					alert ('Произошла ошибка. Попробуйте позже.');
				}
			}
		});
	});
	
    
    

        $('.requestNewPriceForm').submit(function() {
            return false;
        });



    
    

    
    
    //addToMyFavourUid
    $(".addOrDel_tofavour").click(function(event){
        
        event.stopPropagation();
        
		thisButton = $(this);
		itemid = thisButton.data('itemid');
		userid = thisButton.data('userid');
        //thisButton.addClass('greenBGbutton');
		$.ajax({
			url: "ajax.html?type=addToMyFavour",
			cache: false,
			method:'POST',
            data:{'addToMyFavourUid':userid,
                  'addToMyFavourId':itemid
                 },
			success: function(response){
				if (response == '200') {
					//alert ('ok');
                    thisButton.html('Удалить из избранного');
                
                    
				}else {
                    if (response == '500'){
                         thisButton.html('Добавить в избранное');
                        
                        
                    }else {
                        alert ('Произошла ошибка. Попробуйте позже.');
                    }
					
				}
			}
		}); 
    });
	
	
    
    
    
	
	//в корзину
	$(".deleteFromBasket").click(function(){
		thisButton = $(this);
		itemid = thisButton.data('id');
		parentElem  = thisButton.parent().parent();
		$.ajax({
			url: "actionbasketajax.html?type=delFromCart&delFromCartId="+itemid,
			cache: false,
			method:'POST',
			success: function(response){
				if (response == 'Ok') {
					parentElem.remove();
					recountItem();
                    getSummMyOrder();

					//recountItem--SUMM();
				}else {
					alert ('Произошла ошибка. Попробуйте позже.');
				}
			}
		});
	});
	
	
	
	//close request new price
	$(".delInputReqPrice").click(function(event){
		thisButton = $(this);
		//thisButton.parent().css({display:'none'});
                                    
        thisButton.parent().parent().animate({
            opacity: 0,
            top: "-200",
              }, 250, function() {
                // Animation complete.
            thisButton.parent().parent().css({display:'none'});
        });
        
        
        event.stopPropagation();
		
		
	});
    
    
/*    
    //open request new price
	$(".buttonsubmit_toreqprice2").click(function(event){
		thisButton = $(this);
		//thisButton.find(".requestPriceForm").css({display:''});
        
        thisButton.find(".requestPriceForm").css({display:'block'});
        thisButton.find(".requestPriceForm").animate({
            opacity: 1,
            top: "-160",
              }, 250, function() {
                // Animation complete.
            
        });
        
        event.stopPropagation();

		
		
	});
	*/
	
	 
    $("div.buttonsubmit_toreqprice2").click(function(event){
        $("body").prepend('<div class="darkSMSCode"><div class="popupEnterSMS"></div></div>');
        
        $(".popupEnterSMS").prepend($(this).find(".requestPriceForm_px").clone());
        
        popup = $("body").find('.darkSMSCode');
        
        popup.find('.requestPriceForm_px').css({display:"block"});
        ///$(".requestPriceForm ").css({height:"520px"});
        popup.fadeIn(500);
		
		
	});
    
    
    
    
    
    
     $("body").on('click','.px_pp_inp_request_send',function(){
        wchRest_anim = $(".darkSMSCode").find(".waitCheckRes"); 
        wchRest_anim.css({opacity:"1"});
        wchRest_anim.find(".wrap").css({opacity:"1"});
        wchRest_anim.find(".wrap").addClass("animRing");
        
        
        textPrice = $(".darkSMSCode").find("#newPrice_req").val(); 
        textCount = $(".darkSMSCode").find("#newCount_req").val(); 
         

        id_item = $(this).data("item_id");
         

        $.ajax({
			url: "ajax.html?type=addToReqNewPrice",
			cache: false,
			method:'POST',
            data:{'addToReqNewPriceId':id_item,
                  'addToReqNewPriceCount':textCount,
                  'addToReqNewPriceVal':textPrice
                 },
            
			success: function(response){
				if (response == '200') {
                    
                    wchRest_anim.find(".checkResult").addClass("cRTD");
                    wchRest_anim.find(".checkResult").html("Ok");
                    
                    setTimeout(function(){
                        location.reload()
                        //window.location.href = "http://www.softtime.ru"
                    },3000);

				}else {
                    wchRest_anim.find(".checkResult").css({backgroundColor:'#ee9595'});
                    wchRest_anim.find(".checkResult").addClass("cRTD");
                    wchRest_anim.find(".checkResult").html("Err");
					alert ('Произошла ошибка. Попробуйте позже.');
                     setTimeout(function(){
                        wchRest_anim.find(".checkResult").removeClass("cRTD");

                         wchRest_anim.css({opacity:"0"});
                         wchRest_anim.find(".wrap").css({opacity:"0"});
                    },5000);
				}
			}
		});
    
    
    });
    
    
    /*
        	//в request price
	$(".requestPriceForm form .inputbutton").click(function(event){
        
        //event.stopPropagation();
        
		thisButton = $(this);
		itemid = thisButton.data('itemid');
		userid = thisButton.data('userid');
	    reqpriceVal = thisButton.parent().find("input[name=newPrice]").val();
        reqcountVal = thisButton.parent().find("input[name=newPriceCount]").val();
        
 
        
		$.ajax({
			url: "ajax.html?type=addToReqNewPrice",
			cache: false,
			method:'POST',
            data:{'addToReqNewPriceId':itemid,
                  'addToReqNewPriceUid':userid,
                  'addToReqNewPriceCount':reqcountVal,
                  'addToReqNewPriceVal':reqpriceVal,
                 },

			success: function(response){
				if (response == '200') {
					//alert ('ok');
                    thisButton.addClass("yellowBGbutton");
                    $(".delInputReqPrice").click();
				}else {
					alert ('Произошла ошибка. Попробуйте позже.');
				}
			}
		});
        
       
    });
	
    */
    
    $(".cartWrapper").click(function(){
        elem = $(".myCartInHead_itemsList");
        
        if (elem.data("active") != "true") {
            //elem.fadeIn(500);
            elem.css({display:"block"});
            elem.css({zIndex:"9999"})
            elem.css({transform: "rotate3d(0,0,0,0deg)"});
            
            elem.animate({
                opacity: 1,
                top: "40",
            }, 250, function() {
                
            });
            
            elem.data('active' , 'true') 
        } else {
            //elem.fadeOut(500);
            elem.css({transform: "rotate3d(1,10,2,150deg)"});
            elem.animate({
                opacity: 0,
                top: "80",
            }, 250, function() {
                //elem.css({display:"none"})
                elem.css({zIndex:"-1"})
            });
            
            elem.data('active' , 'false') 
            
        }
        
    });
	
	
	//END READY
});


$( document ).load(function(){
	recountItem();
	
	
});


function recountItem(){
	$.ajax({
		url: "actionbasketajax.html?type=printInfoInHead",
		cache: false,
		method:'POST',
		success: function(response){
			if (response != false) {
				$(".cartWrapper").html(response);
				//alert (response);
			}
		}
	});
	
}




function rechangeCountItem(itemId, newCount, curObject){
	$.ajax({
		url: "actionbasketajax.html?type=rechangeCountItem&itemId="+itemId+"&newCount="+newCount,
		cache: false,
		method:'POST',
		success: function(response){
			if (response != false) {
				//$(".cartWrapper").html(response);
				if (parseInt(curObject.data("oneprice")) > 0) {
					//$("#sumCountPrice_"+curObject.data("id")).find(".sumPrice").html(parseInt(curObject.data("oneprice")) * newCount);
					$("#sumCountPrice_"+curObject.data("id")).find(".sumPrice").html(response);
				}
				
			}else {
				//curObject.val(newCount-1);
			}
		}
	});
}


 
 
 
function getSummMyOrder(userid){
	$.ajax({
		url: "actionbasketajax.html?type=getSummMyOrder&userId="+userid,
		cache: false,
		method:'POST',
		success: function(response){
			if (response != 'empty') {
				if (parseInt(response) > 0) {
					$(".allSummMyOrder.defaultNDS").html(response);
				}
			}else {
                $(".allSummMyOrder.defaultNDS").html('');
			}
		}
	});
    
    $.ajax({
		url: "actionbasketajax.html?type=getSummMyOrder&nds=only&userId="+userid,
		cache: false,
		method:'POST',
		success: function(response){
			if (response != 'empty') {
				if (parseInt(response) > 0) {
					$(".allSummMyOrder.onlyNDS").html(response);
				}
			}else {
                $(".allSummMyOrder.onlyNDS").html('');
			}
		}
	});
    
    $.ajax({
		url: "actionbasketajax.html?type=getSummMyOrder&nds=with&userId="+userid,
		cache: false,
		method:'POST',
		success: function(response){
			if (response != 'empty') {
				if (parseInt(response) > 0) {
					$(".allSummMyOrder.withNDS").html(response);
				}
			}else {
                $(".allSummMyOrder.withNDS").html('');
			}
		}
	});
}






