<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

require_once('UrlRewrite.class.php');

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
			$pathinfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
		}
		$this->parsePathInfo($pathinfo);
	}

	/**
	 * This function will take the pathinfo and return an array of elements 
	 * retrieved from the path info.
	 * An ATutor pathinfo will always be in the format of /<course_slug>/<type>/<parts>
	 * course_slug is the course_slug defined in course preference (or course id if it's empty)
	 * type is the folder, particularlly forums, content, tests, blogs, mods, etc.
	 * parts is the extra info about this url request.
	 * @param	string	the pathinfo from the URL
	 * @access	private
	 */
	function parsePathinfo($pathinfo){
		global $db;
		$pathinfo = strtolower($pathinfo);

		//remove AT_PRETTY_URL_HANDLER from the path info.
		if (($pos=strpos($pathinfo, AT_PRETTY_URL_HANDLER))!==FALSE){
			$pathinfo = substr($pathinfo, $pos);
		}

		/* 
		 * matches[1] = course slug/id
		 * matches[2] = path
		 * matches[3] = useless, just a place holder
		 * matches[4] = filename
		 * matches[5] = query string in pretty format
		 * @http://ca3.php.net/preg_match
		 */
		if (strpos($pathinfo, 'mods')!==FALSE){
			//If this is a mod, its file name will be longer with mods/ infront
			preg_match('/^\/[\w\-]+\/?$|(\/[\w]+)(\/mods(\/[\w]+)+)\/([\w\_\.]+\.php)([\/\w\W]*)/', $pathinfo, $matches);			
		} else {
			preg_match('/^\/[\w\-]+\/?$|(\/[\w]+)(([\/\w]*))\/([\w\_\.]+\.php)([\/\w\W]*)/', $pathinfo, $matches);
		}

		if (empty($matches)){
			//no matches.
			$matches[1] = 0;
		} elseif (sizeof($matches)==1){
			//if the url consist of just the course slug, the size would be just 2
			$matches[1] = $matches[0];
		} 

		//take out the front slash
		$matches[1] = preg_replace('/\//', '', $matches[1]);
		$course_id = $matches[1];

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

		//Check if there are any matches for prettied query string, if not, use the actual query.
		if (!isset($matches[5]) || $matches[5] == ''){
			$matches[5] = $_SERVER['QUERY_STRING'];
		}

		//Create object based on this path.
		$matches[2] = isset($matches[2]) ? $matches[2] : '';
		$matches[4] = isset($matches[4]) ? $matches[4] : '';
		$url_obj = new UrlRewrite($matches[2], $matches[4], $matches[5]);

		$this->path_array = array($course_id, $url_obj);
	}

	
	/**
	 * return the path array
	 */
	function getPathArray(){
		return $this->path_array;
	}


	/**
	 * Returns course_id if config_[course_dir_name] is switched off, 
	 * otherwise, return the course dir name.
	 * Called by vitals.inc.php
	 *
	 * @param	int		course id
	 * @return	mixed	course id if config[course_dir_name] is 0, course_dir_name otherwise
	 */
	function getCourseDirName($course_id){
		global $db; 
		$course_id = intval($course_id);

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