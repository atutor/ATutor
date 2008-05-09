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

require_once(dirname(__FILE__) . '/UrlRewrite.class.php');

/**
* FileStorageUrl
* Class for rewriting pretty urls in tests
* @access	public
* @author	Harris Wong
* @package	UrlParser
*/
class FileStorageUrl extends UrlRewrite {
	// local variables
	var $rule;		//an array that maps [lvl->query parts]

	// constructor
	function FileStorageUrl() {
		$this->rule = array(0=>'action', 1=>'ot', 2=>'oid', 3=>'folder');	//default 3=folder, but it can be id as well for comment
		parent::setClassName('file_storage');	//set class name
	}

	// public
	function getRule($rule_key){
	}

	//
	function setRule($id, $ruleName){
		$this->rule[$id] = $ruleName;
	}

	// public
	// return the uri of this pretty url, used by constants.inc.php $_rel_link
	function redirect($parts){
		$sublvl = parent::parsePrettyUrl($parts);
		//0=>fid 1=>pid
		$query = '';
		if (empty($sublvl)){
			$page_to_load = '/file_storage/index.php';
		}
		foreach($sublvl as $order=>$label){			
			if ($this->rule[$order]=='action'){
				if ($label=='comments'){
					$page_to_load = '/file_storage/comments.php';
				} elseif($label=='revisions'){
					$page_to_load = '/file_storage/revisions.php';
				} else {
					$page_to_load = '/file_storage/index.php';
				}
			}
		}
		return $page_to_load;
	}


	// public
	/**
	 * This method will read the parts and tries to put it together as an array.
	 * So that this can get assigned to the GET/POST/REQUEST variable.
	 * @param	string	this is the query after /file_storage/
	 * @return	an array of parts mapped by their query rules.
	 */
	function parts2Array($parts){
		$sublvl = parent::parsePrettyUrl($parts);
		$result = array();

		//if there are no extra query, link it to the defaulted page
		if (empty($sublvl)){
			$result['page_to_load'] = 'file_storage/index.php';
		}

		foreach ($sublvl as $order => $label){
			//check if pages exist, if it does, end parsing because this is the last part of the pretty url
			//don't care if there are anymore strings afterwards.  Not part of my constructions.
//			if (preg_match('/([1-9]+)\.html/', $label, $matches)==true){
//				$result['page'] = $matches[1];
//				break;
//			}

			if ($this->rule[$order]=='action'){
				if ($label=='comments'){
					$result['page_to_load'] = 'file_storage/comments.php';
					$this->setRule(3, 'id');		//for comments, the 'id' attribute is required
				} elseif($label=='revisions'){
					$result['page_to_load'] = 'file_storage/revisions.php';
					$this->setRule(3, 'id');	//for opening folders, the 'folder' attribute is required
				} else {
					$result['page_to_load'] = 'file_storage/index.php';
					$this->setRule(3, 'folder');	//for opening folders, the 'folder' attribute is required
				}
			}

			//Both key and values cannot be emptied.
			if ($this->rule[$order]!='' && $label!=''){
				$result[$this->rule[$order]] = $label;
			}			
		}
		return $result;
	}


	/**
	 * Construct pretty url by the given query string.
	 * Note:	This method will be a bit different from ForumsUrl, TestsUrl, ContentUrl because it has browse/comment in the rule which 
	 *			doesn't exist in the actual query.
	 * @param	string	the query string of the url
	 * @param	string	filename of the request, this consists of revisions.php, index.php, comments.php
	 */
	function constructPrettyUrl($query, $filename){
		$url = $this->getClassName();
		$query_parts = parent::parseQuery($query);
		$query_string = '';
		//determine if this uses 'browse' or 'comment'
		$prefix = $this->configRule($filename);
		$url .=	'/' . $prefix ;	//add either browse or comment to the url

		//construct pretty url on mapping
		foreach ($this->rule as $key=>$value){

			//if this is action, skip it.
			if ($value == 'action'){
				continue;
			} elseif ($query_parts[$value] ==''){
				//if this value is empty, the url construction should quit.
				break;
			}
			$url .= '/'.$query_parts[$value];

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
			return 'browse';
		}

	}


}
?>