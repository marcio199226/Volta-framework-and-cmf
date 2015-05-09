<div class="box">
	<?php 
		Vf_Form_Helper::open('', 'post', true); 
		Vf_Form_Helper::hidden('csrf_token', '{@csrf_token@}');
		Vf_Form_Helper::submit('pdel_comment', __('Usun wszystkie komentarze'), 'height:50px;width:200px;padding:2px;border:none;');
		Vf_Form_Helper::close();
		$form = Vf_Form_Helper::get_form();
		print $form['form_open'];
		print $form['csrf_token'];
	?>
	<div align="center"><?php print $form['pdel_comment']; ?></div>
	<?php print $form['form_close']; ?>
	<?php if(isset($msg)): ?>
		<?php print Vf_Box_Helper::success($msg); ?>
	<?php endif; ?>
	<?php if(isset($error_del)): ?>
		<?php print Vf_Box_Helper::error($error_del); ?>
	<?php endif; ?>
</div>