<?php
/*
 * mods/scorm_packages/scorm-1.2/read.php
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


	header('Content-Type: text/plain; charset=utf-8');

	/*
	 * Get all stored cmi values
	 */
	$sql = "SELECT lvalue, rvalue
		FROM   ".TABLE_PREFIX."cmi
		WHERE  member_id = ".$_SESSION['member_id']."
		AND    item_id   = ".$_POST['sco_id'];
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$cmi[$row['lvalue']]=$row['rvalue'];
	}

	/*
	 * Insert default values
	 */
	$defcmi['cmi.core.total_time']    = "0000:00:00.00";
	$defcmi['cmi.core.lesson_status'] = "not attempted";
	$defcmi['cmi.core.entry']	  = "ab-initio";
	while (list($l, $r) = each($defcmi)) {
		if (!array_key_exists ($l, $cmi)) {
			$cmi[$l]=$r;
			$sql = "INSERT	INTO ".TABLE_PREFIX."cmi
				VALUES (0,
					$_POST[sco_id],
					$_SESSION[member_id],
					'$l',
					'$defcmi[$l]'
				)";
			$result = mysql_query($sql, $db);
		}
	}


	/*
	 * Get cmi values which come from manifest
	 */
	$sql = "SELECT	org_id,
			maxtimeallowed,
			timelimitaction,
			masteryscore,
			datafromlms
		FROM	".TABLE_PREFIX."scorm_1_2_item
		WHERE	item_id = ".$_POST['sco_id'];

	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	$cmi['cmi.launch_data']                    = $row['datafromlms'];
	$cmi['cmi.student_data.max_time_allowed']  = $row['maxtimeallowed'];
	$cmi['cmi.student_data.mastery_score']     = $row['masteryscore'];
	$cmi['cmi.student_data.time_limit_action'] = $row['timelimitaction'];

	$org_id = $row['org_id'];
			
	/*
	 * Get lesson_mode and credit/no credit from organization.
	 * These values are set by the course owner
	 */
	$sql = "SELECT	credit,
			lesson_mode
		FROM    ".TABLE_PREFIX."scorm_1_2_org
		WHERE	org_id = ".$org_id;
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	$cmi['cmi.core.credit']      = $row['credit'];
	$cmi['cmi.core.lesson_mode'] = $row['lesson_mode'];

	/*
	 * WE DON'T GIVE THE VALUE OF student_id !!!
	 * $cmi['cmi.core.student_id']=$_SESSION['member_id'];
	 */

	while (list($l, $r) = each($cmi)) {
		echo $l.'='. urlencode($r)."\n";
	}
?>

