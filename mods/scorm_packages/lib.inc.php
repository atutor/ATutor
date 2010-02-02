<?php
/*
 * mods/scorm_packages/lib.inc.php
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
$ptypes = explode (',', AT_PACKAGE_TYPES);
$plug = Array();
foreach ($ptypes as $type) {
	require ($type . '/lib.inc.php');
}

$sql_get_pkgs = "SELECT  package_id,
		ptype
	FROM  ".TABLE_PREFIX."packages
	WHERE   course_id = $_SESSION[course_id]
	ORDER   BY package_id
";

function getPackagesLearnerLinkList () {
	global $db;
	global $plug;
	global $sql_get_pkgs;

	$rv = Array();

	$result = mysql_query($sql_get_pkgs, $db);

	while ($row = mysql_fetch_assoc($result)) {
		foreach ($plug[$row['ptype']]->getLearnerItemLinks(
			$row['package_id']) as $l) {
			array_push ($rv, $l);
		}
	}
	return $rv;
}

function getPackagesManagerLinkList () {
	global $db;
	global $plug;
	global $sql_get_pkgs;

	$rv = Array();

	$result = mysql_query($sql_get_pkgs, $db);

	while ($row = mysql_fetch_assoc($result)) {
		foreach ($plug[$row['ptype']]->getManagerItemLinks(
			$row['package_id']) as $l) {
			array_push ($rv, $l);
		}
	}
	return $rv;
}

function getScript () {
	return "
<script>
function getObj (o) {
	if(document.getElementById) return document.getElementById(o);
	if(document.all) return document.all[o];
}
function show (n) {
	o = typeof(n)=='string'?getObj (n):n;
	if (!o) return;
	if(o.style) o.style.display = '';
	else o.display='';
}
</script>
";
}

?>
