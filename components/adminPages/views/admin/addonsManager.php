<h5><?php print $title; ?></h5>
<?php print $upload_package_form; ?>
<table align="center" width="100%">
	<tr style="background-color:#993300;color:#000000;">
		<th align="center"><?php print $table_title; ?></th>
	</tr>
</table>
<table align="center" width="100%">
	<?php if(sizeof($data) > 0): ?>
	<?php foreach($data as $CP): ?>
		<tr>
			<td>
				<span style="color:#000000;font-size:12px;"><strong>Nazwa:</strong> <?php print $CP; ?></span>
			</td>
			<td align="right">
				<a style="text-decoration:none;" href="/Vf/index.php/Admin,Index,<?php print $delete_action; ?>,<?php print $CP; ?>">Usun</a>
				<?php if($addonsManager != 'widgets'): ?>
					<a style="text-decoration:none;" href="/Vf/index.php/Admin,Index,<?php print $unistall_action; ?>,<?php print $CP; ?>">Odinstaluj</a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php else: ?>
	<?php print Vf_Box_Helper::error($error_msg); ?>
	<?php endif; ?>
	<?php if(isset($successUD)): ?>
	<?php print Vf_Box_Helper::success($successUD); ?>
	<?php endif; ?>
	<?php if(isset($errorUD)): ?>
	<?php print Vf_Box_Helper::error($errorUD); ?>
	<?php endif; ?>
</table>
<span style="font-size:10px;"><?php print $delete_explain; ?></span>
<Br />
<span style="font-size:10px;"><?php print $unistall_explain; ?></span>