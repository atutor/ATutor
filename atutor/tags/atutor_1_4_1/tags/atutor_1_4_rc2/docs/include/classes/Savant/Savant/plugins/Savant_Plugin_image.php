<?php

/**
* 
* Output an <image ... /> tag.
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
* @version $Id: Savant_Plugin_image.php,v 1.1 2004/04/06 17:56:27 joel Exp $
* 
* @access public
* 
* @param object &$savant A reference to the calling Savant object.
* 
* @param string $src The image source as a relative or absolute HREF.
* 
* @param string $link Providing a link will make the image clickable,
* leading to the URL indicated by $link; defaults to null.
* 
* @param string $alt Alternative descriptive text for the image;
* defaults to the filename of the image.
* 
* @param int $border The border width for the image; defaults to zero.
* 
* @param int $width The displayed image width in pixels; defaults to
* the width of the image.
* 
* @param int $height The displayed image height in pixels; defaults to
* the height of the image.
* 
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_image extends Savant_Plugin {

	function image(
		&$savant,
		$src,
		$alt = null,
		$border = 0,
		$width = null,
		$height = null)
	{
		$size = '';
		
		// build the alt tag
		if (is_null($alt)) {
			$alt = basename($src);
		}
		
		$alt = ' alt="' . htmlentities($alt) . '"';
				
		// build the border tag
		$border = ' border="' . htmlentities($border) . '"';
		
		// get the width and height of the image
		if (is_null($width) && is_null($height)) {
		
			if (substr(strtolower($src), 0, 7) == 'http://' ||
				substr(strtolower($src), 0, 8) == 'https://') {
				
				// the image is not on the local filesystem
				$root = '';
			
			} else {
			
				// we need to set a base root path so we can find images on the
				// local file system
				$root = isset($GLOBALS['HTTP_SERVER_VARS']['DOCUMENT_ROOT'])
					? $GLOBALS['HTTP_SERVER_VARS']['DOCUMENT_ROOT'] . '/'
					: '';
			}
			
			$info = @getimagesize($root . $src);
			
			$width = (is_null($width)) ? $info[0] : $width;
			$height = (is_null($height)) ? $info[1] : $height;
			
			unset($info);
		}
		
		// build the width tag
		if ($width > 0) {
			$size .= ' width="' . htmlentities($width) . '"';
		}
		
		// build the height tag
		if ($height > 0) {
			$size .= ' height="' . htmlentities($height) . '"';
		}
		
		// done!
		return '<img src="' . $src . '"' .
			$alt .
			$border .
			$size .
			' />';
	}
}

?>