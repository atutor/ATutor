<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests';
$_section[2][0] = _AT('results');
$_section[2][1] = 'tools/tests/results_all_quest.php?tid='.$_GET['tid'];
$_section[3][0] = _AT('view_responses');

authenticate(AT_PRIV_TEST_MARK);

require(AT_INCLUDE_PATH.'header.inc.php');
echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif" class="menuimage" border="0" vspace="2" width="42" height="40" alt="" /></a>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
}
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
}
echo '</h3>';

$sql	= "SELECT automark, title FROM ".TABLE_PREFIX."tests WHERE test_id=$_GET[tid]";
$result = mysql_query($sql, $db);
$row = mysql_fetch_array($result);
echo '<h3><a href="tools/tests/results_all_quest.php?tid='.$_GET['tid'].'">'._AT('results_for',AT_print($row['title'], 'tests.title')).'</a></h3><br />';

echo '<p>';
echo '<a href="tools/tests/results_all_quest.php?tid='.$_GET['tid'].'">'._AT('question_statistics').'</a> | <a href="tools/tests/results_all.php?tid='.$_GET['tid'].'">' . _AT('mark_statistics') . '</a>';
//echo ' | <a href="tools/tests/results_all_csv.php?tid='.$_GET['tid'].'">' . _AT('download_test_csv') . '</a>';
echo '<br /><br />';

echo _AT('response_text').' <strong>'.AT_print(urldecode($_GET['q']), 'tests_questions.question').'</strong></p>';

//get the answers
$sql = "SELECT count(*), A.answer
		FROM ".TABLE_PREFIX."tests_answers A, ".TABLE_PREFIX."tests_results R
		WHERE A.question_id=".$_GET['qid']." AND R.result_id=A.result_id AND R.final_score<>''
		GROUP BY A.answer
		ORDER BY A.answer";

$result = mysql_query($sql, $db);

echo '<ul>';
while ($row = mysql_fetch_assoc($result)) {
	if ($row['answer'] != -1 && $row['answer'] != '') {
		echo '<li>'.AT_print($row['answer'], 'tests_answers.answer').'<br /><br /></li>';	
	}
} 
echo '</ul>';


require(AT_INCLUDE_PATH.'footer.inc.php');
?>