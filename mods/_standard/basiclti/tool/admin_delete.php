<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_BASICLTI);

$tool = intval($_REQUEST['id']);

$sql = "SELECT title FROM ".TABLE_PREFIX."basiclti_tools WHERE id = ".$tool.";";
$result = mysql_query($sql, $db) or die(mysql_error());
$row = mysql_fetch_assoc($result);

if ( strlen($row["title"]) < 1) {
        $msg->addFeedback('UNABLE_TO_FIND_TOOL');
        header('Location: ../index_admin.php');
        exit;
}

if (isset($_POST['submit_no'])) {
        $msg->addFeedback('CANCELLED');
        header('Location: ../index_admin.php');
        exit;
} else if (isset($_POST['submit_yes'])) {
	$sql = "DELETE FROM ".TABLE_PREFIX."basiclti_tools WHERE id = ".$tool.";";
	$result = mysql_query($sql, $db) or die(mysql_error());
        write_to_log(AT_ADMIN_LOG_DELETE, 'basiclti_delete', mysql_affected_rows($db), $sql);
        $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
        header('Location: ../index_admin.php');
        exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

if (!isset($_POST['step'])) {
        $hidden_vars['step']   = 2;
        $hidden_vars['id'] = $tool;
        $msg->addConfirm(array('DELETE_TOOL_1', $row['title']), $hidden_vars);
        $msg->printConfirm();
} 
/*

else if ($_POST['step'] == 1) {
        $hidden_vars['step']   = 2;
        $hidden_vars['id'] = $tool;
        $msg->addConfirm(array('DELETE_TOOL_2', $row['title']), $hidden_vars);
        $msg->printConfirm();
}*/

require(AT_INCLUDE_PATH.'footer.inc.php'); 
