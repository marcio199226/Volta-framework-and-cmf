<?php 
	Vf_Form_Helper::open('', 'post', false, true);
	Vf_Form_Helper::input_file('package');
	Vf_Form_Helper::submit($submitName, 'Instaluj');
	Vf_Form_Helper::close(); 
	$form = Vf_Form_Helper::get_form();
	print $form['form_open'];
?>
	<fieldset style="padding:3px;">
		<legend>
			<?php print $title; ?>
		</legend>
		<table>
			<tr>
				<td>
					<strong>Paczka(*.rar/*.zip):</strong> <?php print $form['package']; ?><?php print $form[$submitName]; ?>
				</td>
			</tr>
		</table>
		<?php if(isset($availableLibraries)): ?>
			<p style="font-size:10px;text-align:center;"><?php print $availableLibraries; ?></p>
		<?php endif; ?>
		<?php print $form['form_close']; ?>
		<?php if(isset($success)): ?>
		<?php print Vf_Box_Helper::success($success); ?>
		<?php endif; ?>
		<?php if(isset($error)): ?>
		<?php print Vf_Box_Helper::error($error); ?>
		<?php endif; ?>
		<?php if(sizeof($errors) > 0): ?>
		<?php foreach($errors as $k => $error): ?>
		<?php print Vf_Box_Helper::error($error); ?>
		<?php endforeach; ?>
		<?php endif; ?>
	</fieldset>