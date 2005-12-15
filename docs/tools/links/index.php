<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_LINKS);

if (isset($_POST['edit']) && isset($_POST['link_id'])) {
	header('Location: edit.php?lid='.$_POST['link_id']);
	exit;
} else if (isset($_POST['delete']) && isset($_POST['link_id'])) {
	header('Location: delete.php?lid='.$_POST['link_id']);
	exit;
} else if (isset($_POST['view']) && isset($_POST['link_id'])) {
	$onload = 'window.open(\''.$_base_href.'links/index.php?view='.$_POST['link_id'].'\',\'link\');';
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require (AT_INCLUDE_PATH.'lib/links.inc.php');
$categories = get_link_categories();

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
	if ($parent_id) {
		$sql	= "SELECT * FROM ".TABLE_PREFIX."resource_links L, ".TABLE_PREFIX."resource_categories C WHERE L.CatID=C.CatID AND C.course_id=$_SESSION[course_id] AND L.CatID=$parent_id";
	} else {
		$sql	= "SELECT * FROM ".TABLE_PREFIX."resource_links L, ".TABLE_PREFIX."resource_categories C WHERE L.CatID=C.CatID AND C.course_id=$_SESSION[course_id]";  
	}
	$sql .= " ORDER BY $col $order";

	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {  ?>

	<tfoot>
	<tr>
		<td colspan="6"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /> <input type="submit" name="view" value="<?php echo _AT('view'); ?>" /></td>
	</tr>
	</tfoot>
	<tbody>
<?php do {
			$cat_name = '';			
			$sql_cat	= "SELECT CatName FROM ".TABLE_PREFIX."resource_categories WHERE CatID=".$row['CatID'];
			$result_cat = mysql_query($sql_cat, $db);
			$row_cat = mysql_fetch_assoc($result_cat);
			$cat_name = $row_cat['CatName'];
			 
	?>
			<tr onmousedown="document.form['m<?php echo $row['LinkID']; ?>'].checked = true;rowselect(this);" id="r_<?php echo $row['LinkID'];?>">
				<td width="10"><input type="radio" name="link_id" value="<?php echo $row['LinkID']; ?>" id="m<?php echo $row['LinkID']; ?>" /></td>
				<td><label for="m<?php echo $row['LinkID']; ?>"><?php echo AT_print($row['LinkName'], 'resource_links.LinkName'); ?></label></td>
				<td><?php echo AT_print($cat_name, 'resource_links.CatName'); ?></td>
				<td><?php echo AT_print($row['SubmitName'], 'resource_links.SubmitName'); ?></td>

				<td align="center"><?php 
						if($row['Approved']) { 
							echo _AT('yes1'); 
						} else { 
							echo _AT('no1'); 
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