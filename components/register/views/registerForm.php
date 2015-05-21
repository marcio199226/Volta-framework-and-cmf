{@ assets type="css" path="../components/register/assets/css/styles.css" @}
<div class="box">
<h5><?php print __('registerLabel'); ?></h5>
	<?php 
		Vf_Form_Helper::open('', 'post', true); 
		Vf_Form_Helper::text('login', __('registerFormUserTextValue'), 'height:30px;width:350px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
		Vf_Form_Helper::password('password', 'haslo', 'height:30px;width:350px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
		Vf_Form_Helper::password('re_password', 'haslo2', 'height:30px;width:350px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
		Vf_Form_Helper::text('email', __('registerFormEmailTextValue'), 'height:30px;width:350px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
		Vf_Form_Helper::hidden('csrf_token', '{@csrf_token@}');
		Vf_Form_Helper::submit(array('name' => 'register', 'value' => __('registerFormSubmitButton'), 'id' => 'formRegisterComponentButton'));
		Vf_Form_Helper::close();
		$form = Vf_Form_Helper::get_form();
		print $form['form_open'];
	?>
	<table>
		<tr>
			<td><?php print __('registerUsernameLabel'); ?><Br /><?php print $form['login']; ?></td>
		</tr>
		<tr>
			<td><?php print __('registerPasswordLabel'); ?><Br /><?php print $form['password']; ?></td>
		</tr>
		<tr>
			<td><?php print __('registerRePasswordLabel'); ?><Br /><?php print $form['re_password']; ?></td>
		</tr>
		<tr>
			<td><?php print __('registerEmailLabel'); ?><Br /><?php print $form['email']; ?></td>
		</tr>
		<tr>
			<td align="left"><?php print $form['register']; ?></td>
		</tr>
	</table>
	<?php print $form['csrf_token']; ?>
	<?php print $form['form_close']; ?>
	<?php if(isset($error_add_user)): ?>
		<?php print Vf_Box_Helper::error($error_add_user); ?>
	<?php endif; ?>
	<?php if(isset($success_add_user)): ?>
		<?php print Vf_Box_Helper::success($success_add_user); ?>
	<?php endif; ?>
	<?php if(sizeof($errors) > 0): ?>
		<?php foreach($errors as $k => $error): ?>
			<?php print Vf_Box_Helper::error($error); ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>