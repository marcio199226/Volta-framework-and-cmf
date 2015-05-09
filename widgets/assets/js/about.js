var aboutMeEditFormShow = function editAboutMeForm() {
	$('#aboutMe').fadeOut('slow');
	$('.editAboutMeButton').fadeOut('slow');
	$('.aboutMeFormEdit').fadeIn('slow').delay(500);
}


var aboutMeSendForm = function editAboutMeFormSumbit() {
	
	var contentsPost = $('textarea#aboutMeWidgetTextarea').val();
	var language = $(this).attr('locale');

	$.post($('.aboutMeWidgetSubmit').attr('action_url'), { aboutMeContents: contentsPost, locale: language }, function(data, textStatus, jqXHR) {
		if(textStatus == 'success') {
			var successHtml = '<div align="center" style="padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #33ff33;-moz-border-radius: 15px;border-radius: 15px;">'+ data.msg +'</div>';
			var unsuccessfulHtml = '<div align="center" style="padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #ff0033;-moz-border-radius: 15px;border-radius: 15px;">'+ data.msg +'</div>';
			
			$('.aboutMeMsg').fadeIn('slow').append(successHtml);
			$('.aboutMeFormEdit').fadeOut('slow').delay(500);
			$('#aboutMe').fadeIn('slow').delay(800);
			$('.editAboutMeButton').fadeIn('slow').delay(800);
		}
		else
		{
			$('.aboutMeMsg').fadeIn('slow').append(unsuccessfulHtml);
			$('.aboutMeFormEdit').fadeOut('slow').delay(500);
			$('#aboutMe').fadeIn('slow').delay(800);
			$('.editAboutMeButton').fadeIn('slow').delay(800);
		}
	});
}