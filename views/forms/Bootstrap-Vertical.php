{@ css_inline @}
	.bs_ .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9, .col-xs-10, .col-xs-11, .col-xs-12 { 
		float: none !important;
	}
{@ end @}
<div class="bs_ container">
	<?php print Vf_Html_Helper::tag('form', true, $form->getAttributes(true)); ?>
		<?php if ($form->getFormName()): ?>
			<fieldset>
				<legend><?php print $form->getFormName(); ?></legend>
		<?php endif; ?>
		<?php foreach ($form->getWidgets() as $widget): ?>
			<?php if ($form->isTextBox($widget) || $form->isPasswordBox($widget) || $form->isTextarea($widget) || $form->isSelect($widget)): ?>
				<div class="bs_ form-group">
					<?php $col = ($widget->getSetting('colSize')) ? $widget->getSetting('colSize') : 'col-xs-6'; ?>
					<div class="bs_ <?php print $col; ?>">
						<?php $forLabel = ($widget->getAttribute('id')) ? $widget->getAttribute('id') : $widget->getFieldName(); ?>
						<label for="<?php print $forLabel; ?>"><?php print $widget->getSetting('label'); ?></label>
						<?php print $widget->display(); ?>
						<?php if ($widget->getSetting('help')): ?>
							<span class="help-block"><?php print $widget->getSetting('help'); ?></span>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($form->isCheckBox($widget)): ?>
				<div class="bs_ form-group">
					<?php $col = ($widget->getSetting('colSize')) ? $widget->getSetting('colSize') : 'col-xs-6'; ?>
					<div class="bs_ <?php print $col; ?>">
						<label class="bs_ control-label"><?php print $widget->getSetting('labelGroup') ?></label>
						<?php if(!is_array($widget->display())): ?>
							<div class="bs_ checkbox">
							  <label>
								<?php print $widget->display(); ?>
							  </label>
							</div>
						<?php else: ?>
							<?php foreach($widget->display() as $checkbox): ?>
								<div class="bs_ checkbox">
								  <label>
									<?php print $checkbox; ?>
								  </label>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if ($widget->getSetting('help')): ?>
							<span class="help-block"><?php print $widget->getSetting('help'); ?></span>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($form->isRadio($widget)): ?>
				<div class="bs_ form-group">
					<?php $col = ($widget->getSetting('colSize')) ? $widget->getSetting('colSize') : 'col-xs-6'; ?>
					<div class="bs_ <?php print $col; ?>">
						<label class="bs_ control-label"><?php print $widget->getSetting('labelGroup') ?></label>
						<?php if(!is_array($widget->display())): ?>
							<div class="bs_ radio">
							  <label>
								<?php print $widget->display(); ?>
							  </label>
							</div>
						<?php else: ?>
							<?php foreach($widget->display() as $checkbox): ?>
								<div class="bs_ radio">
								  <label>
									<?php print $checkbox; ?>
								  </label>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if ($widget->getSetting('help')): ?>
							<span class="help-block"><?php print $widget->getSetting('help'); ?></span>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($form->isButton($widget)): ?>
				<div style="clear: both;margin-bottom:15px;"></div>
			<?php endif; ?>
			<?php if ($form->isButton($widget) || $form->isHidden($widget)): ?>
				<div class="bs_ form-group">
					<?php $col = ($widget->getSetting('colSize')) ? $widget->getSetting('colSize') : 'col-xs-12'; ?>
					<div class="bs_ <?php print $col; ?>">
						<?php print $widget->display(); ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if ($form->getFormName()): ?>
			</fieldset>
		<?php endif; ?>
	<?php print Vf_Html_Helper::close('form'); ?>
</div>
