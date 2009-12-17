<?php
/*
 * tools/packages/scorm-1.2/settings.php
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

if (authenticate(AT_PRIV_PACKAGES, AT_PRIV_RETURN)) {
       $_pages['tools/packages/scorm-1.2/settings.php']['parent'] =
               'tools/packages/index.php';
       $_pages['tools/packages/scorm-1.2/settings.php']['children'] = array ();
}

$org_id = $_GET['org_id'];
if (isset($_POST[org_id])) {
	$org_id = $_POST[org_id];
	$sql = "UPDATE	".TABLE_PREFIX."scorm_1_2_org
		SET	lesson_mode = '$_POST[lesson_mode]',
			credit      = '$_POST[credit]'
		WHERE	org_id = $org_id
		";
	$result = mysql_query($sql, $db);
	if ($result) {
		$msg->addFeedback('SCORM_SETTINGS_SAVED');
	} else {
		$msg->addError('SCORM_SETTINGS_SAVE_ERROR');
	}
}

$sql = "SELECT	org_id, title, credit, lesson_mode
	FROM	".TABLE_PREFIX."scorm_1_2_org 
	WHERE	org_id = $org_id
	";

$result = mysql_query($sql, $db);

if (mysql_num_rows($result) == 0) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos (NO_PACKAGES);
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
} else {
	$row = mysql_fetch_assoc($result);
	$_pages['tools/packages/scorm-1.2/settings.php']['children'] = array();
	$_pages['tools/packages/scorm-1.2/settings.php']['title']
		= $row['title'];
	$cr = $row['credit'];
	$lm = $row['lesson_mode'];
}


require(AT_INCLUDE_PATH.'header.inc.php');

?>
<div class="input-form">
<form name="form1" method="post"
      action="tools/packages/scorm-1.2/settings.php"
      enctype="multipart/form-data">

<input type="hidden" name="org_id" value="<?php echo $org_id; ?>"

<p> <?php echo _AT(scorm_credit_mode_info);?> </p>
<p>
      <?php echo _AT(scorm_credit_mode);?> <br />
      <select name="credit">
      <option value="credit" 
      	<?php if ($cr == 'credit') echo 'selected'; ?>><?php echo _AT('scorm_credit'); ?></option>
      <option value="no-credit"
      	<?php if ($cr != 'credit') echo 'selected'; ?>><?php echo _AT('scorm_no_credit'); ?></option>
      </select>
</p>

<p> <?php echo _AT(scorm_lesson_mode_info);?> </p>
<p>
      <?php echo _AT(scorm_lesson_mode);?> <br />
      <select name="lesson_mode">
      <option value="browse" <?php if ($lm == 'browse') echo 'selected'; ?>><?php echo _AT('scorm_browse'); ?></option>
      <option value="normal" <?php if ($lm != 'browse') echo 'selected'; ?>><?php echo _AT('scorm_normal'); ?></option>
      </select>
</p>

<div class="row buttons">
      <input type="submit" name="submit" 
	     onClick="setClickSource('submit');"
	     value="<?php echo _AT('save'); ?>" />
</div>

</form>
</div>


<script language="javascript" type="text/javascript">

var but_src;

function setClickSource(name) {
	but_src = name;
}

</script>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
