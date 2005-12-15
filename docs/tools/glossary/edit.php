<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_GLOSSARY);

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	Header('Location: index.php');
	exit;
}

if ($_POST['submit']) {

	if ($_POST['word'] == '') {
		$msg->addError('TERM_EMPTY');
	}

	if ($_POST['definition'] == '') {
		$msg->addError('DEFINITION_EMPTY');
	}

	$_POST['related_term'] = intval($_POST['related_term']);


	if (!$msg->containsErrors()) {
		$_POST['word']  = $addslashes($_POST['word']);
		$_POST['definition']  = $addslashes($_POST['definition']);

		$sql = "UPDATE ".TABLE_PREFIX."glossary SET word='$_POST[word]', definition='$_POST[definition]', related_word_id=$_POST[related_term] WHERE word_id=$_POST[gid] AND course_id=$_SESSION[course_id]";
		
		$result = mysql_query($sql, $db);

		$msg->addFeedback('GLOS_UPDATED');
		Header('Location: index.php');
		exit;
	}
}

$onload = 'document.form.title.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

if ($_POST['submit']) {
	$gid = intval($_POST['gid']);
} else {
	$gid = intval($_GET['gid']);
}

if ($gid == 0) {
	$msg->printErrors('GLOS_ID_MISSING');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$msg->printErrors();

$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."glossary WHERE word_id=$gid", $db);

if (!( $row = @mysql_fetch_array($result)) ) {
	$msg->printErrors('TERM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if ($_POST['submit']) {
	$row['word']		= $_POST['word'];
	$row['definition']  = $_POST['definition'];
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="gid" value="<?php echo $gid; ?>" />

<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title"><?php echo _AT('glossary_term');  ?></label><br/ >
		<input type="text" name="word" size="40" id="title" value="<?php echo htmlspecialchars(stripslashes($row['word'])); ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="body"><?php echo _AT('glossary_definition'); ?></label><br />
		<textarea name="definition" cols="55" rows="7" id="body"><?php echo $row['definition']; ?></textarea>
	</div>

	<div class="row">
		<?php echo _AT('glossary_related');  ?><br />
	<?php
		$sql = "SELECT * FROM ".TABLE_PREFIX."glossary WHERE course_id=$_SESSION[course_id] AND word_id<>$gid ORDER BY word";

		$result = mysql_query($sql, $db);
		if ($row_g = mysql_fetch_array($result)) {
			echo '<select name="related_term">';
			echo '<option value="0"></option>';
			do {
				if ($row_g['word_id'] == $row['word_id']) {
					continue;
				}
		
				echo '<option value="'.$row_g['word_id'].'"';
			
				if ($row_g['word_id'] == $row['related_word_id']) {
					echo ' selected="selected" ';
				}
			
				echo '>'.$row_g['word'].'</option>';
			} while ($row_g = mysql_fetch_array($result));
			
			echo '</select>';
		
		} else {
			echo  _AT('no_glossary_items');
		}
	?>
	</div>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel');  ?>" />
	</div>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>