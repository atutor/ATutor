<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2003 by Greg GayJoel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$title = _AT('course_enrolment'); 
require(AT_INCLUDE_PATH.'header.inc.php');

$mid = intval($_GET['mid']);

$sql = "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id='".$mid."'";
$result = mysql_query($sql,$db);

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
}
echo '</h2>'."\n";

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/enrol_mng-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo '<a href="tools/enroll_admin.php?course='.$_SESSION['course_id'].'">'._AT('course_enrolment').'</a>';
}
echo '</h3>'."\n";

?>
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr>
		<th colspan="2" align="left"><?php echo _AT('profile_student_information');  ?></th>
	</tr>
<?php 

if (($row = mysql_fetch_assoc($result)) && authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)){
	echo '<tr><td class="row1" align="right"><strong>'._AT('login_name').':</strong></td><td class="row1">'.AT_print($row['login'],'members.login').'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><strong>'._AT('first_name').':</strong></td><td class="row1">&nbsp;'.AT_print($row['first_name'],'members.first_name').'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><strong>'._AT('last_name').':</strong></td><td class="row1">&nbsp;'.AT_print($row['last_name'],'members.last_name').'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><strong>'._AT('email').':</strong></td><td class="row1"><a href="mailto:'.AT_print($row['email'],'members.email').'">'.$row['email'].'</a></td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><strong>'._AT('status').':</strong></td><td class="row1">';
	if ($row['status'] == 0) {
		echo _AT('student1');
	} else {
		echo _AT('instructor');
	}
	echo '</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('age').':</strong></td><td class="row1">'.AT_print($row['age'],'members.age').'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('gender').':</strong></td><td class="row1">'.AT_print($row['gender'],'members.gender').'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('street_address').':</strong></td><td class="row1">'.AT_print($row['address'],'members.address').'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('city').':</strong></td><td class="row1">'.AT_print($row['city'],'members.city').'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('province').':</strong></td><td class="row1">'.AT_print($row['province'],'members.province').'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('postal_code').':</strong></td><td class="row1">'.AT_print($row['postal'],'members.postal').'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('country').':</strong></td><td class="row1">'.AT_print($row['country'],'members.country').'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('web_site').':</strong></td><td class="row1">'.AT_print($row['website'],'members.website').'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('phone').':</strong></td><td class="row1">'.AT_print($row['phone'],'members.phone').'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('date_created').':</strong></td><td class="row1">'.AT_print($row['creation_date'],'members.creation_date').'</td></tr>';
?>
	</table>

<?php
}
require(AT_INCLUDE_PATH.'footer.inc.php');
?>