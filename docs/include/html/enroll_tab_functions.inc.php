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

function get_tabs() {
	//these are the _AT(x) variable names and their include file
	/* tabs[tab_id] = array(tab_name, file_name,                accesskey) */
	$tabs[0] = array('enrolled',    'enroll_admin.php?enrolled_list=1',   'e');
	$tabs[1] = array('unenrolled',  'enroll_admin.php?unenrolled_list=1', 'p');
	$tabs[2] = array('assisstants', 'enroll_admin.php?assisstant_list=1', 'a');

	return $tabs;
}

function output_tabs($current_tab) {
	global $_base_path;
	$tabs = get_tabs();
	echo '<table cellspacing="0" cellpadding="0" width="90%" border="0" summary="" align="center"><tr>';
	echo '<td>&nbsp;</td>';
	
	$num_tabs = count($tabs);

	for ($i=0; $i < $num_tabs; $i++) {
		if ($current_tab == $i) {
			echo '<td class="etabself" width="30%" nowrap="nowrap">';
			echo _AT($tabs[$i][0]).'</td>';

		} else {
			echo '<td class="etab" width="30%">';
			echo '<input type="submit" name="button_'.$i.'" value="'._AT($tabs[$i][0]).'" title="'._AT($tabs[$i][0]).' - alt '.$tabs[$i][2].'" class="buttontab" accesskey="'.$tabs[$i][2].'" onmouseover="this.style.cursor=\'hand\';" '.$clickEvent.' /></td>';
		}
		echo '<td>&nbsp;</td>';
	}	
	echo '</tr></table>';
}

function generate_table($condition, $col, $order, $cid) {
	global $db;
	
	$sql2 = "SELECT member_id FROM ".TABLE_PREFIX."
	//output list of enrolled students
	$sql	= "SELECT DISTINCT cm.member_id, cm.role, m.login, m.first_name, m.last_name, m.email
				FROM ".TABLE_PREFIX."course_enrollment cm, ".TABLE_PREFIX."members m, ".TABLE_PREFIX."courses c
				WHERE cm.member_id = m.member_id
				AND ($condition)
				AND cm.member_id <> c.member_id
				AND cm.course_id = ($cid)
				ORDER BY $col $order";
	$result	= mysql_query($sql, $db);
	
	//if table is empty display message
	if (mysql_num_rows($result) == 0)  {
		echo '<tr><td align="center" class="row1" colspan="6"><i>'._AT('empty').'</i></td></tr>';

	} else {
		
		while ($row  = mysql_fetch_assoc($result)){
			$mem_id  = $row['member_id'];
			$sql1    = "SELECT login, email, first_name, last_name FROM ".TABLE_PREFIX."members
						WHERE member_id = ($mem_id)
						ORDER BY login DESC";
			$result1 = mysql_query($sql1, $db); 
			$row1    = mysql_fetch_assoc($result1);
									
			echo'<tr><td class="row1">
					<input type="checkbox" name="id[]" value="'.$mem_id.'" id="'.$mem_id.'" />';
			echo	'</td>
						<td class="row1">' . $row1['login'] . '</td>
						<td class="row1">' . $row1['email'] . '</td>
						<td class="row1">' . $row1['first_name'] . '</td>
						<td class="row1">' . $row1['last_name']  . '</td>
						<td class="row1">';
								
			if ($row['role']) {
				echo $row['role'];
			} else {
				echo _AT('student');
			}
				echo '</td></tr><tr><td height="1" class="row2" colspan="6"></td></tr>';
		}
	}
			echo '<tr><td height="1" class="row2" colspan="6"></td></tr>';
			echo '<tr><td align="center" colspan="6" class="row1">';
}

function sort_columns ($column, $order, $col) {
	if 	($order == 'asc' && $column == $col) {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?col='.$column.SEP.'order=desc">';
		echo _AT($column);
		echo ' <img src="images/asc.gif" alt="'._AT('id_ascending').'" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a>';
	} 
	
	else if ($order == 'desc' && $column == $col){
		echo '<a href="'.$_SERVER['PHP_SELF'].'?col='.$column.SEP.'order=asc">';
		echo _AT($column);
		echo ' <img src="images/desc.gif" alt="'._AT('id_descending').'" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a>';
	}
	else {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?col='.$column.SEP.'order=asc">';
		echo _AT($column) . '</a>';
		//echo $column .' | '. $col;
	}

}

?>