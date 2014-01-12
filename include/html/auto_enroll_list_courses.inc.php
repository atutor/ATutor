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
	$associate_string = validate_enid($_REQUEST["en_id"]);
	
	$cats	= array();
	$cats[0] = _AT('cats_uncategorized');
		
	$sql = "SELECT cat_id, cat_name FROM %scourse_cats";
	$rows_cats = queryDB($sql, array(TABLE_PREFIX));
	
	foreach($rows_cats as $row){
		$cats[$row['cat_id']] = $row['cat_name'];
	}

	$sql_courses = "SELECT aec.auto_enroll_courses_id auto_enroll_courses_id, 
	                       aec.course_id,
	                       c.cat_id,
	                       c.title title
	                  FROM %sauto_enroll a, %sauto_enroll_courses aec, %scourses c
	                 where a.associate_string='%s'
	                   and a.auto_enroll_id = aec.auto_enroll_id
	                   and aec.course_id = c.course_id";

	$rows_courses = queryDB($sql_courses, array(TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX, $associate_string));

	if (count($rows_courses) > 0)
	{
?>

<fieldset>
	<legend><?php echo _AT('course_to_auto_enroll'); ?></legend>
		<?php echo $table_title; ?>
	
	<div class="row">
		<table summary="" class="data" align="left" style="width: 100%;">
		
		<thead>
		<tr>
			<th scope="col"><?php echo _AT('title'); ?></th>
			<th scope="col"><?php echo _AT('category'); ?></th>
		</tr>
		</thead>

		<tbody>
<?php
	if(isset($rows_courses)):
	    foreach($rows_courses as $row_courses){
		?>
			<tr>
				<td><label for="m<?php echo $row_courses['auto_enroll_courses_id']; ?>"><?php echo $row_courses['title']; ?></label></td>
				<td><?php echo $cats[$row_courses['cat_id']]; ?></td>
			</tr>
		<?php } ?>
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
