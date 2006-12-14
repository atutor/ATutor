<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: merlot.php 6614 2006-09-27 19:32:29Z greg $


if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_MERLOT',       $this->getPrivilege());
define('AT_ADMIN_PRIV_MERLOT', $this->getAdminPrivilege());

/* if this module is to be made available to students on the Home or Main Navigation.  */

$_student_tool = 'mods/merlot/index.php';
$this->_pages['mods/merlot/add_to_links.php']['title_var'] = 'add_link';

/*******
 * add the admin page so the Userplane ID can be managed
 */

if (admin_authenticate(AT_ADMIN_PRIV_USERPLANE, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/merlot/index_admin.php');
	$this->_pages['mods/merlot/index_admin.php']['title_var'] = 'merlot';
	$this->_pages['mods/merlot/index_admin.php']['parent']    = AT_NAV_ADMIN;
}

/*******
 * student or instructor page.
 */
$this->_pages['mods/merlot/index.php']['title_var'] = 'merlot';
$this->_pages['mods/merlot/index.php']['img'] = 'mods/merlot/merlot.gif';

?>