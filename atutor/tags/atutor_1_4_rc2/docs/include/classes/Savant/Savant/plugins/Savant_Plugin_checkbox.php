<?php

/**
* 
* Output a single checkbox <input> element.
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
* @version $Id: Savant_Plugin_checkbox.php,v 1.1 2004/04/06 17:56:27 joel Exp $
* 
* @access public
* 
* @param object &$savant A reference to the calling Savant object.
* 
* @param string $name The HTML "name=" value for the checkbox.
* 
* @param mixed $value The value of the checkbox if checked.
* 
* @param mixed $selected Check $value against this; if they match,
* mark the checkbox as checked.
* 
* @param string $set_unchecked If null, this will add no HTML to the
* output. However, if set to any non-null value, the value will be
* added as a hidden element before the checkbox so that if the
* checkbox is unchecked, the hidden value will be returned instead
* of the checked value.
* 
* @param string $extra Any "extra" HTML code to place within the
* checkbox element.
* 
* @return string
* 
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_checkbox extends Savant_Plugin {

	function checkbox(
		&$savant,
		$name,
		$value,
		$selected = null,
		$set_unchecked = null,
		$extra = null)
	{
		$html = '';
		
		if (! is_null($set_unchecked)) {
			// this sets the unchecked value of the checkbox.
			$html .= "<input type=\"hidden\" ";
			$html .= "name=\"$name\" ";
			$html .= "value=\"$set_unchecked\" />\n";
		}
		
		$html .= "<input type=\"checkbox\" ";
		$html .= "name=\"$name\" ";
		$html .= "value=\"$value\"";
				
		if ($value == $selected) {
			$html .= " checked=\"checked\"";
		}
		
		$html .= " $extra />";
		
		return $html;
	}
}
?>