<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }

/*****
* Free form PHP can appear here to retreive current information
* from the module, or a text description of the module where there is
* not current information
*****/

global $db;

$link_limit = 2;		// Number of links to be displayed on "detail view" box

$groups_list = implode(',',$_SESSION['groups']);

$sql = '';
if (authenticate(AT_PRIV_ASSIGNMENTS, AT_PRIV_RETURN)) { // instructor
	$file_storage_assignments = array();
	$sql = "SELECT * FROM ".TABLE_PREFIX."assignments WHERE course_id=$_SESSION[course_id] ORDER BY date_due DESC";
} else { // students
	if ($groups_list <> '') {
		$sql = "(SELECT a.title, date_due
	           FROM ".TABLE_PREFIX."groups_types gt, ".TABLE_PREFIX."groups g, ".TABLE_PREFIX."assignments a
	          WHERE g.group_id in (".$groups_list.")
	            AND g.group_id in (SELECT group_id FROM ".TABLE_PREFIX."file_storage_groups)
	            AND g.type_id = gt.type_id
	            AND gt.course_id = $_SESSION[course_id]
	            AND gt.type_id = a.assign_to
	            AND (a.date_cutoff=0 OR UNIX_TIMESTAMP(a.date_cutoff) > ".time()."))
	        UNION
	        ";
	}
	$sql .= "(SELECT title, date_due
	           FROM ".TABLE_PREFIX."assignments 
	          WHERE assign_to=0 
	            AND course_id=$_SESSION[course_id] 
	            AND (date_cutoff=0 OR UNIX_TIMESTAMP(date_cutoff) > ".time()."))
	        ORDER BY date_due DESC";
}
$sql .= " LIMIT $link_limit";

$rows_assignments = queryDB($sql, array());
if(count($rows_assignments) > 0){
    foreach($rows_assignments as $row){
		/****
		* SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY are defined in include/lib/constance.lib.inc
		* SUBLINK_TEXT_LEN determins the maxium length of the string to be displayed on "detail view" box.
		*****/
		$title = $row['title'] . ' ('._AT("due_date").': '.$row['date_due'].')';
		$list[] = '<a href="'.AT_BASE_HREF.'mods/_standard/assignment_dropbox/index.php">'. 
		          $title .'</a>';
	}
	return $list;	
} else {
	return 0;
}

?>