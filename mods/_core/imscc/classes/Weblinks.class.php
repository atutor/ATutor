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
// $Id: Weblinks.class.php 10141 2010-08-17 17:56:03Z hwong $

/**
 * A class for Weblinks object
 * based on:
 *  http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_5/imswl_v1p0_localised.xsd
 */

class Weblinks {
	//global variables
	var $title	= '';
	var $url	= array();	//prefs


	/**
	 * Constructor
	 * For now, uses only title and URL
	 */
	function Weblinks($title, $url){
		$this->title = $title;
		$this->url['href'] = $url;
		$this->setUrlPrefs();	//set defaults values
	}

	/**
	 * Set Url prefs
	 * @param	string		resembles HTML target attribute, [_self, _blank, _parent, _top, <name>], default '_self'
	 * @param	string		browser window settings
	 */
	function setUrlPrefs($target='_self', $window_features=''){
		$this->url['target'] = $target;
		$this->url['window_features'] = $window_features;
	}


	/**
	 * Return the title of this weblink
	 * @return	string
	 */
	function getTitle(){
		return $this->title;
	}


	/**
	 * Return the URL array of this weblink
	 * @return	mixed
	 */
	function getUrl(){
		return $this->url;
	}
}
?>