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
// $Id$

$page = 'tools';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ANNOUNCEMENTS);

if (isset($_GET['edit'], $_GET['aid'])) {
	header('Location: '.AT_BASE_HREF.'editor/edit_news.php?aid='.intval($_GET['aid']));
	exit;
} else if (isset($_GET['delete'], $_GET['aid'])) {
	header('Location: '.AT_BASE_HREF.'editor/delete_news.php?aid='.intval($_GET['aid']));
	exit;
} else if ((isset($_GET['edit']) || isset($_GET['delete']))) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php');

$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('title' => 1, 'date' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'date';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'date';
} else {
	// no order set
	$order = 'desc';
	$col   = 'date';
}

$sql	= "SELECT news_id, title, date FROM ".TABLE_PREFIX."news WHERE course_id=$_SESSION[course_id] ORDER BY $col $order";
$result = mysql_query($sql, $db);

?>
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" summary="" rules="cols">
<colgroup>
	<?php if ($col == 'title'): ?>
		<col />
		<col class="sort" />
		<col />
	<?php elseif($col == 'date'): ?>
		<col span="2" />
		<col class="sort" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><a href="tools/news/index.php?<?php echo $orders[$order]; ?>=title"><?php echo _AT('title'); ?></a></th>
	<th scope="col"><a href="tools/news/index.php?<?php echo $orders[$order]; ?>=date"><?php echo _AT('date'); ?></a></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
	<?php if ($row = mysql_fetch_assoc($result)): ?>
		<?php do { ?>
			<tr onmousedown="document.form['n<?php echo $row['news_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['news_id']; ?>">
			
				<td><input type="radio" name="aid" value="<?php echo $row['news_id']; ?>" id="n<?php echo $row['news_id']; ?>" /></td>
				
				<td><label for="n<?php echo $row['news_id']; ?>"><?php echo AT_print($row['title'], 'news.title'); ?></label></td>
				<td><?php echo AT_date(_AT('announcement_date_format'), at_timezone($row['date']), AT_DATE_MYSQL_DATETIME); ?></td>
			</tr>
		<?php } while ($row = mysql_fetch_assoc($result)); ?>
	<?php else: ?>
		<tr>
			<td colspan="3"><?php echo _AT('none_found'); ?></td>
		</tr>
	<?php endif; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>