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

echo '<h3>'._AT('results_for').' '.$_GET['tt'].'</h3><br />';

echo 'The following answers were given in response to: <strong>'.urldecode($_GET['q']).'</strong><br /><br />';

//get the answers
$sql = "SELECT count(*), answer
		FROM ".TABLE_PREFIX."tests_answers 
		WHERE question_id=".$_GET['qid']."
		GROUP BY answer
		ORDER BY answer";

$result = mysql_query($sql, $db);

echo '<ul>';
while ($row = mysql_fetch_assoc($result)) {
	if ($answer != -1) {
		echo '<li>'.$row['answer'].'</li><br />';	
	}
} 
echo '</ul>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>