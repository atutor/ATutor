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
// $Id: 

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php')
?>

<p align="bottom">

<a href="../../get.php/@/<?php echo urldecode($_GET['file']); ?>" target="_top"><?php echo _AT('download_file'); ?></a> |
<a href="../../get.php/<?php echo urldecode($_GET['file']); ?>" target="_top"><?php echo _AT('remove_frame'); ?></a> | 
<a href="index.php" target="_top"><?php echo _AT('return_file_manager'); ?></a>
</p>