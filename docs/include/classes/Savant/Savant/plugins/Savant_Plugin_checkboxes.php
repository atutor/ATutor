<?php

/**
* 
* Output a set of checkbox <input>s.
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
* @version $Id: Savant_Plugin_checkboxes.php,v 1.1.1.1 2003/09/24 15:51:53 pmjones Exp $
* 
* @access public
* 
* @param object &$savant A reference to the calling Savant object.
* 
* @param string $name The HTML "name=" value of all the checkbox
* <input>s. The name will get [] appended to it to make it an array
* when returned to the server.
* 
* @param array $options An array of key-value pairs where the key is
* the checkbox value and the value is the checkbox label.
* 
* @param string $set_unchecked If null, this will add no HTML to the
* output. However, if set to any non-null value, the value will be
* added as a hidden element before every checkbox so that if the
* checkbox is unchecked, the hidden value will be returned instead
* of the checked value.
* 
* @param string $sep The HTML text to place between every checkbox
* in the set.
* 
* @param string $extra Any "extra" HTML code to place within the
* checkbox element.
* 
* @return string
* 
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_checkboxes extends Savant_Plugin {

	function checkboxes(
		&$savant,
		$name,
		$options,
		$selected = array(),
		$set_unchecked = null,
		$sep = "<br />\n",
		$extra = null)
	{
		// force $selected to be an array.  this allows multi-checks to
		// have multiple checked boxes.
		settype($selected, 'array');
		
		// the text to be returned
		$html = '';
		
		if (is_array($options)) {
			
			// an iteration counter.  we use this to track which array
			// elements are checked and which are unchecked.
			$i = 0;
			
			foreach ($options as $value => $label) {
				
				if (! is_null($set_unchecked)) {
					// this sets the unchecked value of the checkbox.
					$html .= "<input type=\"hidden\" ";
					$html .= "name=\"{$name}[$i]\" ";
					$html .= "value=\"$set_unchecked\" />\n";
				}
				
				
				$html .= "<input type=\"checkbox\" ";
				$html .= "name=\"{$name}[$i]\" ";
				$html .= "value=\"$value\"";
				
				if (in_array($value, $selected)) {
					$html .= " checked=\"checked\"";
				}
				
				if (! is_null($extra)) {
					$html .= " $extra";
				}
				
				$html .= " />$label$sep";
				
				$i++;
			}
		}
		
		return $html;
	}
}
?>