headers=[];var addElement="<div style='padding-bottom: 20px!important;'>Columns:&nbsp;&nbsp; ";
//$("#tbl_<?=CurrentPage()->TableName?>list thead tr th").each(function(){
$(".ew-table thead tr th").each(function(){
ThId=$( this ).data("name");CapText=$(this).text();if(CapText.trim()!=""){
if(jQuery.inArray( ThId, hidedCol )){CheckText='checked';}else{CheckText='';}
	addElement += "<input type='checkbox' id='" + ThId + "_toggle' onclick=$(\"*[data-name='"+ThId+"']\").toggle() checked> "+CapText+" &nbsp;&nbsp; ";
	headers.push(ThId);}
});
addElement=addElement+"</div>";
$("div.ew-message-dialog").after(addElement);
	jQuery.each(hidedCol, function (index, value) {
		$("*[data-name='" + value + "']").hide();
		$("#"+value+"_toggle").prop('checked', false);
});