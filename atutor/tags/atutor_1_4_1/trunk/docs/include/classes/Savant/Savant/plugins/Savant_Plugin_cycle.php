<?php

/**
* 
* Cycle through a series of values based on an iteration number,
* with optional group repetition.
* 
* For example, if you have three values in a cycle (a, b, c) the iteration
* returns look like this:
* 
* 0	=> a
* 1	=> b
* 2	=> c
* 3	=> a
* 4	=> b
* 5	=> c
* 
* If you repeat each cycle value (a,b,c) 2 times on the iterations,
* the returns look like this:
* 
* 0 => a
* 1 => a
* 2 => b
* 3 => b
* 4 => c
* 5 => c
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
* @version $Id: Savant_Plugin_cycle.php,v 1.1.1.1 2003/09/24 15:51:53 pmjones Exp $
* 
* @access public
* 
* @param object &$savant A reference to the calling Savant object.
* 
* @param int $iteration The iteration number for the cycle.
* 
* @param array $values The values to cycle through.
* 
* @param int $repeat The number of times to repeat a cycle value.
* 
* @return string
* 
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_cycle extends Savant_Plugin {
	
	function cycle(&$savant, $iteration, $values = null, $repeat = 1)
	{
		settype($values, 'array');
		
		// prevent divide-by-zero errors
		if ($repeat == 0) {
			$repeat = 1;
		}
		
		return $values[($iteration / $repeat) % count($values)];
	}

}
?>