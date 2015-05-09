{@ assets type="css" path="../components/news/assets/css/styles.css" @}
<?php if(sizeof($news) > 0): ?>
	<?php $base = Vf_Uri_Helper::base(true); ?>
	<a href="/Vf/index.php/rss"><img align="right" src="../components/news/assets/img/rss.jpg" /></a><Br />
	<table class="newsList">
	<?php foreach($news as $data): ?>
		<?php $linkToNews = Vf_Uri_Helper::url('news.read', array(':id:' => $data['id'])); ?>
		<div>
			<tr>
				<td><h3><a href="<?php print $linkToNews; ?>"><?php print $data['title']; ?></a></h3><td>
			</tr>
			<tr>
				<td>
					<p>
						<?php print Vf_BBCode_Helper::cutText(stripslashes($data['content']), 25);?>
					</p>
				</td>
			</tr>
			<tr>
				<td>
					<p class="linkReadNews">
						<a href="<?php print $linkToNews; ?>"><?php print __('newsReadLink'); ?></a>
					</p>
				</td>
			</tr>
			<tr>
				<td style="width: 100%;">
					<p id="aboutNewsDate" class="aboutNews">
						<?php print __('newsReadDate'); print $data['data']; ?>
						<?php print __('newsReadAuthor'); print $data['autor']; ?>
						<?php if(Vf_User_Helper::hasRole('news', 'delete') && Vf_User_Helper::hasRole('news_Admin', 'editNews')): ?>
							<a href="<?php print $base; ?>Home,Index,delete,<?php print $data['id']; ?>,{@csrf_token@}"><span><?php print __('newsReadDelete'); ?><span></a>
							<a href="<?php print $base; ?>Admin,News,editNews,<?php print $data['id']; ?>"><span><?php print __('newsReadEdit'); ?><span></a>
						<?php endif; ?>
					</p>
				</td>
			</tr>
		</div>
	<?php endforeach; ?>
		<tr>
			<td align="center"><?php print $pager; ?></td>
		</tr>
	</table>
<?php else: ?>
	<?php print Vf_Box_Helper::error(__('Brak wpisow w bazie')); ?>
<?php endif; ?>