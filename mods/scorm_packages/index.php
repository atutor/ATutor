<?php
/*
 * mods/scorm_packages/index.php
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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_PACKAGES);
require ('lib.inc.php');
$pkgs = getPackagesManagerLinkList();

if (authenticate(AT_PRIV_PACKAGES, AT_PRIV_RETURN)) {
	$_pages['mods/scorm_packages/index.php']['children'] = array (
	        'mods/scorm_packages/import.php'
	);
	
	if (sizeOf ($pkgs) > 0) {
		array_push ($_pages['mods/scorm_packages/index.php']['children'], 
	       		            'mods/scorm_packages/delete.php'
		);
		/*
		array_push ($_pages['mods/scorm_packages/index.php']['children'], 
	       			    'mods/scorm_packages/settings.php'
		);
		*/
	}
	
}


require(AT_INCLUDE_PATH.'header.inc.php');
echo '<div style="display:block; border:thin solid #cccccc;width:95%;padding:1em;margin:auto;">';
if (sizeOf ($pkgs) == 0) {
	$msg->addInfo (NO_PACKAGES);
	$msg->printAll();
} else {
	echo getScript();
        echo '<ol>' . "\n";
	foreach ($pkgs as $pk) {
		echo '<li>' . $pk . '</li>' . "\n";
	}
	echo '</ol>' . "\n";
}
echo "</div>";
require (AT_INCLUDE_PATH.'footer.inc.php');
?>
