<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_HELLO_WORLD);
require_once(AT_INCLUDE_PATH.'lib/file_storage.inc.php');

$files_to_del = array();

// find all the files that need to be deleted
// 1. personal files
$sql = "SELECT owner_type, owner_id, file_name FROM ".TABLE_PREFIX."files 
				WHERE owner_type=".WORKSPACE_PERSONAL." 
				AND owner_id NOT IN (SELECT member_id FROM ".TABLE_PREFIX."members)";
$result = mysql_query($sql, $db);

while ($row = mysql_fetch_assoc($result))
{
	if (isset($_POST['submit_yes']))
		fs_delete_workspace(WORKSPACE_PERSONAL, $row['owner_id']);
	else
		array_push($files_to_del, $row['file_name']);
}

// 2. course group files
$sql = "SELECT owner_type, owner_id, file_name FROM ".TABLE_PREFIX."files 
				WHERE owner_type=".WORKSPACE_ASSIGNMENT." 
				AND owner_id NOT IN (SELECT assignment_id FROM ".TABLE_PREFIX."assignments)";
$result = mysql_query($sql, $db);

while ($row = mysql_fetch_assoc($result))
{
	if (isset($_POST['submit_yes']))
		fs_delete_workspace(WORKSPACE_ASSIGNMENT, $row['owner_id']);
	else
		array_push($files_to_del, $row['file_name']);
}

if (isset($_POST['submit_no'])) 
{
	$msg->addFeedback('CANCELLED');
	Header('Location: ../../admin/index.php');
	exit;
} 
else if (isset($_POST['submit_yes']))
{
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	Header('Location: ../../admin/index.php');
	exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');

if (count($files_to_del) > 0)
{
	$msg_files = '<ul>';
	
	foreach (array_unique($files_to_del) as $file)
		$msg_files .= '<li>'.$file.'</li>';
	
	$msg_files .= '</ul>';

	$confirm = array('DELETE_FILES', $msg_files);
	$msg->addConfirm($confirm);
	$msg->printConfirm();
}
else
{
	$msg->addInfo('NO_FILES');
	$msg->printInfos();
}
?>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>