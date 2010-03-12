<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institute	   */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id: module_news.php 9335 2010-02-11 16:29:01Z hwong $
/*
 * Get the latest updates of this module
 * @return list of news, [timestamp]=>
 */
function links_news() {
	global $db, $enrolled_courses;
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
							'thumb'=>'images/home-links_sm.png', 
							'link'=>'<a href="'.url_rewrite('mods/_standard/links/index.php?view='.$row['link_id'], AT_PRETTY_URL_IS_HEADER).'"'.
									(strlen($row['LinkName']) > SUBLINK_TEXT_LEN ? ' title="'.$row['LinkName'].'"' : '') .'>'. 
									validate_length($row['LinkName'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>');
		}
	}
	return $news;
}

?>