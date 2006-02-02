<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg,				*/
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
$gid = intval($_REQUEST['gid']);
$current_tab = intval($_REQUEST['tab']);

if ($gid) {
	$sql = "SELECT title FROM ".TABLE_PREFIX."groups WHERE group_id=$gid AND course_id=$_SESSION[course_id]";
	$result = mysql_query ($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$group_title = $row['title'];
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
	
	header('Location: '.$_SERVER['PHP_SELF'].'?gid='.$gid.SEP.'tab='.$current_tab);
	exit;

} else if (isset($_POST['remove'], $_POST['id']))  {
	$id_list = implode(',', $_POST['id']);
	$id_list = $addslashes($id_list);
	
	$sql = "DELETE FROM ".TABLE_PREFIX."groups_members WHERE group_id=$gid AND member_id IN ($id_list)";
	$result = mysql_query ($sql,$db);
	
	$msg->addFeedback('STUDENT_REMOVE_GROUP');
	header('Location: '.$_SERVER['PHP_SELF'].'?gid='.$gid.'tab='.$current_tab);
	exit;

} else if (!isset($_POST['id']) && (isset($_POST['add']) || isset($_POST['remove']))) {
	$msg->addError('NO_ITEM_SELECTED');
	header('Location: '.$_SERVER['PHP_SELF'].'?gid='.$gid);
	exit;
}

$tabs = array('group_members', 'non_group_members');
$num_tabs = count($tabs);


require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>'.$group_title.'</h2>';

if ($current_tab == 0) {
	// all the group members:

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
} else {
	// all the non-group members
	$sql = "SELECT member_id FROM ".TABLE_PREFIX."groups_members WHERE group_id=".$gid;
	$result = mysql_query($sql, $db);
	$members_list = '0';
	while ($row = mysql_fetch_assoc($result)) {
		$members_list .= ','.$row['member_id'];
	}
	$members_list .= ','.$system_courses[$_SESSION['course_id']]['member_id'];
	$sql = "SELECT CE.member_id, M.login, M.first_name, M.last_name, M.email
				FROM ".TABLE_PREFIX."course_enrollment CE, ".TABLE_PREFIX."members M 
				WHERE CE.course_id=$_SESSION[course_id] AND CE.approved='y' AND CE.member_id=M.member_id AND CE.member_id NOT IN ($members_list) ORDER BY 'login' 'asc'";
	$result = mysql_query($sql, $db);
}
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="selectform">
<input type="hidden" name="gid" value="<?php echo $gid; ?>" />
<input type="hidden" name="tab" value="<?php echo $current_tab; ?>" />

<div style="width: 90%; margin-right: auto; margin-left: auto;">
<ul id="navlist">
	<?php for ($i = 0; $i< $num_tabs; $i++): ?>
		<?php if ($current_tab == $i): ?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tab=<?php echo $i.SEP; ?>gid=<?php echo $gid; ?>" class="active"><strong><?php echo _AT($tabs[$i]); ?></strong></a></li>
		<?php else: ?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tab=<?php echo $i.SEP; ?>gid=<?php echo $gid; ?>"><?php echo _AT($tabs[$i]); ?></a></li>
		<?php endif; ?>
	<?php endfor; ?>
</ul>
</div>
<table class="data" rules="cols" summary="">
<thead>
<tr>
	<th scope="col" align="left"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAllMembers();" /></th>
	<th scope="col"><?php echo _AT('login_name'); ?></th>
	<th scope="col"><?php echo _AT('first_name'); ?></th>
	<th scope="col"><?php echo _AT('last_name'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<?php if ($current_tab == 0): ?>
		<td colspan="4"><input type="submit" name="remove" value="<?php echo _AT('remove'); ?>" /></td>
	<?php else: ?>
		<td colspan="4"><input type="submit" name="add" value="<?php echo _AT('add'); ?>" /></td>
	<?php endif; ?>
</tr>
</tfoot>
<tbody>
<?php
	if (mysql_num_rows($result) == 0) {
		echo '<tr><td colspan="4">'._AT('none_found').'</td></tr>';
	} else {
		while ($row  = mysql_fetch_assoc($result)) {
			echo '<tr onmousedown="document.selectform[\'m' . $row['member_id'] . '\'].checked = !document.selectform[\'m' . $row['member_id'] . '\'].checked;">';
			echo '<td>';

			$act = "";
			if ($row['member_id'] == $_SESSION['member_id']) {
				$act = 'disabled="disabled"';	
			} 
			
			echo '<input type="checkbox" name="id[]" value="'.$row['member_id'].'" id="m'.$row['member_id'].'" ' . $act . ' onmouseup="this.checked=!this.checked" title="'.AT_print($row['login'], 'members.login').'" /></td>';
			echo '<td>'.AT_print($row['login'], 'members.login') . '</td>';
			echo '<td>'.AT_print($row['first_name'],'members.first_name').'</td>';
			echo '<td>'.AT_print($row['last_name'],'members.last_name').'</td>';
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

-->
</script>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>