<?php
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$fha_student_tools = array();

$sql = "SELECT links FROM ".TABLE_PREFIX."fha_student_tools WHERE course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	$fha_student_tools = explode('|', $row['links']);
}

if($fha_student_tools[0] == "" ){
	$msg->addInfo('NO_TOOLS_FOUND');
}

require (AT_INCLUDE_PATH.'header.inc.php');

$home_links = array();


if($fha_student_tools[0] != "" ){
	//query reading the type of home viewable. 0: icon view   1: detail view
	$sql = "SELECT home_view FROM ".TABLE_PREFIX."fha_student_tools WHERE course_id = $_SESSION[course_id]";
	$result = mysql_query($sql,$db);
	$row= mysql_fetch_assoc($result);
	
	$savant->assign('view_mode', $row['home_view']);
	$savant->assign('home_links', get_home_navigation($fha_student_tools));
}

$savant->assign('num_pages', 0);
$savant->assign('current_page', 0);
$savant->display('index.tmpl.php');

?>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>