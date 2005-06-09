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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

/* make sure we own this course that we're approving for! */
if (!authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('NOT_OWNER');
	require (AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

if (isset($_POST['delete'])) {
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}	
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=remove'.SEP.'curr_tab='.$_POST['curr_tab']);
		exit;
	}
} else if (isset($_POST['enroll'])) {
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}	
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=enroll'.SEP.'curr_tab=0');
		exit;
	}
} else if (isset($_POST['unenroll'])) {
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=unenroll'.SEP.'curr_tab=1');
		exit;	
	}
} else if (isset($_POST['role'])) {
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'mid'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: privileges.php?'.$text);
		exit;
	}
} else if (isset($_POST['alumni'])) {
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=alumni'.SEP.'curr_tab=2');
		exit;
	}
} else if (isset($_POST['group_add'])) {
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}
	else {
		$group_id = intval($_POST['group_id']);
		if ($group_id && is_array($_POST['id'])) {

			$i=0;
			foreach ($_POST['id'] as $elem) {
				$text .= 'id'.$i.'='.$elem.SEP;
				$i++;
			}
			header('Location: enroll_edit.php?'.$text.'func=group'.SEP.'gid='.$group_id.SEP.'curr_tab='.$_POST['curr_tab']);
			exit;
		}
	}
} else if (isset($_POST['group_remove'])) {
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}
	else {
		$group_id = intval($_POST['group_id']);
		if ($group_id && is_array($_POST['id'])) {

			$i=0;
			foreach ($_POST['id'] as $elem) {
				$text .= 'id'.$i.'='.$elem.SEP;
				$i++;
			}
			header('Location: enroll_edit.php?'.$text.'func=group_remove'.SEP.'gid='.$group_id.SEP.'curr_tab='.$_POST['curr_tab']);
			exit;
		}
	}
}

//filter stuff:

if ($_GET['reset_filter']) {
	unset($_GET);
}

$filter=array();

if (isset($_GET['role']) && ($_GET['role'] != '')) {
	$filter['role'] = intval($_GET['role']);
} 

if (isset($_GET['status']) && ($_GET['status'] != '')) {
	$filter['status'] = intval($_GET['status']);
} 

if (isset($_GET['group']) && ($_GET['group'] != '')) {
	$filter['group'] = intval($_GET['group']);
} 

require(AT_INCLUDE_PATH.'html/enroll_tab_functions.inc.php');
$tabs = get_tabs();	
$num_tabs = count($tabs);

for ($i=0; $i < $num_tabs; $i++) {
	if (isset($_POST['button_'.$i]) && ($_POST['button_'.$i] != -1)) { 
		$current_tab = $i;
		$_POST['current_tab'] = $i;
		break;
	}
}

//get present tab if specified
if ($_GET['current_tab']) {
	$current_tab = $_GET['current_tab'];
	$_POST['current_tab'] = $_GET['current_tab'];
}

$orders = array('asc' => 'desc', 'desc' => 'asc');

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = $addslashes($_GET['asc']);
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = $addslashes($_GET['desc']);
} else {
	// no order set
	$order = 'asc';
	$col   = 'login';
}
$view_select = intval($_POST['view_select']);

// the possible tabs. order matters.
$tabs = array('enrolled', 'alumni', 'pending', 'all_users');
$tabs = array('enrolled', 'enrolled_privileged', 'alumni', 'pending', 'unenrolled');
$tabs = array('enrolled_basic', 'enrolled_privileged', 'enrolled_alumni', 'pending_enrollment', 'not_enrolled');
$tabs = array('Enrolled Basic', 'Enrolled w/ Privileges', 'Enrolled Alumni', 'Pending Enrollment', 'Not Enrolled');

$num_tabs = count($tabs);
if (isset($_REQUEST['tab'])) {
	$current_tab = intval($_REQUEST['tab']);
}

if (!isset($current_tab)) {
	$current_tab = 0;
}

if ($current_tab == 0) {
	// enrolled
	$sql_cnt = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment CE, ".TABLE_PREFIX."members M 
				WHERE CE.course_id=$_SESSION[course_id] AND CE.member_id=M.member_id AND approved='y' AND M.member_id<>$_SESSION[member_id] AND CE.privileges=0";
	$sql	=  "SELECT CE.member_id, CE.privileges, CE.approved, M.login, M.first_name, M.last_name, M.email 
				FROM ".TABLE_PREFIX."course_enrollment CE, ".TABLE_PREFIX."members M 
				WHERE CE.course_id=$_SESSION[course_id] AND CE.member_id=M.member_id AND approved='y' AND M.member_id<>$_SESSION[member_id] AND CE.privileges=0
				ORDER BY $col $order";
} else if ($current_tab == 1) {
	// assistants
	$sql_cnt=  "SELECT COUNT(*) AS cnt
				FROM ".TABLE_PREFIX."course_enrollment CE, ".TABLE_PREFIX."members M 
				WHERE CE.course_id=$_SESSION[course_id] AND CE.member_id=M.member_id AND CE.approved='y' AND CE.privileges>0";
	$sql	=  "SELECT CE.member_id, CE.approved, CE.privileges, M.login, M.first_name, M.last_name, M.email 
				FROM ".TABLE_PREFIX."course_enrollment CE, ".TABLE_PREFIX."members M 
				WHERE CE.course_id=$_SESSION[course_id] AND CE.member_id=M.member_id AND CE.approved='y' AND CE.privileges>0
				ORDER BY $col $order";

} else if ($current_tab == 3) {
	// pending
	if ($system_courses[$_SESSION['course_id']]['access'] == 'private') {
		$sql_cnt = "SELECT COUNT(*) AS cnt 
				FROM ".TABLE_PREFIX."course_enrollment CE, ".TABLE_PREFIX."members M 
				WHERE CE.course_id=$_SESSION[course_id] AND CE.member_id=M.member_id AND approved='n'";

		$sql	=  "SELECT CE.member_id, CE.approved, CE.privileges, M.login, M.first_name, M.last_name, M.email 
				FROM ".TABLE_PREFIX."course_enrollment CE, ".TABLE_PREFIX."members M 
				WHERE CE.course_id=$_SESSION[course_id] AND CE.member_id=M.member_id AND approved='n'
				ORDER BY $col $order";
	} else {
		$sql_cnt = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."members WHERE 0";
		$sql = "SELECT login FROM ".TABLE_PREFIX."members WHERE 0";
	}
} else if ($current_tab == 2) {
	// alumni
	$sql_cnt=  "SELECT COUNT(*) AS cnt
				FROM ".TABLE_PREFIX."course_enrollment CE, ".TABLE_PREFIX."members M 
				WHERE CE.course_id=$_SESSION[course_id] AND CE.member_id=M.member_id AND approved='a'";
	$sql	=  "SELECT CE.member_id, CE.approved, CE.privileges, M.login, M.first_name, M.last_name, M.email 
				FROM ".TABLE_PREFIX."course_enrollment CE, ".TABLE_PREFIX."members M 
				WHERE CE.course_id=$_SESSION[course_id] AND CE.member_id=M.member_id AND approved='a'
				ORDER BY $col $order";
} else {
	$tmp_sql	=  "SELECT member_id FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$_SESSION[course_id] AND member_id<>$_SESSION[member_id]";
	$tmp_result = mysql_query($tmp_sql, $db);
	$course_enrollment = '';
	while ($row = mysql_fetch_assoc($tmp_result)) {
		$course_enrollment .= $row['member_id'] .',';
	}
	$course_enrollment .= $_SESSION['member_id'];


	// else if ($current_tab == 3)
	// all users
	//$sql_cnt=  "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."members M LEFT JOIN ".TABLE_PREFIX."course_enrollment CE USING (member_id) WHERE M.member_id <> $_SESSION[member_id] AND M.status>0 AND (CE.course_id=$_SESSION[course_id] OR CE.course_id IS NULL)";

	$sql_cnt=  "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."members M WHERE M.status>1 AND M.member_id NOT IN ($course_enrollment)";


	//$sql	=  "SELECT M.status, M.member_id, M.login, M.first_name, M.last_name, M.email, CE.approved, CE.privileges FROM ".TABLE_PREFIX."members M LEFT JOIN ".TABLE_PREFIX."course_enrollment CE USING (member_id) WHERE M.member_id <> $_SESSION[member_id] AND M.status>1 AND (CE.course_id=$_SESSION[course_id] OR CE.course_id IS NULL) ORDER BY $col $order";

	$sql	=  "SELECT M.member_id, M.login, M.first_name, M.last_name, M.email FROM ".TABLE_PREFIX."members M WHERE M.member_id NOT IN ($course_enrollment) AND M.status>1 ORDER BY $col $order";

/*
	$tmp_sql	=  "SELECT CE.member_id, CE.privileges, CE.approved FROM ".TABLE_PREFIX."course_enrollment CE WHERE CE.course_id=$_SESSION[course_id] AND CE.member_id<>$_SESSION[member_id]";
	$tmp_result = mysql_query($tmp_sql, $db);
	$course_status = array();
	while ($row = mysql_fetch_assoc($tmp_result)) {
		$id = $row['member_id'];
		unset($row['member_id']);
		$course_status[$id] = $row;
	}
*/
}

$results_per_page = 50;

$result = mysql_query($sql_cnt, $db);
$row = mysql_fetch_assoc($result);
$num_results = $row['cnt'];

$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * $results_per_page) + 1;
$offset = ($page-1)*$results_per_page;
$sql .= " LIMIT $offset, $results_per_page";

$enrollment_result = mysql_query($sql, $db);
//debug(mysql_error($db));
$page_string = SEP . 'tab='.$current_tab;
require(AT_INCLUDE_PATH.'header.inc.php');

?>
<!--form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('results_found', $num_results); ?></h3>
		</div>

		<div class="row">
			<?php echo _AT('role'); ?><br />
			<input type="radio" name="role" value="-1" id="r0" <?php if ($_GET['role'] == -1) { echo 'checked="checked"'; } ?> /><label for="r0"><?php echo _AT('assistants'); ?></label> 

			<input type="radio" name="role" value="" id="r" <?php if ($_GET['role'] == '') { echo 'checked="checked"'; } ?> /><label for="r"><?php echo _AT('all'); ?></label>
		</div>

		<div class="row">
			<?php echo _AT('account_status'); ?><br />
			<input type="radio" name="status" value="0" id="s0" <?php if ($_GET['status'] == 0) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('disabled'); ?></label> 

			<input type="radio" name="status" value="1" id="s1" <?php if ($_GET['status'] == 1) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('unconfirmed'); ?></label> 

			<input type="radio" name="status" value="2" id="s2" <?php if ($_GET['status'] == 2) { echo 'checked="checked"'; } ?> /><label for="s2"><?php echo _AT('student'); ?></label>

			<input type="radio" name="status" value="3" id="s3" <?php if ($_GET['status'] == 3) { echo 'checked="checked"'; } ?> /><label for="s3"><?php echo _AT('instructor'); ?></label>

			<input type="radio" name="status" value="" id="s" <?php if ($_GET['status'] == '') { echo 'checked="checked"'; } ?> /><label for="s"><?php echo _AT('all'); ?></label>
		</div>

		<div class="row">
			<?php echo _AT('group'); ?><br />

				<?php
				/*
				$sql    = "SELECT group_id, title FROM ".TABLE_PREFIX."groups WHERE course_id=$_SESSION[course_id] ORDER BY title";
				$result = mysql_query($sql, $db);
				if($row = mysql_fetch_assoc($result)) {
					do {
						//for group dropdown 
						$groups_options .= '<option value="'.$row['group_id'].'"';
						 if ($view_select == $row['group_id']) { 
							 $groups_options .= ' selected="selected"'; 
						 }
						$groups_options .= '>'.$row['title'].'</option>';

						//for filter
						echo '<input type="radio" name="group" value="'.$row['group_id'].'" id="g'.$row['group_id'].'"';
						if ($_GET['group'] == $row['group_id']) { echo 'checked="checked"'; } 
						echo '/><label for="g'.$row['group_id'].'">'.$row['title'].'</label>';
					} while ($row = mysql_fetch_assoc($result));
				} */ ?>

			<input type="radio" name="group" value="" id="g" <?php if ($_GET['group'] == '') { echo 'checked="checked"'; } ?> /><label for="g"><?php echo _AT('all'); ?></label>
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form-->

<div class="paging">
	<ul>
	<?php for ($i=1; $i<=$num_pages; $i++): ?>
		<li>
			<?php if ($i == $page) : ?>
				<a class="current" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string.SEP.$order.'='.$col; ?>"><em><?php echo $i; ?></em></a>
			<?php else: ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string.SEP.$order.'='.$col; ?>"><?php echo $i; ?></a>
			<?php endif; ?>
		</li>
	<?php endfor; ?>
	</ul>
</div>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="selectform">

<div style="width: 90%; margin-right: auto; margin-left: auto;">
<ul id="navlist">
	<?php for ($i = 0; $i< $num_tabs; $i++): ?>
		<?php if ($current_tab == $i): ?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tab=<?php echo $i; ?>" class="active"><strong><?php echo ($tabs[$i]); ?></strong></a></li>
		<?php else: ?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tab=<?php echo $i; ?>"><?php echo ($tabs[$i]); ?></a></li>
		<?php endif; ?>
	<?php endfor; ?>
</ul>
</div>
<table class="data" style="width:90%;" summary="" rules="cols">
<colgroup>
	<?php if ($col == 'login'): ?>
		<col />
		<col class="sort" />
		<col span="3" />
	<?php elseif($col == 'first_name'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="2" />
	<?php elseif($col == 'last_name'): ?>
		<col span="3" />
		<col class="sort" />
		<col />
	<?php elseif($col == 'email'): ?>
		<col span="4" />
		<col class="sort" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col" align="left"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAll();" /></th>

	<th scope="col"><a href="tools/enrollment/index.php?<?php echo $orders[$order]; ?>=login<?php echo SEP;?>tab=<?php echo $current_tab; ?>"><?php echo _AT('login_name'); ?></a></th>

	<th scope="col"><a href="tools/enrollment/index.php?<?php echo $orders[$order]; ?>=first_name<?php echo SEP;?>tab=<?php echo $current_tab; ?>"><?php echo _AT('first_name'); ?></a></th>

	<th scope="col"><a href="tools/enrollment/index.php?<?php echo $orders[$order]; ?>=last_name<?php echo SEP;?>tab=<?php echo $current_tab; ?>"><?php echo _AT('last_name'); ?></a></th>

	<th scope="col"><a href="tools/enrollment/index.php?<?php echo $orders[$order]; ?>=email<?php echo SEP;?>tab=<?php echo $current_tab; ?>"><?php echo _AT('email'); ?></a></th>

	<!--th scope="col"><?php echo _AT('role').'/'._AT('status'); ?></th-->
</tr>
</thead>
<tbody>
<?php if ($num_results): ?>
	<?php while ($row = mysql_fetch_assoc($enrollment_result)): ?>
		<tr onmousedown="document.selectform['m<?php echo $row['member_id']; ?>'].checked = !document.selectform['m<?php echo $row['member_id']; ?>'].checked;">
			<td><input type="checkbox" name="id[]" value="<?php echo $row['member_id']; ?>" id="m<?php echo $row['member_id']; ?>" onmouseup="this.checked=!this.checked" title="<?php echo AT_print($row['login'], 'members.login'); ?>" /></td>
			<td><?php echo AT_print($row['login'], 'members.login'); ?></td>
			<td><?php echo AT_print($row['first_name'], 'members.name'); ?></td>
			<td><?php echo AT_print($row['last_name'], 'members.name'); ?></td>
			<td><?php echo AT_print($row['email'], 'members.email'); ?></td>
		</tr>
	<?php endwhile; ?>
<?php else: ?>
	<tr>
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
<tfoot>
<tr>
	<td colspan="5">
		<?php if ($current_tab == 0): ?>
			<input type="submit" name="role"     value="<?php echo _AT('privileges');  ?>" /> 
			<input type="submit" name="unenroll" value="<?php echo _AT('unenroll');    ?>" /> 
			<input type="submit" name="alumni"   value="<?php echo _AT('mark_alumni'); ?>" />
<?php /*
		if ($filter['group'] > 0) {
			echo '<input type="submit" name="group_remove" value="'._AT('remove_from_group').'" />';
			echo '<input type="hidden" name="group_id" value="'.$filter['group'].'" />';
		} else {
			if ($groups_options) {
				echo '<input type="submit" name="group_add" value="'._AT('add_to_group').'" /> ';
				echo '<select name="group_id"><optgroup label="'._AT('groups').'">'.$groups_options.'</optgroup></select>';
			} else {
				echo '<input type="submit" name="group_add" value="'._AT('add_to_group').'" disabled /> ';
				echo '<select name="group_id"><optgroup label="'._AT('groups').'" >'.$groups_options.'</optgroup></select>';

			}
		} */ ?>
		<?php elseif ($current_tab == 1): ?>
			<input type="submit" name="role" value="<?php echo _AT('privileges'); ?>" /> 
			<input type="submit" name="unenroll" value="<?php echo _AT('unenroll'); ?>" /> 

		<?php elseif ($current_tab == 3): ?>
			<input type="submit" name="enroll" value="<?php echo _AT('enroll'); ?>" /> 
			<input type="submit" name="alumni" value="<?php echo _AT('mark_alumni'); ?>" /> 
			<input type="submit" name="delete" value="<?php echo _AT('remove'); ?>" />

		<?php elseif ($current_tab == 2): ?>
			<input type="submit" name="enroll"   value="<?php echo _AT('enroll'); ?>" /> 
			<input type="submit" name="unenroll" value="<?php echo _AT('remove'); ?>" />
		
		<?php elseif ($current_tab == 4): ?>
			<input type="submit" name="enroll"   value="<?php echo _AT('enroll'); ?>" /> 

		<?php endif; ?></td>
</tr>
</tfoot>
</table>
</form>

<script language="JavaScript" type="text/javascript">
<!--
function CheckAll() {
	
	for (var i=0;i<document.selectform.elements.length;i++)	{
		var e = document.selectform.elements[i];
		if ((e.name == 'id[]') && (e.type=='checkbox'))
			e.checked = document.selectform.selectall.checked;
	}
}
-->
</script>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); exit; ?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="selectform">
<input type="hidden" name="curr_tab" value="<?php echo $current_tab; ?>" />
<input type="hidden" name="view_select_old" value="<?php echo $view_select; ?>" />

<!--output tabs -->
<table cellspacing="0" cellpadding="0" width="90%" border="0" summary="" align="center" style="border-bottom:1px #98AAB1 solid;"><tr>
<?php for ($i=0; $i < $num_tabs; $i++) {
		if ($current_tab == $i) {
			echo '<td class="etab" style="background-color:#fafafa; font-weight:bold;" width="15%" nowrap="nowrap">';
			echo _AT($tabs[$i][0]).'</td>';
		} else {
			echo '<td style="background-color:#efefef;border:1px #98AAB1 solid; text-align:center;" width="15%">';
			echo '<input type="submit" name="button_'.$i.'" value="'._AT($tabs[$i][0]).'" title="'._AT($tabs[$i][0]).' - alt '.$tabs[$i][2].'" class="buttontab2" accesskey="'.$tabs[$i][2].'" onmouseover="this.style.cursor=\'hand\';" '.$clickEvent.' /></td>';
		}
		echo '<td width="5" style="border-bottom:1px #98AAB1 solid;">&nbsp;</td>';
	}	
?>
	<td width="100%" style="border-bottom:1px #98AAB1 solid;">&nbsp;</td>
</tr>
</table>
<!-- end output tabs -->

<table class="data" style="width:90%;" summary="" rules="cols">
<thead>
<tr><?php display_columns($current_tab); ?></tr>
</thead><?php

	$condition = 'CE.member_id<>' . $system_courses[$_SESSION['course_id']]['member_id'];
	echo '<tfoot><tr><td colspan="6">';
	//if viewing list of unenrolled students
	if ($current_tab == 1) {
		echo '<input type="submit" name="enroll" value="'._AT('enroll').'" /> ';
		echo '<input type="submit" name="alumni" value="'._AT('mark_alumni').'" /> ';
		echo '<input type="submit" name="delete" value="'._AT('remove').'" />';
		echo '</td></tr></tfoot>';
		$condition .= " AND CE.approved='n' OR M.status=0";
		generate_table($condition, $col, $order, 1, $filter);
	}
	//if viewing list of Alumni
	else if ($current_tab == 2) {
		echo '<input type="submit" name="enroll"   value="'._AT('enroll').'" /> ';
		echo '<input type="submit" name="unenroll" value="'._AT('unenroll').'" />';
		echo '</td></tr></tfoot>';
		$condition .= " AND CE.approved = 'a'";
		generate_table($condition, $col, $order, 0, $filter);
	} 
	//if veiwing list of enrolled students
	else {
		echo '<input type="submit" name="role" value="'._AT('roles_privileges').'" /> ';
		echo '<input type="submit" name="unenroll" value="'._AT('unenroll').'" /> ';
		echo '<input type="submit" name="alumni" value="'._AT('mark_alumni').'" />';

		if ($filter['group'] > 0) {
			echo '<input type="submit" name="group_remove" value="'._AT('remove_from_group').'" />';
			echo '<input type="hidden" name="group_id" value="'.$filter['group'].'" />';
		} else {
			if ($groups_options) {
				echo '<input type="submit" name="group_add" value="'._AT('add_to_group').'" /> ';
				echo '<select name="group_id"><optgroup label="'._AT('groups').'">'.$groups_options.'</optgroup></select>';
			} else {
				echo '<input type="submit" name="group_add" value="'._AT('add_to_group').'" disabled /> ';
				echo '<select name="group_id"><optgroup label="'._AT('groups').'" >'.$groups_options.'</optgroup></select>';

			}
		}
		echo '</td></tr></tfoot>';
		$condition .= " AND CE.approved='y' AND M.status<>0";
		generate_table($condition, $col, $order, 'button_1', $filter);
	}

?></table>
</form>

<script language="JavaScript" type="text/javascript">
<!--
function CheckAll() {
	
	for (var i=0;i<document.selectform.elements.length;i++)	{
		var e = document.selectform.elements[i];
		if ((e.name == 'id[]') && (e.type=='checkbox'))
			e.checked = document.selectform.selectall.checked;
	}
}
-->
</script>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>