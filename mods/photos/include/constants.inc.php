<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

//Photo Album base variables
define('AT_PA_BASENAME',	'mods/photos/');
define('AT_PA_BASE',		AT_INCLUDE_PATH.'../mods/photos/');
define('AT_PA_INCLUDE',		AT_PA_BASE.'include/');
define('AT_PA_CONTENT_DIR',	AT_CONTENT_DIR.'photos/');
define('AT_PA_PHOTOS_PER_PAGE',			28);	//max # of photos to display
define('AT_PA_ALBUMS_PER_PAGE',			5);		//max # of albums to display on index.php
define('AT_PA_ADMIN_ALBUMS_PER_PAGE',	50);	//max # of albums to display on index.php
define('AT_PA_PAGE_WINDOW',				2);		//max # of photos to display

//Image sizes
define('AT_PA_IMAGE',		604);		//default maximum width/height for images
define('AT_PA_IMAGE_THUMB',	130);		//default maximum width/height for thumbnails

//Album types
define('AT_PA_TYPE_ALL_ALBUM',			-1);//all albums
define('AT_PA_TYPE_MY_ALBUM',			1);	//my album
define('AT_PA_TYPE_COURSE_ALBUM',		2);	//course album
define('AT_PA_TYPE_PERSONAL',			3); //personal album
define('AT_PA_TYPE_GROUP_ALBUM',		4);	//group album
define('AT_PA_TYPE_SOCIAL_GROUP_ALBUM',	5);	//social group album

//Album permissions
define('AT_PA_PRIVATE_ALBUM',			0);	//private album, default
define('AT_PA_SHARED_ALBUM',			1);	//shared album
?>
