<?php

/**
* 
* Output a <script></script> link to a JavaScript file.
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
* @version $Id: Savant_Plugin_javascript.php,v 1.1 2004/04/06 17:56:27 joel Exp $
* 
* @access public
* 
* @param object &$savant A reference to the calling Savant object.
* 
* @param string $href The HREF leading to the JavaScript source
* file.
* 
* @return string
* 
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_javascript extends Savant_Plugin {

	function javascript(&$savant, $href)
	{
		return '<script language="javascript" type="text/javascript" src="' .
			$href . '"></script>';
	}

}
?>