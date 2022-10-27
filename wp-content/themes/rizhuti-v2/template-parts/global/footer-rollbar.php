<div class="rollbar">
	<?php $rollbar = _cao('site_footer_rollbar');
	if ( !empty($rollbar) && is_array($rollbar) ) : ?>
	<ul class="actions">
	<?php foreach ($rollbar as $item) : ?>
		<li>
			<?php $target = (empty($item['is_blank'])) ? '' : ' target="_blank"' ;?>
			<a<?php echo $target;?> href="<?php echo $item['href'];?>" rel="nofollow noopener noreferrer"><i class="<?php echo $item['icon'];?>"></i><span><?php echo $item['title'];?></span></a>
		</li>
		<?php endforeach;?>
	</ul>
	<?php endif;?>
	<div id="back-to-top" class="rollbar-item" title="返回顶部">
		<i class="fas fa-chevron-up"></i>
	</div>
</div>
