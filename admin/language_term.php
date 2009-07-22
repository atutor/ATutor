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

if (isset($_POST['submit'])) {
	$_POST['variable'] = $addslashes($_POST['variable']);
	$_POST['term'] = $addslashes($_POST['term']);
	$_POST['text'] = $addslashes($_POST['text']);

	$_POST['variable'] = '_c' . $_POST['variable'];

	$sql = "REPLACE INTO ".TABLE_PREFIX."language_text VALUES ('$_SESSION[lang]', '{$_POST['variable']}', '{$_POST['term']}', '{$_POST['text']}', NOW(), '')";
	mysql_query($sql, $db);
	header('Location: '.$_SERVER['PHP_SELF'].'?term='.$_POST['term']);
	exit;
} else if (isset($_POST['delete'])) {
	$_POST['variable'] = $addslashes($_POST['variable']);
	$_POST['term'] = $addslashes($_POST['term']);
	$_POST['text'] = $addslashes($_POST['text']);

	$_POST['variable'] = '_c' . $_POST['variable'];

	$sql = "DELETE FROM ".TABLE_PREFIX."language_text WHERE language_code='$_SESSION[lang]' AND `variable`='{$_POST['variable']}' AND term='{$_POST['term']}' LIMIT 1";
	mysql_query($sql, $db);
	header('Location: '.$_SERVER['PHP_SELF'].'?term='.$_POST['term']);
	exit;
}

require(AT_INCLUDE_PATH.'html/frameset/header.inc.php');
if (isset($_GET['term'])) {
	$_GET['term'] = $addslashes($_GET['term']);
	$sql = "SELECT * FROM ".TABLE_PREFIX."language_text WHERE language_code='$_SESSION[lang]' AND term='$_GET[term]' ORDER BY `variable` DESC";
	$result = mysql_query($sql, $db);
	$original_row = mysql_fetch_assoc($result);
	$custom_row = mysql_fetch_assoc($result);
}
?>

<?php if ($original_row): ?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="term" value="<?php echo $_GET['term']; ?>" />
	<input type="hidden" name="variable" value="<?php echo $original_row['variable']; ?>" />

	<div class="input-form" style="width: 99%">
		<div class="row">
			<h3><?php echo $original_row['term']; ?></h3>
			<?php if ($original_row['context']): ?>
				<p><?php echo $original_row['context']; ?></p>
			<?php endif; ?>

			<?php if ($custom_row): ?>
				<h4><?php echo _AT('original_term'); ?></h4>
				<p><?php echo $original_row['text']; ?></p>
				<textarea name="text" rows="8" cols="50"><?php echo htmlspecialchars($custom_row['text']); ?></textarea>
			<?php else: ?>
				<textarea name="text" rows="8" cols="50"><?php echo htmlspecialchars($original_row['text']); ?></textarea>
			<?php endif; ?>
		</div>

		<div class="row">
			<?php 
				$sql	= "SELECT * FROM ".TABLE_PREFIX."language_pages WHERE `term`='$_GET[term]' ORDER BY page LIMIT 11";
				$result	= mysql_query($sql, $db);
				if (mysql_num_rows($result) > 10) {
					echo '<em>'._AT('global_more_than_10_pages').'</em>';
				} else {
					echo '<ul style="padding: 0px; margin: 0px; list-style: none">';
					while ($page_row = mysql_fetch_assoc($result)) {
						echo '<li>'.$page_row['page'] . '</li>';
					}
					echo '</ul>';
				}
			 ?>
		</div>

		<div class="buttons row">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
			<?php if ($custom_row): ?>
				<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
			<?php endif; ?>
		</div>
	</div>
	</form>
<?php else: ?>
	<p><?php echo _AT('select_term_to_edit'); ?></p>
<?php endif; ?>
</body>
</html>