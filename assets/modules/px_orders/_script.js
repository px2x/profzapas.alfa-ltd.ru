

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
	
	
	
	//Одобрить создание раздела
	$(".addnewpath").click(function(){
		var iditem = $(this).data('id');
		thisbutton = $(this);
		$.ajax({
			url: "../adajax.html?enableiditem="+iditem,
			cache: false,
			success: function(json){
				response = $.parseJSON( json );			
				catBlock = $('#category_'+response.idItem);
				catBlock.empty();
				catBlock.append('<span style="color:#0F0;">Раздел создан</span><br /> /'+response.cat+' / '+response.subcat);
				thisbutton.remove();
			}
		});
	});
	
	
	
	//удаление одного 
	$('.deleteOneItem').click(function(){
		curDelId = $(this).data('id');
		$(".checkboxs input[value="+curDelId+"]").prop("checked", true);
		$("button[name=DeleteCheckedItem]").click();
	});
	
	
	//Одобрение одного 
	$('.enableOneItem').click(function(){
		curEnId = $(this).data('id');
		$(".checkboxs input[value="+curEnId+"]").prop("checked", true);
		$("button[name=EnableCheckedItem]").click();
	});
	
	
	
	//dcgksdf.obt jryf
	$(".dark_bg").click(function(){
		$(".modal_tree").css({display:'none'});
		$(".modal_product").css({display:'none'});
		$(".seeAnalogs").css({display:'none'});
		$(this).css({display:'none'});
		
	});
	
	$(".modal_tree").click(function(e){
		e.stopPropagation();
	});
	
	$(".modal_product").click(function(e){
		e.stopPropagation();
	});
	
	
	
	$(".seeAnalogs").click(function(e){
		e.stopPropagation();
	});
	
	
	
	$(".setAnalogItem").click(function(e){
		$(".modal_tree").css({display:'block'});
		$(".modal_product").css({display:'block'});
		$(".dark_bg").css({display:'block'});
		currentAnalog = $(this).data('id');
		
	});
	
	
	
	
	
	
	$(".seeAnalogItem").click(function(e){
		seeAnalogsBlock = $(".listAnalog");
		$(".seeAnalogs").css({display:'block'});
		$(".dark_bg").css({display:'block'}); 
		currentSeeAnalogs = $(this).data('id');
		
		$.ajax({
			url: "../adajax.html?getItemAnalogsList="+currentSeeAnalogs,
			cache: false,
			success: function(json){
				if (json != 'noFinded'){ 
					response = $.parseJSON( json );			
					seeAnalogsBlock.empty();
					console.log(json); 
					seeAnalogsBlock.append('<div class=itemSelectAnalog><div class="analog_checkBox_tit"></div><div class="analog_code_tit">Код товара</div><div class="analog_title_tit">Наименование</div><div class="analog_manuf_tit">Производитель</div><div  class="analog_price_tit">Цена</div><div  class="analog_cur_tit">Валюта</div><div  class="analog_discount_tit">Скидка</div></div><div class="clr">&nbsp;</div>');
					for (var i = 0; i <response.length; i++){
						console.log(response[i]);
						tmp =response[i];
						seeAnalogsBlock.append('<div class=itemSelectAnalog><div class="analog_checkBox_body"><input type="checkbox" name="setThisAnalog['+tmp.aid+']" value="'+tmp.aid+'"></div><div class="analog_code_body">'+tmp.code+'</div><div class="analog_title_body">'+tmp.title+'</div><div class="analog_manuf_body">'+tmp.manufacturer+'</div><div  class="analog_price_body">'+tmp.price+'</div><div  class="analog_cur_body">'+tmp.currency+'</div><div class="analog_discount_body">-'+tmp.discount+'%</div></div><div class="clr">&nbsp;</div>');
					}
				} else {
					seeAnalogsBlock.empty();
					seeAnalogsBlock.append('<div>В данной категории товаров не найдено</div>');					
				}			
			}
		});
		
	});
	
	
	
	
	
	//подгрузить список товаров категории
	$(".modal_tree ul li ul li").click(function(e){
		catBlock = $('.modal_product .list');
		id = $(this).attr('id');
		$.ajax({
			url: "../adajax.html?getItemList="+id+"&currentAnalog="+currentAnalog,
			cache: false,
			success: function(json){
				if (json != 'noFinded'){
					response = $.parseJSON( json );			
					
					catBlock.empty();
					console.log(response);
					
					catBlock.append('<div class=itemSelectAnalog><div class="analog_checkBox_tit"></div><div class="analog_code_tit">Код товара</div><div class="analog_title_tit">Наименование</div><div class="analog_manuf_tit">Производитель</div><div  class="analog_price_tit">Цена</div><div  class="analog_cur_tit">Валюта</div><div  class="analog_discount_tit">Скидка</div></div><div class="clr">&nbsp;</div>');
					
					
					for (var i = 0; i <response.length; i++){
						console.log(response[i]);
						tmp =response[i];
						
						catBlock.append('<div class=itemSelectAnalog><div class="analog_checkBox_body"><input type="checkbox" name="setThisAnalog['+tmp.id+']" value="'+tmp.id+'"></div><div class="analog_code_body">'+tmp.code+'</div><div class="analog_title_body">'+tmp.title+'</div><div class="analog_manuf_body">'+tmp.manufacturer+'</div><div  class="analog_price_body">'+tmp.price+'</div><div  class="analog_cur_body">'+tmp.currency+'</div><div class="analog_discount_body">-'+tmp.discount+'%</div></div><div class="clr">&nbsp;</div>');
					}
					
				} else {
					catBlock.empty();
					catBlock.append('<div>В данной категории товаров не найдено</div>');					
				}			
			}
		});
	
	});
	
	
	
	//установить выбранные аналоги
	$('.itemSelectAnalogButton').on('click' , function(){
		var mass = [];
		var t =0;
		$.each($('.itemSelectAnalog input:checkbox:checked'), function(){
			mass[t++] = $(this).val();
		});
		dataJSON = JSON.stringify(mass);
		console.log(dataJSON);
		$.post("../adajax.html?setAnalogList="+currentAnalog, {sdata: mass} , function(data){
			alert("Data Loaded: " + data);
		});
	});
	
	
	//открепить выбранные аналоги
	$('.itemDisAnalogButton').on('click' , function(){
		
		var mass = [];
		var t =0;
		$.each($('.itemSelectAnalog input:checkbox:checked'), function(){
			mass[t++] = $(this).val();
		});
		dataJSON = JSON.stringify(mass);
		console.log(dataJSON);
		$.post("../adajax.html?disableAnalogList="+currentAnalog, {sdata: mass} , function(data){
			alert("Data Disabled: " + data);
		});
		
	});
	
	
	
	
	
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
			$(".buttonsubmit2").css({backgroundColor:'#ECECEC',color:'#C7C7C7'});
		}else {
			$(".buttonsubmit2").css({backgroundColor:'#e02b2b',color:'#fff'});
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
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//===========================редактирование товара START ====================================
	
		//для редакт картинок
	localStorage.clear();
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
	
	var oneCatLevel= $( '#save_item_catOne' );
	
	//грузит субкатегории при выборе категории
	oneCatLevel.change(function(){
		var link = $('#save_item_catOne option:selected:last').val();
		if (link == '') return false;
		$( '#save_item_catTwo' ).empty();
		$.ajax({
			url: "../ajax.html?getTwoLevelCat="+link,
			cache: false,
			success: function(html){
				$( '#save_item_catTwo' ).append(html);
			}
		});
		
	});
	

	
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
			url: '../ajax.html?uploadfiles',
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
						$('.ajax-respond').append('<div class="documentUpl"  style="background-image:url(../template/images/file_'+val.split('.')[2]+'.png);"><div id="'+val+'" class="deleteDoc"></div><span>'+respond.files.name[ff]+'</span></div>'); 
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
	//if (currentPage == 'itemedit'){
	if (true){
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
				htmlDOC += '<div class="documentUpl" style="background-image:url(../template/images/file_'+test[t].split('.')[2]+'.png);"><div  id="'+test[t]+'" class="deleteDoc"></div><span>'+test2[t] +'</span></div>'; 
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
				url: "../ajax.html?abortDocs="+$(this).attr('id'),
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
			url: '../ajax.html?uploadimgs',
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
						$('.ajax-respond_img').append( '<div class="imageUpl"  style="background-image:url(.'+val+');"><div  id="'+val+'" class="deleteImg"></div> </div>'); 
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
	//if (currentPage == 'itemedit'){
	if (true){
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
				htmlIMG += '<div class="imageUpl" style="background-image:url(.'+test[t]+');"> <div  id="'+test[t]+'" class="deleteImg"> </div></div>'; 
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
				url: "../ajax.html?abortDocs="+$(this).attr('id'),
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
		$(".lkItem").css({height:'50px'});
		$(".seeMoreInfo").css({height:'0'});
		elementExpand = $(this).parent();
		elementExpand.css({height:'350px'});
		elementExpand.find('.seeMoreInfo').css({height:'300px'});
	});
	
	
	
	
	$(".subimg .previevimg").click(function(){
		link = $(this).css('background-image');
		console.log(link);
		$(".mainimg .previevBimg").css({backgroundImage:link});
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
	
	
	
	//===========================редактирование товара END ====================================
		
		
		
		
});




