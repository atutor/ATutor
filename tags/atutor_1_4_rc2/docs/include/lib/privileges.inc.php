<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$privs[AT_PRIV_CONTENT]			= _AT('priv_manage_content');
$privs[AT_PRIV_GLOSSARY]		= _AT('priv_manage_glossary');
$privs[AT_PRIV_TEST_CREATE]		= _AT('priv_create_tests');
$privs[AT_PRIV_TEST_MARK]		= _AT('priv_mark_tests');
$privs[AT_PRIV_FILES]			= _AT('priv_files');
$privs[AT_PRIV_LINKS]			= _AT('priv_links');
$privs[AT_PRIV_FORUMS]			= _AT('priv_forums');
$privs[AT_PRIV_STYLES]			= _AT('priv_styles');
$privs[AT_PRIV_ENROLLMENT]		= _AT('priv_enrollment');
$privs[AT_PRIV_COURSE_EMAIL]	= _AT('priv_course_email');
$privs[AT_PRIV_ANNOUNCEMENTS]	= _AT('priv_announcements');

asort($privs);
reset($privs);

?>