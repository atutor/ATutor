<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

	define('AT_INCLUDE_PATH', '../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	
	require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	global $savant;
	$msg =& new Message($savant);

	if ($_POST['cancel']) {
		$msg->addFeedback('CANCELLED');
		Header('Location: ../glossary/index.php?L='.strtoupper(substr($_POST['word'], 0, 1)));
		exit;
	}

	if ($_POST['submit']) {
		//$_POST['word']			= str_replace('<', '&lt;', trim($_POST['word']));
		//$_POST['definition']	= str_replace('<', '&lt;', trim($_POST['definition']));

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
			Header('Location: ../glossary/index.php');
			exit;
		}
	}

	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('glossary');
	$_section[1][1] = 'glossary/';
	$_section[2][0] = _AT('edit_glossary');

	$onload = 'onload="document.form.title.focus()"';

	require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h2>'._AT('edit_glossary').'</h2>';

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

	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">';

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
<input type="hidden" name="gid" value="<?php echo $gid; ?>" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><img src="images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php echo _AT('edit_glossary');  ?></th>
</tr>
<tr>
	<td align="right" class="row1"><b><label for="title"><?php echo _AT('glossary_term');  ?>:</label></b></td>
	<td class="row1"><input type="text" name="word" size="40" id="title" class="formfield" value="<?php echo htmlspecialchars(stripslashes($row['word'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td valign="top" align="right" class="row1"><b><label for="body"><?php echo _AT('glossary_definition'); ?>:</label></b></td>
	<td class="row1"><textarea name="definition" class="formfield" cols="55" rows="7" id="body"><?php echo $row['definition']; ?></textarea></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td valign="top" align="right" class="row1"><b><?php echo _AT('glossary_related');  ?>:</b></td>
	<td class="row1"><?php

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
	?><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td colspan="2" align="center" class="row1"><br /><input type="submit" name="submit" value="<?php echo _AT('edit_glossary');  ?>[Alt-s]" accesskey="s" class="button" /> - <input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel');  ?>" /></td>
</tr>
</table>

</form>

<?php
	require (AT_INCLUDE_PATH.'footer.inc.php');
?>