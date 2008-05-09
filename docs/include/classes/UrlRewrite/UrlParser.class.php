<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: UrlParser.class.php 7208 2008-04-15 10:00:24Z harris $

// Add classes for the rewrite 
require_once(dirname(__FILE__) . '/UrlRewrite.class.php');

/**
* UrlParser
* Class for rewriting pretty urls.
* @access	public
* @author	Harris Wong
* @package	UrlParser
*/
class UrlParser {
	//Variables
	var $path_array;	//an array [0]->course_id; [1]->class obj

	// Constructor
	function UrlParser($pathinfo=''){
		if ($pathinfo==''){
			$pathinfo = $_SERVER['PATH_INFO'];
		}
		$this->parsePathInfo($pathinfo);
	}

	/**
	 * This function will take in the pathinfo and return an array of elements 
	 * retrieved from the path info.
	 * An ATutor pathinfo will always be in the format of /<course_slug>/<type>/<parts>
	 * course_slug is the course_slug defined in course preference
	 * type is the forums, content, tests, blogs, etc.
	 * parts is the extra info about this url request.
	 * @param	string	the pathinfo from the URL
	 * @access	private
	 */
	function parsePathinfo($pathinfo){
		global $db;
		$pathinfo = strtolower($pathinfo);

		/* 
		 * matches[1] = course slug/id
		 * matches[2] = path
		 * matches[3] = filename
		 * matches[4] = query string in pretty format
		 * @http://ca3.php.net/preg_match
		 */
		preg_match('/^\/[\w\-]+\/?$|(\/[\w]+)([\/\w]*)\/([\w\_\.]+\.php)([\/\w\W]*)/', $pathinfo, $matches);

		if (empty($matches)){
			//no matches.
			$matches[1] = 0;
		} elseif (sizeof($matches)==1){
			//if the url consist of just the course slug, the size would be just 2 b
			$matches[1] = $matches[0];
		} 

		//take out the front slash
		$matches[1] = preg_replace('/\//', '', $matches[1]);
		$course_id = $matches[1];

		//Check if this is using a course_slug.
//		if ($_config['course_dir_name']==true){
			//check if this is a course slug or course id.
			if (preg_match('/^[\d]+$/', $matches[1])==0){
				//it's a course slug, log into the course.
				$sql	= "SELECT course_id FROM ".TABLE_PREFIX."courses WHERE course_dir_name='$matches[1]'";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_assoc($result);
				if ($row['course_id']!=''){
					$course_id = $row['course_id'];
				} else {
					$course_id = 0;
				}
			}
//			$_SESSION['course_id'] = $course_id;
//		} 		

		//Check if the query string is pretty, if not, find it.
		if ($matches[4] == ''){
			$matches[4] = $_SERVER['QUERY_STRING'];
		}

		//Check which tool type this is from
		$url_obj = new UrlRewrite($matches[2], $matches[3], $matches[4]);

		$this->path_array = array($course_id, $url_obj);
	}

	
	/**
	 * return the path array
	 */
	function getPathArray(){
		return $this->path_array;
	}


	/**
	 * Returns course_id if config_[course_dir_name] is off, otherwise, 
	 * return the course dir name.
	 * Called by vitals.inc.php
	 *
	 * @param	int	course id
	 * @return	mixed	course id if config[course_dir_name] is 0, course_dir_name otherwise
	 */
	function getCourseDirName($course_id){
		global $db; 
		$course_id = intval($course_id);

		//it's a course slug, log into the course.
		$sql	= "SELECT course_dir_name FROM ".TABLE_PREFIX."courses WHERE course_id=$course_id";
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		if ($row['course_dir_name']!=''){
			$course_id = $row['course_dir_name'];
		} 

		return $course_id;
	}
}
?>