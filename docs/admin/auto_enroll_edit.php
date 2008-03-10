<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: auto_enroll_create.php 7208 2008-02-20 16:07:24Z cindy $
//phpinfo();
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
//require(AT_INCLUDE_PATH.'lib/themes.inc.php');
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
//	$cat_parent_id  = intval($_POST['cat_parent_id']);
	$name       = trim($_POST['name']);

	$name  = $addslashes($name);

	if (trim($name) == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	}
	$name = validate_length($name, 50);

	if (isset($_POST['add']))
		if (!$_POST['add_ids'])
			$msg->addError('NO_ITEM_SELECTED');
			
	if (!$msg->containsErrors()) 
	{
		if ($auto_enroll_id == 0)
		{
			$sql = "INSERT INTO ".TABLE_PREFIX."auto_enroll(associate_string, name) 
			        VALUES ('". get_random_string(6, 10) ."', '". $name ."')";
			$result = mysql_query($sql, $db) or die(mysql_error());
			$auto_enroll_id = mysql_insert_id($db);
			write_to_log(AT_ADMIN_LOG_INSERT, 'auto_enroll', mysql_affected_rows($db), $sql);
		}
		else
		{
			$sql = "UPDATE ".TABLE_PREFIX."auto_enroll
			           SET name = '". $name ."'
			         WHERE auto_enroll_id = ".$auto_enroll_id;
			
			$result = mysql_query($sql, $db);

			write_to_log(AT_ADMIN_LOG_UPDATE, 'auto_enroll', mysql_affected_rows($db), $sql);
		}
		
		if (isset($_POST['add'])) 
		{
			foreach ($_POST['add_ids'] as $elem) 
			{
				$sql = "SELECT count(*) cnt FROM ".TABLE_PREFIX."auto_enroll_courses
				         WHERE auto_enroll_id = ".$auto_enroll_id ."
				           AND course_id = ". $elem;
				$result = mysql_query($sql, $db) or die(mysql_error());
				$row = mysql_fetch_assoc($result);
				
				if ($row["cnt"] == 0)
				{
					$sql = "INSERT INTO ".TABLE_PREFIX."auto_enroll_courses (auto_enroll_id, course_id)
					        VALUES (" . $auto_enroll_id .", " . $elem . ")";
					$result = mysql_query($sql, $db) or die(mysql_error());
			
					write_to_log(AT_ADMIN_LOG_INSERT, 'auto_enroll_courses', mysql_affected_rows($db), $sql);
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
			$sql = "DELETE FROM ".TABLE_PREFIX."auto_enroll_courses
			        WHERE auto_enroll_courses_id = " . $elem;
//			print $sql."<br>";
			$result = mysql_query($sql, $db) or die(mysql_error());
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		
		write_to_log(AT_ADMIN_LOG_DELETE, 'auto_enroll_courses', mysql_affected_rows($db), $sql);
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
	$sql = "SELECT * FROM ".TABLE_PREFIX."auto_enroll
	         WHERE auto_enroll_id = " . $auto_enroll_id;

	$result = mysql_query($sql, $db) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
}
?>

<form action ="<?php echo $_SERVER['PHP_SELF']; ?>?auto_enroll_id=<?php echo $auto_enroll_id; ?>" method="post" name="form">
<input type="hidden" name="form_submit" value="1" />

<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>
		<h4><label for="name"><?php echo _AT('title'); ?></label><br /></h4>
		<input type="text" id="name" name="name" size="30" value="<?php echo htmlspecialchars($row['name']); ?>" />
	</div>

<? require("auto_enroll_add_courses.php"); ?>

	<div class="row buttons">
		<input type="submit" name="save" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
