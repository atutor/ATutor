<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CONTENT);

if (isset($_GET['edit'], $_GET['id'])) {
	header('Location: '.$_base_href.'editor/edit_content.php?cid='.intval($_GET['id']));
	exit;
} else if (isset($_GET['delete'], $_GET['id'])) {
	header('Location: '.$_base_href.'editor/delete_content.php?cid='.intval($_GET['id']));
	exit;
} else if (isset($_GET['view'], $_GET['id'])) {
	header('Location: '.$_base_href.'content.php?cid='.intval($_GET['id']));
	exit;
} else if (isset($_GET['usage'], $_GET['id'])) {
	header('Location: '.$_base_href.'tools/tracker/page_student_stats.php?content_id='.intval($_GET['id']));
	exit;
} else if (!isset($_GET['id']) && !isset($_GET['sub_content']) && (isset($_GET['usage']) || isset($_GET['view']) || isset($_GET['delete']) || isset($_GET['edit']))) {
	$msg->addError('NO_ITEM_SELECTED');
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
		<h3><label for="id"><?php echo _AT('select_parent_topic'); ?></label></h3>
	</div>

	<div class="row">
		<select name="id" id="id">
			<option value="0"><?php echo _AT('top_level'); ?></option>
			<?php
				print_select(0, 1);
			?>
		</select>
	</div>

	<div class="row buttons">
		<input type="submit" name="sub_content" value="<?php echo _AT('view_sub_topics'); ?>" />
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
	<th scope="col"><?php echo _AT('num_pages'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
		<input type="submit" name="view" value="<?php echo _AT('view'); ?>" />
		<input type="submit" name="usage" value="<?php echo _AT('usage'); ?>" />
		<input type="submit" name="sub_content" value="<?php echo _AT('sub_topics'); ?>" />
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
	<?php if (!empty($content)): ?>
		<?php foreach ($content as $row): ?>
			<tr onmousedown="document.form['c<?php echo $row['content_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['content_id']; ?>">
				<td><input type="radio" name="id" value="<?php echo $row['content_id']; ?>" id="c<?php echo $row['content_id']; ?>"></td>
				<td><?php echo $row['ordering']; ?></td>
				<td><label for="c<?php echo $row['content_id']; ?>"><?php echo AT_print($row['title'], 'content.title'); ?></label></td>
				<td><?php echo count($all_content[$row['content_id']]); ?></td>
			</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="5"><?php echo _AT('none_found'); ?></td>
		</tr>
	<?php endif; ?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>