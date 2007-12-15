<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

?>


<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $_config['prof_pic_max_file_size']; ?>" />

            <div class="row" style="float:right;width:40%;">
		<h3><?php echo _AT('upload_icon'); ?></h3>
		<input type="file" name="customicon" /> (<?php echo implode(', ', $supported_images); ?>)
	</div>


<?php  //require(AT_INCLUDE_PATH.'footer.inc.php'); ?>