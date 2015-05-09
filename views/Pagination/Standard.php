{@ css_inline @}

table.paginationStandard {
	width: auto;
	float: right;
}

table.paginationStandard td {
	height: 20px;
	padding: 4px;
	background-color: #ffffff;
	border: 1px solid #000000;
}

{@ end @}

<table class="paginationStandard">
	<tr>
		<?php if($current['param'] > 1): ?>
			<td>
				<a href="<?php print $first['link']; ?>"><<</a>
			</td>
		<?php endif; ?>
		<?php if($prev['param'] >= 1): ?>
			<td>
				<a href="<?php print $prev['link']; ?>"><?php print $prev['param']; ?></a>
			</td>
		<?php endif; ?>
		<?php if($current['param'] == $page): ?>
		<td>
			<b><?php print $current['param']; ?></b>
		</td>
		<?php endif; ?>
		<?php if($next['param'] > 1): ?>
			<td>
				<a href="<?php print $next['link']; ?>"><?php print $next['param']; ?></a>
			</td>
		<?php endif; ?>
		<?php if($current['param'] < $last['param']): ?>
			<td>
				<a href="<?php print $last['link']; ?>">>></a>
			</td>
		<?php endif; ?>
	</tr>
</table>