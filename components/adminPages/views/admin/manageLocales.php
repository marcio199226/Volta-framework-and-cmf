{@ assets type="css" path="../components/adminPages/assets/css/styles.css" @}
<?php $base = Vf_Uri_Helper::base(true); ?>
<?php 
	Vf_Form_Helper::open('', 'post', true); 
	Vf_Form_Helper::text(array('name' => 'locale', 'value' => '', 'class' => 'formAddLanguageInput'));
	Vf_Form_Helper::text(array('name' => 'language', 'value' => '', 'class' => 'formAddLanguageInput'));
	Vf_Form_Helper::submit(array('name' => 'addLocale', 'value' => __('adminPagesLocalesAddNew'), 'class' => 'formAddLanguageSubmit'));
	Vf_Form_Helper::close();
	$form = Vf_Form_Helper::get_form();
?>
<fieldset class="default" style="margin-top:10px;padding:3px;">
	<legend>
		<?php print __('adminPagesLocalesLabel'); ?>
	</legend>
	<table class="default" cellspacing="0">
		<tr>
			<th><?php print __('adminPagesLocalesLocale'); ?></th>
			<th><?php print __('adminPagesLocalesLanguage'); ?></th>
			<th><?php print __('adminPagesLocalesAction'); ?></th>
		</tr>
		<?php foreach($locales as $locale): ?>
			<tr>
				<td><?php print $locale['locale']; ?></td>
				<td><?php print $locale['language']; ?></td>
				<td><a style="text-decoration:none;" href="<?php print $base; ?>Admin,Index,deleteLanguage,<?php print $locale['locale']; ?>"><?php print __('adminPagesLocalesDelete'); ?></a></td>
			</tr>
		<?php endforeach; ?>
	</table>
</fieldset>
<?php print $form['form_open']; ?>
<table>
	<tr>
		<td><?php print __('adminPagesLocalesLocale'); ?>:</td><td><?php print $form['locale']; ?></td>
		<td><?php print __('adminPagesLocalesLanguage'); ?>:</td><td><?php print $form['language']; ?></td>
	</tr>
</table>
<?php print $form['addLocale']; ?>
<?php print $form['form_close']; ?>
<p style="color:#000000;"><?php print __('adminPagesLocalesInfo'); ?></p>

<?php if(sizeof($errors) > 0): ?>
	<?php foreach($errors as $k => $error): ?>
		<?php print Vf_Box_Helper::error($error); ?>
	<?php endforeach; ?>
<?php endif; ?>

<?php if($flashMessages->hasFlash('languageDeleted')): ?>
	<?php print $flashMessages->from('prev')->languageDeleted; ?>
<?php endif; ?>
<?php if($flashMessages->hasFlash('languageNotDeleted')): ?>
	<?php print $flashMessages->from('prev')->languageNotDeleted; ?>
<?php endif; ?>