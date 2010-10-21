<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: auto_enroll_list_courses.php 7208 2008-01-09 16:07:24Z cindy $

// Lists all courses to auto enroll
if (isset($_REQUEST["en_id"]) && $_REQUEST["en_id"] <> "")
{
	$associate_string = $_REQUEST["en_id"];
	
	$cats	= array();
	$cats[0] = _AT('cats_uncategorized');
	
	$sql = "SELECT cat_id, cat_name FROM ".TABLE_PREFIX."course_cats";
	$result = mysql_query($sql,$db);
	while($row = mysql_fetch_array($result)) {
		$cats[$row['cat_id']] = $row['cat_name'];
	}
	
	$sql_courses = "SELECT aec.auto_enroll_courses_id auto_enroll_courses_id, 
	                       aec.course_id,
	                       c.cat_id,
	                       c.title title
	                  FROM " . TABLE_PREFIX."auto_enroll a, " . 
	                           TABLE_PREFIX."auto_enroll_courses aec, " . 
	                           TABLE_PREFIX ."courses c
	                 where a.associate_string='".$associate_string ."'
	                   and a.auto_enroll_id = aec.auto_enroll_id
	                   and aec.course_id = c.course_id";

	$result_courses = mysql_query($sql_courses, $db) or die(mysql_error());
	
	if (mysql_num_rows($result_courses) > 0)
	{
?>

<fieldset>
	<legend><?php echo _AT('course_to_auto_enroll'); ?></legend>
		<?php echo $table_title; ?>
	
	<div class="row">
		<table summary="" class="data" rules="cols" align="left" style="width: 100%;">
		
		<thead>
		<tr>
			<th scope="col"><?php echo _AT('title'); ?></th>
			<th scope="col"><?php echo _AT('category'); ?></th>
		</tr>
		</thead>

		<tbody>
<?php
	if ($row_courses = mysql_fetch_assoc($result_courses)): 
		do {
		?>
			<tr>
				<td><label for="m<?php echo $row_courses['auto_enroll_courses_id']; ?>"><?php echo $row_courses['title']; ?></label></td>
				<td><?php echo $cats[$row_courses['cat_id']]; ?></td>
			</tr>
		<?php } while ($row_courses = mysql_fetch_assoc($result_courses)); ?>
	<?php else: ?>
			<tr>
				<td colspan="3"><?php echo _AT('none_found'); ?></td>
			</tr>
	<?php endif; ?>
		</tbody>
		</table>
	</div>
	</legend>
</fieldset>

	<?php
	}

}
?>
