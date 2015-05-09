<div>
	<h3><?php print __('Najnowsze wpisy'); ?></h3>
	<?php if(sizeof($news) > 0): ?>
		<?php $base = Vf_Uri_Helper::base(true); ?>
		<ul style="margin-top: 0em;margin-bottom: 0em;margin: 10px;">
		<?php foreach($news as $entry): ?>
			<li>
				<a href="<?php print $base ?><?php print $entry['id_news']; ?>" style="text-decoration:none;"><?php print $entry['title']; ?></a>
			</li>
		<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p style="margin: 10px;"><?php print __('Nie ma zadnych wpisow'); ?></p>
	<?php endif; ?>
</div>