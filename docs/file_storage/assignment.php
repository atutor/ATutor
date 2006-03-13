<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: move.php 5954 2006-03-09 17:43:07Z joel $

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');

$owner_type = abs($_REQUEST['ot']);
$owner_id   = abs($_REQUEST['oid']);
$owner_arg_prefix = '?ot='.$owner_type.SEP.'oid='.$owner_id. SEP;
if (!($owner_status = fs_authenticate($owner_type, $owner_id)) || !query_bit($owner_status, WORKSPACE_AUTH_WRITE)) { 
	exit('NOT AUTHENTICATED');
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php'.$owner_arg_prefix.'folder='.abs($_POST['folder']));
	exit;
} else if (isset($_POST['submit'])) {
	// fs_authenticate(WORKSPACE_ASSIGNMENT, $_POST['assignment'])
	// authenticate the assignment ID

	$_POST['assignment'] = abs($_POST['assignment']);
	foreach ($_POST['files'] as $file) {
		$file = abs($file);
		$sql = "INSERT INTO ".TABLE_PREFIX."files (SELECT 0, ".WORKSPACE_ASSIGNMENT.", $_POST[assignment], $_SESSION[member_id], $owner_id, 0, NOW(), 0, 0, file_name, file_size FROM ".TABLE_PREFIX."files WHERE file_id=$file AND owner_type=$owner_type AND owner_id=$owner_id)";
		$result = mysql_query($sql, $db);
		$id = mysql_insert_id($db);
		$from_file = fs_get_file_path($file) . $file;
		$to_file   = fs_get_file_path($id) . $id;
		copy($from_file, $to_file);
	}
	header('Location: index.php'.$owner_arg_prefix.'folder='.$_POST['folder']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

debug($_GET);

// get all the assignments assigned to $owner_id (which is either a student ID or a group ID)

if ($owner_type == WORKSPACE_GROUP) {
	// get all the assignments assigned to this group type

	$sql = "SELECT type_id FROM ".TABLE_PREFIX."groups WHERE group_id=$owner_id LIMIT 1";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	$sql = "SELECT assignment_id, title FROM ".TABLE_PREFIX."assignments WHERE assign_to=$row[type_id] AND course_id=$_SESSION[course_id] ORDER BY title";

} else if ($owner_type == WORKSPACE_PERSONAL) {
	// get all the assignments assigned to this person

	$sql = "SELECT assignment_id, title FROM ".TABLE_PREFIX."assignments WHERE assign_to=0 AND course_id=$_SESSION[course_id] ORDER BY title";
} else {
	exit('wrong workspace');
}

$assignments = array();
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$assignments[] = $row;
}

if (!$assignments) {
	exit('no assignments found');
}
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'].$owner_arg_prefix; ?>">
<input type="hidden" name="folder" value="<?php echo abs($_GET['folder']); ?>" />
<?php foreach ($_GET['files'] as $file): ?>
	<input type="hidden" name="files[]" value="<?php echo abs($file); ?>" />
<?php endforeach; ?>
<div class="input-form">
	
	<div class="row">
		<select name="assignment">
			<?php foreach ($assignments as $assignment): ?>
				<option value="<?php echo $assignment['assignment_id']; ?>"><?php echo $assignment['title']; ?></option>
			<?php endforeach; ?>
		</select>
	</div>


	<div class="buttons row">
		<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>