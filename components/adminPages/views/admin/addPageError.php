<div class="box">
	<?php if(is_array($message)): ?>
	<?php foreach($message as $mex): ?>
		<?php print Vf_Box_Helper::error($mex); ?>
	<?php endforeach; ?>
	<?php else: ?>
		<?php print Vf_Box_Helper::error($message); ?>
	<?php endif; ?>
</div>