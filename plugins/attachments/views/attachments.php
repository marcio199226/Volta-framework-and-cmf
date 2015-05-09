{@ assets type="css" path="../plugins/attachments/assets/css/styles.css" @}
<?php if(sizeof($attachments) > 0): ?>
	<?php $base = Vf_Uri_Helper::base(false, false); ?>
	<?php $path =  $base.$dir ?>
	<div class="attachmentsPlugin">
		<fieldset>
			<legend>
				<h2><?php print __('attachmentsFilesLabel'); ?></h2>
			</legend>
			<table>
				<div class="inputs_files">
					<?php foreach($attachments as $key => $file): ?>
						<tr>
							<td>
								<a href="<?php print $path.$file; ?>" style="text-decoration:none;" target="_blank" title="Pobierz"><?php print $file; ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
				</div>
			</table>
			<p style="font-size: 10px;color: #000000;text-align: center;"><?php print __('attachmentsOpenFile'); ?></p>
		</fieldset>
	</div>
<?php endif; ?>