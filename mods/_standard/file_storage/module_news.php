<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$
/*
 * Get the latest updates of this module
 * @return list of news, [timestamp]=>
 */

function file_storage_news() {
	global $enrolled_courses, $system_courses;
	$news = array();

	if ($enrolled_courses == '') {
		return $news;
	}
	
	// As personal files are listed in any enrolled courses of the student,
	// randomly pick one course for bouce.php
	$end_of_first_course = strpos($enrolled_courses, ",") - 1;
	$any_one_enrolled_course = substr($enrolled_courses, 1, $end_of_first_course ? $end_of_first_course : -1);
	
	$sql = "(SELECT date, file_id, file_name, owner_id course_id, description 
	           FROM ".TABLE_PREFIX."files 
	          WHERE owner_type = ".WORKSPACE_COURSE." AND owner_id IN ".$enrolled_courses.")
	        UNION
	        (SELECT date, file_id, file_name, ".$any_one_enrolled_course." course_id, description 
	           FROM ".TABLE_PREFIX."files
	          WHERE owner_type = ".WORKSPACE_PERSONAL." AND owner_id = ".$_SESSION['member_id'].")
	        UNION
	        (SELECT f.date, f.file_id, f.file_name, gt.course_id, f.description 
	           FROM ".TABLE_PREFIX."files f, ".TABLE_PREFIX."groups g, ".TABLE_PREFIX."groups_types gt
	          WHERE owner_type = ".WORKSPACE_GROUP." 
	            AND f.owner_id = g.group_id 
	            AND g.type_id = gt.type_id 
	            AND gt.course_id IN ".$enrolled_courses."
	            AND " . $_SESSION['member_id'] . " in 
	               (select member_id 
	                from " . TABLE_PREFIX . "groups_members gm 
	                where gm.group_id = g.group_id))
	         ORDER BY date DESC";

	$rows_files = queryDB($sql, array());
	if(count($rows_files) > 0){
	    foreach($rows_files as $row){
		
			if($row['description'] !=""){
				$filetext = $row['description'];
			} else {
				$filetext = $row['file_name'];
			}

			$sql = "SELECT course_id, home_links, main_links from %scourses WHERE course_id = %d";
			$row2 = queryDB($sql, array(TABLE_PREFIX, $row['course_id']), TRUE);
						
			// check if course has file storage enabled
			
			if(strstr( $row2['home_links'], 'file_storage') || strstr( $row2['main_links'], 'file_storage') ){
			
			$news[] = array('time'=>$row['date'], 
			      'object'=>$row, 
			      'course'=>$system_courses[$row['course_id']]['title'],
			      'alt'=>_AT('download'),
			      'thumb'=>'images/application_get.png', 
			      'link'=>'<a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('mods/_standard/file_storage/index.php?download=1'.SEP.'files[]='. $row['file_id']).'"'.
		          (strlen($filetext) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($filetext, 'input.text').'"' : '') .'>'. 
		          AT_print(validate_length($filetext, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'input.text') .'</a>');
		    }
		}
	}
	return $news;
}

?>
