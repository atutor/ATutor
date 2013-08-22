<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_GLOSSARY);

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	Header('Location: index.php');
	exit;
}

if ($_POST['submit']) {
	$missing_fields = array();

	$_POST['word'] = trim($_POST['word']);
	$_POST['definition'] = trim($_POST['definition']);

	if ($_POST['word'] == '') {
		$missing_fields[] = _AT('glossary_term');
	} else{
		//60 is defined by the sql
		$_POST['word'] = validate_length($_POST['word'], 60);
	}

	if ($_POST['definition'] == '') {
		$missing_fields[] = _AT('glossary_definition');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	$_POST['related_term'] = intval($_POST['related_term']);


	if (!$msg->containsErrors()) {
		$_POST['word']  = $addslashes($_POST['word']);
		$_POST['definition']  = $addslashes($_POST['definition']);
		$_POST['gid'] = intval($_POST['gid']);

		$sql = "UPDATE %sglossary SET word='%s', definition='%s', related_word_id=%d WHERE word_id=%d AND course_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $_POST['word'], $_POST['definition'], $_POST['related_term'], $_POST['gid'], $_SESSION['course_id']));
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
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
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$msg->printErrors();

$sql = "SELECT * FROM %sglossary WHERE word_id=%d";
$row = queryDB($sql, array(TABLE_PREFIX, $gid), TRUE);

if(count($rows) == 0){

	$msg->printErrors('ITEM_NOT_FOUND');
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
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('edit_glossary'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('glossary_term');  ?></label><br/ >
		<input type="text" name="word" size="40" id="title" value="<?php echo htmlentities_utf8($stripslashes($row['word'])); ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body"><?php echo _AT('glossary_definition'); ?></label><br />
		<textarea name="definition" cols="55" rows="7" id="body"><?php echo htmlentities_utf8($row['definition']); ?></textarea>
	</div>

	<div class="row">
		<?php echo _AT('glossary_related');  ?><br />
	<?php

		$sql = "SELECT * FROM %sglossary WHERE course_id=%d AND word_id<>%d ORDER BY word";
		$rows_g = queryDB($sql, array(TABLE_PREFIX, $_SESSION[course_id], $gid));

		if(count($rows_g) != 0){
			echo '<select name="related_term">';
			echo '<option value="0"></option>';
			foreach($rows_g as $row_g){

				if ($row_g['word_id'] == $row['word_id']) {
					continue;
				}
		
				echo '<option value="'.$row_g['word_id'].'"';
			
				if ($row_g['word_id'] == $row['related_word_id']) {
					echo ' selected="selected" ';
				}
			
				echo '>'.htmlentities_utf8($row_g['word']).'</option>';
			} 
			
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
	</fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
