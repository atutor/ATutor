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
	$ins = 'checked="checked"';
	$stud = 'checked="checked"';
	$ta  = 'checked="checked"';
	$conditions[] = "C.approved<>'a'";
}

if ($_GET['status'] != '') {
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
		<label for="roles[]"><?php echo _AT('role'); ?></label><br />
		<input type="checkbox" name="roles[]" id="instructor" value="1" <?php echo $ins; ?> /><label for="instructor"><?php echo _AT('instructors'); ?></label>
		<input type="checkbox" name="roles[]" id="student" value="2" <?php echo $stud; ?> /><label for="student"><?php echo _AT('students'); ?></label>
		<input type="checkbox" name="roles[]" id="ta" value="3" <?php echo $ta; ?> /><label for="ta"><?php echo _AT('assistants'); ?></label>
		<input type="checkbox" name="roles[]" id="alumni" value="4" <?php echo $alum; ?> /><label for="alumni"><?php echo _AT('alumni'); ?></label>
	</div>

	<div class="row">
		<label for="id"><?php echo _AT('online_status'); ?></label><br />
		<input type="radio" name="status" id="online" value="1" <?php echo $on; ?> /><label for="online"><?php echo _AT('user_online'); ?></label>
		<input type="radio" name="status" id="offline" value="0" <?php echo $off; ?> /><label for="offline"><?php echo _AT('user_offline'); ?></label>
		<input type="radio" name="status" id="all" value="2" <?php echo $all; ?> /><label for="all"><?php echo _AT('all'); ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('view'); ?>" onClick="javascript:verify();" />
	</div>
</div>
</form>

<?php

if ($_GET['order']) {
	$order = $addslashes($_GET['order']);
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
				WHERE C.course_id=$_SESSION[course_id] AND C.member_id=M.member_id  AND C.approved<>'n' 
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
		<th scope="col"><?php echo _AT('login') . ' <a href="' . $_SERVER['PHP_SELF'] . '?order=asc" title="' . _AT('username_ascending') . '"><img src="images/asc.gif" alt="' . _AT('username_ascending') . '" border="0" height="7" width="11" /></a> <a href="' . $_SERVER['PHP_SELF'] . '?order=desc" title="' . _AT('username_descending') . '"><img src="images/desc.gif" alt="' . _AT('username_descending') . '" border="0" height="7" width="11" /></a>'; ?></th>
		<th scope="col"><?php echo _AT('role'); ?></th>
		<th scope="col"><?php echo _AT('online_status'); ?></th>
	</tr>
	</thead>
	<tbody>
<?php
if ($final) {
	foreach ($final as $user_id=>$attrs) {
		echo '<tr onmousedown="document.location=\'profile.php?id='.$user_id.'\'">';
		/* if enrolled display login, role */
		if ($attrs['approved'] == 'y') {
			echo '<td><a href="profile.php?id='.$user_id.'">'.$attrs['login'].'</a></td>';
			if ($attrs['role'] != '') {
				echo '<td>'.$attrs['role'].'</td>';
			} else {
				echo '<td>'._AT('student').'</td>';
			}
		}
		
		/* if alumni display alumni */
		else if ($attrs['approved'] == 'a') {
			echo '<td>'.$attrs['login'].'</td>';
			echo '<td>'._AT('alumni').'</td>';
		}
		
		if ($attrs['online'] == TRUE) {
			echo '<td>'._AT('user_online').'</td>';
		} else {
			echo '<td>'._AT('user_offline').'</td>';
		}

		echo '</tr>';
	}	
}
// no students
else {
	echo '<tr><td colspan="3">' . _AT('none_found') . '</td></tr>';
}
echo '</tbody>';
echo '</table>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>

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