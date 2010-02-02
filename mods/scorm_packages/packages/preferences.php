<?php
/*
 * packages/preferences.php
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



$sql = "SELECT	lvalue, rvalue
	FROM   ".TABLE_PREFIX."cmi
	WHERE   item_id = 0
	AND	member_id = " . $_SESSION[member_id]
	;

$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$p[$row['lvalue']] = $row['rvalue'];
}

if (!isset ($p['auto_advance'])) {
	$p['auto_advance']           = 0;
	$sql = "INSERT INTO ".TABLE_PREFIX."cmi
		VALUES (NULL, 0, $_SESSION[member_id], 'auto_advance', '0')";
	$result = mysql_query($sql, $db);

}
if (!isset ($p['show_rte_communication'])) {
	$p['show_rte_communication'] = 0;
	$sql = "INSERT INTO ".TABLE_PREFIX."cmi
		VALUES (NULL, 0, $_SESSION[member_id],
			'show_rte_communication', '0'
		)";
	$result = mysql_query($sql, $db);
}

if (isset ($_POST['upd'])) {
	$p['auto_advance'] = $_POST['auto_advance'];
	$sql = "UPDATE ".TABLE_PREFIX."cmi
		SET	rvalue = '$p[auto_advance]'
		WHERE	member_id = $_SESSION[member_id]
		AND	item_id = 0
		AND	lvalue = 'auto_advance'
		";
	$result1 = mysql_query($sql, $db);


	$p['show_rte_communication'] = $_POST['show_rte_communication'];
	$sql = "UPDATE ".TABLE_PREFIX."cmi
		SET	rvalue = '$p[show_rte_communication]'
		WHERE	member_id = $_SESSION[member_id]
		AND	item_id = 0
		AND	lvalue = 'show_rte_communication'
		";
	$result2 = mysql_query($sql, $db);

	if ($result1 && $result2) {
		$msg->addFeedback (SCORM_SETTINGS_SAVED);
	} else {
		$msg->addError (SCORM_SETTINGS_SAVE_FAILED);
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<div class="input-form" style="padding:1em;">
<form name="form1" method="post"
            enctype="multipart/form-data">
	    <input type="hidden" name="upd" value="1">

<p> <?php echo _AT(packages_auto_advance_info);?> </p>
<p>
<input type="checkbox" id="auto_advance" name="auto_advance" value="1" 
<?php echo ($p['auto_advance']?'checked':'');?> />
<label for="auto_advance"><?php echo _AT('packages_auto_advance');?><label>
</p>

<p> <?php echo _AT(packages_show_rte_communication_info);?> </p>
<p>
<input type="checkbox" id="show_rte_communication" name="show_rte_communication"
       value="1" 
<?php echo ($p['show_rte_communication']?'checked':'');?> />
<label for="show_rte_communication">
	<?php echo _AT('packages_show_rte_communication');?>
<label>
</p>

<div class="row buttons">
<input type="submit" name="submit" value="<?php echo _AT('save');?> " />
</div>

</form>
</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');?>
