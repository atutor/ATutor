<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
	define('AT_INCLUDE_PATH', '../include/');

	require(AT_INCLUDE_PATH.'vitals.inc.php');
	require(AT_INCLUDE_PATH.'lib/tab_functions.inc.php');

	if ($_POST['close']) {
		if ($_POST['pid'] != 0) {
			Header('Location: ../index.php?cid='.$pid.SEP.'f='.AT_FEEDBACK_CANCELLED);
			exit;
		}
		Header('Location: ../index.php?cid='.$cid.SEP.'f='.AT_FEEDBACK_CANCELLED);
		exit;
	}
	
	$tabs = get_tabs();	
	$num_tabs = count($tabs);
	for ($i=0; $i < $num_tabs; $i++) {
		if (isset($_POST['button_'.$i]) && ($_POST['button_'.$i] != -1)) { 
			$current_tab = $i;
			break;
		}
	}
	
	if (isset($_POST['submit'])) {
		/* we're saving. redirects after. */
		$errors = save_changes();
	}
	if (isset($_GET['tab'])) {
		$current_tab = intval($_GET['tab']);
	}
	if (!isset($current_tab) && isset($_POST['button_1']) && ($_POST['button_1'] == -1)) {
		$current_tab = 1;
	} else if (!isset($current_tab)) {
		$current_tab = 0;
	}

	$_section[0][0] = _AT('edit_content');
	//$onload = 'onload="document.form.title.focus()"';
	$path	= $contentManager->getContentPath($cid);
	require(AT_INCLUDE_PATH.'header.inc.php');
	$cid = intval($_REQUEST['cid']);

	$pid = intval($_REQUEST['pid']);
	//debug($pid);

?>
	<h2><?php echo _AT('edit_content');  ?></h2>
<p>(<a href="frame.php?p=<?php echo urlencode($_my_uri); ?>"><?php echo _AT('open_frame'); ?></a>).</p>
<?php
	/* print any errors that occurred */

	$help[] = AT_HELP_EMBED_GLOSSARY;
	$help[] = AT_HELP_CONTENT_PATH;
	$help[] = AT_HELP_CONTENT_BACKWARDS;

	print_errors($errors);
	print_feedback($feedback);
	print_help($help);
?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
	<?php

	if ($cid) {
		$result = $contentManager->getContentPage($cid);

		if (!($row = @mysql_fetch_assoc($result)) ) {
			$errors[] = AT_ERROR_PAGE_NOT_FOUND;
			print_errors($errors);
			require (AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
		

		if (isset($_POST['current_tab'])) {
			$changes_made = check_for_changes($row);
		} else {
			$changes_made = array();
			$_POST['formatting'] = $row['formatting'];
			$_POST['title'] = $row['title'];
			$_POST['text'] = $row['text'];
			$_POST['keywords'] = $row['keywords'];

			$_POST['day']   = substr($row['release_date'], 8, 2);
			$_POST['month'] = substr($row['release_date'], 5, 2);
			$_POST['year']  = substr($row['release_date'], 0, 4);
			$_POST['hour']  = substr($row['release_date'], 11, 2);
			$_POST['minute']= substr($row['release_date'], 14, 2);

			$_POST['ordering'] = $_POST['new_ordering'] = $row['ordering'];
			$_POST['related'] = $contentManager->getRelatedContent($cid);

			$_POST['pid'] = $pid = $_POST['new_pid'] = $row['content_parent_id'];
		}
	} else {
		$cid = 0;
		if (!isset($_POST['current_tab'])) {
			$_POST['day']  = date('d');
			$_POST['month']  = date('m');
			$_POST['year'] = date('Y');
			$_POST['hour'] = date('H');
			$_POST['minute']  = 0;

			$_POST['ordering'] = $_POST['new_ordering'] = count($contentManager->getContent($pid))+1;
			$_POST['pid'] = $_POST['new_pid'] = 0;

		}
		//$_POST['old_ordering'] = count($contentManager->getContent($pid));

		$changes_made = check_for_changes($row);
	}

	echo  '<input type="hidden" name="cid" value="'.$cid.'" />';

	echo '<input type="hidden" name="title" value="'.htmlspecialchars(stripslashes($_POST['title'])).'" />';
	echo '<input type="hidden" name="text" value="'.stripslashes($_POST['text']).'" />';
	echo '<input type="hidden" name="formatting" value="'.$_POST['formatting'].'" />';
	if ($current_tab != 1) {
		echo '<input type="hidden" name="new_ordering" value="'.$_POST['new_ordering'].'" />';
		echo '<input type="hidden" name="new_pid" value="'.$_POST['new_pid'].'" />';
	}

	echo '<input type="hidden" name="ordering" value="'.$_POST['ordering'].'" />';
	echo  '<input type="hidden" name="pid" value="'.$pid.'" />';


	echo '<input type="hidden" name="day" value="'.$_POST['day'].'" />';
	echo '<input type="hidden" name="month" value="'.$_POST['month'].'" />';
	echo '<input type="hidden" name="year" value="'.$_POST['year'].'" />';
	echo '<input type="hidden" name="hour" value="'.$_POST['hour'].'" />';
	echo '<input type="hidden" name="minute" value="'.$_POST['minute'].'" />';

	echo '<input type="hidden" name="current_tab" value="'.$current_tab.'" />';

	if (is_array($_POST['related']) && ($current_tab != 1)) {
		foreach($_POST['related'] as $r_id) {
			echo '<input type="hidden" name="related[]" value="'.$r_id.'" />';
		}
	}

	echo '<input type="hidden" name="keywords" value="'.$_POST['keywords'].'" />';

	/* get glossary terms */
	$matches = find_terms($_POST['text']);
	$num_terms = count($matches[0]);
	$matches = $matches[0];
	$word = str_replace(array('[?]', '[/?]'), '', $matches);

	if (is_array($word)) {
		/* update $_POST['glossary_defs'] with any new/changed terms */
		foreach($word as $w) {
			if (!isset($_POST['glossary_defs'][$w])) {
				$_POST['glossary_defs'][$w] = $glossary[$w];
			}
		}
	}

	if (is_array($_POST['glossary_defs']) && ($current_tab != 3)) {
		foreach($_POST['glossary_defs'] as $w => $d) {
			/* this term still exists in the content */
			if (!in_array($w, $word)) {
				unset($_POST['glossary_defs'][$w]);
				continue;
			}
			echo '<input type="hidden" name="glossary_defs['.$w.']" value="'.$d.'" />';
		}
		$changes_made = check_for_changes($row);
	}

/*
debug($_POST['ordering'], '$_POST[ordering]');
debug($_POST['pid'], '$_POST[pid]');

	debug($word, 'words');
	debug($glossary, 'glossary');
	debug($_POST['glossary_defs'], '$_POST[glossary_defs]');

*/


?>
	<input type="hidden" name="MAX_FILE_SIZE" value="204000" />

<?php output_tabs($current_tab, $changes_made); ?>

		<table cellspacing="1" cellpadding="0" width="90%" border="0" class="bodyline" summary="" align="center">	
<?php if ($changes_made) { ?>
		<tr class="unsaved">
			<td height="1" colspan="2" align="center"><?php echo _AT('save_changes_unsaved'); ?> <input type="submit" name="submit" value="<?php echo _AT('save_changes'); ?>" class="button" accesskey="s" />   <input type="submit" name="close" class="button" value="<?php echo _AT('close'); ?>" /></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php } else { ?>
		<tr class="row1">
			<td height="1" colspan="2" align="center"><?php echo _AT('save_changes_saved'); ?> <input type="submit" name="submit" value="<?php echo _AT('save_changes'); ?>" class="button" accesskey="s" />   <input type="submit" name="close" class="button" value="<?php echo _AT('close'); ?>" /></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php }

	include(AT_INCLUDE_PATH.'html/tabs/'.$tabs[$current_tab][1]);
?>
		</table>
	</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>