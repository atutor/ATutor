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
if (!defined('AT_INCLUDE_PATH')) { exit; }

	/************************************/
	/* presets							*/
	echo '<h3>'._AT('preset_preferences').'</h3>';
	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'theme_settings ORDER BY name';
	$result	= mysql_query($sql, $db);

	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<table border="0" class="bodyline" cellspacing="1" cellpadding="0" align="center">
	<tr>
		<th colspan="2"><?php print_popup_help(AT_HELP_PRESET); echo _AT('preset_preferences')?></th>
	</tr>
	<tr>
		<td class="row1"><label for="preset"><?php echo _AT('select_preset');  ?>:</label></td>
		<td class="row1"><select name="pref_id" id="preset">
				<?php
					if ($row = mysql_fetch_array($result)) {
						do {
							echo '<option  value="'.$row['theme_id'].'">'._AT($row['name']).'</option>';
						} while ($row = mysql_fetch_array($result));
					}
	
					/* check if this course has custom preferences*/
					$sql	= "SELECT preferences FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
					$resultab	= mysql_query($sql, $db);
					$row	= mysql_fetch_array($resultab);
					if ($row['preferences']) {
						echo '<option value="0">'._AT('course_defaults').'</option>';
					}
		?></select>&nbsp;<input type="submit" name="submit" value="<?php echo _AT('set_preset'); ?>" class="button" /></td>
	</tr>
	</table>
	</form>

<br />
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="prefs">
<?php echo '<h3>'._AT('personal_preferences').'</h3>'; ?>
<table cellspacing="5" width="100%" cellpadding="0" summary="" border="0">
<tr>
	<td valign="top"><table border="0" width="100%" class="bodyline" cellspacing="1" cellpadding="0">
	<tr>
		<th colspan="2"><?php print_popup_help(AT_HELP_POSITION_OPTIONS); echo _AT('pos_options')?></th>
	</tr>
	<tr>
		<td class="row1"><label for="pos"><?php echo _AT('menu');  ?>:</label></td>
		<td class="row1"><?php
							if ($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] == MENU_LEFT) {
								$left = ' selected="selected"';
							} else {
								$right = ' selected="selected"';
							}
		?><select name="pos" id="pos">
			<option value="1" <?php echo $left;?>><?php echo _AT('left'); ?></option>
			<option value="2" <?php echo $right;?>><?php echo _AT('right'); ?></option>
		  </select><br /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><label for="seq"><?php echo _AT('seq_links');  ?>:</label></td>
		<td class="row1"><?php
		/* sequence links preference */
		if ($_SESSION['prefs'][PREF_SEQ] == TOP) {
			$top = ' selected="selected"';
		} else if ($_SESSION['prefs'][PREF_SEQ] == BOTTOM) {
			$bottom = ' selected="selected"';
		} else {
			$both = ' selected="selected"';
		}
		?><select name="seq" id="seq">
			<option value="<?php echo TOP; ?>"<?php echo $top; ?>><?php echo _AT('top');  ?></option>
			<option value="<?php echo BOTTOM; ?>"<?php echo $bottom; ?> ><?php echo _AT('bottom');  ?></option>
			<option value="<?php echo BOTH; ?>"<?php echo $both; ?>><?php echo _AT('top_bottom');  ?></option>
	  	  </select><br /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><label for="toc"><?php echo _AT('table_of_contents');  ?>:</label></td>
		<td class="row1"><?php
		// table of contents preference
		$top = $bottom = '';
		if ($_SESSION['prefs'][PREF_TOC] == TOP) {
			$top	= ' selected="selected"';
		} else if ($_SESSION['prefs'][PREF_TOC] == BOTTOM) {
			$bottom = ' selected="selected"';
		} else {
			$neither = ' selected="selected"';
		}
		?><select name="toc" id="toc">
			<option value="<?php echo TOP; ?>"<?php echo $top; ?>><?php echo _AT('top');  ?></option>
			<option value="<?php echo BOTTOM; ?>"<?php echo $bottom; ?>><?php echo _AT('bottom');  ?></option>
			<option value="<?php echo NEITHER; ?>"<?php echo $neither; ?>><?php echo _AT('neither');  ?></option>
		  </select></td>
	</tr>
	</table></td>

	<td valign="top" align="left"><table border="0" width="100%"  class="bodyline" cellspacing="1" cellpadding="0">
	<tr>
		<th colspan="2"><?php print_popup_help(AT_HELP_DISPLAY_OPTIONS); ?><?php echo _AT('disp_options');  ?></th>
	</tr>
	<tr>
		<td class="row1"><?php
		/* Show Topic Numbering Preference */
		if ($_SESSION['prefs'][PREF_NUMBERING] == 1) {
			$num = ' checked="checked"';
		}
		?> <input type="checkbox" name="numering" value="1" <?php echo $num;?> id="numbering" /></td>
		<td class="row1"><label for="numbering"><?php echo _AT('show_numbers');  ?></label></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php
			/* Show Breadcrumbs Preference */
			$num = '';
			if ($_SESSION['prefs'][PREF_BREADCRUMBS] == 1) {
				$num = ' checked="checked"';
			}
			?><input type="checkbox" name="breadcrumbs" value="1" <?php echo $num;?> id="breadcrumbs" /></td>
		<td class="row1"><label for="breadcrumbs"><?php echo _AT('show_breadcrumbs');  ?></label></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php
			$num = '';
			if ($_SESSION['prefs'][PREF_HEADINGS] == 1) {
				$num = ' checked="checked"';
			}
			?> <input type="checkbox" name="headings" value="1" <?php echo $num;?> id="heading" /></td>
		<td class="row1"><label for="heading"><?php echo _AT('show_headings');  ?></label></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php
			$num = '';
			if ($_SESSION['prefs'][PREF_HELP] == 1) {
				$num = ' checked="checked"';
			}
			?><input type="checkbox" name ="use_help" id="use_help" value="1" <?php echo $num; ?> /></td>
		<td class="row1"><label for="use_help"><?php echo _AT('show_help');  ?></label><br /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php
			$num = '';
			if ($_SESSION['prefs'][PREF_MINI_HELP] == 1) {
				$num = ' checked="checked"';
			}
			?><input type="checkbox" name ="use_mini_help" id="use_mini_help" value="1" <?php echo $num; ?> /></td>
		<td class="row1"><label for="use_mini_help"><?php echo _AT('show_mini_help');  ?></label><br /></td>
	</tr>
	</table></td>
</tr>
<tr>
	<td valign="top"><table border="0" width="100%" class="bodyline" cellspacing="1" cellpadding="0">
	<tr>
		<th colspan="2"><?php print_popup_help(AT_HELP_TEXTICON_OPTIONS); ?><?php echo _AT('text_and_icons');  ?></th>
	</tr>
	<tr>
		<td class="row1"><label for="nav_icons"><?php echo _AT('main_nav');  ?>:</label></td>
		<td class="row1"><?php

					$both	= '';
					$text	= '';
					$icons	= '';

					if ($_SESSION['prefs'][PREF_NAV_ICONS] == 1) {
						$icons = ' checked="checked"';
					} else if ($_SESSION['prefs'][PREF_NAV_ICONS] == 2) {
						$text = ' selected="selected"';
					} else {
						$both = ' selected="selected"';
					}
			?><select name="nav_icons" id="nav_icons">
				<option value="1" <?php echo $icons; ?>><?php echo _AT('icons_only');  ?></option>
				<option value="2" <?php echo $text; ?>><?php echo _AT('text_only');  ?></option>
				<option value="0" <?php echo $both; ?>><?php echo _AT('icons_and_text');  ?></option>
			</select></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><label for="login_icons"><?php echo _AT('login_nav');  ?>:</label></td>
		<td class="row1"><?php

				$both = '';
				$text = '';
				$icons = '';

				if ($_SESSION['prefs'][PREF_LOGIN_ICONS] == 1) {
					$icons = ' selected="selected"';
				} else if ($_SESSION['prefs'][PREF_LOGIN_ICONS] == 2) {
					$text = ' selected="selected"';
				} else {
					$both = ' selected="selected"';
				}
			?><select name="login_icons" id="login_icons">
				<option value="1" <?php echo $icons; ?>><?php echo _AT('icons_only');  ?></option>
				<option value="2" <?php echo $text; ?>><?php echo _AT('text_only');  ?></option>
				<option value="0" <?php echo $both; ?>><?php echo _AT('icons_and_text');  ?></option>
			</select></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><label for="seq_icons"><?php echo _AT('seq_nav');  ?>:</label></td>
		<td class="row1"><?php

					$both = '';
					$text = '';
					$icons = '';

					if ($_SESSION['prefs'][PREF_SEQ_ICONS] == 1) {
						$icons = ' selected="selected"';
					} else if ($_SESSION['prefs'][PREF_SEQ_ICONS] == 2) {
						$text = ' selected="selected"';
					} else {
						$both = ' selected="selected"';
					}
				?><select name="seq_icons" id="seq_icons">
					<option value="1" <?php echo $icons; ?>><?php echo _AT('icons_only');  ?></option>
				<option value="2" <?php echo $text; ?>><?php echo _AT('text_only');  ?></option>
				<option value="0" <?php echo $both; ?>><?php echo _AT('icons_and_text');  ?></option>
			</select></td>
	</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><label for="content_icons"><?php echo _AT('content_icons'); ?><?php //echo _AT('login_nav'];  ?>:</label></td>
		<td class="row1"><?php

				$both = '';
				$text = '';
				$icons = '';

				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] == 1) {
					$icons = ' selected="selected"';
				} else if ($_SESSION['prefs'][PREF_CONTENT_ICONS] == 2) {
					$text = ' selected="selected"';
				} else {
					$both = ' selected="selected"';
				}
			?><select name="content_icons" id="content_icons">
				<!--option value="1" <?php echo $icons; ?>><?php echo _AT('icons_only');  ?></option-->
				<option value="2" <?php echo $text; ?>><?php echo _AT('text_only');  ?></option>
				<option value="0" <?php echo $both; ?>><?php echo _AT('icons_and_text');  ?></option>
			</select></td>
	</tr>
	</table></td>
	<td valign="top" width="50%"><table border="0"  width="100%" class="bodyline" cellspacing="1" cellpadding="0">
		<tr>
			<th colspan="2"><?php print_popup_help(AT_HELP_MENU_OPTIONS); ?><?php  echo _AT('menus'); ?></th>
		</tr>
		<tr>
			<td class="row1" align="center"><?php

			$num_stack = count($_stacks);

			for ($i = 0; $i< 6; $i++) {
				echo '<select name="stack'.$i.'">';
				echo '<option value="">'._AT('empty').'</option>';
				for ($j = 0; $j<$num_stack; $j++) {
					echo '<option value="'.$j.'"';
					if (isset($_SESSION['prefs'][PREF_STACK][$i]) && ($j == $_SESSION[prefs][PREF_STACK][$i])) {
						echo ' selected="selected"';
					}
					echo '>'._AT($_stacks[$j]).'</option>';
				}
				echo '</select>';
				echo '<br />'; 
			}

		?></td>
		</tr>
		</table></td>
</tr>
<tr>
	<td colspan="2"><table border="0" width="100%" class="bodyline" cellspacing="1" cellpadding="0">
	<tr>
		<th colspan="2"><?php print_popup_help(AT_HELP_THEME_OPTIONS); ?><?php echo _AT('themes');  ?></th>
	</tr><?php
		/* decide whether or not PREF_OVERRIDE is active	*/
		/* used to disable font and colour themes			*/
			if ($_SESSION['prefs'][PREF_OVERRIDE]) {
				$overy = ' checked="checked"';
				$disabled = ' disabled="disabled" title="'._AT('disabled_theme').'"';
			} else {
				$overn = ' checked="checked"';
			}

		?>
	<tr>
		<td class="row1"><label for="font"><?php echo _AT('font_theme');  ?>:</label></td>
		<td class="row1"><select name="font" id="font" <?php echo $disabled; ?>>
				<?php
				foreach ($_fonts as $font_id => $font_info) {
					echo '<option';
						if ($_SESSION['prefs'][PREF_FONT] == $font_id) {
							echo ' selected="selected"';
						}
					echo ' value="'.$font_id.'">'.$font_info['NAME'].'</option>';
				}
				?></select></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><label for="stylesheet"><?php echo _AT('color_theme'); ?>:</label></td>
		<td class="row1"><select name="stylesheet" id="stylesheet" <?php echo $disabled; ?>>
			<?php
			foreach ($_colours as $colour_id => $colour_info) {
				echo '<option';
					if ($_SESSION['prefs'][PREF_STYLESHEET] == $colour_id) {
						echo ' selected="selected"';
					}
				echo ' value="'.$colour_id.'">'.$colour_info['NAME'].'</option>';
			}
			?>
		</select></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1"><?php  echo _AT('override'); ?></td>
		<td class="row1"><input type="radio" name="override" value="1" id="overy" <?php echo $overy; ?> onclick="disableThemes();" /><label for="overy"><?php echo _AT('yes'); ?></label> <input type="radio" name="override" value="0" id="overn" <?php echo $overn; ?> onclick="enableThemes();" /><label for="overn"><?php  echo _AT('no'); ?></label></td>
	</tr>
	</table></td>
</tr>
<tr>
	<td colspan="2" align="center"><br />
	<input type="submit" name="submit" value="<?php echo _AT('set_prefs'); ?>" title="<?php echo _AT('set_prefs'); ?>" accesskey="s" class="button" /></td>
</tr>
</table>

</form>
<script language="JavaScript" type="text/javascript">
<!--
function enableThemes()
{
	document.prefs.font.disabled	= false;
	document.prefs.stylesheet.disabled = false;
}

function disableThemes()
{
	document.prefs.font.disabled = true;
	document.prefs.stylesheet.disabled = true;
}

// -->
</script>