<?php

/**
* 
* Output a set of radio <input>s with the same name.
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
* @version $Id: Savant_Plugin_radios.php,v 1.1 2004/04/06 17:56:27 joel Exp $
* 
* @access public
* 
* @param object &$savant A reference to the calling Savant object.
* 
* @param string $name The HTML "name=" value of all the radio <input>s.
* 
* @param array $options An array of key-value pairs where the key is the
* radio button value and the value is the radio button label.
* 
* $options = array (
* 	0 => 'zero',
*	1 => 'one',
*	2 => 'two'
* );
* 
* @param string $checked A comparison string; if any of the $option
* element values and $checked are the same, that radio button will
* be marked as "checked" (otherwise not).
* 
* @param string $extra Any "extra" HTML code to place within the
* <input /> element.
* 
* @param string $sep The HTML text to place between every radio
* button in the set.
* 
* @return string
* 
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_radios extends Savant_Plugin {

	function radios(
		&$savant,
		$name,
		$options,
		$checked = null,
		$set_unchecked = null,
		$sep = "<br />\n",
		$extra = null)
	{
		$html = '';
		
		if (is_array($options)) {
			
			if (! is_null($set_unchecked)) {
				// this sets the unchecked value of the
				// radio button set.
				$html .= "<input type=\"hidden\" ";
				$html .= "name=\"$name\" ";
				$html .= "value=\"$set_unchecked\" />\n";
			}
			
			foreach ($options as $value => $label) {
				$html .= "<input type=\"radio\" ";
				$html .= "name=\"$name\" ";
				$html .= "value=\"$value\"";
				
				if ($value == $checked) {
					$html .= " checked=\"checked\"";
				}
				$html .= " $extra />$label$sep";
			}
		}
		
		return $html;
	}
}
?>