<?php if(sizeof($attachments) > 0): ?>
	{@ assets type="js" path="../plugins/attachments/assets/js/attachmentsForm.js" @}
	{@ js_inline @}
		$(document).ready(function() {
			deleteFile();
		});
	{@ end @}
	<div class="box">
		<fieldset style="padding:3px;">
			<legend>
				<h3><?php print __('attachmentsFilesLabel'); ?></h3>
			</legend>
			<table>
				<?php foreach($attachments as $key => $file): ?>
					<tr>
						<td>
							<span style="color: #000000;font-size: 14px;"><?php print $file; ?> 
								<img src="../plugins/attachments/assets/images/delete.png" alt="Usun plik" style="vertical-align:middle;" class="delete_file" id="<?php print $key; ?>" attachment_path="<?php print $dir;?>" attachment_name="<?php print $file; ?>" action_delete="<?php print Vf_Uri_Helper::site(false, ','.$id); ?>" />
							</span>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<div id="attachment_delete_success" style="display:none;"></div>
		</fieldset>
	</div>
<?php endif; ?>