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
	
	foreach ($fha_student_tools as $child) {
		if (isset($_pages[$child])) {
			if (isset($_pages[$child]['title'])) {
				$title = $_pages[$child]['title'];
			} else {
				$title = _AT($_pages[$child]['title_var']);
			}
			$home_links[] = array('url' => $_base_path . $child, 'title' => $title, 'img' => $_base_path.$_pages[$child]['img']);
		}
	}
}
$savant->assign('home_links', $home_links);

$savant->assign('announcements', array());
$savant->assign('num_pages', 0);
$savant->assign('current_page', 0);
$savant->display('index.tmpl.php');

?>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>