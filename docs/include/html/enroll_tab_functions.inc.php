<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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
* Generates the html for the tab action
* @access  private
* @param   int $current_tab		the tab selected currently
* @author  Shozub Qureshi
*/
function output_tabs($current_tab) {
	global $_base_path, $msg;
	$tabs = get_tabs();
	echo '<table cellspacing="0" cellpadding="0" width="92%" border="0" summary="" align="center"><tr>';
	echo '<td>&nbsp;</td>';
	
	$num_tabs = count($tabs);

	for ($i=0; $i < $num_tabs; $i++) {
		if ($current_tab == $i) {

		echo '<td class="etabself" width="23%" nowrap="nowrap">';
		echo _AT($tabs[$i][0]).'</td>';

		} else {
			echo '<td class="etab" width="23%">';
			echo '<input type="submit" name="button_'.$i.'" value="'._AT($tabs[$i][0]).'" title="'._AT($tabs[$i][0]).' - alt '.$tabs[$i][2].'" class="buttontab" accesskey="'.$tabs[$i][2].'" onmouseover="this.style.cursor=\'hand\';" '.$clickEvent.' /></td>';
		}
		echo '<td>&nbsp;</td>';
	}	
	echo '</tr></table>';
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
function generate_table($condition, $col, $order, $unenr, $view_select=0) {
	global $db;

	if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
		$condition .= ' AND CE.member_id<>'.$_SESSION['member_id'];
	}

	if ($view_select == -1) {
		$condition .= ' AND CE.privileges<>0';
	} else if ($view_select > 0) {
		$sql = "SELECT member_id FROM ".TABLE_PREFIX."groups_members WHERE group_id=$view_select";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			$members_list .= ',' . $row['member_id'];
		}
		$condition .= ' AND CE.member_id IN (0'.$members_list.')';
	}
	//output list of enrolled students
	$sql	= "SELECT CE.member_id, CE.role, M.login, M.first_name, M.last_name, M.email
					FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M ON CE.member_id=M.member_id 
					WHERE CE.course_id=$_SESSION[course_id]
					AND      ($condition)
					ORDER BY $col $order";
	$result	= mysql_query($sql, $db);
	
	//if table is empty display message
	if (mysql_num_rows($result) == 0)  {
		echo '<tr><td align="center" class="row1" colspan="6"><i>'._AT('empty').'</i></td></tr>';
	} else {
		
		while ($row  = mysql_fetch_assoc($result)){
			if (authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN) && ($row['member_id'] == $_SESSION['member_id'])) {
				echo'<tr><td class="row1" align="left">
						<input type="checkbox" name="id[]" disabled="disabled" value="'.$row['member_id'].'" />';
			} else {
				echo'<tr><td class="row1" align="left" nowrap="nowrap"><label><input type="checkbox" name="id[]" value="'.$row['member_id'].'" />';
			}
			echo $row['login'] . '</label> </td>
				 <td class="row1">' . $row['email'] . '</td>
				<td class="row1">' . $row['first_name'] . '</td>
				<td class="row1">' . $row['last_name']  . '</td>
				<td class="row1">';
			
			//if role not already assigned, assign role to be student
			//and we are not vieiwing list of unenrolled students
			if ($row['role'] == '' && $unenr != 1) {
				$id2 = $row['member_id'];
				$sql2 = "UPDATE ".TABLE_PREFIX."course_enrollment SET `role`='Student' WHERE member_id=($id2)";
				$result2 = mysql_query($sql2,$db);
				echo _AT('Student');
			} else if ($unenr == 1) {
				echo _AT('na');
			} else {
				echo $row['role'];
			}
			echo '</td></tr><tr><td height="1" class="row2" colspan="6"></td></tr>';
		}
	}
	echo '<tr><td height="1" class="row2" colspan="6"></td></tr>';
	echo '<tr><td align="center" colspan="6" class="row1"><br />';
}

/**
* Generates the html for the SORTED enrollment tables
* @access  private
* @param   string $column		the column presently selected
* @param   string $col			the column to be sorted
* @param   string $order		the sorting order (DESC or ASC)
* @author  Shozub Qureshi
*/
function sort_columns ($column, $order, $col, $curr_tab) {
	if ($curr_tab == '') {
		$curr_tab = 0;
	}

	if 	($order == 'asc' && $column == $col) {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?col='.$column.SEP.'order=desc'.SEP.'current_tab='.$curr_tab.'">';
		echo _AT($column);
		echo ' <img src="images/asc.gif" alt="'._AT('id_ascending').'" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a>';
	} 
	
	else if ($order == 'desc' && $column == $col){
		echo '<a href="'.$_SERVER['PHP_SELF'].'?col='.$column.SEP.'order=asc'.SEP.'current_tab='.$curr_tab.'" >';
		echo _AT($column);
		echo ' <img src="images/desc.gif" alt="'._AT('id_descending').'" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a>';
	}
	else {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?col='.$column.SEP.'order=asc'.SEP.'current_tab='.$curr_tab.'" >';
		echo _AT($column) . '</a>';
	}
}

?>