<?php
/*
 * tools/packages/scorm-1.2/lib.inc.php
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

require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

class package_handler_scorm_1_2 {

	function getManagerItemLinks ($id) {
		$result = $this->getOrgs ($id);
		$rv = Array();
		while ($row = mysql_fetch_assoc($result)) {
			array_push ($rv,
				'<div class="scormitem">'
				. '<a href="tools/packages/scorm-1.2/view.php?org_id='
				. $row['org_id'].'"'
			        . ' title="ADL SCORM-1.2 Package"'
				. ' onclick="show(\'scorm_1_2_throb_' . $row['org_id'] . '\')"'
				. '>'
				.  $row['title'] .
				'</a></div>' . '
			<div class="scormfeedback" id="scorm_1_2_throb_'
			. $row['org_id']
			. '" style="display:none;position:absolute;">'
			. '<p>'
			. _AT(package_scorm_1_2_rte_loading)
			. '</p>'
			. '<img src="images/transfer.gif" height="20" width="90" alt="">'
			. '</div>'
				
			);
		}
		return $rv;
	}

	function getLearnerItemLinks ($id) {
		$result = $this->getOrgs ($id);
		$rv = Array();
		while ($row = mysql_fetch_assoc($result)) {
			array_push ($rv, ''
				. '<div class="scormitem">'
				. '<a href="tools/packages/scorm-1.2/learner_view.php?org_id='
				. $row['org_id'].'"'
			        . ' title="ADL SCORM-1.2 Package"'
				. ' onclick="show(\'scorm_1_2_throb_' . $row['org_id'] . '\')"'
				. '>'
				.  $row['title'] 
				. '</a>'
				. '</div>'

			. ' <div class="scormfeedback" id="scorm_1_2_throb_'
			. $row['org_id']
			. '" style="display:none;position:absolute;">'
			. '<p>'
			. _AT(package_scorm_1_2_rte_loading)
			. '</p>'
			. '<img src="images/transfer.gif" height="20" width="90" alt="">'
			. '</div>'
				
			);
		}
		return $rv;
	}

	function getCMILinks ($id) {
		$result = $this->getOrgs ($id);
		$rv = Array();
		while ($row = mysql_fetch_assoc($result)) {
			array_push ($rv,
				'<div class="scormitem">'
				. '<a href="tools/packages/scorm-1.2/cmi.php?org_id='
				. $row['org_id'].'"'
			        . ' title="SCORM-1.2 CMI Data"'
				. '>'
				.  $row['title'] .
				'</a></div>' 
			);
		}
		return $rv;
	}

	function getSettingsLinks ($id) {
		$result = $this->getOrgs ($id);
		$rv = Array();
		while ($row = mysql_fetch_assoc($result)) {
			array_push ($rv,
				'<div class="scormitem">'
				. '<a href="tools/packages/scorm-1.2/settings.php?org_id='
				. $row['org_id'].'"'
			        . ' title="ADL SCORM-1.2 Package"'
				. '>'
				.  $row['title'] .
				'</a></div>' 
			);
		}
		return $rv;
	}

	function getDeleteFormItems ($id, $i) {
		$result = $this->getOrgs ($id);
		$rv = Array();
		while ($row = mysql_fetch_assoc($result)) {
			array_push ($rv, ''
				. '<input type="checkbox" '
				. 'id="goners['. $i . ']" '
				. 'name="goners[' . $i . ']" '
			        . 'value="' . $row[org_id] . '" />'
				. '<label class="scorminput" '
			        .  'for="goners['. $i .']">' . $row['title']
				.  '</label>'
				
			);
			$i++;
		}
		return $rv;
	}

	function getOrgs ($id) {

		global $db;
		$sql = "SELECT	o.org_id,
				o.title
			FROM	".TABLE_PREFIX."packages p,
				".TABLE_PREFIX."scorm_1_2_org o
			WHERE	p.package_id = $id
			AND	o.package_id = p.package_id
			ORDER	BY o.org_id
		";

		return mysql_query($sql, $db);
	}

	function deletePackages ($pids) {
		global $msg;
		global $db;

		/*
		 * Dangerous deleting begins here
		 */

		foreach ($pids as $id) {
			$sql = "SELECT	p.package_id
				FROM	".TABLE_PREFIX."packages p,
					".TABLE_PREFIX."scorm_1_2_org o
				WHERE	o.org_id = $id
				AND	o.package_id = p.package_id
				AND	p.ptype      = 'scorm-1.2'
				AND	p.course_id  = $_SESSION[course_id]
			";

			$result = mysql_query($sql, $db);
			if (!$result) contunue;

			$row = mysql_fetch_assoc($result);
			$pkg = $row['package_id'];

			$sql = "SELECT	o.org_id
				FROM	".TABLE_PREFIX."packages p,
					".TABLE_PREFIX."scorm_1_2_org o
				WHERE	p.package_id = $pkg
				AND	o.package_id = p.package_id
			";

			$result = mysql_query($sql, $db);
			$orgs = array();
			while ($row = mysql_fetch_assoc($result)) {
				array_push ($orgs, $row['org_id']);	
			}

			$sql = "SELECT item_id
				FROM ".TABLE_PREFIX."scorm_1_2_item 
				WHERE  org_id = $id
				";
			$result = mysql_query($sql, $db);
			$items = array('null');
			while ($row = mysql_fetch_assoc($result)) {
				array_push ($items, $row['item_id']);	
			}
			
			/*
			 * Delete cmi data of all items in organization for
			 * all learners
			 */
			$sql = "DELETE	FROM ".TABLE_PREFIX."cmi
				WHERE	item_id in (" . implode (',', $items) . ")";

			$result = mysql_query ($sql, $db);

	
			/*
			 * Delete all items belonging to this organization
			 */

			$sql = "DELETE	FROM ".TABLE_PREFIX."scorm_1_2_item
				WHERE	org_id = $id";

			$result = mysql_query ($sql, $db);

			/*
			 * Delete the organization entry itself
			 */

			$sql = "DELETE	FROM ".TABLE_PREFIX."scorm_1_2_org
				WHERE	org_id = $id";

			$result = mysql_query ($sql, $db);

			if (sizeOf ($orgs) == 1) {

				/*
				 * There is no more organization left from
				 * this  particular scorm-1.2 package, so
				 * we delete the package entry and all files
				 * which came with the package
				 */

				$sql = "DELETE	FROM ".TABLE_PREFIX."packages WHERE package_id = $pkg";

				$result = mysql_query ($sql, $db);

				$pdir = AT_INCLUDE_PATH
				. '../sco/'
				. $_SESSION['course_id']
				. '/' . $pkg . '/';

				clr_dir ($pdir);
			}
		}
	}
}

$plug['scorm-1.2'] = new package_handler_scorm_1_2();

?>
