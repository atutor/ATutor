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
// $Id: menu_pages.php 4799 2005-06-06 13:19:09Z heidi $

require('../common/body_header.inc.php'); ?>

<h2>8.4 Managing Forums</h2>

<p>Once a forum has been created, you should become familiar with the forum management tools. To access these tools browse a forum while logged in as an instructor, or as a student with forum management privilieges.</p>

<h3>8.4.1 Managing Threads</h3>
<p>For each thread in a forum, you can perform the following actions:</p>
<dl>
<dt>Stick Thread</dt>
<dd><p>This keeps the specified thread at the top of the list. This is useful for keeping important information visible to forum users. Once a post has been stuck, it will have an exclamation ( ! ) icon next to it and remain at the top of the forum's thread list.</p>

<p>To unstick a thread, just select the "Sticky Thread" icon again.</p>

<p>Possible uses of a sticky thread: course dates, forum rules, contact information, or important course material.</p></dd>

<dt>Lock Thread</dt>
<dd><p>When locking a thread you have two options: lock posting and reading, and lock posting only. The first option, lock posting and reading, closes the thread so that no one can read the contents or post replies. However, the title of the thread remains listed in the forum. The second option, lock posting only, allows users to read the entire thread but not post any replies to it. Locked threads will have a special "Lock" icon next to the Topic title.</p>

<p>You can change the lock option by selecting the <em>Unlock Thread</em> icon. By doing so you have the option of unlocking, close the thread to reading and posting, or close the thread to posting.</p></dd>

<dt>Delete Thread</dt>
<dd>You can delete a thread by selecting the <em>Delete Thread</em>. Once a thread is deleted, it can not be undeleted.</dd>
</dl>

<h3>8.4.1 Managing Posts</h3>
<p>For each post in a given thread, the course instructor, or privileged students, can perform the following administrative actions: Edit, and Delete.</p>

<dl>
<dt>Edit</dt>
<dd>Allows the instructor to edit the post title and the body of a post.</dd>

<dt>Delete</dt>
<dd>Allows the instructor to delete the post from the parent thread. Deleting the first post from a thread will delete the entire thread including all replies. A confirmation will be asked prior to each deletion.</dd>
</dl>
</p>

<?php require('../common/body_footer.inc.php'); ?>
