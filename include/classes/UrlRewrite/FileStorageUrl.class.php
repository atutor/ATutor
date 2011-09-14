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
/**
* FileStorageUrl
* Class for rewriting pretty urls in tests
* @access	public
* @author	Harris Wong
* @package	UrlParser
*/
class FileStorageUrl {
	// local variables
	var $rule;		//an array that maps [lvl->query parts]

	// constructor
	// @param the filename that was being called, this can be index.php, comments.php, revisions.php
	function FileStorageUrl($filename) {
		if ($filename == ''){
			$filename = 'index.php';
		}
		$this->rule = array(0=>'action', 1=>'ot', 2=>'oid', 3=>'folder');	//default 3=folder, but it can be id as well for comment
		$this->filename = $filename;
	}

	//
	function setRule($id, $ruleName){
		$this->rule[$id] = $ruleName;
	}

	/**
	 * Construct pretty url by the given query string.
	 * Note:	This method will be a bit different from ForumsUrl, TestsUrl, ContentUrl because it has browse/comment in the rule which 
	 *			doesn't exist in the actual query.
	 * @param	string	the query string of the url
	 * @param	string	filename of the request, this consists of revisions.php, index.php, comments.php
	 */
	function constructPrettyUrl($query){
		if (empty($query)){
			return '';
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
				}
			}
			$query = $new_query;	//done
		}

		$temp = explode(SEP, $query);
		foreach ($temp as $index=>$attributes){
			if(empty($attributes)){
				//skip the ones that are empty.
				continue;
			}
			list($key, $value) = preg_split('/\=/', $attributes, 2);
			$query_parts[$key] = $value;
		}

		$query_string = '';
		//determine if this uses 'browse' or 'comment'
		$prefix = $this->configRule($this->filename);
		if ($prefix != '') {
			$url .=	$prefix.'/' ;	//add either index, revision or comment to the url
		}

		//construct pretty url on mapping
		foreach ($this->rule as $key=>$value){

			//if this is action, skip it.
			if ($value == 'action'){
				continue;
			} elseif ($query_parts[$value] ==''){
				//if this value is empty, the url construction should quit.
				break;
			}
			$url .= $query_parts[$value].'/';

			//if the query parts are not in the defined rules, set it back to query string again
			if ($query_parts[$this->rule[$key]]!=''){
				$query_parts[$this->rule[$key]] = '';
			}
		}

		//Go through the query_parts again, and for those values that are not empty
		// add it to the querystring
		foreach($query_parts as $key=>$value){
			//paginator are handle differently
			if ($value!='' && $key!='page'){
				$query_string .= $key.'='.$value.SEP;
			}
		}
		//take out the last sep.
		$query_string = substr($query_string, 0, -1);

		//handle paginators
		if ($query_parts['page']!=''){
			$url .= '/'.$query_parts['page'].'.html';
		}

		//append query string at the back
		if ($query_string!=''){
			$url .= '?'.$query_string;
		}

		return $url;
	}


	/**
	 * A helper method for constructPrettyUrl
	 * @param	string	filename
	 */
	function configRule($filename){
		//run through the query once, extract if it uses id or folder.
		//if 'id', it is comments.php
		//if 'folder', it is index.php
		if ($filename=='comments.php'){
			$this->setRule(3, 'id');
			return 'comments';
		} elseif ($filename=='revisions.php'){
			$this->setRule(3, 'id');
			return 'revisions';
		} else {
			$this->setRule(3, 'folder');
//			return 'index';
			return '';
		}

	}


}
?>