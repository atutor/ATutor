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
// $Id$

//Include all the classes for external rewrite rules
require_once('ForumsUrl.class.php');
require_once('ContentUrl.class.php');
require_once('FileStorageUrl.class.php');
require_once('TestsUrl.class.php');
require_once('GlossaryUrl.class.php');

/**
* UrlRewrite
* Class for rewriting pretty urls.
* @access	public
* @author	Harris Wong
* @package	UrlRewrite
*/
class UrlRewrite  {
	// local variables
	var $path;		//the path of this script
	var $filename;	//script name
	var $query;		//the queries of the REQUEST
	var $isEmpty;	//true if path, filename, and query are empty

	// constructor
	function UrlRewrite($path, $filename, $query) {
		if ($path=='' && $filename=='' && $query==''){
			$this->isEmpty = true;
		} else {
			$this->isEmpty = false;
		}
		$this->path = $path;
		$this->filename = $filename;
		$this->query = $query;
	}

	/** 
	 * Returns the link that points to this object as a page.
	 * @access public
	 */
	function redirect(){
		//redirect to that url.
		return '/'.$this->getPage();
	}

	/** 
	 * Parser for the pathinfo, return an array with mapped key values similar to the querystring.
	 * @access	public
	 * @return	array	key=>value, where keys and values have the same meaning as the ones in the query strings.
	 */
	function parsePrettyQuery(){
		global $_config;
		$result = array();

		//return empty array if query is empty
		if (empty($this->query)){
			return $result;
		}

		//if course_dir_name is disabled from admin. 
		if ($_config['pretty_url']==0){
			return $this->query;
		}

		//If the first char is /, cut it
		if (strpos($this->query, '/') == 0){
			$query_parts = explode('/', substr($this->query, 1));
		} else {
			$query_parts = explode('/', $this->query);
		}

		//dynamically create the array
		//assumption: pathinfo ALWAYS in the format of key1/value1/key2/value2/key3/value3/etc...
		foreach ($query_parts as $array_index=>$key_value){
			if($array_index%2 == 0 && $query_parts[$array_index]!=''){
				$result[$key_value] = $query_parts[$array_index+1];
			}
		}
		return $result;
	}


	/**
	 * Parser for the querystrings url
	 * @access	public
	 * @param	string	querystring
	 * @return	array	an array of mapped keys and values like the querystrings.
	 *
	 * NOTE:	Stopped using this function since we've decided to dynamically create the URL. 
	 *			See: parsePrettyQuery()
	 */
	function parseQuery($query){
		//return empty array if query is empty
		if (empty($query)){
			return array();
		}

		parse_str($this->query, $result);
		return $result;
	}


	/**
	 * Construct the pretty url based on the given query.
	 * @access	public
	 * @param	string	the pathinfo query
	 * @return	string	pretty url
	 */
	function constructPrettyUrl($query){
		global $_config; 
		$bookmark  = '';

		if (empty($query)){
			return '';
		}

		//Take out bookmark, and store it.
		if (($pos = strpos($query, '#'))!==FALSE){
			$bookmark = substr($query, $pos);
			$query = substr($query, 0, $pos);
		}

		//If this is already a pretty url,but without mod_apache rule
		//unwrap it and reconstruct
		if (is_array($query)){
			$new_query = '';
			foreach($query as $fk=>$fv){
				if 	(preg_match('/\.php/', $fv)==1){
					continue;	//skip the php file
				}

				//check if this is part of the rule, if so,add it, o/w ignore
				if (array_search($fv, $this->rule)!==FALSE){
					$new_query .= $fv . '=' . $query[$fk+1] . SEP;
				} elseif (preg_match('/([0-9]+)\.html/', $fv, $matches)==1){
					$new_query .= 'page=' . $matches[1] . SEP;
				}
			}
			$query = $new_query;	//done
		}

		//do not change query if pretty url is disabled
		if ($_config['pretty_url'] == 0){
			$pretty_url = $query;
		} else {
			$pretty_url = '';		//init url
			$query_parts = explode(SEP, $query);
			foreach ($query_parts as $index=>$attributes){
				if(empty($attributes)){
					//skip the ones that are empty.
					continue;
				}
				list($key, $value) = preg_split('/\=/', $attributes, 2);
				$pretty_url .= $key . '/' . $value .'/';
			}
		}

		//finally, append bookmark if not emptied
		if ($bookmark!=''){
			$pretty_url .= $bookmark;
		}

		return $pretty_url;
	}


	/**
	 * This function is used to convert the input URL to a pretty URL.
	 * @param	int		course id
	 * @param	string	normal URL, WITHOUT the <prototal>://<host>
	 * @return	pretty url
	 */
	function convertToPrettyUrl($course_id, $url){
		global $_config, $db;
		$pretty_url = '';

		if (strpos($url, '?')!==FALSE){
			list($front, $end) = preg_split('/\?/', $url);
		} else {
			$front = $url;
			$end = '';
		}

		$front_array = explode('/', $front);

		//find out what kind of link this is, pretty url? relative url? or PHP_SELF url?
		$dir_deep	 = substr_count(AT_INCLUDE_PATH, '..');
		$url_parts	 = explode('/', $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
		$host_dir	 = implode('/', array_slice($url_parts, 0, count($url_parts) - $dir_deep-1));
		//The link is a bounce link
		if(preg_match('/bounce.php\?course=([\d]+)$/', $url, $matches)==1){
			if (!empty($course_id)) {
				$pretty_url = $course_id;		//course_id should be assigned by vitals depending on the system pref.
			} else {
				$pretty_url = $matches[1];		//happens when course dir name is disabled
			}
		} elseif(in_array(AT_PRETTY_URL_HANDLER, $front_array)===TRUE){
			//The relative link is a pretty URL
			$front_result = array();
			//spit out the URL in between AT_PRETTY_URL_HANDLER to *.php
			//note, pretty url is defined to be AT_PRETTY_URL_HANDLER/course_slug/type/location/...
			//ie. AT_PRETTY_URL_HANDLER/1/forum/view.php/...
			while (($needle = array_search(AT_PRETTY_URL_HANDLER, $front_array)) !== FALSE){
				$front_array = array_slice($front_array, $needle + 1);
			}
			$front_array = array_slice($front_array, $needle + 1);  //+2 because we want the entries after the course_slug

			//Handle url differently IF mod_rewrite is enabled, and if there are no query strings at the back,
			//then we will have to reuse the current pathinfo to reconstruct the query.
			if ($_config['apache_mod_rewrite'] > 0 && $end==''){
				$end = $front_array;	//let the class handles it
			} 

			/* Overwrite pathinfo
			 * ie. /go.php/1/forum/view.php/fid/1/pid/17/?fid=1&pid=17&page=5
			 * In the above case, cut off the original pathinfo, and replace it with the new querystrings
			 * If querystring is empty, then use the old one, ie. /go.php/1/forum/view.php/fid/1/pid/17/.
			 */
			foreach($front_array as $fk=>$fv){
				array_push($front_result, $fv);
				if 	(!empty($end) && preg_match('/\.php/', $fv)==1){
					break;
				}
			}
			$front = implode('/', $front_result);
		} elseif (strpos($front, $host_dir)!==FALSE){
			//Not a relative link, it contains the full PHP_SELF path.
			$front = substr($front, strlen($host_dir)+1);  //stripe off the slash after the host_dir as well
		} elseif ($course_id == ''){
			//if this is my start page
			return $url;
		}
		//Turn querystring to pretty URL
		if ($pretty_url==''){
			//Get the original course id back
			$sql	= "SELECT course_id FROM ".TABLE_PREFIX."courses WHERE course_dir_name='$course_id'";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			$course_orig = $course_id;

			if ($row['course_id']!=''){
				$course_orig = $row['course_id'];
			} 

			//Add course id in if both course_id or course_dir_name are not there
			if (preg_match('/^\/?('.$course_id.'|'.$course_orig.')\//', $front)==0){
				$pretty_url = $course_id.'/';
			}

			//check if there are any rules overwriting the original rules
			//TODO: have a better way to do this
			//		extend modularity into this.
			$obj =& $this;  //default
			//Overwrite the UrlRewrite obj if there are any private rules
			if ($_config['apache_mod_rewrite'] > 0){
				//take out '.php' if any exists. (Apply only to non-modules, otherwise it might cause problems)
				if (preg_match('/^mods/', $front)!=1){
					if ($end=='' && preg_match('/index\.php$/', $front)==1){
						$pretty_url .= preg_replace('/index.php/', '', $front);
					} else {
						$pretty_url .= preg_replace('/\.php/', '', $front);
					}
				} else {
					$pretty_url .= $front;
				}

				if (preg_match('/forum\/(index|view|list)\.php/', $front)==1) {
					$pretty_url = $course_id.'/forum';
					$obj = new ForumsUrl();
				} elseif (preg_match('/(content\.php)(\/cid(\/\d+))?/', $front, $matches)==1){
					$pretty_url = $course_id.'/content';
					//if there are other pretty url queries at the back, append it
					//Note: this is to fix the hopping content problem between diff courses
					if (isset($matches[3]) && $matches[3] != ''){
						$pretty_url .= $matches[3];
					}
					$obj = new ContentUrl();
				} elseif (preg_match('/file_storage\/((index|revisions|comments)\.php)?/', $front, $matches)==1){
					$pretty_url = $course_id.'/file_storage';
					$obj = new FileStorageUrl($matches[1]);
				} elseif (preg_match('/tools\/test_intro\.php/', $front)==1){
					$pretty_url = $course_id.'/tests_surveys';
					$obj = new TestsUrl();
				} elseif (preg_match('/glossary\/index\.php/', $front)==1){
					$pretty_url = $course_id.'/glossary';
					$obj = new GlossaryUrl();
				}
			} else {
				$pretty_url .= $front;
			}

			if ($end != ''){
				//if pretty url is turned off, use '?' to separate the querystring.
				($_config['pretty_url'] == 0)? $qs_sep = '?': $qs_sep = '/';
				 $pretty_url .= $qs_sep.$obj->constructPrettyUrl($end);
			}
		}

		//if mod_rewrite is switched on, defined in constants.inc.php
		if ($_config['apache_mod_rewrite'] > 0){
			return $pretty_url;
		}
		return AT_PRETTY_URL_HANDLER.'/'.$pretty_url;
	}


	/**
	 * Return the paths where this script is
	 */
	function getPath(){
		if ($this->path != ''){
			return substr($this->path, 1).'/';
		}
		return '';
	}

	/**
	 * Return the script name
	 */
	function getFileName(){
		return $this->filename;
	}

	/**
	 * 
	 */
	function getPage(){
		return $this->getPath().$this->getFileName();
	}

	/**
	 * Return true if path, filename, and query are empty.
	 */
	function isEmpty(){
		return $this->isEmpty;
	}
}
?>