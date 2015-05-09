{@ assets type="js" path="../widgets/assets/js/contact.js" @}
{@ assets type="css" path="../widgets/assets/css/contact.css" @}
<?php $hasRole = Vf_User_Helper::hasRole('general', 'edit'); ?>
<?php if($hasRole): ?>
	{@ js_inline @}
		$(document).ready(function() {
			$('.editContactMeButton').bind('click', contactMeEditFormShow);
			$('.contactMeWidgetSubmit').bind('click', contactMeSendForm);
		});
	{@ end @}
<?php endif; ?>
<div class="box">
	<h3>Kontakt</h3>
	<?php if($contacts): ?>
		<div id="contactMe">
			<table>
				<?php foreach($contacts as $type => $value): ?>
					<?php if($value !== null && $value !== '0' && $value !== ''): ?>
						<tr>
							<?php if(in_array($type, $contactsAsLink)): ?>
								<td><b><?php print $type ?>:</b></td> <td><a target="_blank" href="<?php print $value; ?>" style="text-decoration:none;"><?php print $value; ?></a></td>
							<?php else: ?>
								<td><b><?php print $type ?>:</b></td> <td><?php print $value; ?></td>
							<?php endif; ?>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			</table>
		</div>
		<div class="editContactMeWidget">
			<?php if($hasRole): ?>
					<?php Vf_Form_Helper::button(array('name' => 'editButton', 'value' => 'Edytuj', 'class' => 'editContactMeButton')); ?>
					<?php $form = Vf_Form_Helper::get_form(); ?>
					<?php print $form['editButton']; ?>
			<?php endif; ?>
		</div>
		<?php if($hasRole): ?>
			<div class="contactMeFormEdit" style="display: none;">
			<?php 
				Vf_Form_Helper::open('', 'post', true, null, array('id' => 'contactMeForm', 'class' => 'contactMeFormEdit')); 
				Vf_Form_Helper::text(array('name' => 'facebook', 'id' => 'facebook', 'class' => 'contactMeWidgetText', 'value' => $contacts['facebook']));
				Vf_Form_Helper::text(array('name' => 'twitter', 'id' => 'twitter', 'class' => 'contactMeWidgetText', 'value' => $contacts['twitter']));
				Vf_Form_Helper::text(array('name' => 'www', 'id' => 'www', 'class' => 'contactMeWidgetText', 'value' => $contacts['www']));
				Vf_Form_Helper::text(array('name' => 'github', 'id' => 'github', 'class' => 'contactMeWidgetText', 'value' => $contacts['github']));
				Vf_Form_Helper::text(array('name' => 'jabber', 'id' => 'jabber', 'class' => 'contactMeWidgetText', 'value' => $contacts['jabber']));
				Vf_Form_Helper::text(array('name' => 'gg', 'id' => 'gg', 'class' => 'contactMeWidgetText', 'value' => $contacts['gg']));
				Vf_Form_Helper::text(array('name' => 'email', 'id' => 'email', 'class' => 'contactMeWidgetText', 'value' => $contacts['email']));
				Vf_Form_Helper::text(array('name' => 'irc_node', 'id' => 'irc_node', 'class' => 'contactMeWidgetText', 'value' => $contacts['irc_node']));
				Vf_Form_Helper::text(array('name' => 'telephone', 'id' => 'telephone', 'class' => 'contactMeWidgetText', 'value' => $contacts['telephone']));
				Vf_Form_Helper::text(array('name' => 'msn', 'id' => 'msn', 'class' => 'contactMeWidgetText', 'value' => $contacts['msn']));
				Vf_Form_Helper::button(array('name' => 'save_contacts', 'value' => 'Zapisz', 'action_url' => Vf_Uri_Helper::site(false), 'class' => 'contactMeWidgetSubmit'));
				Vf_Form_Helper::close();
				$formEdit = Vf_Form_Helper::get_form();
				print $formEdit['form_open'];
			?>
			<table>
				<?php foreach($contacts as $type => $value): ?>
					<tr>
							<td><?php print $type; ?>:</td>
							<td><?php print $formEdit[$type]; ?></td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<td><?php print $formEdit['save_contacts']; ?></td>
				</tr>
			</table>
			<?php print $formEdit['form_close']; ?>
			</div>
			<div class="contactMeMsg" style="display: none;"></div>
		<?php endif; ?>
	<?php endif; ?>
</div>
