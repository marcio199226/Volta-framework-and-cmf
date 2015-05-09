function addFormForLanguage(event) {
	var locale = $(this).attr('locale');
	var language = $(this).attr('langName');
	var img = '<img class="imgAlignBottom" src="../components/news/assets/img/' + locale + '.png"/>';
	var input = '<input type="text" name="title['+ locale +']" style="height:30px;width:500px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;" />';
	var textarea = '<textarea name="content['+ locale +']" style="height:300px;width:500px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;"></textarea>';

	$('.formsForPosts').append('<tr><td>' + img + language + '</td></tr>');
	$('.formsForPosts').append('<tr><td>' + event.data.translations[locale]['newsAddNewEntryTitle'] + '<Br />' + input + '</td></tr>');
	$('.formsForPosts').append('<tr><td>' + event.data.translations[locale]['newsAddNewEntryContent'] + '<Br />' + textarea + '</td></tr>');;
}