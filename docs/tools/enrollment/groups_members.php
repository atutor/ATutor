<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg,				*/
/* Heidi Hazelton, and Jonathan Hung									*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id:  $
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

/* make sure we own this course that we're approving for! */
if (!authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('NOT_OWNER');
	require (AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

/* Get Group ID */
$_REQUEST['gid']=intval($_REQUEST['gid']);
if (isset($_REQUEST['gid'])) {
	$sql = "SELECT group_id FROM ".TABLE_PREFIX."groups WHERE group_id=".$_REQUEST['gid'];
	$result = mysql_query ($sql, $db);
	if($row = mysql_fetch_assoc($result)) {
		$gid = $_REQUEST['gid'];
	} else {
		$msg->addError('GROUP_NOT_FOUND');
		header('Location: groups.php');
		exit;
	}
} else {
	$msg->addError('GROUP_NOT_FOUND');
	header('Location: groups.php');
	exit;
}

// If adding students.
if (isset($_POST['add'], $_POST['id'])) {
	$gid = intval($_POST['gid']);
	$sql='';
	foreach ($_POST['id'] as $id) {
		$id = intval($id);
		$sql .= "($gid, $id),";
	}
	if ($sql) {
		$sql = substr ($sql, 0,-1);
		$sql = "INSERT INTO ".TABLE_PREFIX."groups_members VALUES ".$sql;
	}
	$result = mysql_query ($sql,$db);
	$msg->addFeedback('STUDENT_ADDED_GROUP');
	header('Location: '.$_SERVER['PHP_SELF'].'?gid='.$gid);
	exit;
} else if (isset($_POST['remove'], $_POST['id']))  {
	$gid = intval($_POST['gid']);
	$id_list = implode(',', $_POST['id']);
	$id_list = $addslashes($id_list);
	$sql = "DELETE FROM ".TABLE_PREFIX."groups_members WHERE group_id=$gid AND member_id IN ($id_list)";
	$result = mysql_query ($sql,$db);
	$msg->addFeedback('STUDENT_REMOVE_GROUP');
	header('Location: '.$_SERVER['PHP_SELF'].'?gid='.$gid);
	exit;
} else if (isset($_POST['remove'], $_POST['add'])) {
	$msg->addError('NO_STUDENT_SELECTED');
}
require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="selectform">
<?php if (isset($gid)) {
	echo '<input type="hidden" value="'.$gid.'" name="gid" />';
} ?>

<table class="data" rules="cols" summary="">
<caption style="font-size: larger; border: 1px solid #e0e0e0;text-transform: uppercase; margin-left: auto; margin-right: auto; background-color: #efefef;"><?php echo _AT('group_members') ?></caption>

<thead>
<tr>
	<th scope="col" align="left"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAllMembers();" /></th>
	<th scope="col"><?php echo _AT('login_name'); ?></th>
	<th scope="col"><?php echo _AT('first_name'); ?></th>
	<th scope="col"><?php echo _AT('last_name'); ?></th>
	<th scope="col"><?php echo _AT('email'); ?></th>
</tr>
</thead>

<tfoot><tr><td colspan="5">
<input type="submit" name="remove" value="<?php echo (_AT('remove')); ?>" />
</td></tr></tfoot>

<tbody>
<?php
	$sql = "SELECT member_id FROM ".TABLE_PREFIX."groups_members WHERE group_id=".$gid;
	$result = mysql_query($sql, $db);
	$members_list = '0';
	while ($row = mysql_fetch_assoc($result)) {
		$members_list .= ','.$row['member_id'];
	}
	$sql = "SELECT CE.member_id, M.login, M.first_name, M.last_name, M.email
				FROM ".TABLE_PREFIX."course_enrollment CE, ".TABLE_PREFIX."members M 
				WHERE CE.course_id=$_SESSION[course_id] AND CE.member_id=M.member_id AND CE.member_id IN ($members_list) ORDER BY 'login' 'asc'";
	$result = mysql_query($sql, $db);

	if (mysql_num_rows($result) == 0) {
		echo '<tr><td colspan="5">'._AT('none_found').'</td></tr>';
	} else {
		while ($row  = mysql_fetch_assoc($result)) {
			echo '<tr onmousedown="document.selectform[\'m' . $row['member_id'] . '\'].checked = !document.selectform[\'m' . $row['member_id'] . '\'].checked;">';
			echo '<td>';

			$act = "";
			if ($row['member_id'] == $_SESSION['member_id']) {
				$act = 'disabled="disabled"';	
			} 
			
			echo '<input type="checkbox" name="id[]" value="'.$row['member_id'].'" id="m'.$row['member_id'].'" ' . $act . ' onmouseup="this.checked=!this.checked" title="'.AT_print($row['login'], 'members.login').'" /></td>';
			echo '<td>' . AT_print($row['login'], 'members.login') . '</td>';
			echo '<td>' . AT_print($row['first_name'], 'members.name') . '</td>';
			echo '<td>' . AT_print($row['last_name'], 'members.name')  . '</td>';
			echo '<td>' . AT_print($row['email'], 'members.email') . '</td>';
			echo '</tr>';
		}		
	}
?>
</tbody>
</table>
</form>
<br />

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="selectform2">
<?php if (isset($gid)) {
	echo '<input type="hidden" value="'.$gid.'" name="gid" />';
}?>
<table class="data" rules="cols" summary="">
<caption style="font-size: larger; border: 1px solid #e0e0e0;text-transform: uppercase; margin-left: auto; margin-right: auto; background-color: #efefef;"><?php echo _AT('non_group_members') ?></caption>

<thead>
<tr>
	<th scope="col" align="left"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAllNonMembers();" /></th>
	<th scope="col"><?php echo _AT('login_name'); ?></th>
	<th scope="col"><?php echo _AT('first_name'); ?></th>
	<th scope="col"><?php echo _AT('last_name'); ?></th>
	<th scope="col"><?php echo _AT('email'); ?></th>
</tr>
</thead>

<tfoot><tr><td colspan="5">
<input type="submit" name="add" value="<?php echo _AT('add'); ?>" />
</td></tr></tfoot>

<tbody>
<?php

	$members_list .= ','.$system_courses[$_SESSION['course_id']]['member_id'];
	$sql = "SELECT CE.member_id, M.login, M.first_name, M.last_name, M.email
				FROM ".TABLE_PREFIX."course_enrollment CE, ".TABLE_PREFIX."members M 
				WHERE CE.course_id=$_SESSION[course_id] AND CE.approved='y' AND CE.member_id=M.member_id AND CE.member_id NOT IN ($members_list) ORDER BY 'login' 'asc'";
	$result = mysql_query($sql, $db);

	if (mysql_num_rows($result) == 0) {
		echo '<tr><td colspan="5">'._AT('none_found').'</td></tr>';
	} else {
		while ($row  = mysql_fetch_assoc($result)) {
			echo '<tr onmousedown="document.selectform2[\'m' . $row['member_id'] . '\'].checked = !document.selectform2[\'m' . $row['member_id'] . '\'].checked;">';
			echo '<td>';

			$act = "";
			if ($row['member_id'] == $_SESSION['member_id']) {
				$act = 'disabled="disabled"';	
			} 
			
			echo '<input type="checkbox" name="id[]" value="'.$row['member_id'].'" id="m'.$row['member_id'].'" ' . $act . ' onmouseup="this.checked=!this.checked" title="'.AT_print($row['login'], 'members.login').'" /></td>';
			echo '<td>' . AT_print($row['login'], 'members.login') . '</td>';
			echo '<td>' . AT_print($row['first_name'], 'members.name') . '</td>';
			echo '<td>' . AT_print($row['last_name'], 'members.name')  . '</td>';
			echo '<td>' . AT_print($row['email'], 'members.email') . '</td>';
			echo '</tr>';
		}		
	}
?>
</tbody>
</table>
</form>

<script language="JavaScript" type="text/javascript">
<!--
function CheckAllMembers() {
	
	for (var i=0;i<document.selectform.elements.length;i++)	{
		var e = document.selectform.elements[i];
		if ((e.name == 'id[]') && (e.type=='checkbox'))
			e.checked = document.selectform.selectall.checked;
	}
}

function CheckAllNonMembers() {
	
	for (var i=0;i<document.selectform2.elements.length;i++)	{
		var e = document.selectform2.elements[i];
		if ((e.name == 'id[]') && (e.type=='checkbox'))
			e.checked = document.selectform2.selectall.checked;
	}
}

-->
</script>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>