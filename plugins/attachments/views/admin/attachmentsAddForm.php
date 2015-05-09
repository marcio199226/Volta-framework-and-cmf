{@ assets type="js" path="../plugins/attachments/assets/js/attachmentsForm.js" @}
{@ js_inline @}
	$(document).ready(function() {
		var translations = <?php print transToJsArray(); ?>;
		submitAttachmentsForm('<?php print $idFormSubmitButton; ?>', '<?php print $parentIdForm; ?>');
		addInputFile('<?php print $locale; ?>', translations);
	});
{@ end @}
<div class="box">
	<fieldset style="padding:3px;">
		<legend>
			<h3><?php print __('attachmentsFilesLabel'); ?></h3>
		</legend>
		<?php 
			Vf_Form_Helper::open('', 'post', true, true, array('id' => 'attachmentsAddForm'), 'attachments_form_open'); 
			Vf_Form_Helper::input_file('files', 'height:30px;width:300px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;', 3);
			Vf_Form_Helper::hidden('csrf_token', '{@csrf_token@}');
			Vf_Form_Helper::close('attachments_form_close');
			$form = Vf_Form_Helper::get_form();
			print $form['attachments_form_open'];
			print $form['csrf_token'];
		?>
		<table class="inputs_files">
			<?php foreach($form['files'] as $key => $file): ?>
				<tr>
					<td><?php print $key+1; ?>. <?php print __('attachmentsFormFilesLabel'); ?><Br /><?php print $file; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php print $form['attachments_form_close']; ?>
		<div>
			<img src="../plugins/attachments/assets/images/add.png" alt="Dodaj plik" id="add_input" /> <?php print __('Dodaj nastepny plik'); ?>
		</div>
		<p style="font-size: 10px;color: #000000;text-align: center;"><?php print __('Zalaczniki zostana wyslane razem z glownym formularzem'); ?></p>
	</fieldset>
	<?php if($flashMessages->hasFlash($flashErrorsKey)): ?>
		<?php $messages = $flashMessages->getMessages(); ?>
		<?php foreach($messages[$flashErrorsKey] as $k => $error): ?>
			<?php print $error; ?>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if(isset($msg)): ?>
		<?php print Vf_Box_Helper::success($msg); ?>
	<?php endif; ?>
	<?php if(isset($exception)): ?>
		<?php print Vf_Box_Helper::error($exception); ?>
	<?php endif; ?>
	<?php if(sizeof($errors) > 0): ?>
		<?php foreach($errors as $k => $error): ?>
			<?php print Vf_Box_Helper::error($error); ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>