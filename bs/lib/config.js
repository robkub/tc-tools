$(function(){
	$(".js-hidden").hide();
	$(".tabs input").hide();
	
//	$(".select div.option input[checked]").each(function() {
//		$(this).parent().addClass("selected");
//	});

	 addOptionClickAction();
	
	$("div.select input:checked").trigger('change');

	if(parseInt($("#ally-select").val()) != 0)
		$("#ally-select").trigger('change');
});

/**
 * 
 */
function addOptionClickAction(elems) {
	if(typeof elems == "undefined") {
		elems = $(".select .option input"); 
//		$(".select div.option label").click(function() {$(this).parent('div.option').trigger('click');});
	}
	elems.change(function() {optionSelect(this.parentNode)});
//	$(this).click(function() {$(this).parent('div.option').trigger('click');});
}

/**
 * 
 * @param id
 * @param clicked
 */
function showFields(id, clicked) {
	$("#"+id).siblings("div").slideUp(400);
	$("#"+id).slideDown(400);
	
	$(clicked).siblings("span").removeClass("selected");
	$(clicked).siblings("label").removeClass("selected");
	$(clicked).addClass("selected");
}

/**
 * 
 * @param elem
 */
function optionSelect(elem) {
	var val = $(elem).children("input").val();
	var name = $(elem).children("input").attr("name");
	var elems = $("input[name="+ name +"][value="+ val +"]").parent();
	
	elems.toggleClass("selected");
	var inputs = elems.find("input");
	if(elems.hasClass("selected") && elems.find("input:checked").length == 0)
		elems.find("input").attr("checked", "checked");
	else if(!elems.hasClass("selected") && elems.find("input:checked").length > 0)
		elems.find("input").removeAttr("checked");
}

/**
 * 
 * @param pics_prefix
 */
function imageUpload(pics_prefix) {
	//starting setting some animation when the ajax starts and completes
	$("#upload-loading").show();
	
	var allytag = ($("input#action-edit:checked").length > 0) ? $("#edit_allytag").val() : $("#allytag").val();
	var allyname = ($("input#action-edit:checked").length > 0) ? $("#edit_allyname").val() : $("#allyname").val();
	
	$.ajaxFileUpload(
		{
			url: DIR_PREFIX +'ajax.php?action=upload&tag='+ allytag +'&name='+ allyname, 
			secureuri:false,
			fileElementId: 'uploadBanner',
			dataType: 'json',
			success: function (data, status)
			{
				if(data.error != undefined)	{
					alert(data.error);
				}else if(data.success != undefined) {
					var newE = '<label for="banner-sel-'+ data.id +'" class="option selected">'
								+'<input type="checkbox" name="banner[]" value="'+ data.id +'" id="banner-sel-'+ data.id +'" checked="checked" />'
								+'<img src="'+ unescape(decodeURI(pics_prefix+data.path)) +'" alt="Banner'+ data.id +'" />'
							+'</label>';
		
					var elems = $("fieldset.banner div.select div.clearer").before(newE);

					addOptionClickAction($("fieldset.banner input[value="+ data.id +"]"));
					
					$("#banner-new-list").show();
					$("#banner-new-list").removeClass("hide");
					$("#upload-loading").hide();
				}
			},
			error: function (data, status, e)
			{
				$("#upload-loading").hide();
				alert(e);
			}
		}
		
	)
	
	return false;

} 

/**
 * 
 * @param neueRunde
 * @param neueRundeListe
 * @param loading
 */
function neueRunde(neueRunde, neueRundeListe, loading) {
	$("#"+loading).show();
		
	var value = $("#"+ neueRunde).val();
	$.post(DIR_PREFIX +'ajax.php?action=newRound', { name: value },
	  function(data, textStatus, jqXHR) {
	  	console.log(data);
			if(data.error != undefined)	{
				alert(data.error);
			} else if(data.success != undefined) {
				var elems = $("#"+neueRundeListe).prepend('<label for="runden-sel-'+ data.id +'" class="option selected">'
					+'<input type="checkbox" name="runden[]" value="'+ data.id +'" id="runden-sel-'+ data.id +'" checked="checked" />'
						+ data.name +'</label>');
			
				addOptionClickAction($("#"+neueRundeListe+" input[value="+ data.id +"]"));
				$("#"+neueRundeListe).show();
			}
			$("#"+loading).hide();
		}, "json");
	
}

/**
 * 
 * @param field
 */
function loadAllyData(field) {
	$("#ally-loading").show();
	
	var hideEditAllyBox = function() {
		$("#allianz-edit-box").removeClass("selected");
		$("#allianz-edit-box").slideUp();
		$("#banner-belong-button").hide();
	}
	var fillEditBox = function(data) {
		$("#edit_allytag").val(data.tag);
		$("#edit_allyname").val(data.name);

		$("div.select input:checked").trigger('change');
		$("#banner-belong label.option").remove();
		
		for(var i in data.urls) {
			var option = $("div#banner-all img[alt=Banner"+ i +"]").parents("label.option");
			var e = option.clone()
			$("#banner-belong div.clearer").before(e);
			option.children("input").trigger("change");
			addOptionClickAction(e.children("input"));
		}
		for(var i in data.runden) {
			var runde = data.runden[i];
			$("fieldset.runden label[for=runden-sel-"+ runde +"] input").trigger("change");
		}
		
		$("#allianz-edit-box").addClass("selected");
		$("#allianz-edit-box").slideDown();
		$("#banner-belong-button").show();
		$("#banner-belong-button").trigger("click");
		
		$("#action-edit").parent().trigger("click");
	}
	
	var value = parseInt(field.value);
	if(value == 0)
		hideEditAllyBox();
	else
		$.post(DIR_PREFIX +'ajax.php?action=getAlly', { id: value },
		  function(data) {
			if(data.error != undefined)	{
				hideEditAllyBox();
				alert(data.error);
			}else if(data.success == true) {
				fillEditBox(data);
			}
			$("#ally-loading").hide();
		}, "json");	
}
