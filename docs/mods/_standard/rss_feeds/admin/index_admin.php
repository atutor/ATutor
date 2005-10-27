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
// $Id: $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_RSS);


if ((isset($_GET['enable']) || isset($_GET['disable']) || isset($_GET['edit']) || isset($_GET['delete'])) && !isset($_GET['fid'])) {
	$msg->addError('NO_ITEM_SELECTED');
} else if (isset($_GET['edit'])) {
	header("Location:edit_feed.php?fid=".intval($_GET['fid']));
	exit;
} else if (isset($_GET['delete'])) {
	header("Location:delete_feed.php?fid=".intval($_GET['fid']));
	exit;
} else if (isset($_GET['preview'])) {
	header("Location:preview.php?fid=".intval($_GET['fid']));
	exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form">

<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('url'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3">
		<input type="submit" name="edit"    value="<?php echo _AT('edit'); ?>" />
		<input type="submit" name="delete"  value="<?php echo _AT('delete'); ?>" />
		<input type="submit" name="preview" value="<?php echo _AT('preview'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
<?php 
$sql	= "SELECT * FROM ".TABLE_PREFIX."feeds ORDER BY feed_id";
$result = mysql_query($sql, $db);

if (!($row = mysql_fetch_assoc($result))) { 
?>

	<tr>
		<td colspan="3"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php } else { ?>
	<?php do { 
		$title_file = AT_CONTENT_DIR.'feeds/'.$row['feed_id'].'_rss_title.cache'; ?>
		<tr onmousedown="document.form['f_<?php echo $row['feed_id']; ?>'].checked = true;">
			<td valign="top"><input type="radio" id="f_<?php echo $row['feed_id']; ?>" name="fid" value="<?php echo $row['feed_id']; ?>" /></td>
			<td><label for="f_<?php echo $row['feed_id']; ?>"><?php if (file_exists($title_file)) { readfile($title_file); } ?></label></td>
			<td><?php echo $row['url']; ?></td>
		</tr>
	<?php } while ($row = mysql_fetch_assoc($result)); ?>

<?php } ?>
</tbody>
</table>
</form>


<? require (AT_INCLUDE_PATH.'footer.inc.php'); ?>