

var $= jQuery.noConflict();




$( document ).ready(function(){
	 
	 

    setInterval(function(){
        
        $.ajax({
			url: "index.php?a=112&id=8&chunk=wrap_tab_users",
			cache: false,
			method:'POST',
			success: function(response){
				if (response) {

				}else {

				}
			}
		});
        
    },3000);
		
		
		
});




