<?php

/**
* 
* Output a series of HTML <option>s based on an associative array
* where the key is the option value and the value is the option
* label. You can pass a "selected" value as well to tell the
* function which option value(s) should be marked as seleted.
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
* @version $Id: Savant_Plugin_options.php,v 1.1 2004/04/06 17:56:27 joel Exp $
* 
* @access public
* 
* @param object &$savant A reference to the calling Savant object.
* 
* @param array $options An associative array of key-value pairs; the
* key is the option value, the value is the option lable.
* 
* @param mixed $selected A string or array that matches one or more
* option values, to tell the function what options should be marked
* as selected.  Defaults to an empty array.
* 
* @return string
* 
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_options extends Savant_Plugin {

	function options(&$savant, $options, $selected = array(), $extra = null)
	{
		$html = '';
		
		// force $selected to be an array.  this allows multi-selects to
		// have multiple selected options.
		settype($selected, 'array');
		
		// is $options an array?
		if (is_array($options)) {
			
			// loop through the options array
			foreach ($options as $value => $label) {
				
				$html .= '<option value="' . $value . '"';
				$html .= ' label="' . $label . '"';
				
				if (in_array($value, $selected)) {
					$html .= ' selected="selected"';
				}
				
				if (! is_null($extra)) {
					$html .= ' ' . $extra;
				}
				
				$html .= ">$label</option>\n";
			}
		}
		
		return $html;
	}

}
?>