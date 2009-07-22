<?php
/*
 * tools/packages/delete.php
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

$ptypes = explode (',', AT_PACKAGE_TYPES);
$plug = Array();
foreach ($ptypes as $type) {
	include ('./' . $type . '/lib.inc.php');
}

if (sizeOf ($_POST['goners']) > 0) {
	foreach ($ptypes as $type) {
		$plug[$type]->deletePackages ($_POST['goners']);
	}
}

$sql = "SELECT package_id, ptype FROM ".TABLE_PREFIX."packages WHERE course_id = $_SESSION[course_id] ORDER BY package_id";
$result = mysql_query($sql, $db);

$num = 0;
while ($row = mysql_fetch_assoc($result)) {
	foreach ($plug[$row['ptype']]->getDeleteFormItems ($row['package_id'], $num) as $l) {
		$p .= '<li>' . $l . '</li>' . "\n";
		$num++;
	}
}

if ($num == 0) {
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php' );
	exit;
} 

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<div class="input-form">	
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('delete_package'); ?></legend>
	<form method="post" action="tools/packages/delete.php">
		<ol>
		<?php echo $p; ?>
		</ol>
		<div class="row buttons">
			<input type="submit" name="submit"  value="<?php echo _AT('delete_selected_package_s'); ?>" />
		</div>
	</form>
	</fieldset>
</div>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
