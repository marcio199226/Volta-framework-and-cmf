{@ assets type="css" path="../components/register/assets/css/styles.css" @}
<div class="box">
<h5><?php print __('registerRetrievePasswordLabel'); ?></h5>
	<?php 
		Vf_Form_Helper::open('', 'post', true); 
		Vf_Form_Helper::text('loginOrEmail', '', 'height:30px;width:350px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
		Vf_Form_Helper::hidden('csrf_token', '{@csrf_token@}');
		Vf_Form_Helper::submit(array('name' => 'retrievePassword', 'value' => __('registerRetrievePasswordSubmit'), 'id' => 'formRegisterComponentButton'));
		Vf_Form_Helper::close();
		$form = Vf_Form_Helper::get_form();
		print $form['form_open'];
	?>
	<table>
		<tr>
			<td><?php print __('registerRetrieveUserEmailFormLabel'); ?><Br /><?php print $form['loginOrEmail']; ?></td>
		</tr>
		<tr>
			<td align="left"><?php print $form['retrievePassword']; ?></td>
		</tr>
	</table>
	<?php print $form['csrf_token']; ?>
	<?php print $form['form_close']; ?>
	<?php if(isset($error)): ?>
		<?php print Vf_Box_Helper::error($error); ?>
	<?php endif; ?>
	<?php if(isset($success)): ?>
		<?php print Vf_Box_Helper::success($success); ?>
	<?php endif; ?>
	<?php if(sizeof($errors) > 0): ?>
		<?php foreach($errors as $k => $error): ?>
			<?php print Vf_Box_Helper::error($error); ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>