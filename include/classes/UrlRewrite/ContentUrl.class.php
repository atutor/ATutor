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
/**
* ContentUrl
* Class for rewriting pretty urls.
* @access	public
* @author	Harris Wong
* @package	UrlParser
*/
class ContentUrl {
	// constructor
	function ContentUrl() {
		$this->rule = array(0=>'cid');
	}

	/**
	 * Construct pretty url by the given query string.
	 */
	function constructPrettyUrl($query){
		$url = '';  //url to be returned
		$bookmark = '';		//html bookmark

		if (empty($query)){
			return '';
		}

		//Take out bookmark
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
		//construct pretty url on mapping
		foreach ($this->rule as $key=>$value){
			//if this value is empty, the url construction should quit.
			if ($query_parts[$value] ==''){
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
//		if ($query_parts['page']!=''){
//			$url .= '/'.$query_parts['page'].'.html';
//		}

		//append query string at the back
		if ($query_string!=''){
			$url .= '?'.$query_string;
		}

		//finally, append bookmark if not emptied
		if ($bookmark!=''){
			$url .= $bookmark;
		}

		return $url;
	}
}
?>