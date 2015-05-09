var contactMeEditFormShow = function editContactMeForm() {
	$('#contactMe').fadeOut('slow');
	$('.editContactMeButton').fadeOut('slow');
	$('.contactMeFormEdit').fadeIn('slow').delay(500);
}


var contactMeSendForm = function editContactMeFormSumbit() {
	var data = $('form.contactMeFormEdit').serialize();
	$.post($('.contactMeWidgetSubmit').attr('action_url'), data, function(data, textStatus, jqXHR) {
		if(textStatus == 'success') {
			var successHtml = '<div align=\"center\" style=\"padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #33ff33;\">'+ data.msg +'</div>';
			$('.contactMeMsg').fadeIn('slow').append(successHtml);
			$('.contactMeFormEdit').fadeOut('slow').delay(500);
			$('#contactMe').fadeIn('slow').delay(800);
			$('.editContactMeButton').fadeIn('slow').delay(800);
		}
	});
}