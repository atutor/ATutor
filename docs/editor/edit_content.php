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
	$cid = $_REQUEST['cid'];
	if ($_POST['cancel']) {
		if ($_POST['pid'] != 0) {
			Header('Location: ../index.php?cid='.$_POST['pid'].SEP.'f='.AT_FEEDBACK_CANCELLED);
			exit;
		}
		Header('Location: ../index.php?cid='.$_POST['cid'].SEP.'f='.AT_FEEDBACK_CANCELLED);
		exit;
	}

	if( ($_POST['submit_file'] == 'Upload') && ($_FILES['uploadedfile']['name'] == ''))	{
		$errors[] = AT_ERROR_FILE_NOT_SELECTED;
	} else if ($_POST['submit_file']) {
		if ($_FILES['uploadedfile']['name']
			&& (($_FILES['uploadedfile']['type'] == 'text/plain')
			|| ($_FILES['uploadedfile']['type'] == 'text/html')) )
		{
			$_POST['text'] = file_get_contents($_FILES['uploadedfile']['tmp_name']);

			$path_parts = pathinfo($_FILES['uploadedfile']['name']);
			$ext = strtolower($path_parts['extension']);
			if (in_array($ext, array('html', 'htm'))) {
				/* get the <title></title> of this page				*/

				$start_pos	= strpos(strtolower($_POST['text']), '<title>');
				$end_pos	= strpos(strtolower($_POST['text']), '</title>');

				if (($start_pos !== false) && ($end_pos !== false)) {
					$start_pos += strlen('<title>');
					$_POST['title'] = trim(substr($_POST['text'], $start_pos, $end_pos-$start_pos));
				}

				unset($start_pos);
				unset($end_pos);

				/* strip everything before <body> */
				$start_pos	= strpos(strtolower($_POST['text']), '<body');
				if ($start_pos !== false) {
					$start_pos	+= strlen('<body');
					$end_pos	= strpos(strtolower($_POST['text']), '>', $start_pos);
					$end_pos	+= strlen('>');

					$_POST['text'] = substr($_POST['text'], $end_pos);
				}

				/* strip everything after </body> */
				$end_pos	= strpos(strtolower($_POST['text']), '</body>');
				if ($end_pos !== false) {
					$_POST['text'] = trim(substr($_POST['text'], 0, $end_pos));
				}

				/* change formatting to HTML? */
				/* $_POST['formatting']	= 1; */
			}
			$_POST['cid']=$_POST['cid'];
			$feedback[]=AT_FEEDBACK_FILE_PASTED;
		} else {
			$errors[] = AT_ERROR_BAD_FILE_TYPE;
		}
	}
	if ($_POST['submit']) {
		$_POST['title'] = trim($_POST['title']);
		$_POST['text']	= trim($_POST['text']);
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
			if($_POST['cid']){
			$err = $contentManager->editContent($_POST['cid'], $_POST['title'], $_POST['text'], $_POST['keywords'], $_POST['new_ordering'], $_POST['related'], $_POST['formatting'], $_POST['move'], $release_date);
			}

			/* check if a definition is being used that isn't already in the glossary */
			$r = count(find_terms(&$_POST['text']));				
			//$r = preg_match_all("/(\[\?\])(.*[^\?])(\[\/\?\])/i", $_POST['text'], $matches, PREG_PATTERN_ORDER);

			if ($r != 0) {
				/* redirect to add glossery terms, but we do not know if those have been defined or not */
				Header('Location: add_new_glossary.php?pcid='.$cid);
				exit;
			} else {
				Header('Location: ../index.php?cid='.$cid.SEP.'f='.urlencode_feedback(AT_FEEDBACK_CONTENT_UPDATED));
				exit;
			}
		 }
	}

	$_section[0][0] = _AT('edit_content');

	$onload = 'onload="document.form.title.focus()"';
	$path	= $contentManager->getContentPath($cid);
	require(AT_INCLUDE_PATH.'header.inc.php');

	if (isset($_GET['pid'])) {
		$pid = intval($_GET['pid']);
	} else {
		$pid = intval($_POST['pid']);
	}

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
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
	<?php

	if(!$_POST['cid']){
		$result = $contentManager->getContentPage($cid);

		if (!( $row = @mysql_fetch_array($result)) ) {
			$errors[]=AT_ERROR_PAGE_NOT_FOUND;
			print_errors($errors);
			require (AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
	}

	$top_level = $contentManager->getContent($row['content_parent_id']);

	if ($_POST['pid']) {
		echo '<input type="hidden" name="pid" value="'.$_POST['pid'].'" />';
	} else {
	 	echo '<input type="hidden" name="pid" value="'.$pid.'" />';
	}

	if ($_POST['cid']) {
		echo '<input type="hidden" name="cid" value="'.$_POST['cid'].'" />';
	} else {
		echo '<input type="hidden" name="cid" value="'.$cid.'" />';
	}
?>

	<input type="hidden" name="MAX_FILE_SIZE" value="204000" />
		<table cellspacing="1" cellpadding="0" width="90%" border="0" class="bodyline" summary="" align="center">
		<tr>
			<th colspan="2"class="left"><img src="images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php echo _AT('edit_content');  ?>
			<?php
			 echo '<input type="hidden" name="revision" value="'.ContentManager::cleanOutput($row['revision']).'" />';
			 echo '<input type="hidden" name="last_modified" value="'.$row['last_modified'].'" />';
			if($_POST['revision'] && $_POST['last_modified']){
				echo '<small class="spacer"> ( '._AT('last_modified').':'.$_POST['last_modified'].'.'. _AT('revision').':'.$_POST['revision'].'. )</small>';

			}else{
				echo '<small class="spacer"> ( '._AT('last_modified').':'.$row['last_modified'].'.'. _AT('revision').':'.ContentManager::cleanOutput($row['revision']).'. )</small>';

			}
			?>
			</th>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="right" class="row1" valign="top"><?php
				$errors[]=AT_ERROR_BAD_DATE;
				//$def = 'text<b>as</b>';   //whats this? untranslated
				print_popup_help(AT_HELP_PASTE_FILE1);
				?>
			<b><?php echo _AT('paste_file'); ?>:</b></td>
			<td class="row1" valign="top">
			<input type="file" name="uploadedfile" class="formfield" size="20" /> <input type="submit" name="submit_file" value=" <?php echo _AT('upload'); ?>" class="button" /><?php
			?><br />
			<small class="spacer"><?php echo _AT('html_only') ?><br />
			<?php echo _AT('edit_after_upload'); ?></small>

			</td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="center" class="row1" colspan="2"><b><?php echo _AT('or');?></b></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<?php
		if($_POST['title']){
		?>
		<tr>
			<td align="right" class="row1"><b><label for="title"><?php echo _AT('title');  ?>:</label></b></td>
			<td class="row1"><input type="text" name="title" size="40" class="formfield" value="<?php echo ContentManager::cleanOutput($_POST['title']); ?>" id="title" /></td>
		</tr>
		<?php }else{ ?>
		
		<tr>
			<td align="right" class="row1"><b><label for="title"><?php echo _AT('title');  ?>:</label></b></td>
			<td class="row1"><input type="text" name="title" size="40" id="title" class="formfield" value="<?php echo ContentManager::cleanOutput($row['title']); ?>" /></td>
		</tr>
		
		<?php } ?>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<?php if ($_POST['day']) { ?>
		<tr>
			<td align="right" class="row1"><?php print_popup_help(AT_HELP_NOT_RELEASED); ?><b><?php echo _AT('release_date');  ?></b></td>
			<td class="row1"><?php

				$today_day   = $day;
				$today_mon   = $month;
				$today_year  = $year;

				$today_hour  = $hour;
				$today_min   = $min;
				require(AT_INCLUDE_PATH.'lib/release_date.inc.php');
		?>
	</td>
	</tr>
	<?php } else { ?>
	<tr>
	<td align="right" class="row1"><?php print_popup_help(AT_HELP_NOT_RELEASED); ?><b><?php echo _AT('release_date');  ?>:</b></td>
	<td class="row1"><?php

			$today_day   = substr($row['release_date'], 8, 2);
			$today_mon   = substr($row['release_date'], 5, 2);
			$today_year  = substr($row['release_date'], 0, 4);

			$today_hour  = substr($row['release_date'], 11, 2);
			$today_min   = substr($row['release_date'], 14, 2);
			require(AT_INCLUDE_PATH.'lib/release_date.inc.php');

			?>
	</td>
	</tr>
	
	<?php } ?>


		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<?php
			if ($row['content_path']) {
				echo '<tr>';
				echo '<td colspan="2" class="row1"><b>'._AT('packaged_in').': '.$row['content_path'].'</b></td>';
				echo '</tr>';
				echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
			}
		?>
		<tr>
			<td colspan="2" valign="top" align="left" class="row1"><?php print_popup_help(AT_HELP_BODY); ?><b><label for="body"><?php echo _AT('body');  ?>:</label></b><br />

			<?php if (isset($_POST['text'])) { ?>
				<p><textarea name="text" class="formfield" cols="73" rows="20" id="body"><?php echo ContentManager::cleanOutput($_POST['text']); ?></textarea></p>
				<br />
			<?php } else {  ?>
				<p><textarea name="text" class="formfield" cols="73" rows="20" id="body"><?php echo ContentManager::cleanOutput($row['text']); ?></textarea></p>
				<br />
			<?php } ?>
				</td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td colspan="2" valign="top" align="left" class="row1">
			<?php print_popup_help(AT_HELP_KEYWORDS); ?>
			<b><label for="keywords"><?php echo _AT('keywords'); ?>:</label></b><br />
			<?php if ($_POST['keywords']) { ?>
				<p><textarea name="keywords" class="formfield" cols="73" rows="2" id="keywords"><?php echo ContentManager::cleanOutput($_POST['keywords']); ?></textarea></p>
				<br />
			<?php } else {  ?>
				<p><textarea name="keywords" class="formfield" cols="73" rows="2" id="keywords"><?php echo ContentManager::cleanOutput($row['keywords']); ?></textarea></p>
				<br />
			<?php } ?>
			</td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="right" class="row1">	
			<?php print_popup_help(AT_HELP_FORMATTING); ?>
			<b><?php echo _AT('formatting'); ?>:</b></td>
			<td class="row1"><input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] === 0) { echo 'checked="checked"'; } ?> /><label for="text"><?php echo _AT('plain_text'); ?></label>, <input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] !== 0) { echo 'checked="checked"'; } ?> /><label for="html"><?php echo _AT('html'); ?></label> <?php

			?></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" colspan="2"><a href="<?php echo substr($_my_uri, 0, strlen($_my_uri)-1); ?>#jumpcodes" title="<?php echo _AT('jump_codes') ?>"><?php print_popup_help(AT_HELP_ADD_CODES); ?><img src="images/clr.gif" height="1" width="1" alt="<?php echo _AT('jump_codes') ?>" border="0" /></a><?php require(AT_INCLUDE_PATH.'html/code_picker.inc.php'); ?></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="right" class="row1"><a name="jumpcodes"></a><?php print_popup_help(AT_HELP_INSERT); ?><b><label for="move"><?php echo _AT('move_to'); ?>:</label></b></td>
			<td class="row1"><select name="new_ordering" class="formfield" id="move">
				<option value="-1"></option><?php

			if ($row['ordering'] != count($top_level)) {
				echo '<option value="'.count($top_level).'">'._AT('end_section').'</option>';
			}
			if ($row['ordering'] != 1) {
				echo '<option value="1">'._AT('start_section').'</option>';
			}

			foreach ($top_level as $x => $info) {
				if (($info['ordering'] != $row['ordering']-1) 
					&& ($info['ordering'] != $row['ordering']))
				{
					echo '<option value="';
					
					if ($info['ordering'] == count($top_level)) {
						/* special case, last item */
						echo $info['ordering'];
					} else {
						echo $info['ordering']+1;
					}

					echo '">'._AT('after').': '.$info['ordering'].' "'.$info['title'].'"</option>';
				} else {
					echo '<option value="-1">'._AT('no_change').': '.$info['ordering'].' "'.$info['title'].'"</option>';
				}
			}
		?></select><?php

			$temp_menu = $contentManager->getContent();
			echo _AT('or').' <select name="move">';
			echo '<option value="-1"></option>';
			echo '<option value="0">'._AT('top').'</option>';
			print_move_select(0, $temp_menu, $row['content_parent_id']);
			echo '</select>';

		?></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td align="right" class="row1"><?php print_popup_help(AT_HELP_RELATED); ?><b><?php echo _AT('related_to');  ?>:</b></td>
		<td class="row1"><?php
		reset($temp_menu);

		if ($contentManager->getNumSections() > 1) {
			/* get existing related content */
			if ($_POST['submit'] != '') {
				$related_content = $_POST['related'];
			} else {
				$related_content = $contentManager->getRelatedContent($cid);
			}

			echo '<select class="formfield" name="related[]">';
			echo '<option value="0"></option>';

			print_select_menu(0, $temp_menu, $related_content[0]);

			echo '</select></td></tr>';
			

			for ($i=1; $i<max( min(4, $contentManager->getNumSections()-1 ), count($related_content) ); $i++) {
				echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
				echo '<tr><td align="right" class="row1">&nbsp;</td>';
				echo '<td class="row1"><select class="formfield" name="related[]">
							<option value="0"></option>';
				
				print_select_menu(0, $temp_menu, $related_content[$i]);

				echo '</select></td></tr>';
			}
		} else {
			echo _AT('none_available').'</td></tr>';
		}
?>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td colspan="2" align="center" class="row1"><br /><input type="submit" name="submit" value="<?php echo _AT('save_content') ?>" class="button" accesskey="s" />  - <input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" /></td>
		</tr>
		</table>
	</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>
