var httpurl = "http://form.eo.test/";
var from = '';
$(document).ready(function() {
	var obj = $('#detailform').attr('form-id');
//	var userid = $('#detailform').attr('user-id');
	$.ajax({
		dataType: "jsonp",
//		url : httpurl+"admin/index/selform/id/"+obj+"/userid/"+userid,
		url : httpurl+"admin/index/selform/id/"+obj,
		success : function(json)
		{
//			alert(json.state);
//			if(json.state == 1){
//				$.each(json, function(k, data){
//					//alert(k+':'+data);
//					from+= "<ul>";
//					if(k != 'userid' && k != 'formId' && k != 'state'){
//						from+= "<li>"+k+':'+data+"</li>";
//					}
//					from+= "</ul>";
//				});
//			} else {
				from+= "<form id='fillform' action='"+httpurl+"rest/feedback/"+obj+"' method='post'><div class='element-current'><ul class='form-editor'>";
				$.each(json, function(k, data){
					from+= "<li draggable='true' class='solid drag-handle' id='form_element_"+data._id+"'><div class='element-label'>"+data.label;
					if(data.required == 1){
						from+="<font color='#f00'>(必填)</font>";
					}
//					from+= "</div><div class='element'><input type='hidden' id='userid' name='userid' value='"+userid+"'>";
					from+= "</div><div class='element'>";
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
					}
					from+="</div><div class='element-desc'>"+data.desc+"</div></li>";
				});
				from+="<input type='submit' name='button' id='button' value='提交' /><input type='reset' name='button2' id='button2' value='重置' />";
				from+="</ul></div></form>";
//			}
			$('#detailform').html(from);
			
		}
	});
});