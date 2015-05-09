<div class="box">
	<?php 
		Vf_Form_Helper::open();
		Vf_Form_Helper::password('old_password', '', 'height:30px;width:200px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
		Vf_Form_Helper::password('new_password', '', 'height:30px;width:200px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
		Vf_Form_Helper::password('new_password_re', '', 'height:30px;width:200px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
		Vf_Form_Helper::submit('edit_password', 'Zapisz');
		Vf_Form_Helper::close(); 
		$form = Vf_Form_Helper::get_form();
		print $form['form_open'];
	?>
	<h3>Zmiana hasla</h3>
	<table>
		<tr>
			<td>Stare haslo:<Br /><?php print $form['old_password']; ?></td>
		</tr>
		<tr>
			<td>Nowe haslo:<Br /><?php print $form['new_password']; ?></td>
		</tr>
		<tr>
			<td>Powtorz:<Br /><?php print $form['new_password_re']; ?></td>
		</tr>
		<tr>
			<td align="left"><?php print $form['edit_password']; ?></td>
		</tr>
	</table>
	<?php print $form['form_close']; ?>
	<?php if(isset($success)): ?>
	<?php print Vf_Box_Helper::success($success); ?>
	<?php endif; ?>
	<?php if(isset($error_edit)): ?>
	<?php print Vf_Box_Helper::error($error_edit); ?>
	<?php endif; ?>
	<?php if(sizeof($errors) > 0): ?>
	<?php foreach($errors as $k => $error): ?>
	<?php print Vf_Box_Helper::error($error); ?>
	<?php endforeach; ?>
	<?php endif; ?>
</div>