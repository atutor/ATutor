<?php
/****************************************************************/
/* ATutor														                            */
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Cindy Qi Li            */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												                      */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				        */
/****************************************************************/
// $Id: index_admin.php 2008-01-23 14:49:24Z cindy $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_UTF8CONV);

require ('utf8conv.php');

?>
