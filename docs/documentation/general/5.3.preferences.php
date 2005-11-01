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

<h2>5.3 Preferences</h2>
<p>ATutor can be personlised to each user by changing the account's preferences.</p>

<dl>
    <dt>Theme</dt>
    <dd>Themes are used for changing the look and feel of an ATutor installation.</dd>

    <dt>Inbox Notification</dt>
    <dd>If enabled, an email notification message will be sent each time an Inbox message is received.</dd>

    <dt>Topic Numbering</dt>
    <dd>If enabled, numbers will appear prefixed to topic titles.</dd>

    <dt>Direct Jump</dt>
    <dd>If enabled, using the Jump feature will redirect to the current section in ATutor, but of the selected course.</dd>

    <dt>Auto-Login</dt>
    <dd>If enabled, a manual login is no longer required, instead viewing ATutor will automatically log one in.</dd>

    <dt>Form Focus On Page Load</dt>
    <dd>If enabled, the cursor will be placed at the first field in the form.</dd>

    <dt>Default Language</dt>
    <dd>Controls which language ATutor should be presented in.</dd>
</dl>


<?php require('../common/body_footer.inc.php'); ?>