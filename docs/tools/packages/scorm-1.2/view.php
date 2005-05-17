<?php
/*
 * tools/packages/scorm-1.2/view.php
 *
 * This file is part of ATutor, see http://www.atutor.ca
 * 
 * Copyright (C) 2005  Matthai Kurian 
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$me = 'tools/packages/scorm-1.2/view.php';
$im = 'tools/packages/scorm-1.2/images/';

$_pages[$me]['parent'] = 'tools/packages/index.php';
$_pages[$me]['children'] = array();
require ('./view.inc.php');
require (AT_INCLUDE_PATH.'footer.inc.php');
?>
