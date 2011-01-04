<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: language_editor.php 10142 2010-08-17 19:17:26Z hwong $

/**
 * BEWARE OF THE HACKS USED TO IMPLEMENT THIS FEATURE:
 *
 * this page is to allow admins to edit/customize their language
 * and save the changes made in a way that allows the upgrading of
 * ATutor without the loss of that language. It also allows customized
 * language to be reverted back to its original form.
 *
 * since we couldn't change the database as it would break backwards
 * compatability, none of the fields could be changed which means
 * that the only way to store the extra language would be by reusing
 * the `variable` field, which is part of the PK.
 *
 * reusing the `variable` is a huge hack and doesn't correctly support
 * module language as there is nothing enfocing storing module language
 * in an independant way. ideally there would be another field in the
 * database designating custom or not and the `variable` field would
 * be removed completely since it doesn't have much effect any more.
 *
 * custom language is stored as `_c_template` and `_c_msgs` for template
 * and feedback messages, respectively. Why use "_c" as the prefix?
 * because it comes before "_t" and _m" in the alphabet. This lets us
 * sort the language by `variable` and limit it to one result. That is 
 * how the custom language terms are retrieved in place of default
 * language.
 *
 * another oddity is that although custom language text isn't deleted
 * upon upgrades, the language definitions are, which means those terms
 * cannot be edited until after the language pack is reinstalled.
 * this also means that if a term has changed the system might be unaware
 * of new replacement tokens and could break.
 *
 */

define('AT_INCLUDE_PATH', '../../../include/');
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
		if ($strlen($word) > 1) {
			$word = str_replace(array('%','_'), array('\%', '\_'), $word);
			$words[$key] = "(CAST(`term` AS CHAR) LIKE '%$word%' OR CAST(`text` AS CHAR) LIKE '%$word%')";
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
	<table cellspacing="0" cellpadding="0">
	<tr>
	<td valign="top">
		<?php if ($num_results): ?>
			<select size="<?php echo min(max($num_results,2), 25); ?>" name="terms" id="terms" onchange="javascript:showtext(this);">
				<?php
					while ($row = mysql_fetch_assoc($result)): 
						if ($strlen($row['text']) > 30) {
							$row['text'] = $substr($row['text'], 0, 28) . '...';
						}
					?>
						<option value="<?php echo $row['term']; ?>"><?php echo htmlspecialchars($row['text']); ?></option>
					<?php endwhile; ?>
			</select>
		<?php else: ?>
			<p><?php echo _AT('none_found'); ?></p>
		<?php endif; ?>
	</td>

	<td valign="top">
		<div class="row">
			<iframe src="mods/_core/languages/language_term.php" frameborder="0" height="430" width="450" marginheight="0" marginwidth="0" name="tran" id="tran"></iframe>
		</div>
	</td>
	</tr>
	</table>
</div>
</form>

<script language="javascript" type="text/javascript">
//<!--
function showtext(obj) {
	frames['tran'].location.href = "<?php echo AT_BASE_HREF; ?>mods/_core/languages/language_term.php?type=<?php echo $_variables[$_GET['type']].SEP; ?>term=" + obj.value;
}
//-->
</script>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>