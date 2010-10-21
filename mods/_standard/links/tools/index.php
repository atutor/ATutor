<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require (AT_INCLUDE_PATH.'../mods/_standard/links/lib/links.inc.php');

if (!manage_links()) {
	$msg->addError('ACCESS_DENIED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/links/index.php');
	exit;
}

if (isset($_POST['edit']) && isset($_POST['link_id'])) {
	header('Location: edit.php?lid='.$_POST['link_id']);
	exit;
} else if (isset($_POST['delete']) && isset($_POST['link_id'])) {
	header('Location: delete.php?lid='.$_POST['link_id']);
	exit;
} else if (isset($_POST['view']) && isset($_POST['link_id'])) {
	$onload = 'window.open(\''.AT_BASE_HREF.'mods/_standard/links/index.php?view='.$_POST['link_id'].'\',\'link\');';
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
}

$categories = get_link_categories(true);

require(AT_INCLUDE_PATH.'header.inc.php');

if ($_GET['col']) {
	$col = addslashes($_GET['col']);
} else {
	$col = 'LinkName';
}

if ($_GET['order']) {
	$order = addslashes($_GET['order']);
} else {
	$order = 'asc';
}

if (!isset($_GET['cat_parent_id'])) {
	$parent_id = 0;	
} else {
	$parent_id = intval($_GET['cat_parent_id']);
}

if ($_SESSION['groups']) {
	$groups = implode(',', $_SESSION['groups']);
} else {
	// not in any groups
	$groups = 0;
}

$auth = manage_links();

if ($auth == LINK_CAT_AUTH_ALL) {
	$sql = "SELECT * FROM ".TABLE_PREFIX."links L INNER JOIN ".TABLE_PREFIX."links_categories C USING (cat_id) WHERE ((owner_id=$_SESSION[course_id] AND owner_type=".LINK_CAT_COURSE.") OR (owner_id IN ($groups) AND owner_type=".LINK_CAT_GROUP."))";
} else if ($auth == LINK_CAT_AUTH_GROUP) {
	$sql = "SELECT * FROM ".TABLE_PREFIX."links L INNER JOIN ".TABLE_PREFIX."links_categories C USING (cat_id) WHERE owner_id IN ($groups) AND owner_type=".LINK_CAT_GROUP;
} else if ($auth == LINK_CAT_AUTH_COURSE) {
	$sql = "SELECT * FROM ".TABLE_PREFIX."links L INNER JOIN ".TABLE_PREFIX."links_categories C USING (cat_id) WHERE ((owner_id=$_SESSION[course_id] AND owner_type=".LINK_CAT_COURSE.") OR (owner_id IN ($groups) AND owner_type=".LINK_CAT_GROUP."))";
} 

if ($parent_id) {
	$sql .= " AND L.cat_id=$parent_id";
} 
$sql .= " ORDER BY $col $order";

$result = mysql_query($sql, $db);

if (!empty($categories)) {
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<h3><label for="category_parent"><?php echo _AT('select_cat'); ?></label></h3>
	</div>

	<div class="row">
		<select name="cat_parent_id" id="category_parent"><?php

				if ($parent_id) {
					$current_cat_id = $parent_id;
					$exclude = false; /* don't exclude the children */
				} else {
					$current_cat_id = $cat_id;
					$exclude = true; /* exclude the children */
				}

				echo '<option value="0">&nbsp;&nbsp;&nbsp; '._AT('cats_all').' &nbsp;&nbsp;&nbsp;</option>';
				select_link_categories($categories, 0, $current_cat_id, FALSE);
			?>
		</select>
	</div>

	<div class="row buttons">
		<input type="submit" name="cat_links" value="<?php echo _AT('cats_view_links'); ?>" />
	</div>
</div>
</form>
<?php } ?>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('category'); ?></th>
	<th scope="col"><?php echo _AT('submitted_by'); ?></th>
	<th scope="col"><?php echo _AT('approved'); ?></th>
	<th scope="col"><?php echo _AT('hit_count'); ?></th>
</tr>
</thead>

<?php
	if ($row = mysql_fetch_assoc($result)) {  ?>
	<tfoot>
	<tr>
		<td colspan="6"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /> <input type="submit" name="view" value="<?php echo _AT('view'); ?>" /></td>
	</tr>
	</tfoot>
	<tbody>
<?php do {
		if ($row['owner_type'] == LINK_CAT_GROUP) {
			$row['name'] = get_group_name($row['owner_id']);
		}
?>
		<tr onmousedown="document.form['m<?php echo $row['link_id']; ?>'].checked = true;rowselect(this);" id="r_<?php echo $row['link_id'];?>">
			<td width="10"><input type="radio" name="link_id" value="<?php echo $row['link_id'].'-'.$row['owner_type'].'-'.$row['owner_id']; ?>" id="m<?php echo $row['link_id']; ?>" /></td>
			<td><label for="m<?php echo $row['link_id']; ?>"><?php echo AT_print($row['LinkName'], 'resource_links.LinkName'); ?></label></td>
			<td><?php echo AT_print(htmlentities_utf8($row['name']), 'resource_links.CatName'); ?></td>
			<td><?php echo AT_print($row['SubmitName'], 'resource_links.SubmitName'); ?></td>

			<td align="center"><?php 
					if($row['Approved']) { 
						echo _AT('yes'); 
					} else { 
						echo _AT('no'); 
					} ?></td>
			<td align="center"><?php echo $row['hits']; ?></td>
		</tr>
<?php 
	} while ($row = mysql_fetch_assoc($result));					
} else {
?>
	<tbody>
	<tr>
		<td colspan="6"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php
}					
?>

</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>