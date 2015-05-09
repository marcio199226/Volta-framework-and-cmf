<?php if(!isset($error)): ?>
<?php 
	//zwraca tablice z plugininami do wlaczenia/wylaczenia dla aktualnego pluginu z warunkiem dependsOn/dependsOff
	$depends = function($pluginName, $case) use($plugins) {
		$plugs = array();
			if(isset($plugins[$pluginName][$case]))
				foreach($plugins[$pluginName][$case] as $depends)
					foreach($depends as $names)
						if($names != 'empty')
							$plugs[] = $names;
		return $plugs;
	};
	
	//zwraca tablice ktora podaje nam stan pluginow na podstawie tablicy zwracanej przez funkcje $depends() czyli tylko dla plugnow ktore sa uzaleznione od innych plugnow
	$setStateOfDependicies = function($pluginDepends, $plugins, $when) {
		if(sizeof($pluginDepends) > 0)
			foreach($pluginDepends as $name)
				if($when == 'off')
					$states[$name] = ($plugins[$name]['active'] == 0 ||  $plugins[$name]['active'] == 2) ? 'off' : 'on';
				else if($when == 'on')
					$states[$name] = ($plugins[$name]['active'] == 1) ? 'on' : 'off';
		return $states;
	};

	//sprawdza czy wszystkie zalezne pluginy maja stan on/off zalezy od ustawien jesli tak zwraca true
	$checkState = function($dependicies, $states, $when) {
		if(is_array($dependicies))
			foreach($dependicies as $pluginName => $state)
				$state = (in_array($pluginName, $states) && $state == $when) ? true : false;
		return $state;
	};
?>
<h5>Zarzadzanie plugin-ami dla komponentu <?php print $component; ?></h5>
<table width="100%">
	<?php if(sizeof($plugins) > 0): ?>
		<?php foreach($plugins as $name => $plugin): ?>
			<tr>
				<td>
					<span style="color:#000000;font-size:12px;"><strong>Nazwa:</strong> <?php print $plugin['name']; ?></span>
				</td>
			</tr>
			<tr>
				<td>
					<span style="color:#000000;font-size:12px;"><strong>Wersja:</strong> <?php print $plugin['version']; ?></span>
				</td>
			</tr>
			<tr>
				<td>
					<span style="color:#000000;font-size:12px;"><?php print $plugin['description']; ?></span>
				</td>
			</tr>
			<?php 
				$dependsOff = $depends($name, 'dependsOff');
				$dependsOn = $depends($name, 'dependsOn');
				$dependiciesOff = $setStateOfDependicies($dependsOff, $plugins, 'off');
				$dependiciesOn = $setStateOfDependicies($dependsOn, $plugins, 'on');			
			?>
			<?php if($checkState($dependiciesOff, $dependsOff, 'off') === false): ?>
				<?php 
					$link = Vf_Box_Helper::info('Wylacz najpierw pluginy: '.implode(',', $depends($name, 'dependsOff')));
				?>
			<?php elseif($checkState($dependiciesOn, $dependsOn, 'on') === false): ?>
				<?php 
					$link = Vf_Box_Helper::info('Wlacz najpierw pluginy: '.implode(',', $depends($name, 'dependsOn')));
				?>
			<?php else: ?>
				<?php if($plugin['active'] == 0): ?>
					<?php $link = '<a style="text-decoration:none;" href="/Vf/index.php/Admin,Index,activePlugin,'.$plugin['id'].'"><span style="color:#00ff00;font-size:12px;">Wlacz</span></a>'; ?>
				<?php elseif($plugin['active'] == 1): ?>
					<?php $link = '<a style="text-decoration:none;" href="/Vf/index.php/Admin,Index,disactivePlugin,'.$plugin['id'].'"><span style="color:#ff0000;font-size:12px;">Wylacz</span></a>'; ?>
				<?php elseif($plugin['active'] == 2): ?>
					<?php $link = '<a style="text-decoration:none;" href="/Vf/index.php/Admin,Index,addPlugin,'.$plugin['page'].','.$plugin['module'].','.$plugin['component'].','.$plugin['name'].'"><span style="color:#00ff00;font-size:12px;">Wlacz</span></a>'; ?>
				<?php endif; ?>
			<?php endif; ?>
			<?php if(sizeof($pluginsActions[$plugin['name']]) > 0): ?>
				<tr>
					<td>
						<ol style="list-style-type: square;margin-left: 25px;">
							<?php foreach($pluginsActions[$plugin['name']] as $description): ?>
								<li><?php print $description; ?></li>
							<?php endforeach; ?>
						</ol>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<td align="right"><?php print $link; ?></td>
			</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<?php print Vf_Box_Helper::error('Ten komponent nie posiada zadnych plugin-ow'); ?>
	<?php endif; ?>
</table>
<?php else: ?>
	<?php print Vf_Box_Helper::error($error); ?>
<?php endif; ?>