<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }


function get_tabs() {
	global $_config;

	//these are the _AT(x) variable names and their include file
	/* tabs[tab_id] = array(tab_name, file_name, accesskey) */
	$tabs[0] = array('atutor_settings', 'atutor_settings.inc.php', 'n');
	$tabs[1] = array('display_settings', 'display_settings.inc.php', 'p');
	
	if(!$_config['just_social']){
		$tabs[2] = array('content_settings', 'content_settings.inc.php', 'g');
		$tabs[3] = array('tool_settings', 'tool_settings.inc.php', 'r');
		$tabs[4] = array('control_settings', 'control_settings.inc.php', 'a');	
	}
	return $tabs;
}

function output_tabs($current_tab, $changes) {
	global $_base_path;
	$tabs = get_tabs();
	$num_tabs = count($tabs);
?>
	<table class="etabbed-table" border="0" cellpadding="0" cellspacing="0">
	<tr>		
		<?php 
		for ($i=0; $i < $num_tabs; $i++): 
			if ($current_tab == $i):?>
				<td class="selected" style="white-space: nowrap">
					<?php if ($changes[$i]): ?>
						<img src="<?php echo $_base_path; ?>images/changes_bullet.gif" alt="<?php echo _AT('usaved_changes_made'); ?>" height="12" width="15" />
					<?php echo '<input type="submit" name="button_'.$i.'" value="'._AT($tabs[$i][0]).'" title="'._AT($tabs[$i][0]).' - alt '.$tabs[$i][2].'" class="buttontab" accesskey="'.$tabs[$i][2].'" onmouseover="this.style.cursor=\'pointer\';" '.$clickEvent.' />'; ?>
					<?php endif; ?>
					<?php echo _AT($tabs[$i][0]); ?>
				</td>
				<td class="tab-spacer">&nbsp;</td>
			<?php else: ?>
				<td class="tab" style="white-space: nowrap">
					<?php if ($changes[$i]): ?>
						<img src="<?php echo $_base_path; ?>images/changes_bullet.gif" alt="<?php echo _AT('usaved_changes_made'); ?>" height="12" width="15" />
					<?php endif; ?>

					<?php echo '<input type="submit" name="button_'.$i.'" value="'._AT($tabs[$i][0]).'" title="'._AT($tabs[$i][0]).' - alt '.$tabs[$i][2].'" class="buttontab" accesskey="'.$tabs[$i][2].'" onmouseover="this.style.cursor=\'pointer\';" '.$clickEvent.' />'; ?>
				</td>
				<td class="tab-spacer">&nbsp;</td>
			<?php endif; ?>
		<?php endfor; ?>
		<td >&nbsp;</td>
	</tr>
	</table>
<?php }

// returns given $languges in html <option> tag
function output_language_options($languages, $selected_lang)
{
	foreach ($languages as $codes)
	{
		$language = current($codes);
		
		$lang_code = $language->getCode();
		$lang_native_name = $language->getNativeName();
		$lang_english_name = $language->getEnglishName()
?>
		<option value="<?php echo $lang_code ?>" <?php if ($selected_lang == $lang_code) echo 'selected="selected"'; ?>><?php echo $lang_english_name . ' - '. $lang_native_name; ?></option>
<?php
	}
}

?>