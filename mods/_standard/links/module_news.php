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
function links_news() {
	global $db, $enrolled_courses, $system_courses;
	$news = array();

	if ($enrolled_courses == ''){
		return $news;
	} 

	$sql = "SELECT * FROM ".TABLE_PREFIX."links L INNER JOIN ".TABLE_PREFIX."links_categories C ON C.cat_id = L.cat_id WHERE owner_id IN $enrolled_courses AND L.Approved=1 ORDER BY SubmitDate DESC";
	$result = mysql_query($sql, $db);
	if($result){
		while($row = mysql_fetch_assoc($result)){
			$news[] = array(
							'time'=>$row['SubmitDate'], 
							'object'=>$row, 
							'alt'=>_AT('links'),
							'course'=>$system_courses[$row['owner_id']]['title'],
							'thumb'=>'images/home-links_sm.png', 
							'link'=>'<a href="bounce.php?course='.$row['owner_id'].'&p='.urlencode('mods/_standard/links/index.php?view='.$row['link_id']).'"'.
									(strlen($row['LinkName']) > SUBLINK_TEXT_LEN ? ' title="'.$row['LinkName'].'"' : '') .'>'. 
									validate_length($row['LinkName'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>');
		}
	}
	return $news;
}

?>