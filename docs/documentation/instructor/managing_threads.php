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

<h2>8.4 Managing Threads</h2>

<p>As an instructor, it is wise to become familiar with the forum management tools. To access these tools, browse a forum while logged in as an instructor or as an assistant with forum management privileges.</p>

<p>For each thread in a forum, the following actions are available:</p>
<dl>
	<dt>Stick Thread</dt>
	<dd><p>Use the exclamation point icon next to a thread to stick it.  This keeps the specified thread at the top of the forum's thread list and is useful for keeping important information visible to forum users. </p>

	<p>To unstick a thread, just use the <em>Sticky Thread</em> icon again.</p>

	<p>Some possible uses of a sticky thread include: course dates, forum rules, contact information, or important course material.</p>
	</dd>

	<dt>Lock Thread</dt>
	<dd><p>Use the <em>Lock</em> icon next to the thread title to lock a thread. There are two options for locking a thread - lock posting and reading, and lock posting only. Lock <em>posting and reading</em> closes the thread so that no one can read the contents or post replies. But note that the title of the thread will remain listed in the forum. Lock <em>posting only</em> will let users read the entire thread but not post any replies to it. </p>

	<p>To change the lock preferences or unlock a thread, use the <em>Unlock Thread</em> icon.</p>
	</dd>

	<dt>Delete Thread</dt>
	<dd>To delete a thread, use the <em>Delete Thread</em> icon next to the thread title.  This will delete all messages within the thread and cannot be undeleted.</dd>
</dl>

<?php require('../common/body_footer.inc.php'); ?>
