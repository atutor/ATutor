<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$section = 'users';
$_include_path = '../include/';
require ($_include_path.'vitals.inc.php');
//require ($_include_path.'lib/atutor_mail.inc.php');
	$sql2 = "SELECT * from ".TABLE_PREFIX."members where member_id='$_GET[mid]'";
	$result2 = mysql_query($sql2);
		require($_include_path.'cc_html/header.inc.php');

?>
<h2><?php echo _AT('course_enrolment');  ?></h2>
<h3><?php echo _AT('profile_student_information');  ?></h3>
<p>[<a href="users/enroll_admin.php?course=<?php echo $_GET['course']; ?>"><?php echo _AT('profile_back_to_enrollment'); ?></a>]</p>
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr>
		<th colspan="2" align="left" class="left"><?php echo _AT('profile_student_information');  ?></th>
	</tr>
<?php 
if ($row=mysql_fetch_array($result2)){

	echo '<tr><td class="row1" align="right"><strong>'._AT('login').':</strong></td><td class="row1">'.$row['login'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><strong>'._AT('first_name').':</strong></td><td class="row1">&nbsp;'.$row['first_name'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><strong>'._AT('last_name').':</strong></td><td class="row1">&nbsp;'.$row['last_name'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><strong>'._AT('email').':</strong></td><td class="row1">'.$row['email'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><strong>'._AT('status').':</strong></td><td class="row1">';
	if ($status == 0) {
		echo _AT('student1');
	} else {
		echo _AT('instructor');
	}
	echo '</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('age').':</strong></td><td class="row1">'.$row['age'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('gender').':</strong></td><td class="row1">'.$row['gender'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('street_address').':</strong></td><td class="row1">'.$row['address'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('city').':</strong></td><td class="row1">'.$row['city'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('province').':</strong></td><td class="row1">'.$row['province'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('postal_code').':</strong></td><td class="row1">'.$row['postal'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('country').':</strong></td><td class="row1">'.$row['country'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('web_site').':</strong></td><td class="row1">'.$row['website'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('phone').':</strong></td><td class="row1">'.$row['phone'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
		echo '<tr><td class="row1" align="right"><strong>'._AT('date_created').':</strong></td><td class="row1">'.$row['creation_date'].'</td></tr>';
?>
	</table>

	
<?php
}
require($_include_path.'cc_html/footer.inc.php');
?>