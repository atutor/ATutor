<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

session_cache_limiter('private, must-revalidate');
session_set_cookie_params(0); // seconds
session_cache_expire(120); // minutes
session_start();
session_register('login');		    /* login name                   */
session_register('valid_user');     /* =true or =false/[empty]      */
session_register('member_id');	    /* duh                          */
session_register('is_admin');		/* is this an instructor, T/F   */
session_register('lang');			/* language						*/
session_register('course_id');		/*								*/
session_register('menus');          /* the menus array              */
session_register('is_guest');
session_register('is_super_admin');
session_register('this_topic_id');
session_register('edit_mode');		/* true/false for admin only		   */
session_register('prefs');			/* array of preferences			   */
session_register('cprefs');			/* array of course default preferences						*/
session_register('layout');			/* array of layout options		   */
session_register('use_default_prefs');  /* override personal prefs with course prefs */
session_register('s_cid');			/* content id								*/
session_register('from_cid');		/* from cid									*/
session_register('course_title');	/* course title								*/

session_register('enroll');			/* true iff a user is enrolled or pending.*/

session_register('last_updated');	/* last time the online list was updated	*/

session_register('my_referer');		/* previous page							*/

session_register('prefs_saved');	/* true|false have prefs been saved?	*/
session_register('track_me');		/* true|false whether or not this user gets tracked */
session_register('pretime');		/* keep track of the timestamp for the previous page for duration calculation */

$current_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

if (!isset($_SESSION['course_id'])
	&& (strcasecmp($current_url, $_base_href.'login.php'))
	&& (strcasecmp($current_url, $_base_href.'bounce.php')) 
	&& (strcasecmp($current_url, $_base_href.'registration.php')) 
	&& (strcasecmp($current_url, $_base_href.'browse.php')) 
	&& !$_public
	&& (strcasecmp($current_url, $_base_href.'password_reminder.php')) 
	&& (strcasecmp($current_url, $_base_href.'about.php'))
	)
{
	header('Location: '.$_base_href.'login.php');
	exit;
}

?>