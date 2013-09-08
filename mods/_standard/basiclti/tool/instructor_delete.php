<?php
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_BASICLTI);

if ( !is_int($_SESSION['course_id']) || $_SESSION['course_id'] < 1 ) {
    $msg->addFeedback('NEED_COURSE_ID');
    exit;
}

$tool = intval($_REQUEST['id']);

$sql = "SELECT title FROM %sbasiclti_tools WHERE id = %d AND course_id = %d";
$row = queryDB($sql, array(TABLE_PREFIX, $tool, $_SESSION['course_id']), TRUE);

if ( strlen($row["title"]) < 1) {
        $msg->addError('UNABLE_TO_FIND_TOOL');
        header('Location: ../index_instructor.php');
        exit;
}

if (isset($_POST['submit_no'])) {
        $msg->addFeedback('CANCELLED');
        header('Location: ../index_instructor.php');
        exit;
} else if (isset($_POST['step']) && ($_POST['step'] == 1) && isset($_POST['submit_yes'])) {

	$sql = "DELETE FROM %sbasiclti_tools WHERE id = %d AND course_id = %d";
	$result = queryDB($sql, array(TABLE_PREFIX, $tool, $_SESSION['course_id']));
	    global $sqlout;
        write_to_log(AT_ADMIN_LOG_DELETE, 'basiclti_delete', $result, $sqlout);
        $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
        header('Location: ../index_instructor.php');
        exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

if (!isset($_POST['step'])) {
        $hidden_vars['step']   = 1;
        $hidden_vars['id'] = $tool;
        $msg->addConfirm(array('DELETE_TOOL_1', $row['title']), $hidden_vars);
        $msg->printConfirm();
} 

require(AT_INCLUDE_PATH.'footer.inc.php'); 
