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
// $Id: module_news.php 10142 2010-08-17 19:17:26Z hwong $
/*
 * Get the latest updates of this module
 * @return list of news, [timestamp]=>
 */
function reading_list_news() {
	global $db;
	$news = array();

	$sql = "SELECT * FROM ".TABLE_PREFIX."reading_list R INNER JOIN ".TABLE_PREFIX."external_resources E ON E.resource_id = R.resource_id WHERE R.course_id=$_SESSION[course_id] ORDER BY R.reading_id DESC";
	$result = mysql_query($sql, $db);

	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)) {
			$news[] = array('time'=>$row['date_end'], 
							'object'=>$row,
							'thumb'=>'images/home-reading_list_sm.png',
							'link'=>'<a href="'.url_rewrite('mods/_standard/reading_list/display_resource.php?id=' . $row['resource_id'],
									AT_PRETTY_URL_IS_HEADER).'"'.(strlen($row['title']) > SUBLINK_TEXT_LEN ? ' title="'.$row['title'].'"' : '') .'>'. 
									validate_length($row['title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>');
		}	
	}
	return $news;
}

?>