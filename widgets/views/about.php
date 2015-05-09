{@ assets type="js" path="../widgets/assets/js/about.js" @}
{@ assets type="css" path="../widgets/assets/css/about.css" @}
<?php $hasRole = Vf_User_Helper::hasRole('general', 'edit'); ?>
<?php if($hasRole): ?>
	{@ js_inline @}
		$(document).ready(function() {
			$('.editAboutMeButton').bind('click', aboutMeEditFormShow);
			$('.aboutMeWidgetSubmit').bind('click', aboutMeSendForm);
		});
	{@ end @}
<?php endif; ?>
<div class="box">
	<h5><?php print __('aboutMeWidget'); ?></h5>
	<p id="aboutMe">
		<?php print Vf_BBCode_Helper::parse($contents);?>
	</p>
	<div class="editAboutMeWidget">
		<?php 
			Vf_Form_Helper::button(array('name' => 'editButton', 'value' => __('aboutMeWidgetEditButton'), 'class' => 'editAboutMeButton')); 
			$form = Vf_Form_Helper::get_form();
			if($hasRole):
				print $form['editButton'];
			endif;
		?>
	</div>
	<?php if($hasRole): ?>
		<div class="aboutMeFormEdit" style="display:none;">
			<?php 
				Vf_Form_Helper::open('', 'post', true, null, array('class' => 'aboutMeFormEdit')); 
				Vf_Form_Helper::textarea(array('name' => 'contents', 'id' => 'aboutMeWidgetTextarea', 'value' => $contents));
				Vf_Form_Helper::button(array('name' => 'save_contents', 'value' => __('aboutMeWidgetSaveButton'), 'action_url' => Vf_Uri_Helper::site(false), 'locale' => $locale, 'class' => 'aboutMeWidgetSubmit'));
				Vf_Form_Helper::close();
				$formEdit = Vf_Form_Helper::get_form();
				print $formEdit['form_open'];
			?>
			<table>
				<tr>
					<td><?php print $formEdit['contents'] ?></td>
				</tr>
				<tr>
					<td align="right"><?php print $formEdit['save_contents'] ?></td>
				</tr>
				<tr>
					<td align="center">
						<span style="color:#000000;font-size:10px;">
							<?php print __('aboutMeWidgetTagsLabel'); ?> [i][/i], [b][/b], [u][/u], [s][/s], [url][/url], [url=opis][/url], [code][/code], [quote][/quote]
						<span>
					</td>
				</tr>
			</table>
			<?php print $formEdit['form_close']; ?>
		</div>
		<div class="aboutMeMsg" style="display: none;"></div>
	<?php endif; ?>
</div>
