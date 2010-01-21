<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

$db;

/**
* Generates the tabs for the enroll admin page
* @access  private
* @return  string				The tabs for the enroll_admin page
* @author  Shozub Qureshi
*/
function get_tabs() {
	//these are the _AT(x) variable names and their include file
	/* tabs[tab_id] = array(tab_name, file_name,                accesskey) */
	$tabs[0] = array('enrolled',   'enroll_admin.php', 'e');
	$tabs[1] = array('unenrolled', 'enroll_admin.php', 'u');
	//$tabs[2] = array('assistants', 'enroll_admin.php', 'a');
	$tabs[2] = array('alumni',	   'enroll_admin.php', 'a');

	return $tabs;
}

/**
* Generates the html for the enrollment tables
* @access  private
* @param   string $condition	the condition to be imposed in the sql query (approved = y/n/a)
* @param   string $col			the column to be sorted
* @param   string $order		the sorting order (DESC or ASC)
* @param   int $unenr			is one if the unenrolled list is being generated
* @author  Shozub Qureshi
* @author  Joel Kronenberg
*/
function generate_table($condition, $col, $order, $unenr, $filter) {
	global $db;

	if ($filter['role'] == -1) {
		$condition .= ' AND CE.privileges<>0';
	}
	if ($filter['group'] > 0) {
		$sql = "SELECT member_id FROM ".TABLE_PREFIX."groups_members WHERE group_id=".$filter['group'];
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			$members_list .= ',' . $row['member_id'];
		}
		$condition .= ' AND CE.member_id IN (0'.$members_list.')';
	}
	if (isset($filter['status'])) {
		$condition .= ' AND M.status='.$filter['status'];
	}

	//output list of enrolled students
	$sql	=  "SELECT CE.member_id, CE.role, M.login, M.first_name, M.last_name, M.email, M.status 
				FROM ".TABLE_PREFIX."course_enrollment CE, ".TABLE_PREFIX."members M 
				WHERE CE.course_id=$_SESSION[course_id] AND CE.member_id=M.member_id AND ($condition) 
				ORDER BY $col $order";
	$result	= mysql_query($sql, $db);
	echo '<tbody>';
	//if table is empty display message
	if (mysql_num_rows($result) == 0) {
		echo '<tr><td colspan="6">'._AT('none_found').'</td></tr>';
	} else {
		while ($row  = mysql_fetch_assoc($result)) {
			echo '<tr onmousedown="document.selectform[\'m' . $row['member_id'] . '\'].checked = !document.selectform[\'m' . $row['member_id'] . '\'].checked;">';
			echo '<td>';

			$act = "";
			if ($row['member_id'] == $_SESSION['member_id']) {
				$act = 'disabled="disabled"';	
			} 
			
			echo '<input type="checkbox" name="id[]" value="'.$row['member_id'].'" id="m'.$row['member_id'].'" ' . $act . ' onmouseup="this.checked=!this.checked" title="'.AT_print($row['login'], 'members.login').'" />';
			echo AT_print($row['login'], 'members.login') . '</td>';
			echo '<td>' . AT_print($row['first_name'], 'members.name') . '</td>';
			echo '<td>' . AT_print($row['last_name'], 'members.name')  . '</td>';
			echo '<td>' . AT_print($row['email'], 'members.email') . '</td>';
			
			//if role not already assigned, assign role to be student
			//and we are not vieiwing list of unenrolled students
			echo '<td>';
			if ($row['status'] == AT_STATUS_DISABLED) {
				echo _AT('disabled');
			} else if ($row['status'] == AT_STATUS_UNCONFIRMED) {
				echo _AT('unconfirmed');
			} else if ($row['role'] == '' && $unenr != 1) {
				echo _AT('Student');
			} else if ($unenr == 1) {
				echo _AT('na');
			} else {
				echo AT_print($row['role'], 'members.role');
			}
			echo '</td>';

			echo '</tr>';
		}		
	}
	echo '</tbody>';
}

/**
* Generates the html for the SORTED enrollment tables
* @access  private
* @param   int $curr_tab	the current tab (enrolled, unenrolled or alumni)
* @author  Shozub Qureshi
*/
function display_columns ($curr_tab) {
	global $orders;
	global $order;
?>
	<th scope="col"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAll();" /> <a href="mods/_core/enrolment/index.php?<?php echo $orders[$order]; ?>=login<?php echo SEP;?>current_tab=<?php echo $curr_tab; ?>"><?php echo _AT('login_name'); ?></a></th>

	<th scope="col"><a href="mods/_core/enrolment/index.php?<?php echo $orders[$order]; ?>=first_name<?php echo SEP;?>current_tab=<?php echo $curr_tab; ?>"><?php echo _AT('first_name'); ?></a></th>

	<th scope="col"><a href="mods/_core/enrolmentt/index.php?<?php echo $orders[$order]; ?>=last_name<?php echo SEP;?>current_tab=<?php echo $curr_tab; ?>"><?php echo _AT('last_name'); ?></a></th>

	<th scope="col"><a href="mods/_core/enrolment/index.php?<?php echo $orders[$order]; ?>=email<?php echo SEP;?>current_tab=<?php echo $curr_tab; ?>"><?php echo _AT('email'); ?></a></th>

	<th scope="col"><a href="mods/_core/enrolment/index.php?<?php echo $orders[$order]; ?>=role<?php echo SEP;?>current_tab=<?php echo $curr_tab; ?>"><?php echo _AT('role').'/'._AT('status'); ?></a></th>
<?php	
}

?>