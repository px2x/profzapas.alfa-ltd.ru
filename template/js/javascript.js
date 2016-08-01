

var $= jQuery.noConflict();


var suggest_count = 0;
var input_initial_value = '';
var suggest_selected = 0;


Array.prototype.remove = function(value) {
    var idx = this.indexOf(value);
    if (idx != -1) {
        return this.splice(idx, 1);
    }
    return false;
}



$(window).scroll(function(){
	CTM_black();
});
$(window).resize(function(){
	CTM_black();
});


$( document ).ready(function(){
	
	
	
	


	//для редакт картинок
	
	if ($("input").is(".linkToUplDocs")){
		//alert('jeee');
		//tmpDoc = JSON.parse(localStorage.getItem("fileUploadedArrDOC"));
		//tmpDocName = JSON.parse(localStorage.getItem("fileUploadedArrDOC"));
		masEl  = new Array();
		countEl=0;
		$.each($(".linkToUplDocs") , function(){
			masEl[countEl] = $(this).val();
			countEl++;
		});
		//console.log(masEl);
		localStorage.setItem('fileUploadedArrDOCedit' , JSON.stringify(masEl));
		
		masEl  = Array();
		countEl=0;
		$.each($(".originalnameToUplDocs") , function(){
			masEl[countEl] = $(this).val();
			countEl++;
		});
		localStorage.setItem('fileUploadedArrDOCnameedit' , JSON.stringify(masEl));
		
		
	}
		
		
		
	if ($("input").is(".linkToUplImgs")){
		masEl  = new Array();
		countEl=0;
		$.each($(".linkToUplImgs") , function(){
			masEl[countEl] = $(this).val();
			countEl++;
		});
		//console.log(masEl);
		localStorage.setItem('imageUploadedArrIMGedit' , JSON.stringify(masEl));
		

		
		
	}
	

	
	
	
	
	
	
	
	//------------------------------------------------------------------
	$( '.default_value' ).each(function(){
		var defval= $( this ).data( 'default' );
		if( $( this ).val() == '' || $( this ).val() == defval )
		{
			$( this ).addClass( 'default_value_flag' );
			$( this ).val( $( this ).data( 'default' ) );
		}
	});
	$( '.default_value' ).focus(function(){
		var defval= $( this ).data( 'default' );
		if( $( this ).val() == '' || $( this ).val() == defval )
		{
			$( this ).val( '' );
			$( this ).removeClass( 'default_value_flag' );
		}
	});
	$( '.default_value' ).focusout(function(){
		var defval= $( this ).data( 'default' );
		if( $( this ).val() == '' || $( this ).val() == defval )
		{
			$( this ).val( defval );
			$( this ).addClass( 'default_value_flag' );
		}
	});
	
	//------------------------------------------------------------------
	$( '.tsch_maininput' ).focus(function(){
		$( this ).parent().addClass( 'tsch_pole_focus' );
	});
	$( '.tsch_maininput' ).focusout(function(){
		$( this ).parent().removeClass( 'tsch_pole_focus' );
	});
	
	//------------------------------------------------------------------
	$( '.tsch_dop' ).click(function(){
		if( $( '.topsearch' ).hasClass( 'topsearch_active' ) )
		{
			$( '.topsearch' ).removeClass( 'topsearch_active' );
			$( '.tsch_dop_input_flag' ).val( 'n' );
			$( '.tsch_block_2' )
				.stop()
				.animate({ opacity: 0 }, 300, function(){
					$( this ).hide();
				});
			}else{
			$( '.topsearch' ).addClass( 'topsearch_active' );
			$( '.tsch_dop_input_flag' ).val( 'y' );
			$( '.tsch_block_2' )
				.stop()
				.css({ opacity: 0 })
				.show()
				.animate({ opacity: 1 }, 500 );
		}
	});
	
	//------------------------------------------------------------------
	$( 'form' ).submit(function( event ){
		if( $( '.default_value', this ).val() == $( '.default_value', this ).data( 'default' ) )
			$( '.default_value', this ).val( '' );
	});
	
	//------------------------------------------------------------------
	$( '.catalog_table .descr' ).hover(function(){
		$( '.fulldescription', $( this ).parent() ).addClass( 'active' )
			.stop()
			.css({ opacity: 0 })
			.show()
			.animate({ opacity: 1 }, 200 );
	},function(){
		$( '.fulldescription', $( this ).parent() ).removeClass( 'active' ).stop().hide();
	});
	
	//------------------------------------------------------------------
	$( '.catalog_top_menu .ctm_button' ).click(function(){
		if( $( '.catalog_top_menu' ).hasClass( 'open' ) )
		{
			$( '.mainwrapper' ).css({ overflow: 'auto', height: 'auto' });
			$( '.catalog_top_menu' ).removeClass( 'open' );
			$( '.catalog_top_menu .ctm_black' )
				.stop()
				.animate({ opacity: 0 }, 200, function(){
					$( this ).hide();
				});
			$( '.catalog_top_menu .ctm_catalog' )
				.stop()
				.animate({ opacity: 0 }, 200, function(){
					$( this ).hide();
				});
		}else{
			//$( 'body' ).css({ overflow: 'hidden' });
			$( '.catalog_top_menu' ).addClass( 'open' );
			CTM_black();
			$( '.catalog_top_menu .ctm_black' )
				.stop()
				.css({ opacity: 0 })
				.show()
				.animate({ opacity: 1 }, 300 );
			$( '.catalog_top_menu .ctm_catalog' )
				.stop()
				.css({ opacity: 0 })
				.show()
				.animate({ opacity: 1 }, 300 );
			$( '.mainwrapper' ).css({ overflow: 'hidden', height: $( '.ctm_catalog_abs' ).offset().top + $( '.ctm_catalog_abs' ).outerHeight() + 50 });
		}
	});
	
	
	
	var oneCatLevel= $( '#save_item_catOne' );

	//==================================отсюда до конца Ready писал я =========
	
	//грузит субкатегории при выборе категории
	oneCatLevel.change(function(){
		var link = $('#save_item_catOne option:selected:last').val();
		if (link == '') return false;
		$( '#save_item_catTwo' ).empty();
		$.ajax({
			url: "ajax.html?getTwoLevelCat="+link,
			cache: false,
			success: function(html){
				$( '#save_item_catTwo' ).append(html);
			}
		});
		
	});
	
	

	//полная х***я
	/*
	$( '#save_item_warehouse1').change(function(){
		var link = $('#select_item_warehouse1 option:selected:last').val();
		console.log (link);
		if (link == -1) {
			$( '#save_item_warehouse2').css({display:'none'});
			$( '#save_item_warehouse3').css({display:'none'});
			$( "#select_item_warehouse2 [value='-1']").attr("selected", "selected");
			$( "#select_item_warehouse3 [value='-1']").attr("selected", "selected");
			
			$( "#select_item_warehouse2 option").removeAttr("disabled");
			$( "#select_item_warehouse3 option").removeAttr("disabled");
		}else {
	
			$( '#save_item_warehouse2').css({display:'block'});
			$( "#select_item_warehouse2 option").removeAttr("disabled");
			$( "#select_item_warehouse3 option").removeAttr("disabled");
			$( "#select_item_warehouse2 [value='"+link+"']").attr("disabled","disabled");
			$( "#select_item_warehouse3 [value='"+link+"']").attr("disabled","disabled");
		}
		
	});
	
	var selectedElem = '';
	$( '#save_item_warehouse2').change(function(){
		var link = $('#select_item_warehouse2 option:selected:last').val();
		console.log (link);
		if (link == -1) {
			$( '#save_item_warehouse3').css({display:'none'});
			$( "#select_item_warehouse3 [value='-1']").attr("selected", "selected");
			$( "#select_item_warehouse3 [value='"+selectedElem+"']").removeAttr("disabled");
		}else {
			selectedElem = link;
			$( '#save_item_warehouse3').css({display:'block'});
			$( "#select_item_warehouse3 [value='"+selectedElem+"']").removeAttr("disabled");
			$( "#select_item_warehouse3 [value='"+link+"']").attr("disabled","disabled");
		}
		
	});
	*/
	
	//------------------------------------------------------------------
	//сделано с божъей помощью
	var stockList = [];
	$.each($( '#save_item_warehouse1 option'),function(){
		stockList.push($(this).val());
	});	
	$('.smallWH').change(function(){
		var link = $($(this),'option:selected:last').val();
		if (link > 0) {
			$.each($(this).find('option'),function(){
				console.log($(this));
				if ($(this).attr('disabled') != 'disabled'){
					$(".smallWH [value='"+$(this).val()+"']").removeAttr("disabled");
				}
			});
			$(".smallWH [value='"+link+"']").not($(this).find("option")).attr("disabled","disabled");
			$("#save_item_count").attr('readonly' , true);
			$(this).parent().find('.smallINSTCK').removeAttr("readonly");
			$(this).parent().find('.smallINSTCK').attr('placeholder','Введите количество на складе');
			$(this).parent().parent().next('._LK_form_line').css({display:''});
		}else {
			checkAllEnabled = true;
			$.each($(this).find('option'),function(){
				console.log($(this));
				if ($(this).attr('disabled') != 'disabled'){
					$(".smallWH [value='"+$(this).val()+"']").removeAttr("disabled");
				}else {
					checkAllEnabled = false;
				}
			});
			$(this).parent().find('.smallINSTCK').attr('readonly' , true);
			$(this).parent().find('.smallINSTCK').val('');
			$(this).parent().find('.smallINSTCK').removeAttr("placeholder");
			if (checkAllEnabled){
				$("#save_item_count").removeAttr("readonly");
			}
			$('.smallINSTCK').keyup();
			//$(this).parent().parent().next('._LK_form_line').css({display:'none'});
			
		}
	});
	
	$('.smallINSTCK').keyup(function(){
		sumCount = 0;
		$.each($('.smallINSTCK'),function(){
			if (parseInt($(this).val())) {
				sumCount = sumCount +  parseInt($(this).val());
			}
		});
		$("#save_item_count").val(sumCount);
	});
	

	//----
	//$( '#save_item_warehouse2').css({display:''});
	//$( '#save_item_warehouse3').css({display:''});
	
	//localStorage.clear(); 
	
	
	//--------------------DOCS UPLOAD START----------------------------------------------
	var filesDOC;
	var htmlDOC = '';
	var iDocs = 0;
	var fileUploadedArrDOC = new Array();
	var fileUploadedArrDOCname = new Array();
	$('#uploaded_file').change(function(){
		if (this.files.length + iDocs >4 || iDocs == 4) {
			alert ('Для одного товара можно загрузить не более 4 документов. Вы уже загрузили '+iDocs+' и выбрали еще '+this.files.length);
			return;
		}
		var progressBar = $('#docprogress');
		filesDOC = this.files;
		var data = new FormData();
		$.each( filesDOC, function( key, value ){
			data.append( key, value );
		});	
		$('#submitAddItem').attr('disabled', "disabled");
		form = $('#addMewItemForm');
		$.ajax({
			url: './ajax.html?uploadfiles',
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false, 
			contentType: false,
			xhr: function(){
				var xhr = $.ajaxSettings.xhr(); 
				xhr.upload.addEventListener('progress', function(evt){ 
				  if(evt.lengthComputable) { 
					var percentComplete = Math.ceil(evt.loaded / evt.total * 100);
					progressBar.css({width: percentComplete + '%'});
				  }
				}, false);
				return xhr;
		    },
			success: function( respond, textStatus, jqXHR ){
				if( typeof respond.error === 'undefined' ){
					console.log(respond);
					console.log(textStatus);
					setTimeout(function(){
						progressBar.css({width: '0%'});
					},2000);
					var files_path = respond.files.link;
					ff=0;
					$.each( files_path, function( key, val ){  
						$('.ajax-respond').append('<div class="documentUpl"  style="background-image:url(template/images/file_'+val.split('.')[2]+'.png);"><div id="'+val+'" class="deleteDoc"></div><span>'+respond.files.name[ff]+'</span></div>'); 
						fileUploadedArrDOC[iDocs] = val;
						fileUploadedArrDOCname[iDocs] = respond.files.name[ff];
						form.prepend( '<input type="hidden" class="inphiddoc" name="fileDocHandler'+iDocs+'" value="'+val+'" />' );
						form.prepend( '<input type="hidden" class="inphiddocname" name="fileDocHandlername'+iDocs+'" value="'+respond.files.name[ff]+'" />' );
						iDocs++;
						addEvenClick();
					ff++;
					});
				} else {
					if (respond.error == 'fileDenied'){
						alert ('Вы выбрали недопустимый тип фала');
					}
					if (respond.error == 'fileNotMoved'){
						alert ('При загрузке файла произошла ошибка');
					}
					console.log('ОШИБКИ ОТВЕТА сервера: ' + respond.error );
				}
				setTimeout(function(){
					progressBar.css({width: '0%'});
				},1000);
				if (localStorage.getItem("fileUploadedArrDOC")){
					$(".inphiddoc").remove();
					$(".inphiddocname").remove();
					
					var cleanArray = [];
					var cleanArrayName = [];
					
					var j = 0;
					for (var i=0; i<(fileUploadedArrDOC.length); i++) {
						if(fileUploadedArrDOC[i] != null){
							cleanArray[j] = fileUploadedArrDOC[i];
							cleanArrayName[j] = fileUploadedArrDOCname[i];
							form.prepend( '<input type="hidden" class="inphiddoc" name="fileDocHandler'+j+'" value="'+cleanArray[j]+'" />' );
							form.prepend( '<input type="hidden" class="inphiddocname" name="fileDocHandlername'+j+'" value="'+cleanArrayName[j]+'" />' );
							j++; 
						}
					}
					localStorage.setItem('fileUploadedArrDOC' , JSON.stringify(cleanArray));
					localStorage.setItem('fileUploadedArrDOCname' , JSON.stringify(cleanArrayName));
				}else {
					localStorage.setItem('fileUploadedArrDOC' , JSON.stringify(fileUploadedArrDOC));
					localStorage.setItem('fileUploadedArrDOCname' , JSON.stringify(fileUploadedArrDOCname));
				}
				$('#submitAddItem').removeAttr("disabled");
			},
			error: function( jqXHR, textStatus, errorThrown ){
				console.log('ОШИБКИ AJAX запроса: ' + textStatus );
			}
		});
	});
	
	
	currentPage = (location.href.split('.html')[0].split('/lk/')[1]);
	if (currentPage == 'moi-tovary'){
		form = $('#addMewItemForm');
		if  (localStorage.fileUploadedArrDOC) {
			test = localStorage.fileUploadedArrDOC ? JSON.parse(localStorage.fileUploadedArrDOC) : [];
			test2 = localStorage.fileUploadedArrDOCname ? JSON.parse(localStorage.fileUploadedArrDOCname) : [];
			fileUploadedArrDOC = test;
			fileUploadedArrDOCname = test2;
			iDocs = test.length;
			var htmlDOC = '';
			for (var t = 0; t<=test.length-1; t++){
				form.prepend( '<input type="hidden" class="inphiddoc" name="fileDocHandler'+t+'" value="'+test[t]+'" />' );
				form.prepend( '<input type="hidden" class="inphiddocname" name="fileDocHandlername'+t+'" value="'+test2[t]+'" />' );
				htmlDOC += '<div class="documentUpl" style="background-image:url(template/images/file_'+test[t].split('.')[2]+'.png);"><div  id="'+test[t]+'" class="deleteDoc"></div><span>'+test2[t] +'</span></div>'; 
			}
			$('.ajax-respond').html( htmlDOC );
		}
	}



	
	//--------------------DOCS UPLOAD END----------------------------------------------
	addEvenClick();
	function addEvenClick () {
		$('.documentUpl .deleteDoc').on('click'  , function(){
			test = localStorage.fileUploadedArrDOC ? JSON.parse(localStorage.fileUploadedArrDOC) : [];
			iDocs--;
			test.remove($(this).attr('id'));
			fileUploadedArrDOC.remove($(this).attr('id'));
			localStorage.removeItem('fileUploadedArrDOC');
			localStorage.setItem('fileUploadedArrDOC' ,JSON.stringify(test));
			$(this).parent().remove();
			$(".inphiddoc").remove();
			for (var t = 0; t<=fileUploadedArrDOC.length-1; t++){
			form.prepend( '<input type="hidden"  class="inphiddoc" id="'+fileUploadedArrDOC[t]+'"  name="fileDocHandler'+t+'" value="'+fileUploadedArrDOC[t]+'" />' );
			}
			$.ajax({
				url: "ajax.html?abortDocs="+$(this).attr('id'),
				cache: false,
				success: function(html){
						
				}
			});
		});
	}

	
		//--------------------DOCS EDIT START----------------------------------------------
	var filesDOC;
	var htmlDOC = '';
	var iDocs = 0;
	var fileUploadedArrDOCedit = new Array();
	var fileUploadedArrDOCnameedit = new Array();
	$('#uploaded_fileEdit').change(function(){
		if (this.files.length + iDocs >4 || iDocs == 4) {
			alert ('Для одного товара можно загрузить не более 4 документов. Вы уже загрузили '+iDocs+' и выбрали еще '+this.files.length);
			return;
		}
		var progressBar = $('#docprogress');
		filesDOC = this.files;
		var data = new FormData();
		$.each( filesDOC, function( key, value ){
			data.append( key, value );
		});	
		$('#submitAddItem').attr('disabled', "disabled");
		form = $('#addMewItemForm');
		$.ajax({
			url: './ajax.html?uploadfiles',
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false, 
			contentType: false,
			xhr: function(){
				var xhr = $.ajaxSettings.xhr(); 
				xhr.upload.addEventListener('progress', function(evt){ 
				  if(evt.lengthComputable) { 
					var percentComplete = Math.ceil(evt.loaded / evt.total * 100);
					progressBar.css({width: percentComplete + '%'});
				  }
				}, false);
				return xhr;
		    },
			success: function( respond, textStatus, jqXHR ){
				if( typeof respond.error === 'undefined' ){
					console.log(respond);
					console.log(textStatus);
					setTimeout(function(){
						progressBar.css({width: '0%'});
					},2000);
					var files_path = respond.files.link;
					ff=0;
					$.each( files_path, function( key, val ){  
						$('.ajax-respond').append('<div class="documentUpl"  style="background-image:url(template/images/file_'+val.split('.')[2]+'.png);"><div id="'+val+'" class="deleteDoc"></div><span>'+respond.files.name[ff]+'</span></div>'); 
						fileUploadedArrDOCedit[iDocs] = val;
						fileUploadedArrDOCnameedit[iDocs] = respond.files.name[ff];
						form.prepend( '<input type="hidden" class="inphiddoc" name="fileDocHandler'+iDocs+'" value="'+val+'" />' );
						form.prepend( '<input type="hidden" class="inphiddocname" name="fileDocHandlername'+iDocs+'" value="'+respond.files.name[ff]+'" />' );
						iDocs++;
						addEvenClick();
					ff++;
					});
				} else {
					if (respond.error == 'fileDenied'){
						alert ('Вы выбрали недопустимый тип фала');
					}
					if (respond.error == 'fileNotMoved'){
						alert ('При загрузке файла произошла ошибка');
					}
					console.log('ОШИБКИ ОТВЕТА сервера: ' + respond.error );
				}
				setTimeout(function(){
					progressBar.css({width: '0%'});
				},1000);
				if (localStorage.getItem("fileUploadedArrDOCedit")){
					$(".inphiddoc").remove();
					$(".inphiddocname").remove();
					
					var cleanArray = [];
					var cleanArrayName = [];
					
					var j = 0;
					for (var i=0; i<(fileUploadedArrDOCedit.length); i++) {
						if(fileUploadedArrDOCedit[i] != null){
							cleanArray[j] = fileUploadedArrDOCedit[i];
							cleanArrayName[j] = fileUploadedArrDOCnameedit[i];
							form.prepend( '<input type="hidden" class="inphiddoc" name="fileDocHandler'+j+'" value="'+cleanArray[j]+'" />' );
							form.prepend( '<input type="hidden" class="inphiddocname" name="fileDocHandlername'+j+'" value="'+cleanArrayName[j]+'" />' );
							j++; 
						}
					}
					localStorage.setItem('fileUploadedArrDOCedit' , JSON.stringify(cleanArray));
					localStorage.setItem('fileUploadedArrDOCnameedit' , JSON.stringify(cleanArrayName));
				}else {
					localStorage.setItem('fileUploadedArrDOCedit' , JSON.stringify(fileUploadedArrDOCedit));
					localStorage.setItem('fileUploadedArrDOCnameedit' , JSON.stringify(fileUploadedArrDOCnameedit));
				}
				$('#submitAddItem').removeAttr("disabled");
			},
			error: function( jqXHR, textStatus, errorThrown ){
				console.log('ОШИБКИ AJAX запроса: ' + textStatus );
			}
		});
	});
	
	
	
	currentPage = (location.href.split('.html')[0].split('/lk/')[1]);
	if (currentPage == 'itemedit'){
		form = $('#addMewItemForm');
		if  (localStorage.fileUploadedArrDOCedit) {
			test = localStorage.fileUploadedArrDOCedit ? JSON.parse(localStorage.fileUploadedArrDOCedit) : [];
			test2 = localStorage.fileUploadedArrDOCnameedit ? JSON.parse(localStorage.fileUploadedArrDOCnameedit) : [];
			fileUploadedArrDOCedit = test;
			fileUploadedArrDOCnameedit = test2;
			iDocs = test.length;
			var htmlDOC = '';
			for (var t = 0; t<=test.length-1; t++){
				form.prepend( '<input type="hidden" class="inphiddoc" name="fileDocHandler'+t+'" value="'+test[t]+'" />' );
				form.prepend( '<input type="hidden" class="inphiddocname" name="fileDocHandlername'+t+'" value="'+test2[t]+'" />' );
				htmlDOC += '<div class="documentUpl" style="background-image:url(template/images/file_'+test[t].split('.')[2]+'.png);"><div  id="'+test[t]+'" class="deleteDoc"></div><span>'+test2[t] +'</span></div>'; 
			}
			$('.ajax-respond').html( htmlDOC );
		}
	}

	
	//--------------------DOCS EDIT  END----------------------------------------------
	addEvenClick();
	function addEvenClick () {
		$('.documentUpl .deleteDoc').on('click'  , function(){
			test = localStorage.fileUploadedArrDOCedit ? JSON.parse(localStorage.fileUploadedArrDOCedit) : [];
			iDocs--;
			test.remove($(this).attr('id'));
			fileUploadedArrDOCedit.remove($(this).attr('id'));
			localStorage.removeItem('fileUploadedArrDOCedit');
			localStorage.setItem('fileUploadedArrDOCedit' ,JSON.stringify(test));
			$(this).parent().remove();
			$(".inphiddoc").remove();
			for (var t = 0; t<=fileUploadedArrDOCedit.length-1; t++){
			form.prepend( '<input type="hidden"  class="inphiddoc" id="'+fileUploadedArrDOCedit[t]+'"  name="fileDocHandler'+t+'" value="'+fileUploadedArrDOCedit[t]+'" />' );
			}
			$.ajax({
				url: "ajax.html?abortDocs="+$(this).attr('id'),
				cache: false,
				success: function(html){
						
				}
			});
		});
	}

	
	
	
	//--------------------PHOTO UPLOAD START----------------------------------------------
	var filesIMG;
	var iFiles = 0;
	var imageUploadedArrIMG = new Array();
	var htmlIMG = '';
	$('#uploaded_img').change(function(){
		if (this.files.length + iFiles >6 || iFiles == 6) {
			alert ('Для одного товара можно загрузить не более 6 фото. Вы уже загрузили '+iFiles+' и выбрали еще '+this.files.length);
			return;
		}
		var progressBar = $('#photoprogress');
		filesIMG = this.files;
		var data = new FormData();
		var docMaxItem = 0;
		$.each( filesIMG, function( key, value ){
			docMaxItem++;
			if (docMaxItem <= 8) {
				data.append( key, value );
			}
		});
		$('#submitAddItem').attr('disabled', "disabled");
		form = $('#addMewItemForm');
		$.ajax({
			url: './ajax.html?uploadimgs',
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false, 
			contentType: false,
			xhr: function(){
				var xhr = $.ajaxSettings.xhr(); 
				xhr.upload.addEventListener('progress', function(evt){ 
				  if(evt.lengthComputable) { 
					var percentComplete = Math.ceil(evt.loaded / evt.total * 100);
					progressBar.css({width: percentComplete + '%'});
				  }
				}, false);
				return xhr;
		    },
			success: function( respond, textStatus, jqXHR ){
				setTimeout(function(){
						progressBar.css({width: '0%'});
				},2000);
				if( typeof respond.error === 'undefined' ){
					var files_path = respond.files;
					$.each( files_path, function( key, val ){
						$('.ajax-respond_img').append( '<div class="imageUpl"  style="background-image:url('+val+');"><div  id="'+val+'" class="deleteImg"></div> </div>'); 
						imageUploadedArrIMG[iFiles] = val;
						form.prepend( '<input type="hidden"   class="inphidimg" id="'+val+'" name="fileImgHandler'+iFiles+'" value="'+val+'" />' );
						iFiles++;
						addEvenClickDelimg();
						
					});
				}
				else{
					console.log('ОШИБКИ ОТВЕТА сервера: ' + respond.error );
				}				
				console.log(imageUploadedArrIMG+'!!imageUploadedArrIMG!' );
				if (localStorage.getItem("imageUploadedArrIMG")){
					$(".inphidimg").remove();
					var cleanArray = [];
					var j = 0;
					for (var i=0; i<(imageUploadedArrIMG.length); i++) {
						if(imageUploadedArrIMG[i] != null){
							cleanArray[j] = imageUploadedArrIMG[i];
							form.prepend( '<input type="hidden" class="inphidimg" id="'+cleanArray[j]+'" name="fileImgHandler'+j+'" value="'+cleanArray[j]+'" />' );
							j++;
						}
					} 
					console.log(cleanArray+'!!6777!' );	
					localStorage.setItem('imageUploadedArrIMG' , JSON.stringify(cleanArray));
				}else {
					localStorage.setItem('imageUploadedArrIMG' , JSON.stringify(imageUploadedArrIMG));
				}
				$('#submitAddItem').removeAttr("disabled");
			},
			error: function( jqXHR, textStatus, errorThrown ){
				console.log('ОШИБКИ AJAX запроса: ' + textStatus );
			}
		});
	});
	
	
	
	currentPage = (location.href.split('.html')[0].split('/lk/')[1]);
	if (currentPage == 'moi-tovary'){
		form = $('#addMewItemForm');
		if  (localStorage.imageUploadedArrIMG) {
			test = localStorage.imageUploadedArrIMG ? JSON.parse(localStorage.imageUploadedArrIMG) : [];
			imageUploadedArrIMG = test;
			iFiles = test.length;
			console.log(test[1]);
			console.log(test.length);
			var htmlIMG = '';
			for (var t = 0; t<=test.length-1; t++){
				form.prepend( '<input type="hidden"  class="inphidimg" id="'+test[t]+'"  name="fileImgHandler'+t+'" value="'+test[t]+'" />' );
				htmlIMG += '<div class="imageUpl" style="background-image:url('+test[t]+');"> <div  id="'+test[t]+'" class="deleteImg"> </div></div>'; 
			}
			$('.ajax-respond_img').html( htmlIMG );
		}
	}

	
	//--------------------PHOTO UPLOAD END----------------------------------------------
	//localStorage хранятся ссылки на фото  и доки. Что бы при обновлении страницы не выгружать заново
	//
	addEvenClickDelimg();
	function addEvenClickDelimg () {
		$('.imageUpl .deleteImg').on('click'  , function(){
			test = localStorage.imageUploadedArrIMG ? JSON.parse(localStorage.imageUploadedArrIMG) : [];
			idElem = $(this).attr('id');
			test.remove(idElem);
			imageUploadedArrIMG.remove(idElem);
			console.log(imageUploadedArrIMG);
			localStorage.removeItem('imageUploadedArrIMG');
			localStorage.setItem('imageUploadedArrIMG' ,JSON.stringify(test));
			$(this).parent().remove();
			$(".inphidimg").remove();
			for (var t = 0; t<=imageUploadedArrIMG.length-1; t++){
			form.prepend( '<input type="hidden"  class="inphidimg" id="'+imageUploadedArrIMG[t]+'"  name="fileImgHandler'+t+'" value="'+imageUploadedArrIMG[t]+'" />' );
			}
			$.ajax({
				url: "ajax.html?abortDocs="+$(this).attr('id'),
				cache: false,
				success: function(html){	
				}
			});
		});
	}
	
	
	
	
	
	//--------------------PHOTO EDIT START----------------------------------------------
	var filesIMG;
	var iFiles = 0;
	var imageUploadedArrIMGedit = new Array();
	var htmlIMG = '';
	$('#uploaded_imgEdit').change(function(){
		if (this.files.length + iFiles >6 || iFiles == 6) {
			alert ('Для одного товара можно загрузить не более 6 фото. Вы уже загрузили '+iFiles+' и выбрали еще '+this.files.length);
			return;
		}
		var progressBar = $('#photoprogress');
		filesIMG = this.files;
		var data = new FormData();
		var docMaxItem = 0;
		$.each( filesIMG, function( key, value ){
			docMaxItem++;
			if (docMaxItem <= 8) {
				data.append( key, value );
			}
		});
		$('#submitAddItem').attr('disabled', "disabled");
		form = $('#addMewItemForm');
		$.ajax({
			url: './ajax.html?uploadimgs',
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false, 
			contentType: false,
			xhr: function(){
				var xhr = $.ajaxSettings.xhr(); 
				xhr.upload.addEventListener('progress', function(evt){ 
				  if(evt.lengthComputable) { 
					var percentComplete = Math.ceil(evt.loaded / evt.total * 100);
					progressBar.css({width: percentComplete + '%'});
				  }
				}, false);
				return xhr;
		    },
			success: function( respond, textStatus, jqXHR ){
				setTimeout(function(){
						progressBar.css({width: '0%'});
				},2000);
				if( typeof respond.error === 'undefined' ){
					var files_path = respond.files;
					$.each( files_path, function( key, val ){
						$('.ajax-respond_img').append( '<div class="imageUpl"  style="background-image:url('+val+');"><div  id="'+val+'" class="deleteImg"></div> </div>'); 
						imageUploadedArrIMGedit[iFiles] = val;
						form.prepend( '<input type="hidden"   class="inphidimg" id="'+val+'" name="fileImgHandler'+iFiles+'" value="'+val+'" />' );
						iFiles++;
						addEvenClickDelimg();
						
					});
				}
				else{
					console.log('ОШИБКИ ОТВЕТА сервера: ' + respond.error );
				}				
				console.log(imageUploadedArrIMGedit+'!!imageUploadedArrIMGedit!' );
				if (localStorage.getItem("imageUploadedArrIMGedit")){
					$(".inphidimg").remove();
					var cleanArray = [];
					var j = 0;
					for (var i=0; i<(imageUploadedArrIMGedit.length); i++) {
						if(imageUploadedArrIMGedit[i] != null){
							cleanArray[j] = imageUploadedArrIMGedit[i];
							form.prepend( '<input type="hidden" class="inphidimg" id="'+cleanArray[j]+'" name="fileImgHandler'+j+'" value="'+cleanArray[j]+'" />' );
							j++;
						}
					} 
					console.log(cleanArray+'!!6777!' );	
					localStorage.setItem('imageUploadedArrIMGedit' , JSON.stringify(cleanArray));
				}else {
					localStorage.setItem('imageUploadedArrIMGedit' , JSON.stringify(imageUploadedArrIMGedit));
				}
				$('#submitAddItem').removeAttr("disabled");
			},
			error: function( jqXHR, textStatus, errorThrown ){
				console.log('ОШИБКИ AJAX запроса: ' + textStatus );
			}
		});
	});
	form = $('#addMewItemForm');
	currentPage = (location.href.split('.html')[0].split('/lk/')[1]);
	if (currentPage == 'itemedit'){
		form = $('#addMewItemForm');
		if  (localStorage.imageUploadedArrIMGedit) {
			test = localStorage.imageUploadedArrIMGedit ? JSON.parse(localStorage.imageUploadedArrIMGedit) : [];
			imageUploadedArrIMGedit = test;
			iFiles = test.length;
			console.log(test[1]);
			console.log(test.length);
			var htmlIMG = '';
			for (var t = 0; t<=test.length-1; t++){
				form.prepend( '<input type="hidden"  class="inphidimg" id="'+test[t]+'"  name="fileImgHandler'+t+'" value="'+test[t]+'" />' );
				htmlIMG += '<div class="imageUpl" style="background-image:url('+test[t]+');"> <div  id="'+test[t]+'" class="deleteImg"> </div></div>'; 
			}
			$('.ajax-respond_img').html( htmlIMG );
		}
	}

	
	//--------------------PHOTO EDIT END----------------------------------------------
	//localStorage хранятся ссылки на фото  и доки. Что бы при обновлении страницы не выгружать заново
	//
	addEvenClickDelimg();
	function addEvenClickDelimg () {
		$('.imageUpl .deleteImg').on('click'  , function(){
			test = localStorage.imageUploadedArrIMGedit ? JSON.parse(localStorage.imageUploadedArrIMGedit) : [];
			idElem = $(this).attr('id');
			test.remove(idElem);
			imageUploadedArrIMGedit.remove(idElem);
			console.log(imageUploadedArrIMGedit);
			localStorage.removeItem('imageUploadedArrIMGedit');
			localStorage.setItem('imageUploadedArrIMGedit' ,JSON.stringify(test));
			$(this).parent().remove();
			$(".inphidimg").remove();
			for (var t = 0; t<=imageUploadedArrIMGedit.length-1; t++){
			form.prepend( '<input type="hidden"  class="inphidimg" id="'+imageUploadedArrIMGedit[t]+'"  name="fileImgHandler'+t+'" value="'+imageUploadedArrIMGedit[t]+'" />' );
			}
			$.ajax({
				url: "ajax.html?abortDocs="+$(this).attr('id'),
				cache: false,
				success: function(html){	
				}
			});
		});
	}
	
	
	
	if (form){
		form.submit(function(){
			localStorage.clear();
		});
	}
	
	
	
	
	//гарантия
	$("#guarant1").click(function(){
		$(".labfordateGuarant").css({display:'block'});	
		$("#dateGuarant").attr("required","required");
	});
	
	$("#guarant2").click(function(){
		$(".labfordateGuarant").css({display:'none'});
		$("#dateGuarant").removeAttr("required");
		$("#dateGuarant").val('');

	});
	
	
	
	//----
	
	//категория товара (из списка или своя)	
	$("#catType1").click(function(){
		$("#save_item_catOne").css({display:''});
		$("#save_item_catTwo").css({display:''});
		$("#save_item_catOne_alter").removeAttr("required");
		$("#save_item_catTwo_alter").removeAttr("required");
		$("#save_item_catOne_alter").css({display:'none'});
		$("#save_item_catTwo_alter").css({display:'none'});
		$("#save_item_catTwo").attr("required","required");
		$("#save_item_catTwo").attr("required","required");
		$("#save_item_catOne_alter").val('');
		$("#save_item_catTwo_alter").val('');
		
	});
	
	$("#catType2").click(function(){
		$("#save_item_catOne").css({display:'none'});
		$("#save_item_catTwo").css({display:'none'});
		$("#save_item_catOne_alter").css({display:''});
		$("#save_item_catTwo_alter").css({display:''});
		$("#save_item_catOne_alter").attr("required","required");
		$("#save_item_catTwo_alter").attr("required","required");
		$("#save_item_catTwo").removeAttr("required");
		$("#save_item_catTwo").removeAttr("required");

	});
	
	//---
	
	
	//поле скидка при добавлении товара
	$("#checkDiscount").click(function(){
		if ($("#checkDiscount").prop("checked")){
			$('.labforpercentDiscount').css({display:''});
			$("#percentDiscount").attr("required","required");
		}else {
			$('.labforpercentDiscount').css({display:'none'});
			$("#percentDiscount").removeAttr("required");
			$("#percentDiscount").val('');
		}
	});
	//--
	
	//Подроьности товара на сотерации (раскрытие)
	$(".lkItem .more").click(function(){
        elementExpand = $(this).parent();
        longer = $(this).data("longer");
        if (elementExpand.data('expanded') == true){
            elementExpand.data('expanded',false).attr('data-expanded',false);
            elementExpand.css({height:'50px'});
            elementExpand.find('.seeMoreInfo').css({height:'0px'});
            $(this).css({transform:'rotate(0deg)'});
        }else {
            
            $(".lkItem").css({height:'50px'}); 
            $(".seeMoreInfo").css({height:'0'});
            $(".lkItem").data('expanded',false).attr('data-expanded',false);
            $(".lkItem .more").css({transform:'rotate(0deg)'});
            //$(".seeMoreInfo").css({overflow:'hidden'});
            elementExpand.data('expanded',true).attr('data-expanded',true);
            if (longer == true) {
                elementExpand.css({height:'435px'});
                elementExpand.find('.seeMoreInfo').css({height:'385px'});
                
            }else {
                elementExpand.css({height:'300px'});
                elementExpand.find('.seeMoreInfo').css({height:'250px'});
            }
            
//            setTimeout(function(){
//                elementExpand.find('.seeMoreInfo').css({overflow:'visible'});
//            },1000);
            $(this).css({transform:'rotate(180deg)'});
        }	
	});
	
	
    $(".blueDiv").click(function(){
        
        popup = $(this).find(".itemListPopup");
        $(".itemListPopup").css({display:"none"});
		popup.css({display:'block'});
        
        

        
//        if (popup.css("display")){
//			$('.labforpercentDiscount').css({display:''});
//			$("#percentDiscount").attr("required","required");
//		}
        
	});
	//--
	
    
    $(document).mouseup(function (e){ // событие клика по веб-документу
		var div = $(".itemListPopup"); // тут указываем ID элемента
        var div2 = $(".requestPriceForm"); // тут указываем ID элемента
		if (!div.is(e.target) // если клик был не по нашему блоку
		    && div.has(e.target).length === 0) { // и не по его дочерним элементам
			div.hide(); // скрываем его
		}
        
        if (!div2.is(e.target) // если клик был не по нашему блоку
		    && div2.has(e.target).length === 0) { // и не по его дочерним элементам
			div2.hide(); // скрываем его
		}
        
	});
    
	
	$(".subimg .previevimg").click(function(){
		link = $(this).css('background-image');
		console.log(link);
		$(".mainimg .previevBimg").css({backgroundImage:link});
	});
	
	/*
	$(".checkboxs input[name=checkall]").click(function(){

		if ($(this).attr("checked") == 'checked') {
			$(this).removeAttr("checked");
			$(".checkboxs input[type=checkbox]").removeAttr("checked");
		} else {
			$(this).attr("checked" , "checked");
			$(".checkboxs input[type=checkbox]").attr("checked" , "checked");
		}


	});
	*/
	
	
	
	//=============================TAB1 checkBOx START ================
	$(".checkboxs input[name=checkall]").change(function () {
		$(".checkboxs input[type=checkbox]").prop("checked", $(this).is(":checked"));
		if ( $(this).is(":checked") ) {
			$(".buttonsubmit2[name=DeleteCheckedItemNoEnabled]").css({backgroundColor:'#e02b2b',color:'#fff'});
		}else {
			$(".buttonsubmit2[name=DeleteCheckedItemNoEnabled]").css({backgroundColor:'#ECECEC',color:'#C7C7C7'});
		}
	});
	
	$(".checkboxs input[type=checkbox]").change(function(){
		countCheckBox = 0;
		$(".checkboxs input[type=checkbox]:checked").each(function(){
			countCheckBox++;
		});
		if (countCheckBox == 0) {
			$(".buttonsubmit2[name=DeleteCheckedItemNoEnabled]").css({backgroundColor:'#ECECEC',color:'#C7C7C7'});
		}else {
			$(".buttonsubmit2[name=DeleteCheckedItemNoEnabled]").css({backgroundColor:'#e02b2b',color:'#fff'});
		}
		
	});
	
	$('form[name=noEnabledItems]').submit(function(e){
		
		countCheckBox = 0;
		$(".checkboxs input[type=checkbox]:checked").each(function(){
			countCheckBox++;
		});
		if (countCheckBox == 0) {
			e.preventDefault();
		}
		
	});
	//=============================TAB1 checkBOx END ================
	
	
	//=============================TAB2 checkBOx START ================
	$(".checkboxsENA input[name=checkall]").change(function () {
		$(".checkboxsENA input[type=checkbox]").prop("checked", $(this).is(":checked"));
		if ( $(this).is(":checked") ) {
			$(".buttonsubmit2[name=DeleteCheckedItemEnabled]").css({backgroundColor:'#e02b2b',color:'#fff'});
		}else {
			$(".buttonsubmit2[name=DeleteCheckedItemEnabled]").css({backgroundColor:'#ECECEC',color:'#C7C7C7'});
		}
	});
	
	$(".checkboxsENA input[type=checkbox]").change(function(){
		countCheckBox = 0;
		$(".checkboxsENA input[type=checkbox]:checked").each(function(){
			countCheckBox++;
		});
		if (countCheckBox == 0) {
			$(".buttonsubmit2[name=DeleteCheckedItemEnabled]").css({backgroundColor:'#ECECEC',color:'#C7C7C7'});
		}else {
			$(".buttonsubmit2[name=DeleteCheckedItemEnabled]").css({backgroundColor:'#e02b2b',color:'#fff'});
		}
		
	});
	
	$('form[name=EnabledItems]').submit(function(e){
		
		countCheckBox = 0;
		$(".checkboxsENA input[type=checkbox]:checked").each(function(){
			countCheckBox++;
		});
		if (countCheckBox == 0) {
			e.preventDefault();
		}
		
	});
	//=============================TAB2 checkBOx ENS ================
	
	
	
	$('input[name=excellFile]').change(function(){
		//console.log( this.files[0]['name']);
		$('.inputtupefile span').text(this.files[0]['name']);
		
	});
	
	
});



$(window).load(function(){
	
	
	//blockMsgContent = $(".msgContent");
	//blockMsgContent.scrollTop('999999999') 
	//blockMsgContent.animate({scrollTop:9999}, '2000', 'swing', function() { 
	   //alert("Finished animating");
	//});
	

	
	
	//------------------------------------------------------------------
	$( '.catalog_table .jqtric_name' ).each(function(){
		if( $( '.descr', this ).height() < 70 ) $( '.fulldescription', this ).remove();
		else $( '.descr', this ).append( '<div class="shadow">&nbsp;</div>' );
	});
	
	
	
	//------------------------LIVE SEARCH CITY -START---------------------------//
	
	// читаем ввод с клавиатуры
    $("#search_box1").keyup(function(I){
        // определяем какие действия нужно делать при нажатии на клавиатуру
        switch(I.keyCode) {
            // игнорируем нажатия на эти клавишы
            case 13:  // enter
            case 27:  // escape
            case 38:  // стрелка вверх
            case 40:  // стрелка вниз
            break;
 
            default:
                // производим поиск только при вводе более 2х символов
                if($(this).val().length>2){
 
                    input_initial_value = $(this).val();
                    // производим AJAX запрос к /ajax/ajax.php, передаем ему GET query, в который мы помещаем наш запрос
                    $.get("../ajax.html", { "query":$(this).val() },function(data){
                        //php скрипт возвращает нам строку, ее надо распарсить в массив.
                        // возвращаемые данные: ['test','test 1','test 2','test 3']
                        var list = eval("("+data+")");
                        suggest_count = list.length;
                        if(suggest_count > 0){
                            // перед показом слоя подсказки, его обнуляем
                            $("#search_advice_wrapper1").html("").show();
                            for(var i in list){
                                if(list[i] != ''){
                                    // добавляем слою позиции
                                    $('#search_advice_wrapper1').append('<div class="advice_variant">'+list[i].split('||')[0]+'<span>'+list[i].split('||')[1]+'</span></div>');
                                }
                            }
                        }
                    }, 'html');
                }
            break;
        }
    });
	
		// читаем ввод с клавиатуры
    $("#search_box2").keyup(function(I){
        // определяем какие действия нужно делать при нажатии на клавиатуру
        switch(I.keyCode) {
            // игнорируем нажатия на эти клавишы
            case 13:  // enter
            case 27:  // escape
            case 38:  // стрелка вверх
            case 40:  // стрелка вниз
            break;
 
            default:
                // производим поиск только при вводе более 2х символов
                if($(this).val().length>2){
 
                    input_initial_value = $(this).val();
                    // производим AJAX запрос к /ajax/ajax.php, передаем ему GET query, в который мы помещаем наш запрос
                    $.get("../ajax.html", { "query":$(this).val() },function(data){
                        //php скрипт возвращает нам строку, ее надо распарсить в массив.
                        // возвращаемые данные: ['test','test 1','test 2','test 3']
                        var list = eval("("+data+")");
                        suggest_count = list.length;
                        if(suggest_count > 0){
                            // перед показом слоя подсказки, его обнуляем
                            $("#search_advice_wrapper2").html("").show();
                            for(var i in list){
                                if(list[i] != ''){
                                    // добавляем слою позиции
                                    $('#search_advice_wrapper2').append('<div class="advice_variant">'+list[i].split('||')[0]+'<span>'+list[i].split('||')[1]+'</span></div>');
                                }
                            }
                        }
                    }, 'html');
                }
            break;
        }
    });
 
 
 	// читаем ввод с клавиатуры
    $("#search_box3").keyup(function(I){
        // определяем какие действия нужно делать при нажатии на клавиатуру
        switch(I.keyCode) {
            // игнорируем нажатия на эти клавишы
            case 13:  // enter
            case 27:  // escape
            case 38:  // стрелка вверх
            case 40:  // стрелка вниз
            break;
 
            default:
                // производим поиск только при вводе более 2х символов
                if($(this).val().length>2){
 
                    input_initial_value = $(this).val();
                    // производим AJAX запрос к /ajax/ajax.php, передаем ему GET query, в который мы помещаем наш запрос
                    $.get("../ajax.html", { "query":$(this).val() },function(data){
                        //php скрипт возвращает нам строку, ее надо распарсить в массив.
                        // возвращаемые данные: ['test','test 1','test 2','test 3']
                        var list = eval("("+data+")");
                        suggest_count = list.length;
                        if(suggest_count > 0){
                            // перед показом слоя подсказки, его обнуляем
                            $("#search_advice_wrapper3").html("").show();
                            for(var i in list){
                                if(list[i] != ''){
                                    // добавляем слою позиции
                                    $('#search_advice_wrapper3').append('<div class="advice_variant">'+list[i].split('||')[0]+'<span>'+list[i].split('||')[1]+'</span></div>');
                                }
                            }
                        }
                    }, 'html');
                }
            break;
        }
    });
 

	
	    //считываем нажатие клавишь, уже после вывода подсказки
    $("#search_box1").keydown(function(I){
        switch(I.keyCode) {
            // по нажатию клавишь прячем подсказку
            case 13: // enter
            case 27: // escape
                $('#search_advice_wrapper1').hide();
                return false;
            break;
            // делаем переход по подсказке стрелочками клавиатуры
            case 38: // стрелка вверх
            case 40: // стрелка вниз
                I.preventDefault();
                if(suggest_count){
                    //делаем выделение пунктов в слое, переход по стрелочкам
                    key_activate( I.keyCode-39 );
                }
            break;
        }
    });
 
 
     //считываем нажатие клавишь, уже после вывода подсказки
    $("#search_box2").keydown(function(I){
        switch(I.keyCode) {
            // по нажатию клавишь прячем подсказку
            case 13: // enter
            case 27: // escape
                $('#search_advice_wrapper2').hide();
                return false;
            break;
            // делаем переход по подсказке стрелочками клавиатуры
            case 38: // стрелка вверх
            case 40: // стрелка вниз
                I.preventDefault();
                if(suggest_count){
                    //делаем выделение пунктов в слое, переход по стрелочкам
                    key_activate( I.keyCode-39 );
                }
            break;
        }
    });
 
     //считываем нажатие клавишь, уже после вывода подсказки
    $("#search_box3").keydown(function(I){
        switch(I.keyCode) {
            // по нажатию клавишь прячем подсказку
            case 13: // enter
            case 27: // escape
                $('#search_advice_wrapper3').hide();
                return false;
            break;
            // делаем переход по подсказке стрелочками клавиатуры
            case 38: // стрелка вверх
            case 40: // стрелка вниз
                I.preventDefault();
                if(suggest_count){
                    //делаем выделение пунктов в слое, переход по стрелочкам
                    key_activate( I.keyCode-39 );
                }
            break;
        }
    });
 
 
    // делаем обработку клика по подсказке
    $('#search_advice_wrapper1').on('click', '.advice_variant' ,function(){
        // ставим текст в input поиска
		//alert ($(this).text());
		$(this).find('span').empty();
        $('#search_box1').val($(this).text().split('<span>')[0]);
        // прячем слой подсказки
        $('#search_advice_wrapper1').fadeOut(350).html('');
    });
	
	    // делаем обработку клика по подсказке
    $('#search_advice_wrapper2').on('click', '.advice_variant' ,function(){
        // ставим текст в input поиска
		//alert ($(this).text());
		$(this).find('span').empty();
        $('#search_box2').val($(this).text().split('<span>')[0]);
        // прячем слой подсказки
        $('#search_advice_wrapper2').fadeOut(350).html('');
    });
	
	    // делаем обработку клика по подсказке
    $('#search_advice_wrapper3').on('click', '.advice_variant' ,function(){
        // ставим текст в input поиска
		//alert ($(this).text());
		$(this).find('span').empty();
        $('#search_box3').val($(this).text().split('<span>')[0]);
        // прячем слой подсказки
        $('#search_advice_wrapper3').fadeOut(350).html('');
    });
 
    // если кликаем в любом месте сайта, нужно спрятать подсказку
    $('html').click(function(){
        $('#search_advice_wrapper1').hide();
		$('#search_advice_wrapper2').hide();
		$('#search_advice_wrapper3').hide();
    });
	
	
	
	
    // если кликаем на поле input и есть пункты подсказки, то показываем скрытый слой
    $('#search_box1').click(function(event){
        //alert(suggest_count);
        if(suggest_count)
            $('#search_advice_wrapper2').show();
        event.stopPropagation();
    });
	
	
	    $('#search_box1').click(function(event){
        //alert(suggest_count);
        if(suggest_count)
            $('#search_advice_wrapper2').show();
        event.stopPropagation();
    });
	
	    $('#search_box1').click(function(event){
        //alert(suggest_count);
        if(suggest_count)
            $('#search_advice_wrapper2').show();
        event.stopPropagation();
    });
	
	//------------------------LIVE SEARCH CITY -END---------------------------//
	//------------------------------------------------------------------
});



function CTM_black()
{
	if( ! $( '.catalog_top_menu' ).hasClass( 'open' ) ) return;
	$( '.catalog_top_menu .ctm_black' ).css({
		width: $(window).width(),
		height: $(window).height(),
		left: 0,
		top: $(window).scrollTop()
	});
}


function key_activate(n){
    $('#search_advice_wrapper div').eq(suggest_selected-1).removeClass('active');
 
    if(n == 1 && suggest_selected < suggest_count){
        suggest_selected++;
    }else if(n == -1 && suggest_selected > 0){
        suggest_selected--;
    }
 
    if( suggest_selected > 0){
        $('#search_advice_wrapper div').eq(suggest_selected-1).addClass('active');
        $("#search_box").val( $('#search_advice_wrapper div').eq(suggest_selected-1).text() );
    } else {
        $("#search_box").val( input_initial_value );
    }
}



function addwarehouse(){
		currentEnableElem = 2;
		if ($('.seeWarehouse'+currentEnableElem).css('display')  == 'none'){
			$('.seeWarehouse'+currentEnableElem).css({display:'block'});
		}else {
			currentEnableElem++;
			$('.seeWarehouse'+currentEnableElem).css({display:'block'});
		}

	}
	
function deletewarehouse(thisElem){
		$('.seeWarehouse'+thisElem).css({display:'none'});
		$('.seeWarehouse'+thisElem).find('input').val('');
		
	}
	

function addContactFace(){
		currentEnableElem = 2;
		if ($('.seeContacts'+currentEnableElem).css('display')  == 'none'){
			$('.seeContacts'+currentEnableElem).css({display:'block'});
		}else if ($('.seeContacts'+(currentEnableElem+1)).css('display')  == 'none'){
			$('.seeContacts'+(currentEnableElem+1)).css({display:'block'});
		}else if ($('.seeContacts'+(currentEnableElem+2)).css('display')  == 'none'){
			$('.seeContacts'+(currentEnableElem+2)).css({display:'block'});
		}else if ($('.seeContacts'+(currentEnableElem+3)).css('display')  == 'none'){
			$('.seeContacts'+(currentEnableElem+3)).css({display:'block'});
		}else {
			$('.seeWarehouse'+(currentEnableElem+4)).css({display:'block'});
		}

	}

	
function deleteContactFace(thisElem){
		elem = $('.seeContacts'+thisElem);
		console.log(elem);
		elem.css({display:'none'});
		elem.find('input').val('');
		
	}



// наверно уже не нужна. ПРОВЕРИТЬ (при выборе файлов при добавлении товара)
function file_selected() {
  try { 
    var file = document.getElementById('uploaded_file').files[0]; 
    if (file) { 
      var file_size = 0; 
      if (file.size > 1024 * 1024) { 
        file_size = (Math.round(file.size*100/(1024*1024))/100).toString() + 'MB'; 
      } 
      else { 
        file_size = (Math.round(file.size*100/1024)/100).toString() + 'KB'; 
      } 
     // document.getElementById('file_name').innerHTML = 'Name: ' + file.name+' ('+file_size+')'; 
      //document.getElementById('file_size').innerHTML = 'Size: ' + file_size; 
    } 
  } 
  catch(e) { 
    var file = document.getElementById('uploaded_file').value; 
    file = file.replace(/\\/g, "/").split('/').pop(); 
    document.getElementById('file_name').innerHTML = 'Name: ' + file; 
  } 
}
	
	
	
	


