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
	global $db, $enrolled_courses, $system_courses;
	$news = array();

	if ($enrolled_courses == ''){
		return $news;
	} 

	$sql = "SELECT date, file_id, file_name, owner_id, description FROM ".TABLE_PREFIX."files WHERE owner_id IN $enrolled_courses ORDER BY date DESC";
	$result = mysql_query($sql, $db);
	if($result){
		while($row = mysql_fetch_assoc($result)){
			$row['course_id'] = $row['owner_id'];
			if($row['description'] !=""){
				$filetext = $row['description'];
			} else {
				$filetext = $row['file_name'];
			}
			$news[] = array('time'=>$row['date'], 
			      'object'=>$row, 
			      'course'=>$system_courses[$row['owner_id']]['title'],
			      'alt'=>_AT('download'),
			      'thumb'=>'images/application_get.png', 
			      'link'=>'<a href="bounce.php?course='.$row['owner_id'].'&p='.urlencode('mods/_standard/file_storage/index.php?download=1'.SEP.'files[]='. $row['file_id']).'"'.
		          (strlen($filetext) > SUBLINK_TEXT_LEN ? ' title="'.$filetext.'"' : '') .'>'. 
		          validate_length($filetext, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>');
		}
	}
	return $news;
}

?>