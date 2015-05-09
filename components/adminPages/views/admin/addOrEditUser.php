<div class="box">
<h5><?php print $header; ?></h5>
	<?php 
		if($action == 'edit' && !isset($userNotExists))
		{
			Vf_Form_Helper::open(); 
			Vf_Form_Helper::text('login', $userData -> login, 'height:30px;width:400px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
			Vf_Form_Helper::password('password', 'password', 'height:30px;width:400px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
			Vf_Form_Helper::menu('group', $aclGroups, false, 'height:30px;width:400px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;', $userData -> role);
			Vf_Form_Helper::text('email', $userData -> email, 'height:30px;width:400px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
			Vf_Form_Helper::hidden('id', $userData -> id);
			Vf_Form_Helper::hidden('hash', $userData -> hash);
			Vf_Form_Helper::hidden('csrf_token', '{@csrf_token@}');
			Vf_Form_Helper::submit('edit_user', 'Edytuj', 'height:30px;width:200px;');
			Vf_Form_Helper::close();
			$form = Vf_Form_Helper::get_form();
			print $form['form_open'];
		}
		else if($action == 'add')
		{
			Vf_Form_Helper::open(); 
			Vf_Form_Helper::text('login', 'Nazwa uzytwkonika', 'height:30px;width:400px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
			Vf_Form_Helper::password('password', 'password', 'height:30px;width:400px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
			Vf_Form_Helper::menu('group', $aclGroups, false, 'height:30px;width:400px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
			Vf_Form_Helper::text('email', 'email', 'height:30px;width:400px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
			Vf_Form_Helper::hidden('csrf_token', '{@csrf_token@}');
			Vf_Form_Helper::submit('add_user', 'Dodaj', 'height:30px;width:200px;');
			Vf_Form_Helper::close();
			$form = Vf_Form_Helper::get_form();
			print $form['form_open'];
		}
	?>
	<?php if(!isset($userNotExists)): ?>
	<table>
		<tr>
			<td>Login:<Br /><?php print $form['login']; ?></td>
		</tr>
		<tr>
			<td>Haslo:<Br /><?php print $form['password']; ?></td>
		</tr>
		<tr>
			<td>Grupa:<Br /><?php print $form['group']; ?></td>
		</tr>
		<tr>
			<td>Email:<Br /><?php print $form['email']; ?></td>
		</tr>
		<tr>
			<td align="left">
				<?php 
					if($action == 'edit')
						print $form['edit_user']; 
					else
						print $form['add_user'];
				?>
			</td>
		</tr>
	</table>
	<?php print $form['id']; ?>
	<?php print $form['hash']; ?>
	<?php print $form['csrf_token']; ?>
	<?php print $form['form_close']; ?>
	<?php if(isset($error_on_user)): ?>
		<?php print Vf_Box_Helper::error($error_on_user); ?>
	<?php endif; ?>
	<?php if(isset($success_on_user)): ?>
		<?php print Vf_Box_Helper::success($success_on_user); ?>
	<?php endif; ?>
	<?php if(sizeof($errors) > 0): ?>
		<?php foreach($errors as $k => $error): ?>
			<?php print Vf_Box_Helper::error($error); ?>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php else: ?>
	<?php print Vf_Box_Helper::error($userNotExists); ?>
	<?php endif; ?>
</div>