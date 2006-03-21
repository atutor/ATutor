<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006                                      */
/* Written by Greg Gay & Joel Kronenberg & Chris Ridpath        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ASSIGNMENTS);

if (isset($_GET['edit'])) {
	$_GET['assignment'] = intval($_GET['assignment']);
	header('Location: edit_assignment.php?id='. $_GET['assignment']);
	exit;
}
else if (isset($_GET['delete'])) {
	$_GET['assignment'] = intval($_GET['assignment']);
	header('Location: delete_assignment.php?id='. $_GET['assignment']);
	exit;
}
else if (isset($_GET['create'])){
	header('Location: add_assignment.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

// sort order of table
$orders = array('ASC' => 'DESC', 'DESC' => 'ASC');
$sort = 'title';
$sort_order = 'ASC';
if (isset ($_GET['sort'])){
	$sort = $addslashes ($_GET['sort']);
	if (($sort != 'title') && ($sort != 'due_date')){
		$sort = 'title';
	}
}
if (isset ($_GET['sort_order'])){
	$sort_order = $addslashes ($_GET['sort_order']);
	if (($sort_order != 'ASC') && ($sort_order != 'DESC')){
		$sort_order = 'ASC';
	}
}
$sql = "SELECT * FROM ".TABLE_PREFIX."assignments WHERE course_id=$_SESSION[course_id] ORDER BY '$sort' $sort_order";
$result = mysql_query($sql, $db);

?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<table class="data" style="width: 90%;" rules="cols">
<colgroup>
	<?php if ($sort == 'title'): ?>
		<col />
		<col class="sort" />
		<col span="5" />
	<?php elseif($sort == 'assign_to'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="4" />
	<?php elseif($sort == 'date_due'): ?>
		<col span="3" />
		<col class="sort" />
		<col span="3" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th>&nbsp;</th>
	<th scope="col"><a href="assignments/index_instructor.php?sort=title&sort_order=<?php echo $orders[$sort_order]; ?>"><?php echo _AT('title'); ?></a></th>
	<th scope="col"><?php echo _AT('assigned_to'); ?></th>
	<th scope="col"><a href="assignments/index_instructor.php?sort=date_due&sort_order=<?php echo $orders[$sort_order]; ?>"><?php echo _AT('due_date'); ?></a></th>
</tr>
</thead>
<?php if (($result != 0) && ($row = mysql_fetch_assoc($result))) : ?>
<tfoot>
<tr>
	<td colspan="6"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
				    <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
	<?php do { ?>
		<tr onmousedown="document.form['a<?php echo $row['assignment_id']; ?>'].checked = true; rowselect(this);" id="a_<?php echo $row['assignment_id']; ?>_0">
		
		<td><input type="radio" id="a<?php echo $row['assignment_id']; ?>" name="assignment" value="<?php echo $row['assignment_id']; ?>" 

		<?php // set first item as checked if nothing selected
		if (isset($_GET['assignment_id'])){
			if ($_GET['assignment_id'] == $row['assignment_id']){ 
				echo ' checked="checked"'; 
			} 
		}
		else {
			echo ' checked="checked"';
			$_GET['assignment_id'] = $row['assignment_id'];
		}
		?>/></td>

		<td><label for="a<?php echo $row['assignment_id']; ?>"><?php echo $row['title']; ?></label></td>

		<td><?php if($row['assign_to'] == '0'){echo _AT('all_students'); } else {
					$sql = "SELECT title FROM ".TABLE_PREFIX."groups_types WHERE type_id=$row[assign_to] AND course_id=$_SESSION[course_id]";
					$result = mysql_query($sql, $db);
					$type_row = mysql_fetch_assoc($result);
					echo $type_row['title']; } ?></td>

		<td><?php  if ($row['date_due'] == '0000-00-00 00:00:00'){
			echo _AT ('none');
		}else {
			echo AT_Date(_AT('forum_date_format'), $row['date_due'], AT_DATE_MYSQL_DATETIME);
		}?></td>
		</tr>
	<?php } while($row = mysql_fetch_assoc($result)); ?>
</tbody>
<?php else: ?>
	<tr>
		<td colspan="6"><em><?php echo _AT('none_found'); ?></em></td>
	</tr>
<?php endif; ?>
</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>