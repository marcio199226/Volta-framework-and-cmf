<div class="box">
<?php 
	if(sizeof($poll) > 0):
		$infoPoll = $poll[0];
?>
<table align="center" style="width:100%;border:1px solid #000000;">
	<form method="post">
	<th style="background-color:#993333;padding:3px;"><span style="font-size:16px;color:#000000;"><?php print $infoPoll['title']; ?></span></th>
	<tr>
		<td>
			<table align="center">
				<tr align="left">
					<td width="200px">
						<span style="font-size:12px;color:#000000;">
							<?php print __('pollOverallVotes'); ?> <?php print $infoPoll['sum']; ?>
						</span>
					</td>
				</tr>
				<?php foreach($poll as $p): 
					$percent = ($p['sum'] == 0) ? 0: round($p['votes'] * 100 / $p['sum']);
					if($voteMode == 'single')
						$vote = '<input type="radio" name="pvote" value="'.$p['id_answer'].'">';
					else
						$vote = '<input type="checkbox" style="visibility: visible;" name="pvote[]" value="'.$p['id_answer'].'">';
				?>
				<tr>
					<?php if(!$hasVoted && !$hasExpired): ?>
						<td width="200px"><?php print $vote; ?> <?php print $p['answer']; ?></td>
					<?php else: ?>
						<td width="200px"><?php print $p['answer']; ?></td>
					<?php endif; ?>
					<?php if($percent > 0): ?>
						<td width="250px"><div style="text-align:center;background:green;height:20px;padding:5px;width:<?php print $percent; ?>%;"><span style="font-size:12px;color:#000000;"><?php print $percent; ?>%</span></div></td>
					<?php else: ?>
						<td width="250px">
							<div style="text-align:center;background:white;height:20px;padding:5px;width:100%;border:1px solid #000000;"><span style="font-size:12px;color:#000000;">0%</span></div>
						</td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
				<?php if(!$hasVoted && !$hasExpired): ?>
				<tr>
					<td>
						<input type="submit" name="submit_padd_vote" value="<?php print __('pollVoteButton'); ?>" style="height:30px;width:100px;padding:2px;background-color:#FFF8DC;border:1px solid #A9A9A9;">
					</td>
				</tr>
				<?php endif; ?>
			</table>
		</td>
	</tr>
	</form>
	<tr>
		<td align="center">
			<?php if($hasExpired): ?>
				<span style="font-size:12px;color:#000000;"><?php print __('pollExpired'); ?></span><Br />
			<?php endif; ?>
			<?php if($hasVoted): ?>
				<span style="font-size:12px;color:#000000;"><?php print __('pollAlreadyVoted') ?></span><Br />
			<?php endif; ?>
			<?php if(isset($success)): ?>
				<?php print Vf_Box_Helper::success($success); ?>
			<?php endif; ?>
			<?php if(isset($error_answer)): ?>
				<?php print Vf_Box_Helper::error($error_answer); ?>
			<?php endif; ?>
		</td>
	</tr>
</table>
<?php endif; ?>
</div>