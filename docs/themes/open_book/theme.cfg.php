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
// $Id$

/* This is the default configuration file for the default theme. */

/* The theme's name. */
	$_theme['name'] = 'Open Book';

/* The theme's version number. */
	$_theme['version'] = '0.1';

/* Which version of ATutor is this theme intended for. */
	$_theme['atutor-version'] = '1.4.1';

/* author information */
	$_theme['author_name']  = 'heidi hazelton';
	$_theme['author_url']   = '';
	$_theme['author_email'] = 'heidi.hazelton@utoronto.ca';


/*	doesn't use custom banner option
	theme default course banner styles: */
	$_theme['banner']['font-family'] = 'Verdana, Arial, sans-serif';
	$_theme['banner']['font-weight'] = 'bold';
	$_theme['banner']['color']       = 'black';
	$_theme['banner']['font-size']   = 'xx-large';
	$_theme['banner']['text-align']  = 'center';

	$_theme['banner']['background-color'] = 'white';
	$_theme['banner']['background-image'] = '';
	$_theme['banner']['vertical-align']   = 'middle';
	$_theme['banner']['padding']          = '3px';


/* public navigation */
	$pub_nav['home']['name']  = _AT('home');
	$pub_nav['home']['url']   = HOME_URL;
	$pub_nav['home']['image'] = $theme_image_path . 'user-home.gif';

	$pub_nav['register']['name'] = _AT('register');
	$pub_nav['register']['url']  = 'registration.php';
	$pub_nav['register']['image'] = $theme_image_path . 'user-profile.gif';

	$pub_nav['browse_courses']['name'] = _AT('browse_courses');
	$pub_nav['browse_courses']['url']  = 'browse.php';
	$pub_nav['browse_courses']['image'] = $theme_image_path . 'user-browse.gif';

	$pub_nav['search']['name']    = _AT('search');
	$pub_nav['search']['url']     = $_base_path . 'search.php';
	$pub_nav['search']['image']   = $theme_image_path.'user-search.gif';

	$pub_nav['login']['name'] = _AT('login');
	$pub_nav['login']['url'] = 'login.php';
	$pub_nav['login']['image'] = $theme_image_path . 'user-login.gif';

	$pub_nav['password_reminder']['name'] = _AT('password_reminder');
	$pub_nav['password_reminder']['url']  = 'password_reminder.php';
	$pub_nav['password_reminder']['image'] = $theme_image_path . 'user-password.gif';

/* admin navigation */
	$admin_nav['home']['name'] = _AT('home');
	$admin_nav['home']['url']  = 'admin/index.php';
	
	$admin_nav['users']['name'] = _AT('users');
	$admin_nav['users']['url'] = 'admin/users.php';

	$admin_nav['courses']['name'] = _AT('courses');
	$admin_nav['courses']['url']  = 'admin/courses.php';

	$admin_nav['categories']['name'] = _AT('cats_course_categories');
	$admin_nav['categories']['url']  = 'admin/course_categories.php';

	$admin_nav['language']['name'] = _AT('language');
	$admin_nav['language']['url']  = 'admin/language.php';

	$admin_nav['server_configuration']['name'] = _AT('server_configuration');
	$admin_nav['server_configuration']['url'] = 'admin/config_info.php';

/* main member navigation */
	$user_nav['my_courses']['name']    = _AT('my_courses');
	$user_nav['my_courses']['url']     = $_base_path . 'users/index.php';
	$user_nav['my_courses']['attribs'] = '';
	$user_nav['my_courses']['image']   = $theme_image_path .'user-star.gif';

	$user_nav['preferences']['name']    = _AT('preferences');
	$user_nav['preferences']['url']     = $_base_path . 'users/preferences.php?g=20';
	$user_nav['preferences']['attribs'] = '';
	$user_nav['preferences']['image']   = $theme_image_path.'user-preferences.gif';

	$user_nav['profile']['name']    = _AT('profile');
	$user_nav['profile']['url']     = $_base_path . 'users/edit.php';
	$user_nav['profile']['attribs'] = '';
	$user_nav['profile']['image']   = $theme_image_path.'user-profile.gif';

	$user_nav['browse_courses']['name']    = _AT('browse_courses');
	$user_nav['browse_courses']['url']     = $_base_path . 'users/browse.php';
	$user_nav['browse_courses']['attribs'] = '';
	$user_nav['browse_courses']['image']   = $theme_image_path.'user-browse.gif';

	$user_nav['inbox']['name']    = 'place holder will be replaced with either inbox_off or inbox_on as appropriate';

	$user_nav['inbox_off']['name']    = _AT('inbox');
	$user_nav['inbox_off']['url']     = $_base_path . 'users/inbox.php?g=21';
	$user_nav['inbox_off']['attribs'] = '';
	$user_nav['inbox_off']['image']   = $theme_image_path.'user-inbox-off.gif';

	$user_nav['inbox_on']['name']    = _AT('inbox');
	$user_nav['inbox_on']['url']     = $_base_path . 'users/inbox.php?g=21';
	$user_nav['inbox_on']['attribs'] = '';
	$user_nav['inbox_on']['image']   = $theme_image_path.'user-inbox-on.gif';

	$user_nav['help']['name']    = _AT('help');
	$user_nav['help']['url']     = $_base_path . 'help/index.php?g=18';
	$user_nav['help']['attribs'] = '';
	$user_nav['help']['image']   = $theme_image_path.'user-help.gif';

	$user_nav['search']['name']    = _AT('search');
	$user_nav['search']['url']     = $_base_path . 'search.php';
	$user_nav['search']['attribs'] = '';
	$user_nav['search']['image']   = $theme_image_path.'user-search.gif';
 

/* main course navigation */
	$nav['home']['name']     =_AT('home');                          /* gets the name from the langauge db                        */
	$nav['home']['url']	     = $_base_path . 'index.php?g=14';      /* relative to the base directory                            */
	$nav['home']['attribs']  = 'accesskey="1"';                     /* as many attributes as needed. accesskey, id, etc..        */
	$nav['home']['image']    = $theme_image_path . 'bullet.gif';  /* the image name, relative to the theme's images/ directory */
	$nav['home']['title']    = _AT('home')." (ALT-1)";              /* title/alt text for links */

	$nav['tools']['name']    = _AT('tools');
	$nav['tools']['url']     = $_base_path . 'tools/index.php?g=15';
	$nav['tools']['attribs'] = 'accesskey="2"';
	$nav['tools']['image']   = $theme_image_path . 'bullet2.gif';
	$nav['tools']['title']   = _AT('tools')." (ALT-2)";

	$nav['resources']['name']    = _AT('resources');
	$nav['resources']['url']     = $_base_path . 'resources/index.php?g=16';
	$nav['resources']['attribs'] = 'accesskey="3"';
	$nav['resources']['image']   = $theme_image_path . 'bullet3.gif';
	$nav['resources']['title']   = _AT('resources')." (ALT-3)";

	$nav['discussions']['name']    = _AT('discussions');
	$nav['discussions']['url']     = $_base_path . 'discussions/index.php?g=17';
	$nav['discussions']['attribs'] = 'accesskey="4"';
	$nav['discussions']['image']   = $theme_image_path . 'bullet4.gif';
	$nav['discussions']['title']   = _AT('discussions')." (ALT-4)";

	$nav['sitemap']['name']    = _AT('sitemap');
	$nav['sitemap']['url']     = $_base_path . 'tools/sitemap/index.php?g=23';
	$nav['sitemap']['attribs'] = 'accesskey="5"';
	$nav['sitemap']['image']   = $theme_image_path . 'bullet6.gif';
	$nav['sitemap']['title']   = _AT('sitemap')." (ALT-5)";

	/* ACollab link: only used when ACollab has been installed as an add-on to ATutor: */
	$nav['acollab']['name']    = _AT('acollab');
	$nav['acollab']['url']     = $_base_path . 'acollab/bounce.php';
	$nav['acollab']['attribs'] = '';
	$nav['acollab']['image']   = $theme_image_path . 'bullet5.gif';
	$nav['acollab']['title']   = _AT('acollab');
?>