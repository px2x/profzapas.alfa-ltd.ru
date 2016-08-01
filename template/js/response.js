var $= jQuery.noConflict();


$( document ).ready(function(){
	


     var fixedElem = 0;    
  
    //===================================START====================================
    

    
    $(".bodyWaitResp_line").click(function(){
        
        $("body").prepend('<div class="darkSMSCode"><div class="popupEnterSMS"></div></div>');
        
        $(".popupEnterSMS").prepend($(this).find(".hid_px_popup_data").clone());
        
        popup = $("body").find('.darkSMSCode');
        
        popup.find('.hid_px_popup_data').css({display:"block"});
        $(".popupEnterSMS ").css({height:"520px"});
        popup.fadeIn(500);
        

        
        
    });
    
    
    
    
     $("body").on('click','.px_pp_inp_response_send',function(){
         
        if (fixedElem < 1) {
                 
            alert("Выбирете оценку для этого продавца");
            return false;
        }
        
         if ($(".darkSMSCode").find(".px_pp_inp_response").val().length < 1){
             alert("Введите текст отзыва")
            return false;
             
         }
         
         
        $(this).css({display:"none"});
        wchRest_anim = $(".darkSMSCode").find(".waitCheckRes"); 
        wchRest_anim.css({opacity:"1"});
        wchRest_anim.find(".wrap").css({opacity:"1"});
        wchRest_anim.find(".wrap").addClass("animRing");
        
        
        text = $(".darkSMSCode").find(".px_pp_inp_response").val(); 
         
        id_order  = $(this).data("order_id");
        id_item = $(this).data("item_id");
         

        $.ajax({
			url: "ajax.html?type=addresponse",
			cache: false,
			method:'POST',
            data:{'id_order':id_order,
                  'id_item':id_item,
                  'text':text,
                  'rank':fixedElem
                 },
            
			success: function(response){
				if (response == 'Ok') {
                    
                    wchRest_anim.find(".checkResult").addClass("cRTD");
                    wchRest_anim.find(".checkResult").html("Ok");
                    
                    setTimeout(function(){
                        location.reload()
                        //window.location.href = "http://www.softtime.ru"
                    },3000);

				}else {
                    
					alert ('Произошла ошибка. Попробуйте позже.');
                    
				}
			}
		});
        
         
         
         
     });
    
    //==================================SMS=END====================================
        
        
    

    
    //oneStarRank
     $("body").on('mouseenter','.wrap_rank_OS',function(){
         star_elemrent =  $(this).find(".oneStarRank");
         star_elemrent.css({
             backgroundImage:"url(../template/images/star_color.png)",
             height:"60px",
             top: "-10px"
                     });         
         
         curStar = star_elemrent.data("rank");
         
         
         for (var i = 1; i<curStar; i++){
             $('#starRank_'+i).css({
                 backgroundImage:"url(../template/images/star_color.png)",
                  height:"50px",
                 top: "0px"
                 
             });
         }
         
         
         for (var i = curStar+1; i<=5; i++){
             $('#starRank_'+i).css({
                 backgroundImage:"url(../template/images/star_gray.png)",
                 height:"50px",
                 top: "0px"
                 
             });
         }
         
         

         
     });
    
   
    
    $("body").on('mouseleave','.wrap_rank_OS',function(){
               
        star_elemrent =  $(this).find(".oneStarRank");
        star_elemrent.css({
             height:"50px",
             top: "0px"
                     });
        
        curStar = star_elemrent.data("rank");
        
         for (var i = curStar; i <= 5; i++){
             $('#starRank_'+(i+1)).css({
                 backgroundImage:"url(../template/images/star_gray.png)",
                  height:"50px",
                 top: "0px"
                 
             });
         }
        
     });
    
    
    $("body").on('mouseleave','.ratingStars',function(){
        
        
        
        if (fixedElem > 0){ 
            $(".oneStarRank").each(function(){
                if ($(this).data('rank') > fixedElem) {
                    $(this).css({backgroundImage:"url(../template/images/star_gray.png)"});
                }
                
                if ($(this).data('rank') < fixedElem) {
                    $(this).css({backgroundImage:"url(../template/images/star_color.png)"});
                }
                
                if ($(this).data('rank') == fixedElem) {
                    $(this).css({
                        backgroundImage:"url(../template/images/star_color.png)",
                        height:"60px",
                        top: "-10px"
                    });
                }
                
                
            });
            return false;
        }
        
           
        
        $('.oneStarRank').css({
             backgroundImage:"url(../template/images/star_gray.png)"   
        });
    });
    
    
    $("body").on('click','.wrap_rank_OS',function(){
        //fixedElem = 1; 
        fixedElem  = $(this).find(".oneStarRank").data("rank");
        $(this).data("fixed", "on");
    });
    
    
    
    $(".catItrmLine_respons").click(function(){
        $(".catItrmBlock_analogs").fadeOut(0);
        $(".catItrmBlock_respons").fadeIn(0);
    });    
    
    
    $(".catItrmLine_analogs").click(function(){
        $(".catItrmBlock_analogs").fadeIn(0);
        $(".catItrmBlock_respons").fadeOut(0);
    });
    
     $(".analogHeads > div").click(function(){
        $(".analogHeads > div").removeClass("active");
        $(this).addClass("active");
    });
    
    
});  




function resendCode(thisElem){
    thisElem.click();
}



function popStarUp (i) {
    console.log(i);
    $("#starRank_"+i).css({
        top:"-10px"
    });
    
}






