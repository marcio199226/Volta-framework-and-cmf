var ban = function banUser() {
	$('#msg').empty();
	var successHtml = '<div align=\"center\" style=\"padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #33ff33;\">Uzytkownik zostal zbanowany</div>';
	var time = prompt("Prosze podac czas w dniach lub zostawic puste jesli nie ma byc limitu czasu.");
	if(time)
	{
		var login = $(this).attr('user');
		var action = $('.ban_user').attr('class');
		var link_id = $(this).attr('id');
		var user_id = $(this).attr('user_id');
		
		if(time != null)
		{
			time = time * 60*60*60;
		}
		
		$.post($('.ban_user').attr('action_url'), { user: login, expire: time, action_type: action }, function(data, textStatus, jqXHR) {
			if(textStatus == 'success') {
				$("#" + link_id).text('Odbanuj');
				//$("#" + link_id).attr('class', 'unban_user');
				//$("#" + link_id).attr('id', 'unban_' + user_id);
				$('.ban_user').unbind('click');
				$('.ban_user').bind('click', unban);
				$('#msg').css('display', 'block');
				$('#msg').fadeIn('slow').append(successHtml);
			}
		});
	}
}


var unban = function unbanUser() {
	$('#msg').empty();
	var successHtml = '<div align=\"center\" style=\"padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #33ff33;\">Uzytkownik zostal odbanowany</div>';
	var login = $(this).attr('user');
	var action = $('.unban_user').attr('class');
	var link_id = $(this).attr('id');
		
	$.post($('.unban_user').attr('action_url'), { user: login, action_type: action}, function(data, textStatus, jqXHR) {
		if(textStatus == 'success') {
			$("#" + link_id).text('Banuj');
			$('.unban_user').unbind('click');
			$('.unban_user').bind('click', ban);
			$('#msg').css('display', 'block');
			$('#msg').fadeIn('slow').append(successHtml);
		}
	});
}


var active_account = function activeUserAccount() {
	$('#msg').empty();
	var successHtml = '<div align=\"center\" style=\"padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #33ff33;\">Konto uzytkownika zostalo aktywowane</div>';
	var login = $(this).attr('user');
	var action = $('.active_account').attr('class');
	var link_id = $(this).attr('id');
		
	$.post($('.active_account').attr('action_url'), { user: login, action_type: action}, function(data, textStatus, jqXHR) {
		if(textStatus == 'success') {
			$("#" + link_id).text('Dezaktywuj');
			$('.active_account').unbind('click');
			$('.active_account').bind('click', disable_account);
			$('#msg').css('display', 'block');
			$('#msg').fadeIn('slow').append(successHtml);
		}
	});
}


var disable_account = function disableUserAccount() {
	$('#msg').empty();
	var successHtml = '<div align=\"center\" style=\"padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #33ff33;\">Konto uzytkownika zostalo dezaktywowane</div>';
	var login = $(this).attr('user');
	var action = $('.disable_account').attr('class');
	var link_id = $(this).attr('id');
		
	$.post($('.disable_account').attr('action_url'), { user: login, action_type: action}, function(data, textStatus, jqXHR) {
		if(textStatus == 'success') {
			$("#" + link_id).text('Aktywuj');
			$('.disable_account').unbind('click');
			$('.disable_account').bind('click', active_account);
			$('#msg').css('display', 'block');
			$('#msg').fadeIn('slow').append(successHtml);
		}
	});
}