<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$page = 'tools';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CONTENT);

if (isset($_GET['edit'])) {
	header('Location: '.$_base_href.'editor/edit_content.php?cid='.$_GET['id']);
	exit;
} else if (isset($_GET['delete'])) {
	header('Location: '.$_base_href.'editor/delete_content.php?cid='.$_GET['id']);
	exit;
} else if (isset($_GET['view'])) {
	header('Location: '.$_base_href.'content.php?cid='.$_GET['id']);
	exit;
}


require(AT_INCLUDE_PATH.'header.inc.php');

if ($_GET['col']) {
	$col = addslashes($_GET['col']);
} else {
	$col = 'content_parent_id, ordering';
}

if ($_GET['order']) {
	$order = addslashes($_GET['order']);
} else {
	$order = 'asc';
}

if (!isset($_GET['sub_content'])) {
	$parent_id = 0;	
} else {
	$parent_id = intval($_GET['id']);
}


$all_content = $contentManager->getContent();

$content = $all_content[$parent_id];
//debug($all_content);
//debug($content);


function print_select($pid, $depth) {
	global $all_content;

	if (!isset($all_content[$pid])) {
		return;
	}

	foreach ($all_content[$pid] as $row) {
		if (isset($all_content[$row['content_id']])) {
			echo '<option value="'.$row['content_id'].'"';
			if ($_GET['id'] == $row['content_id']) {
				echo ' selected="selected"';
			}
			echo '>';
			echo str_repeat('&nbsp;', $depth * 5);
			echo $row['title'].'</option>';

			print_select($row['content_id'], $depth+1);
		}
	}
}

?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<h3>[ Select Parent Topic ]</h3>
	</div>

	<div class="row">
		<select name="id">
			<option value="0">[top level]</option>
			<?php
				print_select(0, 1);
			?>
		</select>
	</div>

	<div class="row buttons">
		<input type="submit" name="sub_content" value="[ view sub topics ]" />
	</div>
</div>
</form>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data" summary="" rules="cols" style="width: 90%;">
<thead>
<tr>
	<th scope="col">&nbsp;</th>

	<th scope="col">#</th>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('last_modified'); ?></th>
	<th scope="col">[# pages]</th>

</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
		<input type="submit" name="view" value="<?php echo _AT('view'); ?>" />
		<input type="submit" name="sub_content" value="<?php echo _AT('view_sub_topics'); ?>" />
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
	<?php foreach ($content as $row): ?>
		<tr onmousedown="document.form['c<?php echo $row['content_id']; ?>'].checked = true;">
			<td><input type="radio" name="id" value="<?php echo $row['content_id']; ?>" id="c<?php echo $row['content_id']; ?>"></td>

			<td><?php echo $row['ordering']; ?></td>

			<td><?php echo AT_print($row['title'], 'content.title'); ?></td>
			<td><?php echo AT_date(_AT('announcement_date_format'), $row['last_modified'], AT_DATE_MYSQL_DATETIME); ?></td>

			<td><?php echo count($all_content[$row['content_id']]); ?></td>
		</tr>
	<?php endforeach; ?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>