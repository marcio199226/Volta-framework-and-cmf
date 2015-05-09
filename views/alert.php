{@ css_inline @}
	.boxAlert {
		padding: 5px;
		margin: 5px;
		background-color: #ffffff;
		color: #000000;
		border: 2px solid #FFFF00;
		text-aling: center;
	}
	
	.boxAlert table {
		text-align: center;
		margin: 0px auto;
	}

	.boxAlert td {
		border-bottom: none;
	}
	
	#boxAlertAccept {
		width: 100px;
		height: 30px;
		border: 1px solid #33ff33;
	}
	
	#boxAlertDecline {
		width: 100px;
		height: 30px;
		border: 1px solid #ff0033;
	}
{@ end @}

<div class="boxAlert">
	<?php 
		Vf_Form_Helper::open(); 
		Vf_Form_Helper::submit(array('name' => 'yes', 'value' => __('Tak'), 'id' => 'boxAlertAccept'));
		Vf_Form_Helper::submit(array('name' => 'no', 'value' => __('Nie'), 'id' => 'boxAlertDecline'));
		Vf_Form_Helper::close();
		$form = Vf_Form_Helper::get_form();
		print $form['form_open'];
	?>
	<table>
		<tr>
			<td><?php print $msg; ?></td>
		</tr>
		<tr>
			<td><?php print $form['yes']; ?> <?php print $form['no']; ?></td>
		</tr>
	</table>
	<?php print $form['form_close']; ?>
</div>