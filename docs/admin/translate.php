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

$_REQUEST['u'] = 'admin';

$variables = array('_template', '_msgs');
// Get the language codes for the languages on the current system


define('AT_INCLUDE_PATH', '../include/');

	$_SECTION[0][0] = _AT('home');
	$_SECTION[0][1] = 'index.php';
	$_SECTION[1][0] = 'ATutor';
	$_SECTION[1][1] = '/atutor/index.php';
	$_SECTION[2][0] = _AT('translation');

?>
	<p><?php echo _AT('langs_on_this_system'); ?> </p>
		<ul>
		<?php
		foreach($available_languages as $key => $thislang){
			echo '<li>';
			echo $thislang[3].' - '.$thislang[1];
			if ($key != 'en'){
				echo ' <small>(<a href="admin/delete_lang.php?delete_lang='.$key.'">'._AT('remove').'</a>)</small>';
			}
			echo '</li>';
		}
		?>
		</ul>

<hr />
<script language="javascript" type="text/javascript">
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
</script>

<h3><?php echo _AT('import_a_new_lang'); ?></h3>
<form name="form1" method="post" action="admin/import_lang.php" enctype="multipart/form-data" onsubmit="openWindow('<?php echo $_base_href; ?>tools/prog.php');">
<input type="hidden" name="import" value="1" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="">
<tr>
	<td class="row1" colspan="2"><?php echo _AT('import_lang_howto'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2"><strong><?php echo _AT('import_a_new_lang'); ?></strong>: <input type="file" name="file" class="formfield"/> - <input type="submit" name="submit" value="<?php echo _AT('import'); ?>" class="button" /><br /><br /></td>
</tr>
</table>
</form>

<?php

	if (!$_REQUEST['f']) {
		$_REQUEST['f']	= 'en';
	}

	if ($_REQUEST['u'] != 'admin') {
		$_REQUEST['t'] = $_REQUEST['u'];
	} else if (!isset($_REQUEST['f'])){
		$_REQUEST['t'] = 'en';
	}

	if ($_REQUEST['t'] == 'en') {
		$_REQUEST['f'] = 'en';
	}
	if (!$_REQUEST['t'] && ($_REQUEST['u'] == 'admin')) {
		$_REQUEST['t']	= 'en';
	}

?>
<hr />
<h3><?php echo _AT('modify_existing_lang'); ?></h3>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="">
<tr>
	<td class="row1" colspan="2"><?php echo _AT('modify_lang_howto');  ?>
	</td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2">
	<input type="hidden" name="u" value="<?php echo $_REQUEST['u']; ?>" />
	<input type="hidden" name="p" value="<?php echo $_REQUEST['p']; ?>" />

<?php
		echo '<strong>'._AT('set_lang_to_modify').'</strong>: <select name="t" class="formfield">';
		foreach($available_languages as $key => $thislang){
			echo '<option value="'.$key.'"';
			if ($key == $_REQUEST['t']) {
				echo ' selected="selected"';
			}
			echo '>'.$thislang[3].' - '.$thislang[1].'</option>';
		}

		echo '</select>';
?>
&nbsp;<input type="submit" name="submit" value="Set" class="button" />
<br /><br /></td></tr></table>
</form>
<br />
<?php

	if ($_POST['function'] == 'edit_term') {
		if ($_POST['submit2']) {
			$sql	= "DELETE FROM `".TABLE_PREFIX."lang_base` WHERE variable='$_REQUEST[v]' AND `key`='$_REQUEST[k]'";
			$result = mysql_query($sql, $db);
			if (!$result) {
				echo mysql_error();
				echo '<div class="error">Error: Not deleted!</div>';
			} else {
				$sql	= "DELETE FROM `".TABLE_PREFIX."lang2` WHERE variable='$_REQUEST[v]' AND `key`='$_REQUEST[k]'";
				$result = mysql_query($sql, $db);

				unset($_REQUEST['k']);
				echo '<div class="good">Success: deleted.</div>';
			}
		} else {
			$_POST['text'] = trim($_POST['text']);

			if ($_REQUEST['t'] == 'en') {
				$sql	= "UPDATE `".TABLE_PREFIX."lang_base` SET `text`='$_POST[text]', revised_date=NOW(), context='$_POST[context]' WHERE variable='$_POST[v]' AND `key`='$_POST[k]'";
			} else {
				$sql	= "REPLACE INTO `".TABLE_PREFIX."lang2` VALUES ('$_REQUEST[t]', '$_POST[v]', '$_POST[k]', '$_POST[text]', NOW())";

				$trans = get_html_translation_table(HTML_ENTITIES);
				$trans = array_flip($trans);
				$sql = strtr($sql, $trans);
			}

			$result = mysql_query($sql, $db);
			if (!$result) {
				echo mysql_error();
				echo '<div class="error">Error: changes not saved!</div>';
				$success_error = '<div class="error">Error: changes not saved!</div>';
				
			} else {
				echo '<div class="good">Success: changes saved.</div>';
				$success_error = '<div class="good">Success: changes saved.</div>';

			}
		}
	}


	echo '<ul>';
	foreach ($variables as $row) {
		echo '<li>';
		if ($_REQUEST['v'] == $row) {
			echo '<strong>';
		}
		echo '<a href="'.$_SERVER['PHP_SELF'].'?v='.$row.SEP.'f='.$_REQUEST['f'].SEP.'t='.$_REQUEST['t'].SEP.'u='.$_REQUEST['u'].SEP.'p='.$_REQUEST['p'].'">';
		echo ucwords(str_replace('_', '', $row));
		echo '</a>';
		if ($_REQUEST['v'] == $row) {
			echo '</strong>';
		}
		echo '</li>';
	}
	echo '</ul>';

	echo '<hr />';

	if ($_REQUEST['v'] && $_REQUEST['k']) {
		if ($_REQUEST['f'] == 'en') {
			$sql	= "SELECT * FROM `".TABLE_PREFIX."lang_base` WHERE `key`='$_REQUEST[k]' AND variable='$_REQUEST[v]' ORDER BY `key`";
		} else {
			$sql	= "SELECT * FROM `".TABLE_PREFIX."lang2` WHERE `key`='$_REQUEST[k]' AND variable='$_REQUEST[v]' AND `lang`='$_REQUEST[f]'";
		}
		$result	= mysql_query($sql, $db);
		$row	= mysql_fetch_array($result);
		if ($_REQUEST['t'] == 'en') {
			$row2 = $row;
		} else {
			$sql	= "SELECT text FROM `".TABLE_PREFIX."lang2` WHERE `key`='$_REQUEST[k]' AND variable='$_REQUEST[v]' AND `lang`='$_REQUEST[t]'";
		}

		$result	= mysql_query($sql, $db);
		$row2	= mysql_fetch_array($result);


function trans_form(){
	global $row;
	global $row2;
	global $langs;
	global $success_error;
	global $available_languages;
	global $db;

	?><a name="anchor"></a>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>#anchor">
	<input type="hidden" name="v" value="<?php echo $row['variable']; ?>" />
	<input type="hidden" name="k" value="<?php echo $row['key']; ?>" />
	<input type="hidden" name="f" value="<?php echo $_REQUEST['f']; ?>" />
	<input type="hidden" name="t" value="<?php echo $_REQUEST['t']; ?>" />
	<input type="hidden" name="u" value="<?php echo $_REQUEST['u']; ?>" />
	<input type="hidden" name="p" value="<?php echo $_REQUEST['p']; ?>" />
	<input type="hidden" name="function" value="edit_term" />

	<table border="0" cellspacing="0" cellpadding="2" width="90%" align="center" class="box">
	<tr>
		<th class="box" colspan="2">Edit </th>
	</tr>
	<tr>
		<td align="right"><b>Variable:</b></td>
		<td><tt><?php echo $row['variable'];?></tt></td>
	</tr>
	<tr>
		<td align="right"><b>Key:</b></td>
		<td><tt><?php echo $row['key'];?></tt></td>
	</tr>
	<tr>
		<td align="right"><b>Context:</b></td>
		<td><?php
			if ($_REQUEST['t'] == 'en') {
				echo '<input type="text" name="context" class="formfield" value="'.$row['context'].'" size="45" />';
			} else {
				if ($row['context'] == '') {
					echo '<em>None specified.</em>';
				} else {
					echo $row['context'];
				}
			} ?>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top" align="right" nowrap="nowrap"><b>Pages:</b></td>
		<td><tt><?php
					$sql	= "SELECT * FROM ".TABLE_PREFIX."lang_base_pages WHERE `key`='$_REQUEST[k]' AND variable='template' ORDER BY page LIMIT 11";
					$result	= mysql_query($sql, $db);
					if (mysql_num_rows($result) > 10) {
						echo '<em>Global (more than 10 pages)</em>';
					} else {
						while ($page_row = mysql_fetch_array($result)) {
							echo $page_row['page'] . '<br />';
						}

					}
				 ?></tt></td>
	</tr>
	<tr>
		<td valign="top" align="right" nowrap="nowrap"><b><tt><?php echo $available_languages[$_REQUEST['f']][3];?></tt> text:</b></td>
		<td><tt><?php echo str_replace('<', '&lt;', $row['text']); ?></tt></td>
	</tr>
	<tr>
		<td valign="top" align="right" nowrap="nowrap"><b><tt><?php echo $available_languages[$_REQUEST['t']][3];?></tt> text:</b></td>
		<td><textarea cols="55" rows="8" name="text" class="formfield"><?php echo $row2['text'];?></textarea></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="submit" value="Save ALT-S" class="button" accesskey="s" /></td>
	</tr>
	</table>
	</form>

	<?php
	echo $success_error;
	}
} //end of edit form function
	if ($_REQUEST['v']) {
		if ($_REQUEST['t'] != 'en') {
			$sql	= "SELECT `key`, revised_date+0  AS r_date FROM `".TABLE_PREFIX."lang2` WHERE variable='$_REQUEST[v]' AND `lang`='$_REQUEST[t]' ORDER BY `key`";
			$result = mysql_query($sql, $db);
			$t_keys = array();
			while ($row = mysql_fetch_array($result)) {
				$t_keys[$row['key']] = $row['r_date'];

			}
		}


		if ($_REQUEST['f'] == 'en') {
			$sql	= "SELECT *, revised_date+0 AS r_date FROM `".TABLE_PREFIX."lang_base` WHERE variable='$_REQUEST[v]' ORDER BY `key`";
		} else {
			$sql	= "SELECT * FROM `".TABLE_PREFIX."lang2` WHERE variable='$_REQUEST[v]' AND `lang`='$_REQUEST[f]' ORDER BY `key`";
		}
		$result	= mysql_query($sql, $db);
		echo '<ul>';
		while ($row = mysql_fetch_array($result)) {
			if ($row['key'] == $_REQUEST['k']) {
				echo '<li class="selected">';
				trans_form($result);

			} else {
				echo '<li>';
			}
			echo '<small>';
			if ($row['key'] != $_REQUEST['k']) {
				echo '<a href="'.$_SERVER['PHP_SELF'].'?v='.$row['variable'].SEP.'k='.$row['key'].SEP.'f='.$_REQUEST['f'].SEP.'t='.$_REQUEST['t'].SEP.'u='.$_REQUEST['u'].SEP.'p='.$_REQUEST['p'].'#anchor">';
				echo $row['key'];
				echo '</a>';
			} else {
				echo '<span class="current_tran">';
				echo $row['key'];
				echo ' </span>';
			}
			echo '</small>';
			echo '</li>';
		}
		echo '</ul>';
	}


