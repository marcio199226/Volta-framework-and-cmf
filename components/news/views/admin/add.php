{@ assets type="js" path="../components/news/assets/js/addNews.js" @}
{@ assets type="css" path="../components/news/assets/css/styles.css" @}
{@ js_inline @}
	var translations = { 
		'translations': <?php print transToJsArray(); ?>
	};
	$(document).ready(function() {
		$(".addForm").on("click", translations, addFormForLanguage);
	});
{@ end @}
<div class="box">
	<?php 
		Vf_Form_Helper::open('', 'post', true, null, array('id' => 'newsAddForm'));
		Vf_Form_Helper::text('title['.$currentLocale.']', __('newsFormAddNewEntryTitle'), 'height:30px;width:500px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
		Vf_Form_Helper::textarea('content['.$currentLocale.']', __('newsFormAddNewEntryContent'), 'height:300px;width:500px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');	
		Vf_Form_Helper::submit(array('name' => 'add_news', 'value' => __('newsFormAddNewEntrySubmit'), 'id' => 'addNewsFormSubmitId', 'style' => 'height:30px;width:100px;padding:2px;background-color:#FFF8DC;border:1px solid #A9A9A9;'));
		Vf_Form_Helper::close(); 
		$form = Vf_Form_Helper::get_form();
		print $form['form_open'];
	?>
	<h3><?php print __('newsAddNewEntry'); ?></h3>
	<table class="alignNewsForms">
		<tr>
			<td>
				<div id="newsAddTranslations">
					<?php print __('newsFormAddTranslationsForm'); ?>
					<?php foreach($locales as $locale => $language): ?>
						<?php if($locale != $currentLocale): ?>
							<a href="#" class="addForm" locale="<?php print $locale; ?>" langName="<?php print $language; ?>"><img class="imgAlignBottom" src="../components/news/assets/img/<?php print $locale; ?>.png" /></a>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</td>
		</tr>
		<tr>
			<td><?php print __('newsAddNewEntryTitle'); ?><Br /><?php print $form['title['.$currentLocale.']']; ?></td>
		</tr>
		<tr>
			<td><?php print __('newsAddNewEntryContent'); ?><Br /><?php print $form['content['.$currentLocale.']']; ?></td>
		</tr>
		<tr><td>
			<div class="formsForPosts"></div>
		</td></tr>
		<tr>
			<td align="center">
				<span id="newsTags">
					<?php print __('newsAddNewEntryTags'); ?> [i][/i], [b][/b], [u][/u], [s][/s], [url][/url], [url=opis][/url], [code][/code], [quote][/quote]
				<span>
			</td>
		</tr>
		<tr>
			<td align="left"><?php print $form['add_news']; ?></td>
		</tr>
	</table>
	<?php print $form['form_close']; ?>
	<?php if(isset($error_add_news)): ?>
		<?php print Vf_Box_Helper::error($error_add_news); ?>
	<?php endif; ?>
	<?php if(sizeof($errors) > 0): ?>
		<?php foreach($errors as $k => $error): ?>
			<?php print Vf_Box_Helper::error($error); ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>