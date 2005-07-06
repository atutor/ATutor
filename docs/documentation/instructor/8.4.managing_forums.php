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

require('../common/body_header.inc.php'); ?>

<h2>8.4 Managing Forums</h2>

<p>As an instructor, it is wise to become familiar with the forum management tools. To access these tools, browse a forum while logged in as an instructor or as a student with forum management privileges.</p>

<h3>8.4.1 Managing Threads</h3>
<p>For each thread in a forum, the following actions are available:</p>
<dl>
<dt>Stick Thread</dt>
<dd><p>Use the exclamation point icon next to a thread to stick it.  This keeps the specified thread at the very top of the forum's thread list and is useful for keeping important information visible to forum users. </p>

<p>To unstick a thread, just use the <em>Sticky Thread</em> icon again.</p>

<p>Some possible uses of a sticky thread include: course dates, forum rules, contact information, or important course material.</p></dd>

<dt>Lock Thread</dt>
<dd><p>Use the <em>Lock<em> icon next to the thread title to lock a thread. There are two options for locking a thread - lock posting and reading, and lock posting only. Lock posting and reading closes the thread so that no one can read the contents or post replies. But note that the title of the thread will remain listed in the forum. Lock posting only will let users read the entire thread but not post any replies to it. </p>

<p>To change the lock preferences or unlock a thread, use the <em>Unlock Thread</em> icon.</p></dd>

<dt>Delete Thread</dt>
<dd>To delete a thread, use the <em>Delete Thread</em> icon next to the thread title.  This will delete all messages within the thread and cannot be undeleted.</dd>
</dl>

<h3>8.4.1 Managing Posts</h3>
<p>The course instructor and students with forum privileges can edit and delete posts. Access to these tools are available when viewing a thread message.</p>

<dl>
<dt>Edit</dt>
<dd>Use the <em>Edit</em> link to edit the title and the body of a post.</dd>

<dt>Delete</dt>
<dd>Use the <em>Delete</em> link to delete a post. Deleting the first post from a thread will delete the entire thread including all replies. A confirmation will be asked prior to each deletion.</dd>
</dl>
</p>

<?php require('../common/body_footer.inc.php'); ?>
