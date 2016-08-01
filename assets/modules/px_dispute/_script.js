

var $= jQuery.noConflict();




Array.prototype.remove = function(value) {
    var idx = this.indexOf(value);
    if (idx != -1) {
        return this.splice(idx, 1);
    }
    return false;
}


$( document ).ready(function(){
	 
	 
	 var currentAnalog = -1;
	 
	 
	//Подроьности товара на сотерации (раскрытие)
	$(".lkItem .more").click(function(){
		$(".lkItem").css({height:'50px'});
		$(".seeMoreInfo").css({height:'0'});
		elementExpand = $(this).parent();
		elementExpand.css({height:'350px'});
		elementExpand.find('.seeMoreInfo').css({height:'300px'});
	});
	
	
	$('.dsrkBG').click(function(e){
        $(this).fadeOut(250);
        
        e.stopPropagation();
        e.preventDefault(); 
        
    });

    
    $('.messagesSmall').click(function(e){
        e.stopPropagation();
        //e.preventDefault(); 
        
    });
    
    $('.seemoreinfoDispute').click(function(){
        $(this).find('.dsrkBG').fadeIn(250);
        
        did = $(this).data('did');
        $.ajax({
			url: "../adajax.html?type=setreadMsg&did="+did,
			cache: false,
			method:'POST',
			success: function(response){

				}
			});
        
    });

	
 
		
		
		
});




