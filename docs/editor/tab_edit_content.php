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
	
	$current_tab = strtolower($_POST['submit']);
	if (empty($current_tab)) { $current_tab = "content"; 
	} else if ($current_tab=="save") { 
		$current_tab = $_POST['current_tab']; 
	}

	$changes_made = false;
	$changes_made = tab_process($current_tab);

	$_section[0][0] = _AT('edit_content');
	//$onload = 'onload="document.form.title.focus()"';
	$path	= $contentManager->getContentPath($cid);
	require(AT_INCLUDE_PATH.'header.inc.php');
	$cid = intval($_REQUEST['cid']);
	$pid = intval($_REQUEST['pid']);

?>
	<h2><?php echo _AT('edit_content');  ?></h2>
<?php
	$help[] = AT_HELP_EMBED_GLOSSARY;
	$help[] = AT_HELP_CONTENT_PATH;
	$help[] = AT_HELP_CONTENT_BACKWARDS;
?>
<p>(<a href="frame.php?p=<?php echo urlencode($_my_uri); ?>"><?php echo _AT('open_frame'); ?></a>).</p>
<?php
	/* print any errors that occurred */
	print_errors($errors);
	print_feedback($feedback);
	print_help($help);
?>
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="form" enctype="multipart/form-data">
	<?php

	if($cid!=0 && $cid!="" ) {
		$result = $contentManager->getContentPage($cid);

		if (!( $row = @mysql_fetch_array($result)) ) {
			$errors[]=AT_ERROR_PAGE_NOT_FOUND;
			print_errors($errors);
			require (AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
	}

	echo  '<input type="hidden" name="pid" value="'.$_REQUEST['pid'].'" />';
	echo  '<input type="hidden" name="cid" value="'.$_REQUEST['cid'].'" />';

	echo '<input type="hidden" name="title" value="'.$_POST['title'].'" />';
	echo '<input type="hidden" name="text" value="'.$_POST['text'].'" />';
	echo '<input type="hidden" name="pid" value="'.$_POST['pid'].'" />';
	echo '<input type="hidden" name="formatting" value="'.$_POST['formatting'].'" />';
	echo '<input type="hidden" name="new_ordering" value="'.$_POST['new_ordering'].'" />';

	echo '<input type="hidden" name="day" value="'.$_POST['day'].'" />';
	echo '<input type="hidden" name="month" value="'.$_POST['month'].'" />';
	echo '<input type="hidden" name="year" value="'.$_POST['year'].'" />';
	echo '<input type="hidden" name="hour" value="'.$_POST['hour'].'" />';
	echo '<input type="hidden" name="minute" value="'.$_POST['minute'].'" />';
	echo '<input type="hidden" name="related" value="'.$_POST['related'].'" />';
	echo '<input type="hidden" name="current_tab" value="'.$current_tab.'" />';

	echo '<input type="hidden" name="keywords" value="'.$_POST['keywords'].'" />';
?>
	<input type="hidden" name="MAX_FILE_SIZE" value="204000" />

<?php output_tabs($current_tab); ?>

		<table cellspacing="1" cellpadding="0" width="90%" border="0" class="bodyline" summary="" align="center">	
<?php if ($changes_made) { ?>
		<tr class="unsaved">
			<td height="1" colspan="2" align="center">Unsaved changes have been made. <input type="submit" name="save" value="Save" class="button" accesskey="s" />   <input type="submit" name="close" class="button" value="<?php echo _AT('close'); ?>" /></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php } else { ?>
		<tr class="row1">
			<td height="1" colspan="2" align="center">No unsaved changes have been made. <input type="submit" name="submit" value="Save" class="button" accesskey="s" />   <input type="submit" name="close" class="button" value="<?php echo _AT('close'); ?>" /></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php }
			
	$tabs = get_tabs();	
	foreach($tabs as $tab) {
		if ($current_tab == $tab[0]) {
			include(AT_INCLUDE_PATH."/html/tabs/".$tab[1]);
		}
	}
 ?>
		</table>
	</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>