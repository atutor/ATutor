<?php

/**
* 
* Output a <link ... /> to a CSS stylesheet.
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
* @version $Id: Savant_Plugin_stylesheet.php,v 1.1.1.1 2003/09/24 15:51:53 pmjones Exp $
* 
* @access public
* 
* @param object &$savant A reference to the calling Savant object.
* 
* @param string $href The HREF leading to the stylesheet file.
* 
* @return string
* 
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_stylesheet extends Savant_Plugin {

	function stylesheet(&$savant, $href)
	{
		return '<link rel="stylesheet" type="text/css" href="' .
			$href . '" />';
	}

}

?>