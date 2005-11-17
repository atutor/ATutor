<?php
/*
 * tools/packages/import.php
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

define ('PACKAGE_TYPES', 'scorm-1.2');

if (isset ($_POST['type'])) {
	require ($_POST['type'] . '/import.php');
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<div class="input-form">
<form name="form1" method="post"
      action="tools/packages/import.php" enctype="multipart/form-data"
      onsubmit="openWindow('<?php echo $_base_href; ?>tools/prog.php');">

	<?php echo _AT('package_type_info')?>
	<div class="row">
	<label for="type"><?php echo _AT('package_type')?></label>
	<br />
	<select name="type">

	<?php
	$ptypes = explode (',', PACKAGE_TYPES);
	foreach ($ptypes as $type) {
		echo '<option value="' . $type . '">' . $type . '</option>';
	}
	?>
	</select>
	</div>

	<?php echo _AT('package_upload_file_info')?>
	<div class="row">
	<label for="to_file"><?php echo _AT('package_upload_file'); ?></label>
	<br />
	<input type="file" name="file" id="to_file" />
	</div>

	<?php echo _AT('package_upload_url_info')?>
	<div class="row">
	<label for="to_url">
	<?php echo _AT('package_upload_url'); ?>
	</label><br />
	<input type="text" name="url" value="http://" size="40" id="to_url" />
	</div>

	<div class="row buttons">
	<input type="submit" name="submit" onClick="setClickSource('submit');" value="<?php echo _AT('import'); ?>" />
	<input type="submit" name="cancel" onClick="setClickSource('cancel');" value="<?php echo _AT('cancel'); ?>" />
	</div>


</form>
</div>
<script language="javascript" type="text/javascript">

var but_src;
function setClickSource(name) {
	but_src = name;
}

function openWindow(page) {
	if (but_src != "cancel") {
		newWindow = window.open(page, "progWin",
			"width=400,height=200,toolbar=no,location=no"
		);
		newWindow.focus();
	}
}
</script>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
