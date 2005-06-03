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
	/* OPTION 1 DELETE/REMOVE */

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
	/* OPTION 2 APPROVE ENROLL */
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
		header('Location: enroll_edit.php?'.$text.'func=enroll'.SEP.'curr_tab='.$_POST['curr_tab']);
		exit;
	}
} else if (isset($_POST['unenroll'])) {
	/* OPTION 3 UNENROLL*/

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
		header('Location: enroll_edit.php?'.$text.'func=unenroll'.SEP.'curr_tab='.$_POST['curr_tab']);
		exit;	
	}
} else if (isset($_POST['role'])) {
	/* OPTION 4 EDIT ROLE */
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
	/* OPTION 5 MAKE ALUMNI */
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
		header('Location: enroll_edit.php?'.$text.'func=alumni'.SEP.'curr_tab='.$_POST['curr_tab']);
		exit;
	}
} else if (isset($_POST['group_add'])) {
	/* OPTION 6 ADD TO GROUP */
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
	/* OPTION 7 REMOVE FROM GROUP */
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

require(AT_INCLUDE_PATH.'header.inc.php');
?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
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
				} ?>

			<input type="radio" name="group" value="" id="g" <?php if ($_GET['group'] == '') { echo 'checked="checked"'; } ?> /><label for="g"><?php echo _AT('all'); ?></label>
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>


<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="selectform">
<input type="hidden" name="curr_tab" value="<?php echo $current_tab; ?>" />
<input type="hidden" name="view_select_old" value="<?php echo $view_select; ?>" />

<!--output tabs -->
<?php $tabs = get_tabs(); 
$num_tabs = count($tabs); ?>
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