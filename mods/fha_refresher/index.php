<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_FHA_REFRESHER);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'tools/index.php');
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['enabled']            = abs($_POST['enabled']);
	$_POST['test_id']            = abs($_POST['test_id']);
	$_POST['score']              = abs($_POST['score']);
	$_POST['refresher_period']   = abs($_POST['refresher_period']);
	$_POST['reminder_period']    = abs($_POST['reminder_period']);
	$_POST['max_refresh_period'] = abs($_POST['max_refresh_period']);
	
	if (!$_POST['test_id']) {
		$msg->addError('FHA_REF_MISSING_TEST');
	}

	if (!$_POST['score']) {
		$msg->addError('FHA_REF_MISSING_SCORE');
	}

	if (!$_POST['refresher_period']) {
		$msg->addError('FHA_REF_MISSING_REF_PERIOD');
	}
	if (!$_POST['reminder_period']) {
		$msg->addError('FHA_REF_MISSING_REMINDER_PERIOD');
	}

	if (!$_POST['max_refresh_period']) {
		$msg->addError('FHA_REF_MISSING_MAX_PERIOD');
	}

	if (!$msg->containsErrors()) {
		$sql = "REPLACE INTO ".TABLE_PREFIX."fha_refresher VALUES ($_SESSION[course_id], $_POST[test_id], $_POST[enabled], $_POST[score], $_POST[refresher_period], $_POST[reminder_period], $_POST[max_refresh_period])";
		mysql_query($sql, $db);

		$msg->addFeedback('FHA_REF_SAVED');
		header('Location: index.php');
		exit;
	}
} else {
	$sql = "SELECT * FROM ".TABLE_PREFIX."fha_refresher WHERE course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$_POST['enabled']            = $row['enabled'];
		$_POST['test_id']            = $row['test_id'];
		$_POST['score']              = $row['pass_score'];
		$_POST['refresher_period']   = $row['refresh_period'];
		$_POST['reminder_period']    = $row['reminder_period'];
		$_POST['max_refresh_period'] = $row['max_refresh_period'];
	} else {
		$_POST['enabled']            = 0;
		$_POST['test_id']            = 0;
		$_POST['score']              = 0;
		$_POST['refresher_period']   = 60;
		$_POST['reminder_period']    = 7;
		$_POST['max_refresh_period'] = 365;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<input type="checkbox" name="enabled" value="1" id="enable" <?php if ($_POST['enabled']) { echo 'checked="checked"'; } ?> /><label for="enable"><?php echo _AT('enable'); ?></label>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="test"><?php echo _AT('fha_ref_test'); ?></label><br />
		<?php
			$sql = "SELECT test_id, title FROM ".TABLE_PREFIX."tests WHERE course_id=$_SESSION[course_id] ORDER BY title";
			$result = mysql_query($sql, $db);
		?>
		<select name="test_id" id="test">
			<?php while ($row = mysql_fetch_assoc($result)): ?>
				<option value="<?php echo $row['test_id']; ?>" <?php if ($row['test_id'] == $_POST['test_id']) { echo ' selected="selected"'; } ?>><?php echo $row['title']; ?></option>
			<?php endwhile; ?>
		</select>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="score"><?php echo _AT('fha_ref_pass_score'); ?></label><br />
		<input type="text" name="score" id="score" size="3" value="<?php echo $_POST['score']; ?>" style="text-align: right" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="ref"><?php echo _AT('fha_ref_refresher_period'); ?></label><br />
		<input type="text" name="refresher_period" size="3" id="ref" value="<?php echo $_POST['refresher_period']; ?>" style="text-align: right" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="rem"><?php echo _AT('fha_ref_reminder_period'); ?></label><br />
		<input type="text" name="reminder_period" size="3" id="rem" value="<?php echo $_POST['reminder_period']; ?>" style="text-align: right" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="max"><?php echo _AT('fha_ref_max_refresh_period'); ?></label><br />
		<input type="text" name="max_refresh_period" size="3" id="max" value="<?php echo $_POST['max_refresh_period']; ?>" style="text-align: right" />
	</div>

	<div class="buttons row">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>