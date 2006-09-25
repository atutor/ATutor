<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_LANGUAGES);

if (defined('AT_DEVEL_TRANSLATE') && AT_DEVEL_TRANSLATE) {
	$msg->addWarning('TRANSLATE_ON');
	require(AT_INCLUDE_PATH.'header.inc.php');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$_variables = array('template' => '_template', 'feedback' => '_msgs');
$_c_variables = array('template' => '_c_template', 'feedback' => '_c_msgs');

$sql_search = '';
if (isset($_GET['filter'], $_GET['search'])) {
	$_GET['search'] = trim($addslashes($_GET['search']));
	$words = explode(' ', $_GET['search']);
	foreach ($words as $key => $word) {
		// search `term` and `text` only
		if (strlen($word) > 1) {
			$word = str_replace(array('%','_'), array('\%', '\_'), $word);
			$words[$key] = "(`term` LIKE '%$word%' OR `text` LIKE '%$word%')";
		} else {
			unset($words[$key]);
		}
	}
	if ($words) {
		$sql_search = ' AND (' . implode(' OR ', $words).')';
	}
} else if ($_GET['reset_filter']) {
	unset($_GET);
}
if (!isset($_GET['type']) || !isset($_variables[$_GET['type']])) {
	$_GET['type'] = 'template';
}

if (isset($_GET['custom'])) {
	$variable = $_c_variables[$_GET['type']];
} else {
	$variable = $_variables[$_GET['type']];
}

$sql = "SELECT * FROM ".TABLE_PREFIX."language_text WHERE language_code='$_SESSION[lang]' AND `variable`='$variable' $sql_search ORDER BY text";
$result = mysql_query($sql, $db);
$num_results = mysql_num_rows($result);
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('results_found', $num_results); ?></h3>
		</div>

		<div class="row">
			<?php echo _AT('type'); ?><br />
			<input type="radio" name="type" value="template" id="tyte" <?php if ($_GET['type'] == 'template') { echo 'checked="checked"'; } ?> /><label for="tyte"><?php echo _AT('template'); ?></label>
			<input type="radio" name="type" value="feedback" id="tyfe" <?php if ($_GET['type'] == 'feedback') { echo 'checked="checked"'; } ?> /><label for="tyfe"><?php echo _AT('feedback'); ?></label>
		</div>

		<div class="row">
			<input type="checkbox" name="custom" value="1" id="cus" <?php if (isset($_GET['custom'])) { echo 'checked="checked"'; } ?> /><label for="cus"><?php echo _AT('only_show_edited_terms'); ?></label>
		</div>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?></label><br />
			<input type="text" name="search" id="search" size="40" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>

<form name="form" method="post">
<div class="input-form">
	<div class="row" style="float: left">
		<?php if ($num_results): ?>
			<select size="<?php echo min(max($num_results,2), 25); ?>" name="terms" id="terms" onchange="javascript:showtext(this);">
				<?php
					while ($row = mysql_fetch_assoc($result)): 
						if (strlen($row['text']) > 30) {
							$row['text'] = substr($row['text'], 0, 28) . '...';
						}
					?>
						<option value="<?php echo $row['term']; ?>"><?php echo htmlspecialchars($row['text']); ?></option>
					<?php endwhile; ?>
			</select>
		<?php else: ?>
			<p><?php echo _AT('none_found'); ?></p>
		<?php endif; ?>
	</div>

	<div class="row" style="float: right">
		<div class="row">
			<iframe src="admin/language_term.php" frameborder="0" height="400" width="450" marginheight="0" marginwidth="0" name="tran" id="tran"></iframe>
		</div>
	</div>
</div>
</form>

<script language="javascript" type="text/javascript">
//<!--
function showtext(obj) {
	frames['tran'].location.href = "admin/language_term.php?type=<?php echo $_variables[$_GET['type']].SEP; ?>term=" + obj.value;
}
//-->
</script>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>