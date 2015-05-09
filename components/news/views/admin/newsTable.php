<div class="box">
	<?php if(sizeof($news) > 0): ?>
	<h5>Spis wpisow.</h5>
	<table cellspacing="0" style="border:2px solid #000000;">
		<tr>
			<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:120px;">id</td>
			<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:120px;">Autor</td>
			<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:120px;">Tytul</td>
			<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:120px;">Data</td>
			<td style="background-color:#3299CC;color:#000000;font-size:16px;font-weight:bold;padding:2px;width:120px;">Akcje</td>
		</tr>
	<?php $i = 0; ?>
	<?php foreach($news as $tab): ?>
	<?php if(($i % 2) == 0): ?>
	<?php $color = '#ffffff'; ?>
	<?php else: ?>
	<?php $color = '#d5d5d5'; ?>
	<?php endif; ?>
		<tr style="background-color:<?php print $color; ?>;color:#000000;padding:2px;">
			<td style="padding:2px;"><?php print $tab['id']; ?></td>
			<td style="padding:2px;"><?php print $tab['autor']; ?></td>
			<td style="padding:2px;"><?php print $tab['tytul']; ?></td>
			<td style="padding:2px;"><?php print $tab['data']; ?></td>
			<td style="padding:2px;">
				<a href="/Vf/index.php/Admin,News,editNews,<?php print $tab['id']; ?>"><img src="../assets/images/edit.png" /></a>
				<a href="/Vf/index.php/Admin,News,delete,<?php print $tab['id']; ?>"><img src="../assets/images/delete.jpg" /></a>
			</td>
		</tr>
	<?php $i++; ?>
	<?php endforeach; ?>
	</table>
	<?php else: ?>
	<?php print Vf_Box_Helper::error('Nie ma zadnych wpisow.'); ?>
	<?php endif; ?>
</div>