<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; } ?>

<div style="width: 100%; margin-top: -5px;">
	<?php foreach ($this->home_links as $link): ?>
		<a href="<?php echo $link['url']; ?>" class="home-link"><img src="<?php echo $link['img']; ?>" alt="" class="img-size-home" /><br /><?php echo $link['title']; ?></a>
	<?php endforeach; ?>
</div>

<h2 class="page-title"><?php echo _AT('announcements'); ?></h2>
<?php if ($this->announcements): ?>
	<?php foreach ($this->announcements as $item): ?>
		<div class="news">
			<h3><?php echo $item['title']; ?></h3>
			<p><span class="date"><?php echo $item['date']; ?></span> &nbsp; <?php echo $item['body']; ?></p>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<p><em><?php echo _AT('no_announcements'); ?></em></p>
<?php endif; ?>

<?php if ($this->num_pages > 1): ?>
	<?php echo _AT('page'); ?>: | 
	<?php for ($i=1; $i<=$this->num_pages; $i++): ?>
		<?php if ($i == $this->current_page): ?>
			<strong><?php echo $i; ?></strong>
		<?php else: ?>
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i; ?>"><?php echo $i; ?></a>
		<?php endif; ?>
		 | 
	<?php endfor; ?>
<?php endif; ?>