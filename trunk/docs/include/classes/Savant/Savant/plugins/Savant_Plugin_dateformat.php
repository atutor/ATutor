<?php

/**
* 
* Output a formatted date using strftime() conventions.
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
* @version $Id: Savant_Plugin_dateformat.php,v 1.1.1.1 2003/09/24 15:51:53 pmjones Exp $
* 
* @access public
* 
* @param object &$savant A reference to the calling Savant object.
* 
* @param string $datestring Any date-time string suitable for
* strtotime().
* 
* @param string $format The strftime() formatting string.
* 
* @return string
* 
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_dateformat extends Savant_Plugin {

	function dateformat(&$savant, $datestring, $format = '%d %b %Y')
	{
		if (trim($datestring != '')) {
			return strftime($format, strtotime($datestring));
		} else {
			return '';
		}
	}

}
?>