<div class="box">
	<h5>Dodaj nowa ankiete</h5>
	<?php
		Vf_Form_Helper::open('', 'post', true); 
		Vf_Form_Helper::text('ptitle_poll', 'Pytanie glosowania', 'height:30px;width:400px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
		Vf_Form_Helper::textarea('panswers_poll', 'Kazda odpowiedz musi byc rozdzielona za pomoca ;', 'height:150px;width:400px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;');
		Vf_Form_Helper::menu('expire', $expiresValues, false, 'height:30px;width:150px;padding:2px;background-color:#ffffff;border:1px solid #B8860B;', null ,true);
		Vf_Form_Helper::hidden('csrf_token', '{@csrf_token@}');
		Vf_Form_Helper::submit('padd_poll', 'Dodaj');
		Vf_Form_Helper::close();
		$form = Vf_Form_Helper::get_form();
		print $form['form_open'];
		print $form['csrf_token'];
	?>
	<table>
		<tr>
			<td>Pytanie:<Br /><?php print $form['ptitle_poll']; ?></td>
		</tr>
		<tr>
			<td>Odpowiedzi:<Br /><?php print $form['panswers_poll']; ?></td>
		</tr>
		<tr>
			<td>Czas ankiety:<Br /><?php print $form['expire']; ?></td>
		</tr>
		<tr>
			<td align="left"><?php print $form['padd_poll']; ?></td>
		</tr>
	</table>
	<?php print $form['form_close']; ?>
	<?php if(isset($success)): ?>
		<?php print Vf_Box_Helper::success($success); ?>
	<?php endif; ?>
	<?php if(isset($error_add_poll)): ?>
		<?php print Vf_Box_Helper::error($error_add_poll); ?>
	<?php endif; ?>
	<?php if(sizeof($errors) > 0): ?>
		<?php foreach($errors as $k => $error): ?>
			<?php print Vf_Box_Helper::error($error); ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>