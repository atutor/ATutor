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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $available_languages[$_SESSION['lang']][2]; ?>">
<head>
	<title>ATutor-AChat</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $available_languages[$_SESSION['lang']][1]; ?>" />
	<link rel="stylesheet" href="<?php echo $_base_href; ?>stylesheet.css" type="text/css" />
	<?php
		if (in_array($_SESSION['lang'], array('ar', 'fa', 'he'))) {
			echo '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />'."\n";
		}
	?>
</head>
<body <?php echo $onload; ?> class="chatbody">
