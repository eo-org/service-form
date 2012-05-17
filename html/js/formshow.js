//var httpurl = "http://form.eo.test/";
var httpurl = "http://form.enorange.cn/";
var from = '';
$(document).ready(function() {
	var obj = $('#detailform').attr('form-id');
	var orgcode = $('#detailform').attr('orgcode');
	$.ajax({
		dataType: "jsonp",
		url : httpurl+orgcode+"/admin/index/showform/id/"+obj,
		success : function(json)
		{
			from+= "<form id='fillform' action='"+httpurl+orgcode+"/default/feedback/reply/id/"+obj+"' method='post'><div class='element-current'><ul class='form-editor'>";
			$.each(json, function(k, data){
				from+= "<li draggable='true' class='solid drag-handle' id='form_element'>";
				if(data.elementType != 'button') {
					from+= "<div class='element-label'>"+data.label;
					if(data.required == 1){
						from+="<font color='#f00'>(必填)</font>";
					}
					from+= "</div><div class='element'>";
				}
				if (data.elementType == 'text') {
					from+= "<input type='text' id='option' name='"+data.label+"'>";
				} else if(data.elementType == 'radio' || data.elementType == 'select') {
					$.each(data.option, function(j, val) {
						from+= "<input id='option"+k+"' type='radio' value='"+val+"' name='"+data.label+"'><label for='option_"+j+"'>"+val+"</label>";
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
//					if(data.type == 'submit'){
//						from+="<input type='button' name='button' id='submitbutton' value='"+data.label+"' /></li>";
//					} else {
						from+="<input type='"+data.type+"' name='button' id='button' value='"+data.label+"' /></li>";
//					}
				}
			});
			from+="</ul></div></form>";
			$('#detailform').html(from);
		}
	});
});