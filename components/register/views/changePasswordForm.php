{@ assets type="css" path="../components/register/assets/css/styles.css" @}
<div class="box">
<h5><?php print __('registerRetrievePasswordChange'); ?></h5>
	<?php 
		Vf_Form_Helper::open(); 
		Vf_Form_Helper::password('passwd', '', 'height:30px;width:350px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
		Vf_Form_Helper::password('repasswd', '', 'height:30px;width:350px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
		Vf_Form_Helper::hidden('csrf_token', '{@csrf_token@}');
		Vf_Form_Helper::submit(array('name' => 'registerChangePwd', 'value' => __('registerRetrieveFormSubmit'), 'id' => 'formRegisterComponentButton'));
		Vf_Form_Helper::close();
		$form = Vf_Form_Helper::get_form();
		print $form['form_open'];
	?>
	<table>
		<tr>
			<td><?php print __('registerRetrievePasswordFormLabel'); ?><Br /><?php print $form['passwd']; ?></td>
		</tr>
		<tr>
			<td><?php print __('registerRetrieveRePasswordFormLabel'); ?><Br /><?php print $form['repasswd']; ?></td>
		</tr>
		<tr>
			<td align="left"><?php print $form['registerChangePwd']; ?></td>
		</tr>
	</table>
	<?php print $form['csrf_token']; ?>
	<?php print $form['form_close']; ?>
	<h4><?php print __('registerRetrievePasswordLabel'); ?></h4>
	<?php if(isset($notChanged)): ?>
		<?php print Vf_Box_Helper::error($notChanged); ?>
	<?php endif; ?>
	<?php if(isset($changed)): ?>
		<?php print Vf_Box_Helper::success($changed); ?>
	<?php endif; ?>
	<?php if(sizeof($errors) > 0): ?>
		<?php foreach($errors as $k => $error): ?>
			<?php print Vf_Box_Helper::error($error); ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>