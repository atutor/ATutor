<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_GLOSSARY);

if ($_POST['cancel']) {	
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

if (isset($_POST['submit'])) {
	$num_terms = intval($_POST['num_terms']);
	$missing_fields = array();

	for ($i=0; $i<$num_terms; $i++) {

		if ($_POST['ignore'][$i] == '') {
			$_POST['word'][$i] = trim($_POST['word'][$i]);
			$_POST['definition'][$i] = trim($_POST['definition'][$i]);

			if ($_POST['word'][$i] == '') {
				$missing_fields[] = _AT('glossary_term');
			} else{
				//60 is defined by the sql
				$_POST['word'] = validate_length($_POST['word'], 60);
			}
			

			if ($_POST['definition'][$i] == '') {
				$missing_fields[] = _AT('glossary_definition');
			}

			if ($terms_sql != '') {
				$terms_sql .= ', ';
			}

			$_POST['related_term'][$i] = intval($_POST['related_term'][$i]);

			/* for each item check if it exists: */

			if ($glossary[urlencode($_POST['word'][$i])] != '' ) {
				$errors = array('TERM_EXISTS', $_POST['word'][$i]);
				$msg->addError($errors);
			} else {
				$_POST['word'][$i]         = $addslashes($_POST['word'][$i]);
				$_POST['definition'][$i]   = $addslashes($_POST['definition'][$i]);
				$_POST['related_term'][$i] = $addslashes($_POST['related_term'][$i]);

				$terms_sql .= "(NULL, $_SESSION[course_id], '{$_POST[word][$i]}', '{$_POST[definition][$i]}', {$_POST[related_term][$i]})";
			}
		}
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		$sql = "INSERT INTO ".TABLE_PREFIX."glossary VALUES $terms_sql";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php');
		exit;
	}
	$_GET['pcid'] = $_POST['pcid'];
}

$onload = 'document.form.title0.focus();';

unset($word);

$num_terms = 1;

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="num_terms" value="<?php echo $num_terms; ?>" />
<?php
for ($i=0;$i<$num_terms;$i++) {
	if ($glossary[$word[$i]] != '') {
		echo '<input type="hidden" name="ignore['.$i.']" value="1" />';
		continue;
	}
	
	for ($j=0;$j<$i;$j++) {
		if ($word[$j] == $word[$i]) {
			echo '<input type="hidden" name="ignore['.$i.']" value="1" />';
			continue 2;
		}
	}

	if ($word[$i] == '') {
		$word[$i] = ContentManager::cleanOutput($_POST['word'][$i]);
	}
?>
<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title<?php echo $i; ?>"><?php echo _AT('glossary_term');  ?></label><br />
		<input type="text" name="word[<?php echo $i; ?>]" size="30" value="<?php echo trim($word[$i]); ?>" id="title<?php echo $i; ?>" /><?php			
		if ($_GET['pcid'] != '') { 
			echo '<input type="checkbox" name="ignore['.$i.']" value="1" id="ig'.$i.'" /><label for="ig'.$i.'">Ignore this term</label>.';	
		}
		?>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="body<?php echo $i; ?>"><?php echo _AT('glossary_definition');  ?></label><br />
		<textarea name="definition[<?php echo $i; ?>]" class="formfield" cols="55" rows="7" id="body<?php echo $i; ?>" style="width:90%;"><?php echo ContentManager::cleanOutput($_POST['definition'][$i]); ?></textarea>
	</div>

	<div class="row">
	<?php echo _AT('glossary_related');  ?><br />
	<?php
			$sql = "SELECT * FROM ".TABLE_PREFIX."glossary WHERE course_id=$_SESSION[course_id] ORDER BY word";
			$result = mysql_query($sql, $db);
			if ($row_g = mysql_fetch_assoc($result)) {
				echo '<select name="related_term['.$i.']">';
				echo '<option value="0"></option>';
				do {
					echo '<option value="'.$row_g['word_id'].'">'.$row_g['word'].'</option>';
				} while ($row_g = mysql_fetch_assoc($result));
				echo '</select>';
			} else {
				echo _AT('none_available');
			}
		} // endfor
	?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>

</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>