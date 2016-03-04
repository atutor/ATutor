<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_LANGUAGES);


$_GET['term'] = htmlspecialchars($_GET['term'], ENT_QUOTES);

if (isset($_POST['submit'])) {
	$_POST['variable'] = $addslashes($_POST['variable']);
	$_POST['term'] = $addslashes($_POST['term']);
	$_POST['text'] = $addslashes($_POST['text']);

	$_POST['variable'] = '_c' . $_POST['variable'];
	
	$sql = "REPLACE INTO %slanguage_text VALUES ('%s', '%s', '%s', '%s', NOW(), '')";
	queryDB($sql, array(TABLE_PREFIX, $_SESSION['lang'], $_POST['variable'], $_POST['term'], $_POST['text']));
	
	header('Location: '.$_SERVER['PHP_SELF'].'?term='.$_POST['term']);
	exit;
} else if (isset($_POST['delete'])) {
	$_POST['variable'] = $addslashes($_POST['variable']);
	$_POST['term'] = $addslashes($_POST['term']);
	$_POST['text'] = $addslashes($_POST['text']);

	$_POST['variable'] = '_c' . $_POST['variable'];

	$sql = "DELETE FROM %slanguage_text WHERE language_code='%s' AND `variable`='{%s}' AND term='{%s}' LIMIT 1";
	queryDB($sql, array(TABLE_PREFIX, $_SESSION['lang'], $_POST['variable'], $_POST['term']));
	
	
	header('Location: '.$_SERVER['PHP_SELF'].'?term='.$_POST['term']);
	exit;
}

require(AT_INCLUDE_PATH.'html/frameset/header.inc.php');
if (isset($_GET['term'])) {
	$_GET['term'] = $addslashes($_GET['term']);
	$sql = "SELECT * FROM %slanguage_text WHERE language_code='%s' AND term='%s' ORDER BY `variable` DESC";
	$original_rows = queryDB($sql, array(TABLE_PREFIX, $_SESSION['lang'], $_GET['term']));
    foreach($original_rows as $row){
        $original_row = $row;
        $custom_row = $original_row;
	}
}
?>

<?php if ($original_row): ?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="term" value="<?php echo htmlspecialchars($_GET['term'], ENT_QUOTES); ?>" />
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
				$rows_pages	= queryDB($sql, array(TABLE_PREFIX, $_GET[term]));
				if(count($rows_pages) > 10){
					echo '<strong>'._AT('global_more_than_10_pages').'</strong>';
				} else {
					echo '<ul style="padding: 0px; margin: 0px; list-style: none">';
					foreach($rows_pages as $page_row){
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