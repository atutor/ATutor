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
function file_storage_news() {
	global $db, $enrolled_courses;
	$news = array();

	if ($enrolled_courses == ''){
		return $news;
	} 

	$sql = "SELECT date, file_id, file_name, description FROM ".TABLE_PREFIX."files WHERE owner_id IN $enrolled_courses ORDER BY date DESC";
	$result = mysql_query($sql, $db);
	if($result){
		while($row = mysql_fetch_assoc($result)){
			if($row['description'] !=""){
				$filetext = $row['description'];
			} else {
				$filetext = $row['file_name'];
			}
			$news[] = array('time'=>$row['date'], 'object'=>$row, 'thumb'=>'images/file_types/images.gif', 'link'=>'<a href="'.url_rewrite('mods/_standard/file_storage/index.php?download=1'.htmlentities(SEP).'files[]='. $row['file_id'], AT_PRETTY_URL_IS_HEADER).'"'.
		          (strlen($filetext) > SUBLINK_TEXT_LEN ? ' title="'.$filetext.'"' : '') .'>'. 
		          validate_length($filetext, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>');
		}
	}
	return $news;
}

?>