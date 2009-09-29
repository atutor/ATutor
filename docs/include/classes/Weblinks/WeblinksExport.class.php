<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: WeblinksExport.class.php 8119 2008-10-29 13:53:06Z hwong $

/**
 * A very simple class to generates a singular webcontent weblink xml file.
 * Based on the namespace:
 * http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_5/imswl_v1p0_localised.xsd
 */
class WeblinksExport {
	//Global Variables
	var	$wl;	//weblink obj

	/**
	 * Constructor
	 * @param	mixed	Weblink Object, ref Weblinks.class.php
	 */
	function WeblinksExport($wl){
		$this->wl = $wl;
	}


	/** 
	 * Export
	 */
	function export(){
		global $savant;

		//localize
		$wl = $this->wl;

		//assign all the neccessarily values to the template.
		$savant->assign('title', htmlentities($wl->getTitle(), ENT_QUOTES, 'UTF-8'));
		$url = $wl->getUrl();
		$savant->assign('url_href', htmlentities($url['href'], ENT_QUOTES, 'UTF-8'));
		$savant->assign('url_target', $url['target']);
		//TODO: not supported yet
		//$savant->assign('url_window_features', $url['window_features']);

		//generates xml
		$xml = $savant->fetch(AT_INCLUDE_PATH.'classes/Weblinks/Weblinks.tmpl.php');

		return $xml;
	}
}
?>