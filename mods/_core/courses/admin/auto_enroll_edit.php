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
//phpinfo();
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
//require(AT_INCLUDE_PATH.'../mods/_core/themes/lib/themes.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

function get_random_string ($minlength, $maxlength)
{
	$charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	
	if ($minlength > $maxlength) 
		$length = mt_rand ($maxlength, $minlength);
	else 
		$length = mt_rand ($minlength, $maxlength);
	
	for ($i=0; $i<$length; $i++) 
		$key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
	
	return $key;
}

// Main process
if (isset($_REQUEST['auto_enroll_id'])) $auto_enroll_id = $_REQUEST['auto_enroll_id'];
else $auto_enroll_id = 0;

if (isset($_POST['save']) || isset($_POST['add'])) 
{
	/* insert or update a category */
	$name       = trim($_POST['name']);
	$name  = $addslashes($name);
	$name = validate_length($name, 50);

	if (isset($_POST['add']) && !$_POST['add_ids'])
			$msg->addError('NO_ITEM_SELECTED');
			
	if (!$msg->containsErrors()) 
	{
		if ($auto_enroll_id == 0)
		{

			$sql = "INSERT INTO %sauto_enroll(associate_string, name)  VALUES ('%s', '%s')";
			$rows_inserted = queryDB($sql, array(TABLE_PREFIX, get_random_string(6, 10), $name));
	
			$auto_enroll_id = at_insert_id();		
	
			write_to_log(AT_ADMIN_LOG_INSERT, 'auto_enroll', $rows_inserted, $sqlout);
		}
		else
		{

			$sql = "UPDATE %sauto_enroll SET name = '%s' WHERE auto_enroll_id = %d";
			$rows_updated = queryDB($sql, array(TABLE_PREFIX, $name, $auto_enroll_id));
			
			write_to_log(AT_ADMIN_LOG_UPDATE, 'auto_enroll', $rows_updated, $sqlout);
		}
		
		if (isset($_POST['add'])) 
		{
			foreach ($_POST['add_ids'] as $elem) 
			{
                // unable to determine the purpose of this query
                // which always returns $row['cnt'] == 0, during queryDB() testing.
				$sql = "SELECT count(*) cnt FROM %sauto_enroll_courses
				         WHERE auto_enroll_id = %d
				           AND course_id = %d";
				$row = queryDB($sql, array(TABLE_PREFIX, $auto_enroll_id, $elem), TRUE);	
						
				if ($row["cnt"] == 0)
				{
	
					$sql = "INSERT INTO %sauto_enroll_courses (auto_enroll_id, course_id) VALUES (%d, %d)";
					$rows_inserted = queryDB($sql, array(TABLE_PREFIX, $auto_enroll_id,  $elem ));
								
					write_to_log(AT_ADMIN_LOG_INSERT, 'auto_enroll_courses', $rows_inserted, $sqlout);
				}
			}
		}
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		if (isset($_POST["save"]))
		{
			header('Location: auto_enroll.php');
			exit;
		}
	}
} 
else if (isset($_POST['delete'])) 
{
	if (!$_POST['delete_ids'])
		$msg->addError('NO_ITEM_SELECTED');
		
	if (!$msg->containsErrors()) 
	{
		foreach ($_POST['delete_ids'] as $elem) 
		{

			$sql = "DELETE FROM %sauto_enroll_courses WHERE auto_enroll_courses_id = %d";
			$rows_deleted = queryDB($sql, array(TABLE_PREFIX, $elem));
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		
		write_to_log(AT_ADMIN_LOG_DELETE, 'auto_enroll_courses', $rows_deleted, $sqlout);
	}
}
else if (isset($_POST['cancel'])) 
{
	$msg->addFeedback('CANCELLED');
	header('Location: auto_enroll.php');
	exit;
}

/* $categories[category_id] = array(cat_name, cat_parent, num_courses, [array(children)]) */
require(AT_INCLUDE_PATH.'header.inc.php'); 
$msg->printAll();

// existing auto enrollment
if ($auto_enroll_id > 0)
{
	$sql = "SELECT * FROM %sauto_enroll
	         WHERE auto_enroll_id = %d";
	$row = queryDB($sql, array(TABLE_PREFIX, $auto_enroll_id), TRUE);

}
?>

<form action ="<?php echo $_SERVER['PHP_SELF']; ?>?auto_enroll_id=<?php echo $auto_enroll_id; ?>" method="post" name="form">
<input type="hidden" name="form_submit" value="1" />

<div class="input-form" style="width:95%;">
	<div class="row">
		<h4><label for="name"><?php echo _AT('title'); ?></label><br /></h4>
		<input type="text" id="name" name="name" size="30" value="<?php echo htmlspecialchars($row['name']); ?>" />
	</div>

<?php
$existing_courses = array();

$cats	= array();
$cats[0] = _AT('cats_uncategorized');

$sql = "SELECT cat_id, cat_name FROM %scourse_cats";
$rows_cats = queryDB($sql, array(TABLE_PREFIX));

foreach($rows_cats as $row){
	$cats[$row['cat_id']] = $row['cat_name'];
}

// display existing courses if auto_enroll_id is given
// don't display this section when creating new record
?>
	<div class="row">
		<h4><?php echo _AT('course_to_auto_enroll'); ?><br /></h4>
	</div>

	<div class="row">
		<table summary="" class="data" align="left" style="width: 95%;">
		
		<thead>
		<tr>
			<th scope="col"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all_delete" title="<?php echo _AT('select_all'); ?>" name="selectall_delete" onclick="CheckAll('delete_ids[]', 'selectall_delete');" /></th>
			<th scope="col"><?php echo _AT('title'); ?></th>
			<th scope="col"><?php echo _AT('category'); ?></th>
		</tr>
		</thead>

		<tfoot>
		<tr>
			<td colspan="4">
				<div class="buttons" style="float:left">
				<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /> 
				</div>
			</td>
		</tr>
		</tfoot>

		<tbody>
<?php
$num_rows_courses = 0;

if ($auto_enroll_id > 0)
{

	$sql_courses = "SELECT auto_enroll_courses.auto_enroll_courses_id auto_enroll_courses_id, 
	                       auto_enroll_courses.course_id,
	                       courses.cat_id,
	                       courses.title title
	                  FROM %sauto_enroll_courses auto_enroll_courses, %scourses courses 
	                 where auto_enroll_courses.auto_enroll_id=%d
	                 and auto_enroll_courses.course_id = courses.course_id";

	$rows_courses = queryDB($sql_courses, array(TABLE_PREFIX, TABLE_PREFIX, $auto_enroll_id));
	$num_rows_courses = count($rows_courses);
	
	if ($num_rows_courses > 0)
	foreach($rows_courses as $row_courses){
		$existing_courses[] = $row_courses["course_id"];
	?>
			<tr onmousedown="document.form['m<?php echo $row_courses['auto_enroll_courses_id']; ?>'].checked = !document.form['m<?php echo $row_courses['auto_enroll_courses_id']; ?>'].checked; togglerowhighlight(this, 'm<?php echo $row_courses['auto_enroll_courses_id']; ?>');" id="rm<?php echo $row_courses['auto_enroll_courses_id']; ?>">
				<td width="10"><label for="tm<?php echo $row_courses['auto_enroll_courses_id']; ?>"><input type="checkbox" name="delete_ids[]" value="<?php echo $row_courses['auto_enroll_courses_id']; ?>" id="m<?php echo $row_courses['auto_enroll_courses_id']; ?>" onmouseup="this.checked=!this.checked" /></label></td>
				<td id="tm<?php echo $row_courses['auto_enroll_courses_id']; ?>"><?php echo $row_courses['title']; ?></td>
				<td><?php echo $cats[$row_courses['cat_id']]; ?></td>
			</tr>
	<?php } ?>
<?php 
}

if ($num_rows_courses == 0 || !isset($auto_enroll_id))
{ 
?>
			<tr>
				<td colspan="3"><?php echo _AT('none_found'); ?></td>
			</tr>
<?php 
}
?>
		</tbody>
	</table>
	</div>

	<div class="row">
		&nbsp;
	</div>
	
	<div class="row buttons" style="clear:left;">
		<input type="submit" name="save" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	</div>
</div>

<?php require("auto_enroll_filter_courses.php"); ?>

</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
