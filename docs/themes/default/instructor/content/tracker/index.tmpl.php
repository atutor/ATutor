<?php global $contentManager;?>
<div class="toolcontainer">
<div class="paging">
	<ul>
	<?php for ($i=1; $i<=$this->num_pages; $i++): ?>
		<li>
			<?php if ($i == $this->page) : ?>
				<a class="current" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$this->page_string; ?>"><strong><?php echo $i; ?></strong></a>
			<?php else: ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$this->page_string; ?>"><?php echo $i; ?></a>
			<?php endif; ?>
		</li>
	<?php endfor; ?>
	</ul>
</div>

<table class="data" rules="cols" summary="">
<colgroup>
	<?php if ($this->col == 'total_hits'): ?>
		<col />
		<col class="sort" />
		<col span="4" />
	<?php elseif($this->col == 'unique_hits'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="3" />
	<?php elseif($this->col == 'average_duration'): ?>
		<col span="3" />
		<col class="sort" />
		<col span="2" />
	<?php elseif($this->col == 'total_duration'): ?>
		<col span="4" />
		<col class="sort" />
		<col />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col"><?php echo _AT('page'); ?></th>
	<th scope="col"><a href="mods/_standard/tracker/tools/index.php?<?php echo $orders[$order]; ?>=total_hits"><?php echo _AT('visits');             ?></a></th>
	<th scope="col"><a href="mods/_standard/tracker/tools/index.php?<?php echo $orders[$order]; ?>=unique_hits"><?php echo _AT('unique_visits');     ?></a></th>
	<th scope="col"><a href="mods/_standard/tracker/tools/index.php?<?php echo $orders[$order]; ?>=average_duration"><?php echo _AT('avg_duration'); ?></a></th>
	<th scope="col"><a href="mods/_standard/tracker/tools/index.php?<?php echo $orders[$order]; ?>=total_duration"><?php echo _AT('duration');       ?></a></th>
	<th scope="col"><?php echo _AT('details');       ?></th>
</tr>
</thead>
<tbody>
<?php if ($row = mysql_fetch_assoc($this->result)): ?>
	<?php do { ?>
		<tr onmousedown="document.location='<?php echo AT_BASE_HREF; ?>mods/_standard/tracker/tools/page_student_stats.php?content_id=<?php echo $row['content_id']; ?>'" title="<?php echo _AT('details'); ?>">
			<td><?php echo $contentManager->_menu_info[$row['content_id']]['title']; ?></td>
			<td><?php echo $row['total_hits'];       ?></td>
			<td><?php echo $row['unique_hits'];      ?></td>
			<td><?php echo $row['average_duration']; ?></td>
			<td><?php echo $row['total_duration'];   ?></td>
			<td><a href="mods/_standard/tracker/tools/page_student_stats.php?content_id=<?php echo $row['content_id']; ?>"><?php echo _AT('details'); ?></a></td>
		</tr>
	<?php } while ($row = mysql_fetch_assoc($this->result)); ?>
<?php else: ?>
	<tr>
		<td colspan="6"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</div>