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
authenticate(AT_PRIV_FORUMS);

if (isset($_GET['edit'])) {
	header('Location: '.$_base_href.'editor/edit_forum.php?fid='.$_GET['id']);
	exit;
} else if (isset($_GET['delete'])) {
	header('Location: '.$_base_href.'editor/delete_forum.php?fid='.$_GET['id']);
	exit;
}

require(AT_INCLUDE_PATH.'lib/forums.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printAll();

if ($_GET['col']) {
	$col = addslashes($_GET['col']);
} else {
	$col = 'title';
}

if ($_GET['order']) {
	$order = addslashes($_GET['order']);
} else {
	$order = 'asc';
}

$all_forums = get_forums($_SESSION['course_id']);

/*$sql	= "SELECT news_id, title, date FROM ".TABLE_PREFIX."news WHERE course_id=$_SESSION[course_id] $and ORDER BY $col $order";
$result = mysql_query($sql, $db); */

?>
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>

	<th scope="col"><?php echo _AT('title'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
	<?php foreach($all_forums['nonshared'] as $row): ?>
		<tr onmousedown="document.form['f<?php echo $row['forum_id']; ?>'].checked = true;">
			<td><input type="radio" name="id" value="<?php echo $row['forum_id']; ?>" id="f<?php echo $row['forum_id']; ?>"></td>
			<td><?php echo AT_print($row['title'], 'forums.title'); ?></td>
		</tr>
	<?php endforeach; ?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>