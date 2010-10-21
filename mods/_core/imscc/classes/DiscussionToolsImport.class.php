<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

include(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');
/**
 * A class for DiscussionToolsParser
 * based on:
 *  http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_5/imsdt_v1p0_localised.xsd
 */
class DiscussionToolsImport {
	//global variables
	var $fid;	//the forum id that is imported 

	//constructor
	function DiscussionToolsImport(){}

	//import
	function import($forum_obj){
		$title = $forum_obj->getTitle();
		$text = $forum_obj->getText();

		$this->fid = $this->createForum($title, $text);
		$this->associateForum($cid, $this->fid);
	}

	
	/**
	 * create a forum
	 * @param	string	title
	 * @param	string  text/description
	 * @return	added forum's id
	 */
	function createForum($title, $text){
		global $db;
		//create POST array
		$temp['title'] = $title;
		$temp['body'] = $text;
		$temp['edit'] = 0;	//default 0 minutes 

		add_forum($temp);	//check forums.inc.php

		$sql = 'SELECT MAX(forum_id) FROM '.TABLE_PREFIX.'forums';
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_row($result);
		return $row[0];
	}	


	/**
	 * create an association between forum and content
	 * @param	int		content id
	 * @return	
	 */
	function associateForum($cid, $fid){
		global $db;
		$sql = 'INSERT INTO '.TABLE_PREFIX."content_forums_assoc (content_id, forum_id) VALUES ($cid, $fid)";
		mysql_query($sql, $db);
	}

	/**
	 * Return the fid that was created by this import
	 * @return	int	 forum id.
	 */
	function getFid(){
		return $this->fid;
	}
}
?>