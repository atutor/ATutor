<?php

/**
* 
* Output a single <textarea> element.
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
* @version $Id: Savant_Plugin_textarea.php,v 1.1 2004/01/08 16:20:38 pmjones Exp $
* 
* @access public
* 
* @param object &$savant A reference to the calling Savant object.
* 
* @param string $name The HTML "name=" value.
* 
* @param string $text The initial value of the textarea element.
* 
* @param int $tall How many rows tall should the area be?
* 
* @param mixed $wide The many columns wide should the area be?
* 
* @param string $extra Any "extra" HTML code to place within the
* checkbox element.
* 
* @return string
* 
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_textarea extends Savant_Plugin {
	
	function textarea(&$savant, $name, $text, $tall = 24, $wide = 80, $extra = '')
	{
		$output = "<textarea name=\"$name\" rows=\"$tall\" ";
		$output .= "cols=\"$wide\" $extra>$text</textarea>";
		return $output;
	}
}

?>