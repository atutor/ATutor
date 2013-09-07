<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_BASICLTI);

$tool = intval($_REQUEST['id']);

$sql = "SELECT title FROM %sbasiclti_tools WHERE id = %d";
$row = queryDB($sql, array(TABLE_PREFIX, $tool), TRUE);


if ( strlen($row["title"]) < 1) {
        $msg->addError('UNABLE_TO_FIND_TOOL');
        header('Location: ../index_admin.php');
        exit;
}

if (isset($_POST['submit_no'])) {
        $msg->addFeedback('CANCELLED');
        header('Location: ../index_admin.php');
        exit;
} else if (isset($_POST['submit_yes'])) {

		$sql = "DELETE FROM %sbasiclti_tools WHERE id =%d";
	    $result = queryDB($sql, array(TABLE_PREFIX, $tool));
	    global $sqlout;
        write_to_log(AT_ADMIN_LOG_DELETE, 'basiclti_delete', $result, $sqlout);
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


require(AT_INCLUDE_PATH.'footer.inc.php'); 
