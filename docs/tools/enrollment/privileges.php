<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
$page = 'enrollment';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ADMIN);

$num_cols = 2;

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
} else if (isset($_POST['submit'])) {
	//update privileges	
	$mid   = $_POST['dmid'];
	$privs = $_POST['privs'];
	$role  = $_POST['role'];

	//loop through selected users to perform update
	$i=0;
	while ($mid[$i]) { 
		change_privs(intval($mid[$i]), $privs[$i], $addslashes($role[$i]));
		$i++;
	}
	
	$msg->addFeedback('PRIVS_CHANGED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<div class="input-form">
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
<?php
	$mem_id = $_GET['mid'.$k];

	//NO!!! extra check to ensure that user doesnt send in instructor for change privs
	$sql = "SELECT cm.privileges, m.login FROM ".TABLE_PREFIX."course_enrollment cm JOIN ".TABLE_PREFIX."members m ON cm.member_id = m.member_id WHERE m.member_id=($mem_id) AND cm.course_id = $_SESSION[course_id]";

	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
?>
	<div class="row">
		<h3><?php echo $row['login']; ?></h3>
	</div>

	<div class="row">
		<?php echo _AT('user_privileges'); ?><br />
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

				echo ' />'._AT($priv['name']).'</label></td>'."\n";
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
		</div>
<?php 
	}//end for
?>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save');  ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel');  ?>" />
	</div>
</div>
</form>

<?php 

/**
* Updates the Role & Priviliges of users
* @access  private
* @param   int $member			The member_id of the user whose values are to be updated
* @param   int $privs			value of the privileges of the user
* @param   string $role			The role of the user
* @author  Joel Kronenberg
*/
function change_privs ($member, $privs, $role) {
	global $db;

	//calculate privileges
	$privilege = 0;
	if (!(empty($privs))) {
		foreach ($privs as $key => $priv) {	
			$privilege += intval($key);
		}	
	}
	
	$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET `privileges`=($privilege), `role`='$role' WHERE member_id=($member) AND course_id=$_SESSION[course_id] AND `approved`='y'";

	$result = mysql_query($sql,$db);

	//print error or confirm change
	if (!$result) {
		$msg->printErrors('DB_NOT_UPDATED');
		exit;
	}
}

require(AT_INCLUDE_PATH.'footer.inc.php'); ?>