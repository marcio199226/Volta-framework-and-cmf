<div class="box">
	<?php 
		Vf_Form_Helper::open();
		Vf_Form_Helper::textarea('cfg_content', $config_content, 'height:600px;width:500px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
		Vf_Form_Helper::submit('edit_config_file', 'Zapisz');
		Vf_Form_Helper::close(); 
		$form = Vf_Form_Helper::get_form();
		print $form['form_open'];
	?>
	<h3>Zmiana konfiguracji</h3>
	<table>
		<tr>
			<td>Zawartosc konfiguracji:<Br /><?php print $form['cfg_content']; ?></td>
		</tr>
		<tr>
			<td align="left"><?php print $form['edit_config_file']; ?></td>
		</tr>
	</table>
	<?php print $form['form_close']; ?>
	<div align="left">
		<span style="font-size:12px;color:#000000;">
			Prosze edytowac lub dodawac elementy, poleca sie jednak nie usuwac istniejacych juz tagow.
		</span>
	</div>
	<?php if(isset($success)): ?>
	<?php print Vf_Box_Helper::success($success); ?>
	<?php endif; ?>
	<?php if(isset($error_edit)): ?>
	<?php print Vf_Box_Helper::error($error_edit); ?>
	<?php endif; ?>
</div>