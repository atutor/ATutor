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
* FeedbackMessage
* Class for managing interface with $_SESSION with respect to feedback messages
* @access	public
* @see		Message.class.php
* @author	Jacek Materna
*/
class FeedbackMessage extends Message {

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
	function FeedbackMessage($title, $base_href) { 
		
		$header = '<br /><table border="0" class="fbkbox" cellpadding="0" '.
					'cellspacing="2" width="90%" summary="" align="center">' .
					'<tr class="fbkbox"><td><h3><img src="' . $base_href .
					'images/feedback_x.gif" align="top" alt="' . $title . 
					'" class="menuimage5" /><small>' . $title . '</small></h3>';
		
		$footer = '</td></tr></table><br />';
		
		Message::Message('feedback', $header, $footer);
	}
	
	// @see Message.class.php
	
}

?>