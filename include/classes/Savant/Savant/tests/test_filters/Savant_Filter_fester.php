<?php

/**
* 
* Abstract Savant_Filter class.  You have to extend this class for it to
* be useful; e.g., "class Savant_Filter_example extends Savant_Filter".
* 
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as
* published by the Free Software Foundation; either version 2.1 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful, but
* WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* @license http://www.gnu.org/copyleft/lesser.html LGPL
* 
* @author Paul M. Jones <pmjones@ciaweb.net>
* 
* @package Savant
* 
* @version $Id: Savant_Filter_fester.php,v 1.2 2003/10/02 19:03:39 pmjones Exp $
* 
*/

require_once 'Savant/Filter.php';

class Savant_Filter_fester extends Savant_Filter {
	
	var $count = 0;
	
	function Savant_Filter_fester(&$savant)
	{
		// initialize the parent constructor
		$this->Savant_Filter($savant);
	}
	
	function fester(&$savant, &$text)
	{
		$text .= "<br />Fester has a light bulb in his mouth (" .
			$this->count++ . ")\n";
	}
}
?>