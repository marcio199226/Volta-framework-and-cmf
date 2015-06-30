{@ assets type="css" path="../assets/css/bootstrap.prefix.min.css" @}
<nav class="bs_">
	<ul class="bs_ pagination">
		<?php if($current['param'] > 1): ?>
			<li>
			  <a href="<?php print $first['link']; ?>" aria-label="Previous">
				<span aria-hidden="true">&laquo;</span>
			  </a>
			</li>
		<?php endif; ?>
		<?php if($prev['param'] >= 1): ?>
			<li><a href="<?php print $prev['link']; ?>"><?php print $prev['param']; ?></a></li>
		<?php endif; ?>
		<?php if($current['param'] == $page): ?>
			<li class="bs_ active"><a href="#"><?php print $current['param']; ?><span class="bs_ sr-only">(current)</span></a></li>
		<?php endif; ?>
		<?php if($next['param'] > 1): ?>
			<li><a href="<?php print $next['link']; ?>"><?php print $next['param']; ?></a></li>
		<?php endif; ?>
		<?php if($current['param'] < $last['param']): ?>
			<li>
			  <a href="<?php print $last['link']; ?>" aria-label="Next">
				<span aria-hidden="true">&raquo;</span>
			  </a>
			</li>
		<?php endif; ?>
	</ul>
</nav>