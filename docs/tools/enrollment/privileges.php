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
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$db;
$num_cols = 2;

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enrollment/index.php';
$_section[2][0] = _AT('roles_privileges');

/* make sure we own this course that we're approving for! */
$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id]";
$result	= mysql_query($sql, $db);

if (!($result) || !authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('NOT_OWNER');
	
	require (AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

//if user wants to cancel action
if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

//update privileges	
if (isset($_POST['submit'])) {
	$mid   = $_POST['dmid'];
	$privs = $_POST['privs'];
	$role  = $_POST['role'];

	//loop through selected users to perform update
	$i=0;
	while ($mid[$i]) { 
		change_privs($mid[$i], $_POST['course_id'], $privs[$i], $role[$i]);
		$i++;
	}
	
	$msg->addFeedback('PRIVS_CHANGED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');
/* we own this course! */
$msg->addHelp('ROLES_PRIVILEGES');



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
	echo '<a href="tools/enrollment/index.php">'._AT('course_enrolment').'</a>';
}
echo '</h3>'."\n";

$msg->printAll();
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

	//loop through all the students
	for ($k = 0; $k < $j; $k++) {
?>
<br />
<table align="center" cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%">
<?php
	$mem_id = $_GET['mid'.$k];
	$cid = $_GET['fcid'];

	//NO!!! extra check to ensure that user doesnt send in instructor for change privs
	$sql = "SELECT cm.privileges, cm.role, m.login FROM ".TABLE_PREFIX."course_enrollment cm JOIN ".TABLE_PREFIX."members m ON cm.member_id = m.member_id WHERE m.member_id=($mem_id) AND cm.course_id = ($cid)";

	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
?>

	<tr><th scope="col"><?php echo _AT('roles_privileges') . ' - ' . $row['login'];   ?></th></tr>

	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1"><label><strong><?php echo _AT('user_role'); ?>:</strong></label> <input type="text" name="role[<?php echo $k; ?>]" class="formfield" value="<?php if ($row['role'] !='') { echo $row['role']; } else { echo _AT('student'); } ?>" size="35" />
		</td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1"><label><strong><?php echo _AT('user_privileges'); ?>:</strong></label><br />
			<table width="100%" border="0" cellspacing="5" cellpadding="0" summary="">
			<tr>
			<?php		
			$count =0;

			foreach ($_privs as $key => $priv) {		
				$count++;
				echo '<td><label><input type="checkbox" name="privs['.$k.']['.$key.']" value="'.$key.'" ';

				if (query_bit($row['privileges'], $key)) { 
					echo 'checked="checked"';
				} 

				echo ' />'.htmlspecialchars($priv['name']).'</label></td>'."\n";
				if (!($count % $num_cols)) {
					echo '</tr><tr>';
				}
			}
			if ($count % $num_cols) {
				echo '<td colspan="'.($num_cols-($count % $num_cols)).'">&nbsp;</td>';
			} else {
				echo '<td colspan="'.$num_cols.'">&nbsp;</td>';
			}
			?>
			</tr>
			</table>
		</td>
	</tr>
	</table>
<?php 
	}//end for
?>
<table align="center" cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%">
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1" align="center"><input type="submit" name="submit" value="<?php echo _AT('save_changes');  ?> [alt-s]" class="button" accesskey="s" /> <input type="submit" name="cancel" value="<?php echo _AT('cancel');  ?>" class="button" /></td>
	</tr>
	</table>
</form>

<?php 

/**
* Updates the Role & Priviliges of users
* @access  private
* @param   int $member			The member_id of the user whose values are to be updated
* @param   int $form_course_id  Course ID
* @param   int $privs			value of the privileges of the user
* @param   string $role			The role of the user
* @author  Joel Kronenberg
*/
function change_privs ($member, $form_course_id, $privs, $role) {
	global $db;

	//calculate privileges
	$privilege = 0;
	if (!(empty($privs))) {
		foreach ($privs as $key => $priv) {	
			$privilege += $key;
		}	
	}
	
	$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET `privileges`=($privilege), `role`='$role' WHERE member_id=($member) AND course_id=($form_course_id) AND `approved`='y'";

	$result = mysql_query($sql,$db);

	//print error or confirm change
	if (!$result) {
		$msg->printErrors('DB_NOT_UPDATED');
		exit;
	}
}

require(AT_INCLUDE_PATH.'footer.inc.php'); ?>