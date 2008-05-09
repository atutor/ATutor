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
* TestsUrl
* Class for rewriting pretty urls in tests
* @access	public
* @author	Harris Wong
* @package	UrlParser
*/
class TestsUrl extends UrlRewrite {
	// local variables
	var $rule;		//an array that maps [lvl->query parts]

	// constructor
	function TestsUrl() {
		$this->rule = array(0=>'tid', 1=>'rid');
		parent::setClassName('test');	//set class name
	}

	// public
	function getRule($rule_key){
	}

	//
	function setRule($rule){
		echo 'child setting the rule';
		$this->rule = $rule;
	}

	// public
	// deprecated
	function redirect($parts){
		$sublvl = parent::parsePrettyUrl($parts);
		//0=>fid 1=>pid
		$query = '';
		foreach($sublvl as $order=>$label){
			//construct query
			$query .= $this->rule[$order].'='.$label.'&';
		}
		$query = substr(trim($query), 0, -1);
//		return 'forum/view.php?'.$query;
		return 'tools/my_tests.php';
	}


	// public
	/**
	 * This method will read the parts and tries to put it together as an array.
	 * So that this can get assigned to the GET/POST/REQUEST variable.
	 * @param	string	this is the query after /test/
	 * @return	an array of parts mapped by their query rules.
	 */
	function parts2Array($parts){
		$sublvl = parent::parsePrettyUrl($parts);
		$result = array();

		//if there are no extra query, link it to the defaulted page
		if (empty($sublvl)){
			$result['page_to_load'] = 'tools/my_tests.php';
		}
		foreach ($sublvl as $order => $label){
			//check if pages exist, if it does, end parsing because this is the last part of the pretty url
			//don't care if there are anymore strings afterwards.  Not part of my constructions.
			if (preg_match('/([1-9]+)\.html/', $label, $matches)==true){
				$result['page'] = $matches[1];
				break;
			}

			if ($this->rule[$order]=='tid'){
				$result['page_to_load'] = 'tools/test_intro.php';
			} elseif ($this->rule[$order]=='rid'){
				$result['page_to_load'] = 'tools/view_results.php';
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
	 */
	function constructPrettyUrl($query){
		$url = $this->getClassName();
		$query_parts = parent::parseQuery($query);
		$query_string = '';
		//construct pretty url on mapping
		foreach ($this->rule as $key=>$value){

			//if this value is empty, the url construction should quit.
			if ($query_parts[$value] ==''){
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


}
?>