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
authenticate(AT_PRIV_ANNOUNCEMENTS);

if (isset($_GET['edit'])) {
	header('Location: '.$_base_href.'editor/edit_news.php?aid='.$_GET['id']);
	exit;
} else if (isset($_GET['delete'])) {
	header('Location: '.$_base_href.'editor/delete_news.php?aid='.$_GET['id']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printAll();

if ($_GET['col']) {
	$col = addslashes($_GET['col']);
} else {
	$col = 'date';
	$_GET['order'] = 'desc';
}

if ($_GET['order']) {
	$order = addslashes($_GET['order']);
} else {
	$order = 'asc';
}

$sql	= "SELECT news_id, title, date FROM ".TABLE_PREFIX."news WHERE course_id=$_SESSION[course_id] $and ORDER BY $col $order";
$result = mysql_query($sql, $db);

?>
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>

	<th scope="col"><?php echo _AT('title'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=title<?php echo SEP; ?>order=asc<?php echo SEP; ?>id=<?php echo $_GET['id']; ?>" title="<?php echo _AT('title_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('title_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=title<?php echo SEP; ?>order=desc<?php echo SEP; ?>id=<?php echo $_GET['id']; ?>" title="<?php echo _AT('title_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('title_descending'); ?>" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('date'); ?>  <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=date<?php echo SEP; ?>order=asc<?php echo SEP; ?>id=<?php echo $_GET['id']; ?>" title="<?php echo _AT('date_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('date_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=date<?php echo SEP; ?>order=desc<?php echo SEP; ?>id=<?php echo $_GET['id']; ?>" title="<?php echo _AT('date_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('date_descending'); ?>" border="0" height="7" width="11" /></a></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
	<?php while ($row = mysql_fetch_assoc($result)): ?>
		<tr onmousedown="document.form['n<?php echo $row['news_id']; ?>'].checked = true;">
			<td><input type="radio" name="id" value="<?php echo $row['news_id']; ?>" id="n<?php echo $row['news_id']; ?>"></td>

			<td><?php echo AT_print($row['title'], 'news.title'); ?></td>
			<td><?php echo AT_date(_AT('announcement_date_format'), $row['date'], AT_DATE_MYSQL_DATETIME); ?></td>
		</tr>
	<?php endwhile; ?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>