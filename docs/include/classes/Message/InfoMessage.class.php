<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
require_once('Message.class.php');

/**
* InfoMessage
* Class for managing interface with $_SESSION with respect to information messages
* @access	public
* @see		Message.class.php
* @author	Jacek Materna
*/
class InfoMessage extends Message {

	/**
	* Constructor for this class. Takes the _AT() translated title of the Message
	* and the $base_path which is the system install root
	* @access  public
	* @param   string $title			_AT() translated title of the message
	* @param   string $base_href		root install url
	* @see     $_base_path				in include/vitals.inc.php
	* @see     _AT()					in include/lib/output.inc.php
	* @author  Jacek Materna
	*/
	function InfoMessage($title, $base_href) { 
		
		$header = '<br /><table border="0" cellpadding="3" cellspacing="2" ' .
					'width="90%" summary="" align="center"  class="hlpbox">' .
					'<tr class="hlpbox"><td><h3><img src="' . $base_url . 
					'images/infos.gif" align="top" class="menuimage5" alt="' .
					$title . '" /><small>' . $title . '</small></h3>';
				
		$footer = '</td></tr></table>';
		
		Message::Message('info', $header, $footer);
	}
	
	// @see Message.class.php
	
}

?>