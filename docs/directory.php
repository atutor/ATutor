<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

/* should only be in here if you are enrolled in the course!!!!!! */
if ($_SESSION['enroll'] == '') {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->addInfo('NOT_ENROLLED');
	$msg->printAll();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if ($_GET['reset_filter']) {
	unset($_GET);
}

if (isset($_GET['online_status']) && ($_GET['online_status'] != '')) {
	if ($_GET['online_status'] == 1) {
		$on = 'checked="checked"';
	} else if ($_GET['online_status'] == 2) {
		$all = 'checked="checked"';
	} else if ($_GET['online_status'] == 0) {
		$off = 'checked="checked"';
	}
} else {
	$all = 'checked="checked"';
}

$group = abs($_GET['group']);

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
<div class="input-form">
	<div class="row">
		<?php echo _AT('online_status'); ?><br />
		<input type="radio" name="online_status" id="s1" value="1" <?php echo $on; ?>  /><label for="s1"><?php echo _AT('user_online');  ?></label>
		<input type="radio" name="online_status" id="s0" value="0" <?php echo $off; ?> /><label for="s0"><?php echo _AT('user_offline'); ?></label>
		<input type="radio" name="online_status" id="s2" value="2" <?php echo $all; ?> /><label for="s2"><?php echo _AT('all');          ?></label>
	</div>

		<div class="row">

			<label for="groups"><?php echo _AT('groups'); ?></label><br />
			<?php
			$sql_groups = implode(',', $_SESSION['groups']);
			$sql = "SELECT G.title, G.group_id, T.title AS type_title FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."groups_types T USING (type_id) WHERE T.course_id=$_SESSION[course_id] AND G.group_id IN ($sql_groups) ORDER BY T.title";
			$result = mysql_query($sql, $db);
			?>
			<select name="group" id="groups">
				<option value="0" id="g0" ><?php echo _AT('entire_course'); ?></option>
			<?php while ($row = mysql_fetch_assoc($result)): ?>
				<option value="<?php echo $row['group_id']; ?>" id="g<?php echo $row['group_id']; ?>" <?php if ($group == $row['group_id']) { echo 'selected="selected"'; } ?> ><?php echo $row['type_title'] . ': ' . $row['title']; ?></option>
			<?php endwhile; ?>
			</select>

		</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('filter'); ?>" />
		<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
	</div>
</div>
</form>

<?php
if ($_GET['order'] == 'asc') {
	$order = 'desc';
} else {
	$order = 'asc';
}

$group_members = '';
if ($group) {
	$group_members = array();
	$sql = "SELECT member_id FROM ".TABLE_PREFIX."groups_members WHERE group_id=$group";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$group_members[] = $row['member_id'];
	}
	$group_members = ' AND C.member_id IN (' . implode(',', $group_members) . ')';
}

/* look through enrolled students list */
$sql_members = "SELECT C.member_id, C.approved, C.privileges, M.login, M.first_name, M.second_name, M.last_name FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M	WHERE C.course_id=$_SESSION[course_id] AND C.member_id=M.member_id AND (C.approved='y' OR C.approved='a')	$group_members ORDER BY M.login $order";

$result_members = mysql_query($sql_members, $db);

while ($row_members = mysql_fetch_assoc($result_members)) {
	$all_[$row_members['member_id']] = $row_members;
	$all_[$row_members['member_id']]['online'] = FALSE;
}

$sql_online = "SELECT member_id FROM ".TABLE_PREFIX."users_online WHERE course_id = $_SESSION[course_id] AND expiry>".time();
$result_online = mysql_query($sql_online, $db);

while ($row_online = mysql_fetch_assoc($result_online)) {
	if ($all_[$row_online['member_id']] != '') {
		$all_[$row_online['member_id']]['online'] = TRUE;
		$online[$row_online['member_id']] = $all_[$row_online['member_id']];
	}
}

if ($all) {
	$final = $all_;
} else if ($on) {
	$final = $online;
} else {
	foreach ($all_ as $id=>$attrs) {
		if ($attrs['online'] == FALSE) {
			$final[$id] = $attrs;
		}
	}
}

?>
<table class="data" rules="cols" summary="">
<thead>
<tr>
	<th scope="col"><?php echo _AT('login_name'); ?></th>
	<th scope="col"><?php echo _AT('full_name'); ?></th>
	<th scope="col"><?php echo _AT('status'); ?></th>
	<th scope="col"><?php echo _AT('online_status'); ?></th>
</tr>
</thead>
<tbody>
<?php
if ($final) {
	foreach ($final as $user_id=>$attrs) {
		echo '<tr onmousedown="document.location=\''.$_base_href.'profile.php?id='.$user_id.'\'">';
		$type = 'class="user"';
		if ($system_courses[$_SESSION['course_id']]['member_id'] == $user_id) {
			$type = 'class="user instructor" title="'._AT('instructor').'"';
		}
		echo '<td><a href="profile.php?id='.$user_id.'" '.$type.'>'.AT_print($attrs['login'], 'members.login') . '</a></td>';

		//echo '<td>'.AT_print($attrs['first_name'] .' '. $attrs['second_name'] .' '. $attrs['last_name'],'members.first_name').'</td>';
		echo '<td>'.AT_print(get_display_name($user_id)).'</td>';	
		
		if ($attrs['privileges'] != 0) {
			echo '<td>'._AT('assistant').'</td>';
		} else if ($attrs['approved'] == 'a') {
			/* if alumni display alumni */
			echo '<td>'._AT('alumni').'</td>';
		} else if ($attrs['approved'] == 'y') {
			if ($user_id == $system_courses[$_SESSION['course_id']]['member_id']) {
				echo '<td>'._AT('instructor').'</td>';
			} else {
				echo '<td>'._AT('enrolled').'</td>';
			}
		} else {
			echo '<td></td>';
		}
		
		if ($attrs['online'] == TRUE) {
			echo '<td><strong>'._AT('user_online').'</strong></td>';
		} else {
			echo '<td>'._AT('user_offline').'</td>';
		}

		echo '</tr>';
	}	
} else {
	echo '<tr><td colspan="3">' . _AT('none_found') . '</td></tr>';
}
?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>