<?php

/**
*
* Example plugin for unit testing.
* 
* @version $Id: Savant_Plugin_fester.php,v 1.1 2004/04/06 17:56:27 joel Exp $
*
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_fester extends Savant_Plugin {
	
	var $message = "Fester";
	var $count = 0;
	
	function Savant_Plugin_fester(&$savant)
	{
		// initialize the parent constructor
		$this->Savant_Plugin(&$savant);
		
		// do some other constructor stuff
		$this->message .= " is printing this: ";
	}
	
	function fester(&$savant, &$text)
	{
		$output = $this->message . $text . " ({$this->count})";
		$this->count++;
		return $output;
	}
}
?>