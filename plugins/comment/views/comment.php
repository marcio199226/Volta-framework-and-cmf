{@ assets type="css" path="../plugins/comment/assets/css/styles.css" @}
<div class="commentPlugin">
	<h3><?php print __('Komentarze'); ?></h3>
		<?php 
			Vf_Form_Helper::open('', 'post', true); 
			Vf_Form_Helper::text(array('name' => 'puser', 'value' => __('commentFormUser'), 'class' => 'formcommentPluginInput'));
			Vf_Form_Helper::text(array('name' => 'pcaptcha', 'value' => '', 'class' => 'formcommentPluginInput'));
			Vf_Form_Helper::textarea(array('name' => 'pcomment', 'id' => 'formcommentPluginTextarea', 'value' => __('commentFormTextareaValue')));
			Vf_Form_Helper::hidden('csrf_token', '{@csrf_token@}');
			Vf_Form_Helper::submit(array('name' => 'padd_comment', 'value' => __('commentFormSubmit'), 'id' => 'formCommentPluginButton'));
			Vf_Form_Helper::close();
			$form = Vf_Form_Helper::get_form();
			print $form['form_open'];
			print $form['csrf_token'];
		?>
		<table class="commentPluginListAll">
			<?php if(Vf_User_Helper::anonymous()): ?>
			<tr>
				<td><?php print __('commentUserLabel'); ?><Br /><?php print $form['puser']; ?></td>
			</tr>
			<?php endif; ?>
			<tr>
				<td><?php print __('commentTextareaLabel'); ?><Br /><?php print $form['pcomment']; ?></td>
			</tr>
			<tr>
				<td><?php print __('commentCaptcha'); ?><Br /><?php print $captcha; ?><Br /><?php print $form['pcaptcha']; ?></td>
			</tr>
			<tr>
				<td align="left"><?php print $form['padd_comment']; ?></td>
			</tr>
		</table>
		<?php print $form['form_close']; ?>
		<?php if(isset($msg)): ?>
			<?php print Vf_Box_Helper::error($msg); ?>
		<?php endif; ?>
		<?php if(isset($error_add_comment)): ?>
			<?php print Vf_Box_Helper::error($error_add_comment); ?>
		<?php endif; ?>
		<?php if(sizeof($errors) > 0): ?>
			<?php foreach($errors as $k => $error): ?>
				<?php print Vf_Box_Helper::error($error); ?>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if(sizeof($comments) > 0): ?>
			<form method="post">
			<input type="hidden" name="csrf" value="{@csrf_token@}">
			<?php foreach($comments as $comment): ?>
				<fieldset>
					<legend>
						<p id="commentPluginAuthor">
							<?php print $comment['author']; ?>
							<?php if(Vf_User_Helper::hasRole('comment', 'delete')): ?>
								<input src="../plugins/comment/assets/images/delete_min.jpg" style="background-color:none;border:none;padding-top:15px;" type="image" name="del_comment" value="<?php print $comment['id']; ?>">
							<?php endif; ?>
						</p>
					</legend>
					<p id="commentPluginContent">
						<?php print Vf_BBCode_Helper::parse($comment['content']); ?>
					</p>
					<p id="commentPluginDate"><?php print __('commentData'); print $comment['data']; ?></p>
				</fieldset>
			<?php endforeach; ?>
			</form>
		<?php else: ?>
			<p id="commentPluginNoEntry"><?php print __('Ten wpis nie posiada komentarzy'); ?></p>
		<?php endif; ?>
</div>