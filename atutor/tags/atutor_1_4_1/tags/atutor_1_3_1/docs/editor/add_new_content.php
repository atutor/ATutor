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


	define('AT_INCLUDE_PATH', '../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	require(AT_INCLUDE_PATH.'lib/format_content.inc.php');

	if ($_POST['cancel']) {
		if ($_POST['pid'] != 0) {
			Header('Location: ../index.php?cid='.$_POST['pid'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
			exit;
		}
		Header('Location: ../index.php?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
		exit;
	}

	if( ($_POST['submit_file'] == 'Upload') && ($_FILES['uploadedfile']['name'] == ''))	{
		$errors[] = AT_ERROR_FILE_NOT_SELECTED;
	} else if ($_POST['submit_file']) {
		if ($_FILES['uploadedfile']['name']
			&& (($_FILES['uploadedfile']['type'] == 'text/plain')
			|| ($_FILES['uploadedfile']['type'] == 'text/html')) )
		{
			$fd = fopen ($_FILES['uploadedfile']['tmp_name'], 'r');
			$_POST['body'] = fread ($fd, filesize($_FILES['uploadedfile']['tmp_name']));

			$path_parts = pathinfo($_FILES['uploadedfile']['name']);
			$ext = strtolower($path_parts['extension']);
			if (in_array($ext, array('html', 'htm'))) {
				/* get the <title></title> of this page				*/

				$start_pos	= strpos(strtolower($_POST['body']), '<title>');
				$end_pos	= strpos(strtolower($_POST['body']), '</title>');

				if (($start_pos !== false) && ($end_pos !== false)) {
					$start_pos += strlen('<title>');
					$_POST['title'] = trim(substr($_POST['body'], $start_pos, $end_pos-$start_pos));
				}

				unset($start_pos);
				unset($end_pos);

				$_POST['body'] = get_html_body($_POST['body']);

				/* change formatting to HTML? */
				/* $_POST['formatting']	= 1; */
			}
			$feedback[]=AT_FEEDBACK_FILE_PASTED;
			fclose ($fd);
		} else {
			$errors[] = AT_ERROR_BAD_FILE_TYPE;
		}
	}

	if ($_POST['submit']) {
		$_POST['title'] = trim($_POST['title']);
		$_POST['body']	= trim($_POST['body']);
		$_POST['keywords']	= trim($_POST['keywords']);
		$_POST['pid']	= intval($_POST['pid']);
		$_POST['formatting']	= intval($_POST['formatting']);

		if ($_POST['title'] == '') {
			$errors[] = AT_ERROR_NO_TITLE;
		}

		$day	= intval($_POST['day']);
		$month	= intval($_POST['month']);
		$year	= intval($_POST['year']);
		$hour	= intval($_POST['hour']);
		$min	= intval($_POST['min']);

		if (!checkdate($month, $day, $year)) {
			$errors[] = AT_ERROR_BAD_DATE;
		}

		if ($errors == '') {
			if (strlen($month) == 1){
				$month = "0$month";
			}
			if (strlen($day) == 1){
				$day = "0$day";
			}
			if (strlen($hour) == 1){
				$hour = "0$hour";
			}
			if (strlen($min) == 1){
				$min = "0$min";
			}
			$release_date = "$year-$month-$day $hour:$min:00";

			$cid = $contentManager->addContent($_SESSION['course_id'],
												  $_POST['pid'],
												  $_POST['ordering'],
												  $_POST['title'],
												  $_POST['body'],
												  $_POST['keywords'],
												  $_POST['related'],
												  $_POST['formatting'],
												  $release_date);

			/* check if a definition is being used that isn't already in the glossary */
			$r = find_terms(&$_POST['body']);

			if ($r != 0) {
				Header('Location: ./add_new_glossary.php?pcid='.$cid);
				exit;
			} else {
				Header('Location: ../index.php?cid='.$cid.SEP.'f='.urlencode_feedback(AT_FEEDBACK_CONTENT_ADDED));
				exit;
			}
		}
	}

	$_section[0][0] = _AT('add_content');

	require(AT_INCLUDE_PATH.'header.inc.php');

	if (isset($_GET['pid'])) {
		$pid = intval($_GET['pid']);
	} else {
		$pid = intval($_POST['pid']);
	}
	$top_level = $contentManager->getContent($pid);

	 echo '<h2>';
		if($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2){
			echo '<img src="images/icons/default/square-large-content.gif" width="42" height="40" hspace="3" vspace="3" class="menuimage" border="0" alt="" />';
		}

	 echo _AT('add_content').'</h2>';
	$help[] = AT_HELP_EMBED_GLOSSARY;
	$help[] = AT_HELP_CONTENT_PATH;
	$help[] = AT_HELP_CONTENT_BACKWARDS;
	$help[] = AT_HELP_LINK_FILES;

print_help($help);
	?>
<p>(<a href="frame.php?p=<?php echo urlencode($_my_uri); ?>"><?php echo _AT('open_frame');  ?></a>).</p>
<?php

	/* print any errors that occurred */
	print_feedback($feedback);
	print_errors($errors);
?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
	<input type="hidden" name="MAX_FILE_SIZE" value="204000" />

		<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center"		 width="90%">
		<tr>
			<th colspan="2"class="left"><img src="images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php echo _AT('add_content'); ?></th>
			</tr>
		<tr>
			<td align="right" class="row1" valign="top"><?php
				print_popup_help(AT_HELP_PASTE_FILE);
				?>
				<b><?php echo _AT('paste_file'); ?>:</b></td>
			<td class="row1" valign="top">
				<input type="file" name="uploadedfile" class="formfield" size="20" /> <input type="submit" name="submit_file" value="<?php echo _AT('upload'); ?>" class="button" /><?php
				?><br />
				<small class="spacer"><?php echo _AT('html_only'); ?><br />
				<?php echo _AT('edit_after_upload'); ?></small>

			</td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="center" class="row1" colspan="2"><b><?php echo _AT('or'); ?></b></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="right" class="row1"><b><label for="title"><?php echo _AT('title'); ?>:</label></b></td>
			<td class="row1"><input type="text" name="title" size="40" class="formfield" value="<?php echo ContentManager::cleanOutput($_POST['title']); ?>" id="title" /></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="right" class="row1"><?php print_popup_help(AT_HELP_NOT_RELEASED); ?><b><?php echo _AT('release_date'); ?>:</b></td>
			<td class="row1"><?php
					$today_day  = date('d');
					$today_mon  = date('m');
					$today_year = date('Y');
					$today_hour = date('H');
					$today_min  = 0;

					require(AT_INCLUDE_PATH.'lib/release_date.inc.php');

			?></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td colspan="2" valign="top" align="left" class="row1"><?php print_popup_help(AT_HELP_BODY); ?><b><label for="body"><?php echo _AT('body'); ?>:</label></b><br />
				<p><textarea class="formfield" cols="73" rows="20" id="body" name="body"><?php echo ContentManager::cleanOutput($_POST['body']); ?></textarea></p>
				</td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td colspan="2" valign="top" align="left" class="row1">
			<?php print_popup_help(AT_HELP_KEYWORDS); ?>
			<b><label for="keywords"><?php echo _AT('keywords'); ?>:</label></b><br />
				<p><textarea name="keywords" class="formfield" cols="73" rows="2" id="keywords"><?php echo ContentManager::cleanOutput($_POST['keywords']); ?></textarea></p>
			</td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="right" class="row1"><?php print_popup_help(AT_HELP_FORMATTING); ?><b><?php echo _AT('formatting'); ?>:</b></td>
			<td class="row1"><input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] === 0) { echo 'checked="checked"'; } ?> /><label for="text"><?php echo _AT('plain_text'); ?></label>, <input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] !== 0) { echo 'checked="checked"'; } ?> /><label for="html"><?php echo _AT('html'); ?></label> <?php

			?></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" colspan="2"><a href="<?php echo substr($_my_uri, 0, strlen($_my_uri)-1); ?>#jumpcodes" title="<?php echo _AT('jump_code'); ?>"><?php  print_popup_help(AT_HELP_ADD_CODES1); ?><img src="images/clr.gif" height="1" width="1" alt="<?php echo _AT('jump_code'); ?>" border="0" /></a><?php require(AT_INCLUDE_PATH.'html/code_picker.inc.php'); ?></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>

		<tr>
			<td align="right" class="row1"><a name="jumpcodes"></a><?php print_popup_help(AT_HELP_INSERT); ?><b><label for="insert"><?php echo _AT('insert'); ?>:</label></b></td>
			<td class="row1"><select name="ordering" id="insert" class="formfield">
				<option value="0"><?php echo _AT('start_section'); ?></option>
			<?php
			if (is_array($top_level)) {
				$count = count($top_level);
				if ($count > 0) {
					echo '<option value="'.$count.'" selected="selected">'._AT('end_section').'</option>';
				}
				foreach ($top_level as $x => $info) {
					echo '<option value="'.$info['ordering'].'">'._AT('after').': '.$info['ordering'].' "'.$info['title'].'"</option>';
				}
			}			
			?></select></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="right" class="row1"><?php print_popup_help(AT_HELP_RELATED); ?><label for="related"><b><?php echo _AT('related_to'); ?></b></label></td>
			<td class="row1"><?php

				$temp_menu = $contentManager->getContent();

				if ($contentManager->getNumSections() > 0) {
					echo '<select class="formfield" name="related[]" id="related">';
					echo '<option value="0"></option>';

					print_select_menu(0, $temp_menu, $_POST['related'][0]);

					echo '</select></td></tr>';
 
					for ($i=1; $i<min(4, $contentManager->getNumSections() ); $i++) {
						echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
						echo '<tr><td align="right" class="row1">&nbsp;</td>';
						echo '<td class="row1"><select class="formfield" name="related[]" id="related">';
						echo '<option value="0"></option>';
						print_select_menu(0, $temp_menu, $_POST['related'][$i]);
						echo '</select></td></tr>';
					}

				} else {
					echo _AT('none_available').'</td></tr>';
				}

				echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
			?>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td colspan="2" align="center" class="row1"><br /><input type="submit" name="submit" value="<?php echo _AT('add_content');  ?>" class="button" accesskey="s" />  - <input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel');  ?>" /></td>
		</tr>
		</table>
	
	</form>
<?php

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>