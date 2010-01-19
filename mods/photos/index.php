<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institution  */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include (AT_PA_INCLUDE.'lib.inc.php');
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');
$_custom_css = $_base_path . AT_PA_BASENAME . 'module.css'; // use a custom stylesheet

//instantiate obj
$pa = new PhotoAlbum();
$type = intval($_GET['album_type']);
$albums = $pa->getAlbums($_SESSION['member_id'], $type);

include (AT_INCLUDE_PATH.'header.inc.php'); 
$savant->assign('albums', $albums);
$savant->display('pa_index.tmpl.php');
include (AT_INCLUDE_PATH.'footer.inc.php'); 
?>