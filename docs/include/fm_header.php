<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

global $myLang;
global $page;
global $savant;
global $errors, $onload;
global $_base_href, $content_base_href, $course_base_href;
global $_user_location;
global $_base_path;
global $cid;
global $contentManager;
global $_section;
global $addslashes;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<title>ATutor File Manager</title>

	<link rel="stylesheet" href="<?php echo $_base_href.'themes/'.$_SESSION['prefs']['PREF_THEME']; ?>/styles.css" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<base href="<?php echo $_base_href; ?>" />

</head>

<body >