<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

/**
* Detect if the provided AContent supports Live Content Linking feature,
* which is supported by AContent 1.3+.
* @access	public
* @author	Cindy Qi Li
*/

function AContent_has_lcl_support() {
	$tool = @get_headers($GLOBALS['_config']['transformable_uri'] . 'oauth/tool.php');

	return $tool[0] != 'HTTP/1.1 404 Not Found';
}
?>