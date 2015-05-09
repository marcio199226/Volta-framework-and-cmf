<?php $base = Vf_Uri_Helper::base(true); ?>
<div>
<h5>Komponenty:</h5>
<table>
<?php foreach($cmpAdmin as $cmpName => $cmpPA): ?>
	<?php if($cmpPA == 0): ?>
		<tr>
			<td><strong><?php print $cmpName; ?>:</strong><td>
			<td><?php print Vf_Box_Helper::error('Ten komponent nie posiada czesci administracyjnej.'); ?></td>
			<td><a href="/Vf/index.php/Admin,Index,editConfig,<?php print $cmpName; ?>" style="text-decoration:none;"><span style="font-size:10px;">[Konfiguracja]</span></a></td>
		</tr>
	<?php else: ?>
		<tr>
			<td><strong><?php print $cmpName; ?>:</strong><td>
			<td><a href="<?php print $base; ?>Admin,<?php print $cmpName; ?>" style="text-decoration:none;">[Czesc administracyjna]</a></td>
			<td><a href="<?php print $base; ?>Admin,Index,editConfig,<?php print $cmpName; ?>" style="text-decoration:none;"><span style="font-size:10px;">[Konfiguracja]</span></a></td>
		</tr>
	<?php endif; ?>
<?php endforeach; ?>
</table>
<h5>Dodaj podstrone i jej komponenty</h5>
<?php
	Vf_Form_Helper::open();
	Vf_Form_Helper::menu('page', $fronts, false, 'height:30px;width:120px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
	Vf_Form_Helper::text('module', 'Index', 'height:25px;width:120px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
	Vf_Form_Helper::menu('place', $places, false, 'height:30px;width:120px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
	Vf_Form_Helper::menu('component', $components, false, 'height:30px;width:120px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
	Vf_Form_Helper::submit('add_page', 'Dodaj');
	Vf_Form_Helper::close();
	$form_add = Vf_Form_Helper::get_form();
	print $form_add['form_open'];
?>
	<table cellspacing="0" style="width:100%;border:2px solid #000000;">
		<tr>
			<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:140px;">Strona</td>
			<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:140px;">Modul</td>
			<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:140px;">Miejsce</td>
			<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:140px;">Komponent</td>
		</tr>
		<tr align="center" style="height:40px;padding:2px;">
			<td><?php print $form_add['page']; ?></td>
			<td><?php print $form_add['module']; ?></td>
			<td><?php print $form_add['place']; ?></td>
			<td><?php print $form_add['component']; ?></td>
		</tr>
		<tr style="height:40px;">
			<td align="center"><?php print $form_add['add_page']; ?></td>
		</tr>
	</table>
	<?php print $error_view; ?>
	<?php print $form_add['form_close']; ?>
	<?php if(sizeof($pages) > 0): ?>
		<?php foreach($pages as $key => $pageName): ?>
			<h5 style="color:#000000;">Zarzadzaj istniejacymi stronami dla: <?php print($key); ?></h5>
			<table cellspacing="0" style="border:2px solid #000000;">
				<tr>
					<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:120px;">Strona</td>
					<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:120px;">Modul</td>
					<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:120px;">Miejsce</td>
					<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:120px;">Komponent</td>
					<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:120px;">Akcje</td>
				</tr>
			<?php $i = 0; ?>
			<?php foreach($pageName as $page): ?>
				<?php if(($i % 2) == 0): ?>
					<?php $color = '#ffffff'; ?>
				<?php else: ?>
					<?php $color = '#d5d5d5'; ?>
				<?php endif; ?>
				<tr style="background-color:<?php print $color; ?>;color:#000000;padding:2px;">
					<td style="padding:2px;"><?php print $page['page']; ?></td>
					<td style="padding:2px;"><?php print $page['module']; ?></td>
					<td style="padding:2px;"><?php print $page['place']; ?></td>
					<td style="padding:2px;"><?php print $page['component']; ?></td>
					<td style="padding:2px;">
						<a href="<?php print $base; ?>Admin,Index,editPage,<?php print $page['id']; ?>"><img src="../components/adminPages/assets/images/edit.png" /></a>
						<a href="<?php print $base; ?>Admin,Index,deletePage,<?php print $page['id']; ?>"><img src="../components/adminPages/assets/images/delete.jpg" /></a>
						<a href="<?php print $base; ?>Admin,Index,managePlugins,<?php print $page['page']; ?>,<?php print $page['module']; ?>,<?php print $page['component']; ?>"><img src="../components/adminPages/assets/images/plugins.png" /></a>
					</td>
				</tr>
				<?php $i++; ?>
			<?php endforeach; ?>
			</table>
		<?php endforeach; ?>
	<?php endif; ?>
</div>