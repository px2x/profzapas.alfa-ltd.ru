

var $= jQuery.noConflict();

var thisForm = false; 

$( document ).ready(function(){
	//------------------------------------------------------------------
	if( $( '.input_mask_mobile' ).length ) $( '.input_mask_mobile' ).mask( '+7 999 999-9999' );
	
	//------------------------------------------------------------------
	$( '.vkladki_butts .vkldk_butt' ).click(function(){
		var id1= $( '.vkladki_butts .active' ).data( 'id' );
		var id2= $( this ).data( 'id' );
		if( ! $( this ).hasClass( 'active' ) )
		{
			var e1= $( '.vkldk_div_'+id1 );
			var e2= $( '.vkldk_div_'+id2 );
			
			$( '.vkladki_butts .active' ).removeClass( 'active' );
			$( this ).addClass( 'active' );
			e1.css({ zIndex: 1 });
			e2.css({ zIndex: 5, opacity: 0 });
			e1.removeClass( 'active' );
			e2.addClass( 'active' );
			e1.stop();
			e2.stop();
			e1.animate({ opacity: 0 }, 300, function(){ e1.hide(); });
			e2.show();
			e2.animate({ opacity: 1 }, 300 );
		}
	});
	
	//------------------------------------------------------------------
	$( '.LK_form_urlico_checkbox' ).change(function(){
		var flag= $( this ).is(':checked');
		if( flag )
		{
			$( '.LK_form_urlico' ).addClass( 'LK_form_urlico_active' );
		}else{
			$( '.LK_form_urlico' ).removeClass( 'LK_form_urlico_active' );
		}
	});
	
	//------------------------------------------------------------------
    
    
    
    
    //PX2X START 
    
    $(".openedDisputeLine").click(function(event){
        
        event.stopPropagation();
        
        idoreder=$(this).find(".stat_orederNumber").data("idorder");
        targetsDiv=$(".seeItemsInOrderB");
        targetsDiv.html("");
        
        $.ajax({
			url: "adajax.html?getItemListInOrder&numberIdOrder="+idoreder,
			cache: false,
			method:'POST',
			success: function(response){
				if (response != 'acces denied') {
					targetsDiv.html(response);
					
				}else {
					alert ('Произошла ошибка. Попробуйте позже.');
				}
			}
		});
        
        
        
        $(".fadeDarkBG").fadeIn('fast', function() {
      
        });
        
        
        
        
        
     
        
    });
    
    $(".fadeDarkBG").click(function(){
        
         $(this).fadeOut('fast', function() {

        });
    });
//    
//    $(document).mouseup(function (e){ // событие клика по веб-документу
//		var div = $(".seeItemsInOrderB"); // тут указываем ID элемента
//		if (!div.is(e.target) // если клик был не по нашему блоку
//		    && div.has(e.target).length === 0) { // и не по его дочерним элементам
//			div.fadeOut('fast', function() {
//              
//            }); 
//            
//            $(".fadeDarkBG").fadeOut('fast', function() {
//                
//            });
//		}
//        
//	});
//    
    
    //PX2X END 
    
    
    
    
    $(".statisticMainLine").click(function(event){
        
        
        event.stopPropagation();
        
        
        seeMoreStatBlock = $(this).find('.seeMoreStatBlock');
        seePic = $(this).find('.seeMoreStat').find('img');
        

         console.log(seePic);            
         
         
        //alert(seeMoreStatBlock.height());
        
        if (seeMoreStatBlock.height() == 0) {
            
            seeMoreStatBlock.css({height:'initial'});
            $(this).css({marginBottom:seeMoreStatBlock.height()+'px'});
            seePic.css({transform:'rotate(180deg)'});
            
        }else {
            
            seeMoreStatBlock.css({height:'0px'});
            $(this).css({marginBottom:'0px'});
            seePic.css({transform:'rotate(0deg)'});
        }
        
        
        
        
        
    });

 $(".seeMoreStatBlock").css({height:'0'});
        

    
    
    
    function checkForm6(){
        oldPass =  $(".oldPass").val();
        newPass =  $(".newPass").val();
        newPassConfirm =  $(".newPassConfirm").val();
        
        console.log(newPass);
        err = false;
        
        if (newPass.length < 6) {
            $(".pxNtc_newPass").html("Слишком короткий пароль");
            err = true;
        }
        
        if (newPass != newPassConfirm) {
            $(".pxNtc_newPassConfirm").html("Пароли не совпадают");
            err = true;
        }
        
        
        
        
                    
         $.ajax({
                url: "ajax.html?checkPassswd",
                cache: false,
                method:'POST',
                async: false, 
                data: "passwh="+oldPass,
                success: function(response){
                    if (response != 'yes') {
                        $(".pxNtc_oldPass").html("Неверный пароль");
                        err = true;
  
                    } else {
                       
                    }
                }
         }); 
    
        
        if (!err) return true;
        else return false;
        
    }
    
    
    
    
    
        
    function checkForm1(){

        return true;
    }
    
    
    function checkForm2(){

        return true;
    }
    
    
    
        //seeItemsInOrderB
    $("body").on("click" , '.seeItemsInOrderB',function(e){
        e.preventDefault();
        e.stopPropagation();
    });
    
    
    
     $(".seeItemsInOrderB").click(function(e){
        e.preventDefault();
        e.stopPropagation();
    });
    
    
  
    //==================================SMS=START====================================
    
    var timerCountDown = false;
    //var thisForm = false; 
    
    $(".genSMS").click(function(){
        
    
        $('.pxNtc').html('');
        
        if ($(this).attr("name") == 'save_6') result = checkForm6();
        if ($(this).attr("name") == 'save_2') result = checkForm2();
        if ($(this).attr("name") == 'save_1') result = checkForm1();
        
        if (!result) return false;
        
        
        $("body").prepend('<div class="darkSMSCode"><div class="popupEnterSMS"><div class="popupHtext">Введите код из СМС</div><div class="popupHdescr">На Ваш мобильный телефон было отправлено сообщение с кодом подтверждения для изменения данных</div><div class="popupDublicSMS">Повторно СМС можно выслать через 120 сек.</div><input type="text" class="verifySMScode" maxlength="5"/><div class="waitCheckSMS"><div class="checkResult"></div><div class="wrap"><div class="dot"></div></div></div></div><div class="closeAll"></div></div>');
        
        
        thisElem = $(this);
        thisForm = $(this).parent().parent().parent();
        popup = $("body").find('.darkSMSCode');
       
        
        popup.fadeIn(500);
        popup.find(".verifySMScode").focus();
        //focus()
        
        $.ajax({
             url: "ajax.html?sendConfirmCode",
             cache: false,
             method:'POST',
             success: function(response){
                console.log(response);
                if (response == 'ok') {
                    
                    cndwn = 0;
                    clearInterval(timerCountDown);
                    
                    timerCountDown = setInterval(function(){
                        $(".popupDublicSMS").html('Повторно СМС можно выслать через '+(120-cndwn)+' сек.');
                        cndwn++;
                        if (cndwn > 120)  {
                            clearInterval(timerCountDown);
                            $(".popupDublicSMS").html('<div onclick="resendCode(thisElem);">Выслать код еще раз</div>');
                        }
                    },1000);
                    
                    
                }else {
                    
                    console.log(timerCountDown);
                    
                    if (timerCountDown === false) {
                        cndwn = 0;
                        clearInterval(timerCountDown);
                        timerCountDown = setInterval(function(){
                            $(".popupDublicSMS").html('Повторно СМС можно выслать через '+(120-cndwn)+' сек.');
                            cndwn++;
                            if (cndwn > 120)  {
                                clearInterval(timerCountDown);
                                $(".popupDublicSMS").html('<div onclick="resendCode(thisElem);">Выслать код еще раз</div>');
                            }
                        },1000);
                    }else {
                       // clearInterval(timerCountDown);
                    }
                    
                }
            }
        });
        
        
        
        
        
        
    });
    
    
     $("body").on("click", '.darkSMSCode', function(){
        //clearInterval(timerCountDown);
        $(this).fadeOut(500,function(){
            $(".verifySMScode").val("");
            $(".waitCheckSMS .wrap").removeClass("animRing");
            $(".waitCheckSMS").css({opacity:"0"});
            
            $('.darkSMSCode').remove();
        });
        
    });
    
    
    //popupEnterSMS
    $("body").on("click" , '.popupEnterSMS',function(e){
        e.preventDefault();
        e.stopPropagation();
    });
    
    
    var ringTimer = false;
    
    
    $("body").on('keyup','.verifySMScode',function(){
        
        clearTimeout(ringTimer);
        wchSMS = $(".darkSMSCode").find(".waitCheckSMS"); 
        console.log(thisForm);
        //thisForm = wchSMS.parent().parent().parent();
        
//        wchSMS.css({opacity:"1"});
//        wchSMS.find(".wrap").addClass("animRing");
        
        console.log($(this).val().length);
        if ($(this).val().length == 5){
            
            tcode = $(this).val();
            
//            wchSMS.css({opacity:"1"});
//            wchSMS.find(".wrap").addClass("animRing");
            
            
            $.ajax({
                url: "ajax.html?checkConfirmCode&code="+tcode,
                cache: false,
                method:'POST',
                success: function(response){
                    wchSMS.find(".wrap").css({opacity:"0"});
                    if (response == 'yes') {
                       // alert(response);
                        console.log(response);
                        wchSMS.find(".checkResult").addClass("cRTD");
                        wchSMS.find(".checkResult").html("Ok");
                        setTimeout(function(){
                            thisForm.submit();
                        },3000);

                    }else {
                        console.log(response);
                        wchSMS.find(".checkResult").css({backgroundColor:'#ee9595'});
                        wchSMS.find(".checkResult").addClass("cRTD");
                        wchSMS.find(".checkResult").html("Err");
                        //alert(response);
                
                    }

//                   wchSMS.find(".checkResult").addClass("cRTD");

                }
            });
            
            
            
        }else {
            
            
            if ($(this).val().length > 0){
                
                wchSMS.css({opacity:"1"});
                wchSMS.find(".wrap").css({opacity:"1"});
                wchSMS.find(".wrap").addClass("animRing");
                wchSMS.find(".checkResult").css({backgroundColor:'#abe798'});
                wchSMS.find(".checkResult").removeClass("cRTD");
                
            }else {
                wchSMS.find(".checkResult").removeClass("cRTD");
                wchSMS.find(".wrap").css({opacity:"1"});
                wchSMS.css({opacity:"0"});

                ringTimer = setTimeout(function(){
                    wchSMS.find(".wrap").removeClass("animRing");
                },800);
                
            }
            
            

            
        }
        
    });
    
    //==================================SMS=END====================================
        
        
});




function resendCode(thisElem){
    
    thisElem.click();
    
}

$(window).load(function(){
	//------------------------------------------------------------------
	var maxheight= 0;
	$( '.vkladki_divs .vkldk_div' ).each(function(){
		if( $( this ).outerHeight( true ) > maxheight ) maxheight= $( this ).outerHeight( true );
	});
	$( '.vkladki_divs' ).css({ height: maxheight });
	
	//------------------------------------------------------------------
});











