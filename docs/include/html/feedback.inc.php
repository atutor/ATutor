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
// $Id$

//THIS FILE IS DEPRECATED

if ($_GET['f']) {
	$f = intval($_GET['f']);
	if ($f > 0) {
		print_feedback($f);
		unset($_GET['f']);
	} else {
		/* it's probably an array */
		$f = unserialize(urldecode(stripslashes($_GET['f'])));
		print_feedback($f);
		unset($_GET['f']);
	}
} else if ($feedback) {
	print_feedback($feedback);
	unset($feedback);
}

if (isset($_info)) {
	print_infos($_info);
	unset($_info);
}

if (isset($warnings)) {
	print_warnings($warnings);
	unset($warnings);
}


if (isset($errors)) {
	print_errors($errors);
}

?>
