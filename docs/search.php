<?php
/************************************************************************/
/* ATutor														        */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca												        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.				        */
/************************************************************************/
// $Id$

$_user_location = 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');
require(AT_INCLUDE_PATH . 'lib/search.inc.php');
$onload = 'document.form.keywords.focus();';
require(AT_INCLUDE_PATH . 'header.inc.php');
$savant->display('search.tmpl.php');
require(AT_INCLUDE_PATH . 'footer.inc.php');
?>