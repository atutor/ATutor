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
function glossary_news() {
	global $db;
	$news = array();

	$sql = 'SELECT * FROM '.TABLE_PREFIX.'glossary WHERE course_id='.$_SESSION['course_id'].' ORDER BY word_id DESC';
	$result = mysql_query($sql, $db);
	if($result){
		//no time entry, and not a _standard module
	}
	return $news;
}

?>