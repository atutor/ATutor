<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: privileges.inc.php 10142 2010-08-17 19:17:26Z hwong $
if (!defined('AT_INCLUDE_PATH')) { exit; }

$num_cols = 2;

//if user wants to cancel action
if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?course_id='.$course_id);
	exit;
} else if (isset($_POST['submit'])) {

	//update privileges	
	$mid   = $_POST['dmid'];
	$privs = $_POST['privs'];
	$role  = $_POST['role'];

	//loop through selected users to perform update
	$i=0;
	while ($mid[$i]) { 
		change_privs(intval($mid[$i]), $privs[$i]);
		$i++;
	}

	$msg->addFeedback('PRIVS_CHANGED');
	header('Location: index.php?tab=1'.SEP.'course_id='.$course_id);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="course_id" value="<?php echo $course_id; ?>"/>
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
	$mem_id = intval($_GET['mid'.$k]);

	//NO!!! extra check to ensure that user doesnt send in instructor for change privs
	$sql = "SELECT CE.privileges, M.login FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id) WHERE M.member_id=$mem_id AND CE.course_id=$course_id AND CE.approved='y'";

	$result = mysql_query($sql, $db);
	$student_row = mysql_fetch_assoc($result);
?>
	<div class="row">
		<h3><?php echo $student_row['login']; ?></h3>
	</div>

	<div class="row">
		<?php echo _AT('privileges'); ?><br />
			<table width="100%" border="0" cellspacing="5" cellpadding="0" summary="">
			<tr>
			<?php		
			$count =0;
			$student_row['privileges'] = intval($student_row['privileges']);
			$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED, 0, TRUE);
			$keys = array_keys($module_list);
			foreach ($keys as $module_name) {
				$module =& $module_list[$module_name];
				if (!($module->getPrivilege() > 1)) {
					continue;
				}
				$count++;
				echo '<td><label><input type="checkbox" name="privs['.$k.'][]" value="'.$module->getPrivilege().'" ';

				if (query_bit($student_row['privileges'], $module->getPrivilege())) { 
					echo 'checked="checked"';
				} 

				echo ' />'.$module->getName().'</label></td>';

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
* @author  Joel Kronenberg
*/
function change_privs ($member, $privs) {
	global $db, $course_id;

	//calculate privileges
	$privilege = 0;
	if (!(empty($privs))) {
		foreach ($privs as $priv) {	
			$privilege += intval($priv);
		}	
	}

	/*
	* if we're making a student a GROUP TA then we have to remove them
	* from all the groups they may belong to. 
	*/
	if (query_bit($privilege, AT_PRIV_GROUPS)) {
		$group_list = implode(',', $_SESSION['groups']);
		if ($group_list) {
			$sql = "DELETE FROM ".TABLE_PREFIX."groups_members WHERE group_id IN ($group_list) AND member_id=$member";
			$result = mysql_query($sql,$db);
		}
	}

	$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET `privileges`=$privilege WHERE member_id=$member AND course_id=$course_id AND `approved`='y'";
	$result = mysql_query($sql,$db);


	//print error or confirm change
	if (!$result) {
		$msg->printErrors('DB_NOT_UPDATED');
		exit;
	}
}

require(AT_INCLUDE_PATH.'footer.inc.php'); ?>