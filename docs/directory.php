<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
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

if (!empty($_GET['roles'])) {
	foreach ($_GET['roles'] as $num=>$ena) {
		$ins_id = $system_courses[$_SESSION['course_id']]['member_id'];
		if ($ena == 1) {
			$ins = 'checked="checked"';
			$conditions[] = "C.member_id=$ins_id";
		}
		if ($ena == 2) {
			$stud = 'checked="checked"';
			$conditions[] = "(C.approved = 'y' AND C.member_id<>$ins_id)";
		}
		if ($ena == 3) {
			$ta  = 'checked="checked"';
			$conditions[] = "C.privileges <> 0";
		}
		if ($ena == 4) {
			$alum = 'checked="checked"';
			$conditions[] = "C.approved = 'a'"; 
		}
	}
} else {
	$ins  = 'checked="checked"';
	$stud = 'checked="checked"';
	$ta   = 'checked="checked"';
	$conditions[] = "C.approved<>'a'";
}

if (isset($_GET['status']) && ($_GET['status'] != '')) {
	if ($_GET['status'] == 1) {
		$on = 'checked="checked"';
	} else if ($_GET['status'] == 2) {
		$all = 'checked="checked"';
	} else if ($_GET['status'] == 0) {
		$off = 'checked="checked"';
	}
} else {
	$all = 'checked="checked"';
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
<div class="input-form">
	<div class="row">
		<?php echo _AT('role'); ?><br />
		<input type="checkbox" name="roles[]" id="r1" value="1" <?php echo $ins;  ?> /><label for="r1"><?php echo _AT('instructors'); ?></label>
		<input type="checkbox" name="roles[]" id="r2" value="2" <?php echo $stud; ?> /><label for="r2"><?php echo _AT('students');    ?></label>
		<input type="checkbox" name="roles[]" id="r3" value="3" <?php echo $ta;   ?> /><label for="r3"><?php echo _AT('assistants');  ?></label>
		<input type="checkbox" name="roles[]" id="r4" value="4" <?php echo $alum; ?> /><label for="r4"><?php echo _AT('alumni');      ?></label>
	</div>

	<div class="row">
		<?php echo _AT('online_status'); ?><br />
		<input type="radio" name="status" id="s1" value="1" <?php echo $on; ?>  /><label for="s1"><?php echo _AT('user_online');  ?></label>
		<input type="radio" name="status" id="s0" value="0" <?php echo $off; ?> /><label for="s0"><?php echo _AT('user_offline'); ?></label>
		<input type="radio" name="status" id="s2" value="2" <?php echo $all; ?> /><label for="s2"><?php echo _AT('all');          ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('filter'); ?>" onClick="javascript:verify();" />
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

if ($ins && $stud && $ta && $alum) {
	$conditions = "";
} else {
	$conditions = 'AND (' . implode(" OR ", $conditions) . ')';
}


/* look through enrolled students list */
$sql_members = "SELECT C.member_id, C.approved, C.role, M.login 
				FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M
				WHERE C.course_id=$_SESSION[course_id] AND C.member_id=M.member_id 
				AND C.approved<>'n' AND M.status>1 
				$conditions
				ORDER BY M.login $order";
$result_members = mysql_query($sql_members, $db);

while ($row_members = mysql_fetch_assoc($result_members)) {
	$all_[$row_members['member_id']] = $row_members;
	$all_[$row_members['member_id']]['online'] = FALSE;
}

$sql_online = "SELECT member_id FROM ".TABLE_PREFIX."users_online WHERE course_id = $_SESSION[course_id]";
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
	foreach ($all_ as $id=>$attrs)
		if ($attrs['online'] == FALSE) {
		$final[$id] = $attrs;
	}
}

?>
<table class="data" rules="cols" summary="">
<thead>
<tr>
	<th scope="col"><?php echo _AT('username'); ?></th>
	<th scope="col"><?php echo _AT('role'); ?></th>
	<th scope="col"><?php echo _AT('online_status'); ?></th>
</tr>
</thead>
<tbody>
<?php
if ($final) {
	foreach ($final as $user_id=>$attrs) {
		echo '<tr onmousedown="document.location=\'profile.php?id='.$user_id.'\'">';
		echo '<td><a href="profile.php?id='.$user_id.'">'.AT_print($attrs['login'], 'members.login') . '</a></td>';
		
		if ($attrs['approved'] == 'y') {
			if ($attrs['role'] != '') {
				echo '<td>'.AT_print($attrs['role'], 'members.login') . '</td>';
			} else {
				echo '<td>'._AT('student').'</td>';
			}
		} else if ($attrs['approved'] == 'a') {
			/* if alumni display alumni */
			echo '<td>'._AT('alumni').'</td>';
		}
		
		if ($attrs['online'] == TRUE) {
			echo '<td>'._AT('user_online').'</td>';
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

<script type="text/javascript">
<!--
function verify() {
	var roles = document.form['roles[]'];
	
	txt = "";
	for (i = 0; i < roles.length; i++) {
		if (roles[i].checked) {
			txt = txt + "something";
		}
	}

	if (txt == "") {
		alert("<?php echo _AT('no_roles_selected'); ?>");
	}

	return false;
}

-->
</script>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>