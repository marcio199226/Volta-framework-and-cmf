<div class="box">
<h4><?php print __('registerAccountActivateLabel'); ?></h4>
	<?php if(isset($isActive)): ?>
		<?php print Vf_Box_Helper::error($isActive); ?>
	<?php endif; ?>
	<?php if(isset($isDisactive)): ?>
		<?php print Vf_Box_Helper::success($isDisactive); ?>
	<?php endif; ?>
</div>