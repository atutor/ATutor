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
* @version $Id: Filter.php,v 1.1.1.1 2003/09/24 15:51:53 pmjones Exp $
* 
*/

class Savant_Filter {
	
	/**
	* 
	* A reference to the calling Savant object.
	* 
	* @access public
	* 
	* @var object
	* 
	*/
	
	var $savant;
	
	
	/**
	* 
	* Constructor.  If your extended class is static (which is the
	* default), you don't need to deal with this at all.
	* 
	* @access public
	* 
	* @param object &$savant A reference to the calling Savant object.
	* 
	*/
	
	function Savant_Filter(&$savant)
	{
		$this->savant =& $savant;
		return;
	}
}
?>