<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $_base_path;
global $db; // must be global to validate sql-link resoruce below

?>
<div align="center">
<br />
<?php
	
		$getcopyright_sql="select copyright from ".TABLE_PREFIX."courses where course_id='$_SESSION[course_id]'";	
		$result2 = @mysql_query($getcopyright_sql, $db);
		$row = @mysql_fetch_row($result2);
		$show_edit_copyright = $row[0];
		if(strlen($show_edit_copyright)>0){
			echo '<small>' , $show_edit_copyright , '</small><br />';
		} 

	/****************************************************************************************/
	/* VERY IMPORTANT
	   IN KEEPING WITH THE TERMS OF THE ATUTOR LICENCE AGREEMENT (GNU GPL), THE FOLLOWING
	   COPYRIGHT LINES MAY NOT BE ALTERED IN ANY WAY.
	*/
		?>
	<small><?php echo _AT('copyright').'. '; echo '<a href="'.$_base_path.'about.php">'._AT('about_atutor').'</a>.'; ?><br />
	<span id="howto"><?php echo _AT('general_help'); ?></span></small>
</div>