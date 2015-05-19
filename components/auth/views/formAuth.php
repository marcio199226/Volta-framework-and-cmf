{@ assets type="css" path="../components/auth/assets/css/styles.css" @}
<div id="boxAuth">
	<!-- <li>remove it if you change template, it's for sidebar box of current layout / added in template <li></li>-->
		<h4><?php print __('logowanie'); ?></h4>
		<?php 
			Vf_Form_Helper::open(); 
			Vf_Form_Helper::text(array('name' => 'username', 'value' => __('formTextLoginValue'), 'class' =>'formAuthInput'));
			Vf_Form_Helper::text(array('name' => 'authCaptcha', 'class' =>'formAuthInput'));
			Vf_Form_Helper::password(array('name' => 'passwd', 'value' => 'password', 'class' =>'formAuthInput'));
			Vf_Form_Helper::checkbox(array('name' => 'remember_me', 'value' => 'Zapamietaj mnie', 'id' => 'checkbox'));
			Vf_Form_Helper::submit(array('name' => 'log', 'value' => __('formSubmitValue'), 'id' =>'formAuthButton'));
			Vf_Form_Helper::close();
			$form = Vf_Form_Helper::get_form();
			print $form['form_open'];
		?>
		<table class="tableAuth">
			<tr>
				<td><?php print $form['username']; ?></td>
			</tr>
			<tr>
				<td><?php print $form['passwd']; ?></td>
			</tr>
			<?php if(isset($captcha)): ?>
				<tr>
					<td align="center">
						<?php print __('Przepisz captche'); ?>
						<Br />
						<?php print $captcha; ?>
					</td>
				</tr>
				<tr>
					<td align="center"><?php print $form['authCaptcha']; ?></td>
				</tr>
			<?php endif; ?>
			<?php if(isset($invalidCaptcha)): ?>
				<tr>
					<td align="center"><?php print Vf_Box_Helper::error($invalidCaptcha); ?></td>
				</tr>
			<?php endif; ?>
			<?php if($remember == 1): ?>
				<tr>
					<td>
						<div class="checkbox">
							<?php print $form['remember_me']; ?>
							<label for="checkbox"></label>
						</div>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<td align="center"><?php print $form['log']; ?></td>
			</tr>
			<?php if(isset($msg)): ?>
				<tr>
					<td align="center">
						<?php print Vf_Box_Helper::error($msg); ?>
					</td>
				</tr>
			<?php endif; ?>
			<?php if(sizeof($errors) > 0): ?>
				<?php foreach($errors as $k => $error): ?>
					<tr>
						<td align="center"><?php print Vf_Box_Helper::error($error); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</table>
		<?php print $form['form_close']; ?>
	<!-- </li>remove it if you change template, it's for sidebar box of current layout-->
</div>