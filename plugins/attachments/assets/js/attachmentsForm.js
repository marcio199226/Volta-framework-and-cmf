function submitAttachmentsForm(idFormSubmitButton, parentIdForm) {
	$('#' + idFormSubmitButton).click(function() {
		$('#' + parentIdForm).submit(function(event) {
			event.preventDefault(); 
			$.post($('#' + parentIdForm).attr("action"), $('#' + parentIdForm).serialize(), function(data) {
				$('#attachmentsAddForm').submit(); 
			});
		});
	});
}


function addInputFile(locale, translations) {
	var i = $('input:file').size() + 1;
	var input = '<input type="file" name="files[]" style="height:30px;width:300px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;" />';
	
	$('#add_input').click(function() {
		$('<tr><td>' + i + '. ' + translations[locale]['attachmentsFormFilesLabel'] + '<Br />' + input + '</td></tr>')
		.fadeIn('slow')
		.appendTo('.inputs_files');
		i++;
	});
}


function deleteFile() {
	$('.delete_file').click(function(event) {
		if(confirm('Czy na pewno chcesz permanentnie usunac ten zalacznik?')) {
			var fileName = $(this).attr('attachment_name');
			var dirPath = $('.delete_file').attr('attachment_path');
			var id = $(this).attr('id');
		
			$.post($('.delete_file').attr('action_delete'), { filename: fileName, path: dirPath }, function(data, textStatus, jqXHR) {
				if(textStatus == 'success') {
					var successHtml = '<div align="center" style="padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #33ff33;-moz-border-radius: 15px;border-radius: 15px;">' + data.msg + '</div>';
					var unsuccessfulHtml = '<div align="center" style="padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #ff0033;-moz-border-radius: 15px;border-radius: 15px;">' + data.msg + '</div>';
					$('#' + id).remove();
					$('#attachment_delete_success').css('display', 'block');
					$('#attachment_delete_success').fadeIn('slow').append(successHtml);
				}
				else
				{
					$('#attachment_delete_success').css('display', 'block');
					$('#attachment_delete_success').fadeIn('slow').append(unsuccessfulHtml);
				}
			});
		}
	});
}
