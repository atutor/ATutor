<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

	$_include_path = 'include/';
	
	require ($_include_path.'vitals.inc.php');

	$_section[0][0] = _AT('404');

	require ($_include_path.'header.inc.php');
	
	echo '<h2>'._AT('404').'</h2>';
	$infos = _AT('404_blurb', $_SERVER['REQUEST_URI']);
	print_infos($infos);

	require ($_include_path.'footer.inc.php'); 
?>