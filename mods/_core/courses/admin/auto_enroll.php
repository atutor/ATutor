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
// $Id: auto_enroll.php 7208 2008-02-20 16:07:24Z cindy $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/themes/lib/themes.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['delete'], $_POST['auto_enroll_id'])) {
	header('Location: auto_enroll_delete.php?auto_enroll_id='.$_POST['auto_enroll_id']);
	exit;
} else if (isset($_POST['edit'], $_POST['auto_enroll_id'])) {
	header('Location: auto_enroll_edit.php?auto_enroll_id='.$_POST['auto_enroll_id']);
	exit;
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

?>
<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data" rules="cols" align="center" style="width: 95%;">

<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('name'); ?></th>
	<th scope="col"><?php echo _AT('courses'); ?></th>
	<th scope="col"><?php echo _AT('url'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4">
		<div class="row buttons">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /> 
		</div>
	</td>
</tr>
</tfoot>
<tbody>
<?php
$sql	= "SELECT * FROM ".TABLE_PREFIX."auto_enroll ae ORDER BY name";
$result = mysql_query($sql, $db) or die(mysql_error());

if ($row = mysql_fetch_assoc($result)): ?>
	<?php
	do {
		$courses = "";
		$sql_courses = "SELECT c.title FROM ". TABLE_PREFIX."auto_enroll_courses aec, " . 
		                         TABLE_PREFIX ."courses c 
		          WHERE aec.auto_enroll_id = ". $row["auto_enroll_id"] . "
		            AND aec.course_id = c.course_id
		          ORDER BY c.title";

		$result_courses = mysql_query($sql_courses, $db) or die(mysql_error());

		while ($row_courses = mysql_fetch_assoc($result_courses))
			$courses .= $row_courses["title"] . "<br>";
	?>
		<tr onmousedown="document.form['m<?php echo $row['auto_enroll_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['auto_enroll_id']; ?>">
			<td width="10"><input type="radio" name="auto_enroll_id" value="<?php echo $row['auto_enroll_id']; ?>" id="m<?php echo $row['auto_enroll_id']; ?>" /></td>
			<td><label for="m<?php echo $row['auto_enroll_id']; ?>"><?php if ($row['name']=="") echo _AT('na'); else echo $row['name']; ?></label></td>
			<td><?php echo $courses; ?></td>
			<td nowrap><?php echo $_base_href. "registration.php?en_id=". $row['associate_string']; ?></td>
		</tr>
	<?php } while ($row = mysql_fetch_assoc($result)); ?>
<?php else: ?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>

</form>


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>