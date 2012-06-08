//var httpurl = "http://form.eo.test/";
var httpurl = "http://form.enorange.cn/";
$(document).ready(function() {
	var obj = $('#detailform').attr('form-id');
	var orgcode = $('#detailform').attr('orgcode');
	var from = '';
	$.ajax({
		dataType: "jsonp",
		url : httpurl+orgcode+"/default/index/showform/id/"+obj,
		success : function(json)
		{
			from+= "<form id='fillform' action='"+httpurl+orgcode+"/default/feedback/reply/id/"+obj+"' method='post'><div class='element-current'><ul class='form-editor'>";
			$.each(json, function(k, data){
				from+= "<li draggable='true' class='solid drag-handle' id='form_element_"+k+"'>";
				from+= "<div class='element-elementType'>";
				if(data.elementType != 'button') {
					from+= data.label;
					if(data.required == 1){
						from+= "<font color='#f00'>(必填)</font>";
					}
					from+= "</div><div class='element'>";
				}
				if (data.elementType == 'text') {
					from+= "<input type='text' id='option' name='"+data.label+"'>";
				} else if(data.elementType == 'radio' || data.elementType == 'select') {
					$.each(data.option, function(j, val) {
						from+= "<input id='option"+k+"' type='radio' value='"+val+"' name='"+data.label+"'";
						if(j == 0){from+= "checked='checked'";}
						from+= "><label for='option_"+j+"'>"+val+"</label>";
					});
				} else if(data.elementType == 'textarea') {
					from+= "<textarea id='option' name='"+data.label+"' type='textarea'></textarea>";
				} else if(data.elementType == 'multi-checkbox') {
					$.each(data.option, function(j, val) {
						from+= "<input id='option_"+k+"' type='checkbox' value='"+j+"' name='"+data.label+"_"+j+"_"+val+"'><label for='option_"+j+"'>"+val+"</label>";
					});
				} else if(data.elementType == 'menu'){
					from+= "<select id='select' name='"+data.label+"'>";
					$.each(data.option, function(j, val) {
						from+= "<option value='"+val+"'>"+val+"</option>";
					});
					from+= "</select>";
				}
				if(data.elementType != 'button') {
					from+="</div><div class='element-desc'>"+data.desc+"</div></li>";
				} else {
					from+="<input type='"+data.type+"' name='button' id='button' value='"+data.label+"' /></li>";
				}
			});
			from+="</ul></div>";
			$('#detailform').html(from);			
		}
	});
});

