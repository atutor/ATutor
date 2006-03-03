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
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ASSIGNMENTS);

if (isset($_GET['edit'])) {
	$_GET['assignment'] = intval($_GET['assignment']);
	header('Location: add_assignment.php?id='. $_GET['assignment']);
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
?>

<?php
$sql = "SELECT * FROM ".TABLE_PREFIX."assignments WHERE course_id=$_SESSION[course_id] ORDER BY date_due";
$result = mysql_query($sql, $db);
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<table class="data" style="width: 90%;">
<thead>
<tr>
	<th>&nbsp;</th>
	<th><?php echo _AT('title'); ?></th>
	<th><?php echo _AT('assigned_to'); ?></th>
	<th><?php echo _AT('due_date'); ?></th>
	<th><?php echo _AT('accept_late_submissions'); ?></th>
	<th><?php echo _AT('allow_re_submissions'); ?></th>
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

		<td><?php if($row['assign_to'] == '0'){echo _AT('all_students'); } else {echo _AT('group_name_here');} ?></td>

		<td><?php  if ($row['date_due'] == AM_TIME_0){
			echo _AT ('none');
		}else {
			echo AT_Date(_AT('forum_date_format'), $row['date_due'], AT_DATE_MYSQL_DATETIME);
		}?></td>

		<td><?php  if ($row['date_cutoff'] == AM_TIME_0){
			echo _AT ('always');
		}
		else if ($row['date_cutoff'] == AM_TIME_1){
			echo _AT ('never');
		}
		else {
			echo AT_Date(_AT('forum_date_format'), $row['date_cutoff'], AT_DATE_MYSQL_DATETIME);
		}?></td>
		<td><?php if($row['multi_submit'] == '0'){echo _AT('no'); } else {echo _AT('yes');} ?></td>
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
