<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
define('AT_INCLUDE_PATH', '../include/');

require (AT_INCLUDE_PATH.'vitals.inc.php');

if(isset($_GET['view'])) {

	//add to the num hits
	$sql = "SELECT Url, hits FROM ".TABLE_PREFIX."resource_links WHERE LinkID=$_GET[view]";
	$results = mysql_query($sql,$db);

	if ($row = mysql_fetch_assoc($results)) { 
		$row['hits']++;
		$sql = "UPDATE ".TABLE_PREFIX."resource_links SET hits=$row[hits] WHERE LinkID=$_GET[view]";
		mysql_query($sql,$db);

		//redirect
		Header("Location: $row[Url]");
		exit;
	}
}

require (AT_INCLUDE_PATH.'lib/links.inc.php');

require (AT_INCLUDE_PATH.'header.inc.php');

$categories = get_link_categories();

if (!isset($_GET['cat_parent_id'])) {
	$parent_id = 0;	
} else {
	$parent_id = intval($_GET['cat_parent_id']);
}
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('select_cat'); ?></h3>
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
				echo '<option value="0"></option>';
				select_link_categories($categories, 0, 0, FALSE);
			?>
		</select>
	</div>

	<div class="row buttons">
		<input type="submit" name="cat_links" value="<?php echo _AT('cats_view_links'); ?>" />
	</div>
</div>
</form>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data static" summary="" rules="cols">
<thead>
<tr>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('category'); ?></th>

</tr>
</thead>

<tbody>
<?php
	$sql = "SELECT * FROM ".TABLE_PREFIX."resource_links L, ".TABLE_PREFIX."resource_categories C WHERE L.CatID=C.CatID AND C.course_id=$_SESSION[course_id] AND L.Approved=1";

	if ($parent_id) {
		$sql .= " AND L.CatID=$parent_id";
	}
	
	//$sql .= " ORDER BY $col $order";

	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) { 
		do {
			$cat_name = '';			
			$sql_cat	= "SELECT CatName FROM ".TABLE_PREFIX."resource_categories WHERE CatID=".$row['CatID'];
			$result_cat = mysql_query($sql_cat, $db);
			$row_cat = mysql_fetch_assoc($result_cat);
			$cat_name = $row_cat['CatName'];
			 
	?>
			<tr onmousedown="document.form['m<?php echo $row['LinkID']; ?>'].checked = true;">
				<td><a href="links/index.php?view=<?php echo $row['LinkID']; ?>" target="_new" title="<?php echo _AT('links_windows'); ?>"><?php echo AT_print($row['LinkName'], 'resource_links.LinkName'); ?></a></td>
				<td><?php echo AT_print($cat_name, 'resource_links.CatName'); ?></td>
			</tr>
<?php 
		} while ($row = mysql_fetch_assoc($result));					
} else {
?>
	<tr>
		<td colspan="5"><?php echo _AT('no_links'); ?></td>
	</tr>
<?php
}					
?>

</tbody>
</table>

<?php
require (AT_INCLUDE_PATH.'footer.inc.php');
?>
