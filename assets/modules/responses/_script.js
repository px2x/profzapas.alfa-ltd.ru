var $= jQuery.noConflict();




Array.prototype.remove = function(value) {
    var idx = this.indexOf(value);
    if (idx != -1) {
        return this.splice(idx, 1);
    }
    return false;
}


$( document ).ready(function(){
	 
    
	 $('#resp_accept').click(function(){
        idresponse = $(this).data("idresponse");
         
        $.ajax({
			url: "../adajax.html?acceptresponse",
			cache: false,
			method:'POST',
            data:{'idresponse':idresponse
                 },
            
			success: function(response){
				if (response == 'Ok') { 
                    
                     window.location.href=document.location;
                    
				}else {
                    
					alert ('Произошла ошибка. Попробуйте позже.');
				}
			}
		 });
     });

    
    
        
	 $('#resp_block').click(function(){
        idresponse = $(this).data("idresponse");
         
        $.ajax({
			url: "../adajax.html?blockresponse",
			cache: false,
			method:'POST',
            data:{'idresponse':idresponse
                 },
            
			success: function(response){
				if (response == 'Ok') { 
                    
                     window.location.href=document.location;
                    
				}else {
                    
					alert ('Произошла ошибка. Попробуйте позже.');
				}
			}
		 });
     });
    
    
});




