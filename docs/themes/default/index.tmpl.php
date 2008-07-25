<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; } ?>

<?php if ($this->banner): ?><?php echo $this->banner; ?><?php endif; ?>

<div style="width: 100%; margin-top: -5px; float:left;">
	<ul id="home-links">
	<?php foreach ($this->home_links as $link): ?>
		<li><a href="<?php echo $link['url']; ?>"><img src="<?php echo $link['img']; ?>" alt="" class="img-size-home" border="0" /><?php echo $link['title']; ?></a></li>
	<?php endforeach; ?>
	</ul>
</div>

<?php if ($this->announcements): ?>
<h2 class="page-title"><?php echo _AT('announcements'); ?></h2>
	<?php foreach ($this->announcements as $item): ?>
		<div class="news">
			<h3><?php echo $item['title']; ?></h3>
			<p><span class="date"><?php echo $item['date'] .' '. _AT('by').' ' . $item['author']; ?></span> &nbsp; <?php echo $item['body']; ?></p>
		</div>
	<?php endforeach; ?>

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
<?php endif; ?>
