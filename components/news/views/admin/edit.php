{@ assets type="css" path="../components/news/assets/css/styles.css" @}
<div>
	<?php 
		Vf_Form_Helper::open();
		Vf_Form_Helper::text('title', $news['title'], 'height:30px;width:400px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
		Vf_Form_Helper::textarea('content', $news['content'], 'height:300px;width:400px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
		Vf_Form_Helper::submit(array('name' => 'edit_news', 'value' => __('newsFormEditSubmit'), 'id' => 'editNewsButtonConfirm'));
		Vf_Form_Helper::close(); 
		$form = Vf_Form_Helper::get_form();
		print $form['form_open'];
	?>
	<h3><?php print __('newsFormEditNews'); ?></h3>
	<table>
		<tr>
			<td><?php print __('newsFormEditNewsTitle'); ?><Br /><?php print $form['title']; ?></td>
		</tr>
		<tr>
			<td><?php print __('newsFormEditNewsContent'); ?><Br /><?php print stripslashes($form['content']); ?></td>
		</tr>
		<tr>
			<td align="center">
				<span style="color:#000000;font-size:10px;">
					<?php print __('newsFormEditNewsTags'); ?> [i][/i], [b][/b], [u][/u], [s][/s], [url][/url], [url=opis][/url], [code][/code], [quote][/quote]
				<span>
			</td>
		</tr>
		<tr>
			<td align="left"><?php print $form['edit_news']; ?></td>
		</tr>
	</table>
	<?php print $form['form_close']; ?>
	<?php if(isset($msg_edit_news)): ?>
		<?php print Vf_Box_Helper::success($msg_edit_news); ?>
	<?php endif; ?>
	<?php if(isset($error_edit_news)): ?>
		<?php print Vf_Box_Helper::error($error_edit_news); ?>
	<?php endif; ?>
	<?php if(sizeof($errors) > 0): ?>
		<?php foreach($errors as $k => $error): ?>
			<?php print Vf_Box_Helper::error($error); ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>