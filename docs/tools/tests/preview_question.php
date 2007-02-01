<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/testQuestions.class.php');
authenticate(AT_PRIV_TESTS);

$_letters = array(_AT('A'), _AT('B'), _AT('C'), _AT('D'), _AT('E'), _AT('F'), _AT('G'), _AT('H'), _AT('I'), _AT('J'));

if (isset($_GET['submit'])) {
	header('Location: '.$_base_href.'tools/tests/question_db.php');
	exit;
}

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require(AT_INCLUDE_PATH.'header.inc.php');

$qid = $addslashes($_GET['qid']);
$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND question_id IN ($qid)";
$result	= mysql_query($sql, $db);
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<?php
	while ($row = mysql_fetch_assoc($result)) {
		$obj = test_question_factory($row['type']);
		$obj->display($row);
	}
	?>
	<div class="row buttons"><input type="submit" name="submit" value="<?php echo _AT('back'); ?>"/></div>
</div>
</form>
<script type="text/javascript">
//<!--
function iframeSetHeight(id, height) {
	document.getElementById("qframe" + id).style.height = (height + 20) + "px";
}
//-->
</script>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>