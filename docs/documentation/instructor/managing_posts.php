<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: 8.4.managing_forums.php 5080 2005-07-06 15:06:11Z heidi $

require('../common/body_header.inc.php'); ?>

<h3>8.4.1 Managing Posts</h3>
<p>The course instructor and assistants with forum privileges can edit and delete posts. Access to these tools are available when viewing a thread message.</p>

<dl>
	<dt>Edit</dt>
	<dd>Use the <em>Edit</em> link to edit the title and the body of a post.</dd>

	<dt>Delete</dt>
	<dd>Use the <em>Delete</em> link to delete a post. Deleting the first post from a thread will delete the entire thread including all replies. A confirmation will be asked prior to each deletion.</dd>
</dl>


<?php require('../common/body_footer.inc.php'); ?>
