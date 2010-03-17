<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index.php 7208 2008-01-09 16:07:24Z greg $

$page = 'tools';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_FORUMS);

if (isset($_GET['edit'], $_GET['id'])) {
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/edit_forum.php?fid='.intval($_GET['id']));
	exit;
} else if (isset($_GET['delete'], $_GET['id'])) {
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/delete_forum.php?fid='.intval($_GET['id']));
	exit;
} else if (isset($_GET['edit']) || isset($_GET['delete'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');


$all_forums = get_forums($_SESSION['course_id']);
?>
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('description'); ?></th>
	<th scope="col"><?php echo _AT('allow_editing'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<?php if ($all_forums['nonshared']): ?>
	<?php foreach($all_forums['nonshared'] as $row): ?>
		<tr onmousedown="document.form['f<?php echo $row['forum_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['forum_id']; ?>">
			<td width="10"><input type="radio" name="id" value="<?php echo $row['forum_id']; ?>" id="f<?php echo $row['forum_id']; ?>" /></td>
			<td><label for="f<?php echo $row['forum_id']; ?>"><?php echo AT_print(htmlentities_utf8($row['title']), 'forums.title'); ?></label></td>
			<td><?php echo AT_print(htmlentities_utf8($row['description']), 'forums.description'); ?></td>
			<td>
				<?php if (!$row['mins_to_edit']): ?>
					<?php echo _AT('no'); ?>
				<?php else: ?>
					<?php echo  _AT('minutes', $row['mins_to_edit']); ?>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>