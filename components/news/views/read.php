{@ assets type="css" path="../components/news/assets/css/styles.css" @}
<div class="boxNewsRead">
	<h2><?php print $news['title']; ?></h2>
		<p class="boxNewsReadContent">
			<?php print Vf_BBCode_Helper::parse($news['content']); ?>
		</p>
		<div class="boxNewsReadAbout">
			<p>
				<?php print __('newsReadDate'); print $news['data']; ?>
				<Br />
				<?php print __('newsReadAuthor'); print $news['autor']; ?>
				<?php if(Vf_User_Helper::hasRole('news', 'delete') && Vf_User_Helper::hasRole('news_Admin', 'editNews')): ?>
					<a href="/Vf/index.php/Home,Index,delete,<?php print $news['id']; ?>,{@csrf_token@}"><span><?php print __('newsReadDelete'); ?><span></a>
					<a href="/Vf/index.php/Admin,News,editNews,<?php print $news['id']; ?>"><span><?php print __('newsReadEdit'); ?><span></a>
				<?php endif; ?>
			</p>
		</div>
</div>
