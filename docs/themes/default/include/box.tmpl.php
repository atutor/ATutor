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
if (!defined('AT_INCLUDE_PATH')) { exit; }
?>

	<h5><?php echo $this->tmpl_menu_url; ?><?php echo $this->title; ?> </h5>
	<br />

	<div class="body">	
		<div class="content odd">
			<?php echo $this->tmpl_dropdown_contents; ?>
		</div>
	</div>
	<br />