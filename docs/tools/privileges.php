<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$page = 'enrollment';
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enroll_admin.php';
$_section[2][0] = _AT('roles_privileges');

authenticate(AT_PRIV_ENROLLMENT);

$db;
$num_cols = 2;

if (isset($_POST['cancel'])) {
	header('Location: enroll_admin.php?f='.AT_FEEDBACK_CANCELLED);
	exit;
}

	
if (isset($_POST['submit'])) {
	$mid   = $_POST['dmid'];
	$privs = $_POST['privs'];
	$role  = $_POST['role'];

	//if user did not chnage any privileges but may have changed the role title
	$i=0;
	while ($mid[$i]) { 
		if ($privs[$i] == 0) {
			change_roles($mid[$i], $_POST['course_id'], $role[$i]);
		}
		else {
			change_privs($mid[$i], $_POST['course_id'], $privs[$i], $role[$i]);
		}
		$i++;
	}
	
	header('Location: enroll_admin.php?course='.$course.SEP.'f='.AT_FEEDBACK_PRIVS_CHANGED);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

print_feedback ($feedback);
print_errors   ($errors);

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
echo '</h3><br />'."\n";
require (AT_INCLUDE_PATH . 'html/feedback.inc.php');
//print_errors($_GET['f']);
?>


<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="course_id" value="<?php echo $_GET['fcid']; ?>" />
<?php
	//Store id's into a hidden element for use by functions
	$j = 0;
	while ($_GET['mid'.$j]) {
		echo '<input type="hidden" name="dmid[]" value="'.$_GET['mid'.$j].'" />';		
		$j++;
	}


	for ($k = 0; $k < $j; $k++) {
?>
	<table align="center" cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%">
<?php
	$mem_id = $_GET['mid'.$k];
	$cid = $_GET['fcid'];
	$sql = "SELECT m.login FROM ".TABLE_PREFIX."members m, ".TABLE_PREFIX."course_enrollment cm, ".TABLE_PREFIX."courses c WHERE m.member_id=($mem_id) AND cm.course_id = ($cid) AND cm.member_id = m.member_id AND cm.member_id <> c.member_id";

	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
?>

	<tr><th><?php echo _AT('roles_privileges') . ' - ' . $row['login'];   ?></th></tr>

	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1"><label for="role"><strong><?php echo _AT('user_role'); ?>:</strong></label> <input type="input" name="role[<?php echo $k; ?>]" id="role[<?php echo $k; ?>]" class="formfield" value="<?php if ($row['role'] !='') { echo $row['role']; } else { echo _AT('student'); } ?>" size="35" />
		</td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1"><label for="course_list"><strong><?php echo _AT('user_privileges'); ?>:</strong></label><br />
			<table width="100%" border="0" cellspacing="5" cellpadding="0" summary="">
			<tr>
			<?php		
			$count =0;

			foreach ($_privs as $key => $priv) {		
				$count++;
				echo '<td><input type="checkbox" name="privs['.$k.']['.$key.']" id="'.$key.'" ';

				if (query_bit($row['privileges'], $key)) { 
					echo 'checked="checked"';
				} 

				echo ' /><label for="'.$key.'">'.$priv['name'].'</label></td>'."\n";
				if (!($count % $num_cols)) {
					echo '</tr><tr>';
				}
			}
			if ($count % $num_cols) {
				echo '<td colspan="'.($num_cols-($count % $num_cols)).'">&nbsp;</td>';
			} else {
				echo '<td colspan="'.$num_cols.'">&nbsp;</td>';
			}
			echo '</tr>';
			?>
			</tr></table></td>
			<?php } ?>
	</tr>
	<tr><td height="1" class="row2"></td></tr><br />
	<tr>
		<td class="row1" align="center"><input type="submit" name="submit" value="<?php echo _AT('save_changes');  ?>" class="button" /> <input type="submit" name="cancel" value="<?php echo _AT('cancel');  ?>" class="button" /></td>
	</tr>
	</table>

<br />
</form>

<?php 

function change_privs ($member, $form_course_id, $privs, $role) {
	global $db;

	$privilege = 0;
	foreach ($privs as $key => $priv) {	
		$privilege += $key;
	}	
	
	$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET `privileges`=($privilege), `role`='$role' WHERE member_id=($member) AND course_id=($form_course_id) AND `approved`='y'";


	$result = mysql_query($sql,$db);

	//print error or confirm change
	if (!$result) {
		$errors[]=AT_ERROR_DB_NOT_UPDATED;
		print_errors($errors);
		exit;
	}
}

function change_roles ($member, $form_course_id, $role) {
	global $db;
	
	$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET `role`='$role' WHERE member_id=($member) AND course_id=($form_course_id) AND `approved`='y'";


	$result = mysql_query($sql,$db);

	//print error or confirm change
	if (!$result) {
		$errors[]=AT_ERROR_DB_NOT_UPDATED;
		print_errors($errors);
		exit;
	}
}

require(AT_INCLUDE_PATH.'footer.inc.php'); ?>