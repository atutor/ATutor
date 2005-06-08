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

<h2>4.1.2 Properties</h2>
	<p>In the properties tab, you can move the content, select a Release Date, enter keywords for easier searching, and you can specify related topics.</p>

	<dl>
		<dt>Move</dt>
		<dd>In the left column of the Properties screen in the Content Editor choose the 'up arrow' to move the current content <em>Before</em> another item. Choose the 'down arrow' to move the content <em>After</em> that item. Choose the 'plus sign' to make the current content a <em>Child of</em>, or sub-topic, for that item. </dd>
		<dt>Release Date</dt>
		<dd>The release date is the date when the content will be visible to students. You can schedule a release in the future by specifying a later date. Also, if you specify a release date that has past, it will be released immediately. By default the Release Date is set to the current date and time.</dd>
		<dt>Keywords</dt>
		<dd>Words entered into the Keywords area are given greater emphasis during searching. Therefore they will be placed higher in a list of search results than if there were no keywords. Keywords are also used as Learning Object Metadata when a content package is generated.</dd>
		<dt>Related Topics</dt>
		<dd><p>For each piece of content you have in the course, you can specify what other content in the course is related to it. Related topics appear in a content menu module, so students can quickly jump to the topic. A return link is available from the related topic back to the topic currently being viewed.</p>
		</dd>
	</dl>

<?php require('../common/body_footer.inc.php'); ?>
