<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
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
<div class="etabbed-list-container">
	<ul class="etabbed-list" >
	
		
		<?php for ($i=0; $i < $num_tabs; $i++): 
			if ($current_tab == $i):?>
				<li class="prefs_tab_selected">
					<?php if ($changes[$i]): ?>
						<img src="<?php echo $_base_path; ?>images/changes_bullet.gif" alt="<?php echo _AT('usaved_changes_made'); ?>" height="12" width="15" />
					<?php echo '<input type="submit" name="button_'.$i.'" value="'._AT($tabs[$i][0]).'" title="'._AT($tabs[$i][0]).' - alt '.$tabs[$i][2].'" class="prefs_buttontab" accesskey="'.$tabs[$i][2].'" onmouseover="this.style.cursor=\'pointer\';" '.$clickEvent.' />'; ?>
					<?php endif; ?>
					<?php echo _AT($tabs[$i][0]); ?>
				</li>
			
			<?php else: ?>
				<li class="prefs_tab" >
					<?php if ($changes[$i]): ?>
						<img src="<?php echo $_base_path; ?>images/changes_bullet.gif" alt="<?php echo _AT('usaved_changes_made'); ?>" height="12" width="15" />
					<?php endif; ?>

					<?php echo '<input type="submit" name="button_'.$i.'" value="'._AT($tabs[$i][0]).'" title="'._AT($tabs[$i][0]).' - alt '.$tabs[$i][2].'" class="prefs_buttontab" accesskey="'.$tabs[$i][2].'" onmouseover="this.style.cursor=\'pointer\';" '.$clickEvent.' />'; ?>
				</li>
				
			<?php endif; ?>
		<?php endfor; ?>
		
	
	</ul>
</div>	
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
/**
 * Assigns posted preferences to a $temp_prefs array
 * 
 * @return array of preferences
 */
function assignPostVars() {
global $addslashes;
	
	$temp_prefs = array();
    foreach($_SESSION['prefs'] as $pref_name => $value) {
        $temp_prefs[$pref_name] = $value;
    }
		
	/* custom prefs */
	// atutor settings (tab 0)
	if (isset($_POST['numbering'])) $temp_prefs['PREF_NUMBERING'] = intval($_POST['numbering']);	
	if (isset($_POST['theme'])) $temp_prefs['PREF_THEME'] = $addslashes($_POST['theme']);
	if (isset($_POST['mobile_theme'])) $temp_prefs['PREF_MOBILE_THEME'] = $addslashes($_POST['mobile_theme']);
	if (isset($_POST['time_zone'])) $temp_prefs['PREF_TIMEZONE'] = $addslashes($_POST['time_zone']);
	if (isset($_POST['use_jump_direct'])) $temp_prefs['PREF_JUMP_REDIRECT'] = intval($_POST['use_jump_redirect']);
	if (isset($_POST['form_focus'])) $temp_prefs['PREF_FORM_FOCUS'] = intval($_POST['form_focus']);
	if (isset($_POST['content_editor'])) $temp_prefs['PREF_CONTENT_EDITOR'] = intval($_POST['content_editor']);
	if (isset($_POST['show_guide']))$temp_prefs['PREF_SHOW_GUIDE'] = intval($_POST['show_guide']);

	// display settings (tab 1)
	if (isset($_POST['fontface'])) $temp_prefs['PREF_FONT_FACE'] = $addslashes($_POST['fontface']);
	if (isset($_POST['font_times'])) $temp_prefs['PREF_FONT_TIMES'] = $addslashes($_POST['font_times']);
	if (isset($_POST['fg'])) $temp_prefs['PREF_FG_COLOUR'] = $addslashes($_POST['fg']);
	if (isset($_POST['bg'])) $temp_prefs['PREF_BG_COLOUR'] = $addslashes($_POST['bg']);
	if (isset($_POST['hl'])) $temp_prefs['PREF_HL_COLOUR'] = $addslashes($_POST['hl']);
	
	// content settings (tab 2)
	if (isset($_POST['use_alternative_to_text'])) $temp_prefs['PREF_USE_ALTERNATIVE_TO_TEXT'] = intval($_POST['use_alternative_to_text']);
	if (isset($_POST['preferred_alt_to_text'])) $temp_prefs['PREF_ALT_TO_TEXT'] = $addslashes($_POST['preferred_alt_to_text']);
	if (isset($_POST['alt_to_text_append_or_replace'])) $temp_prefs['PREF_ALT_TO_TEXT_APPEND_OR_REPLACE'] = $addslashes($_POST['alt_to_text_append_or_replace']);
	if (isset($_POST['alt_text_prefer_lang'])) $temp_prefs['PREF_ALT_TEXT_PREFER_LANG'] = $addslashes($_POST['alt_text_prefer_lang']);
	if (isset($_POST['use_alternative_to_audio'])) $temp_prefs['PREF_USE_ALTERNATIVE_TO_AUDIO'] = intval($_POST['use_alternative_to_audio']);
	if (isset($_POST['preferred_alt_to_audio'])) $temp_prefs['PREF_ALT_TO_AUDIO'] = $addslashes($_POST['preferred_alt_to_audio']);
	if (isset($_POST['alt_to_audio_append_or_replace'])) $temp_prefs['PREF_ALT_TO_AUDIO_APPEND_OR_REPLACE'] = $addslashes($_POST['alt_to_audio_append_or_replace']);
	if (isset($_POST['alt_audio_prefer_lang'])) $temp_prefs['PREF_ALT_AUDIO_PREFER_LANG'] = $addslashes($_POST['alt_audio_prefer_lang']);
	if (isset($_POST['use_alternative_to_visual'])) $temp_prefs['PREF_USE_ALTERNATIVE_TO_VISUAL'] = intval($_POST['use_alternative_to_visual']);
	if (isset($_POST['preferred_alt_to_visual'])) $temp_prefs['PREF_ALT_TO_VISUAL'] = $addslashes($_POST['preferred_alt_to_visual']);
	if (isset($_POST['alt_to_visual_append_or_replace'])) $temp_prefs['PREF_ALT_TO_VISUAL_APPEND_OR_REPLACE'] = $addslashes($_POST['alt_to_visual_append_or_replace']);
	if (isset($_POST['alt_visual_prefer_lang'])) $temp_prefs['PREF_ALT_VISUAL_PREFER_LANG'] = $addslashes($_POST['alt_visual_prefer_lang']);

	// tool settings (tab 3)
	if (isset($_POST['dictionary_val'])) $temp_prefs['PREF_DICTIONARY'] = intval($_POST['dictionary_val']);
	if (isset($_POST['thesaurus_val'])) $temp_prefs['PREF_THESAURUS'] = intval($_POST['thesaurus_val']);
	if (isset($_POST['note_taking_val'])) $temp_prefs['PREF_NOTE_TAKING'] = intval($_POST['note_taking_val']);
	if (isset($_POST['calculator_val'])) $temp_prefs['PREF_CALCULATOR'] = intval($_POST['calculator_val']);
	if (isset($_POST['abacus_val'])) $temp_prefs['PREF_ABACUS'] = intval($_POST['abacus_val']);
	if (isset($_POST['atlas_val'])) $temp_prefs['PREF_ATLAS'] = intval($_POST['atlas_val']);
	if (isset($_POST['encyclopedia_val'])) $temp_prefs['PREF_ENCYCLOPEDIA'] = intval($_POST['encyclopedia_val']);	

	// control settings (tab 4)
	if (isset($_POST['show_contents'])) $temp_prefs['PREF_SHOW_CONTENTS'] = intval($_POST['show_contents']);
	if (isset($_POST['show_next_previous_buttons'])) $temp_prefs['PREF_SHOW_NEXT_PREVIOUS_BUTTONS'] = intval($_POST['show_next_previous_buttons']);
	if (isset($_POST['show_bread_crumbs'])) $temp_prefs['PREF_SHOW_BREAD_CRUMBS'] = intval($_POST['show_bread_crumbs']);
		
	return $temp_prefs;
}

/**
 * Assigns default preferences to a preferences array
 *  
 * @return an array of preferences
 */
function assignDefaultPrefs() {
global $db, $_config_defaults;      
        $sql    = "SELECT value FROM ".TABLE_PREFIX."config WHERE name='pref_defaults'";
        $result = mysql_query($sql, $db);
        
        if (mysql_num_rows($result) > 0)
        {
            $row_defaults = mysql_fetch_assoc($result);
            $default = $row_defaults["value"];
            
            $temp_prefs = unserialize($default);
            
            // Many new preferences are introduced in 1.6.2 that are missing in old admin 
            // default preference string. Solve this case by completing settings on new
            // preferences with $_config_defaults
            foreach (unserialize($_config_defaults['pref_defaults']) as $name => $value) {
                if (!isset($temp_prefs[$name])) $temp_prefs[$name] = $value;
            }
        }
        else {
            $temp_prefs = unserialize($_config_defaults['pref_defaults']);
        }
        return $temp_prefs;
}

/**
 * gets the default preference for inbox notification (disabled usually)
 * 
 * @return the value of the inbox notification preference
 */
function assignDefaultMnot() {
global $db, $_config_defaults;
        $sql    = "SELECT value FROM ".TABLE_PREFIX."config WHERE name='pref_inbox_notify'";
        $result = mysql_query($sql, $db);
        if (mysql_num_rows($result) > 0)
        {
            $row_notify = mysql_fetch_assoc($result);
            $mnot = $row_notify["value"];
        }
        else
            $mnot = $_config_defaults['pref_inbox_notify'];
        return $mnot;
}

/**
 * gets the default preference for auto login preference (disabled usually)
 * 
 * @return the value of the auto login preference
 */
function assignDefaultAutologin() {
global $db, $_config_defaults;;
        $sql    = "SELECT value FROM ".TABLE_PREFIX."config WHERE name='pref_is_auto_login'";
        $result = mysql_query($sql, $db);
        if (mysql_num_rows($result) > 0)
        {
            $row_is_auto_login = mysql_fetch_assoc($result);
            $auto_login = $row_is_auto_login["value"];
        }
        else
            $auto_login = $_config_defaults['pref_is_auto_login'];
        return $auto_login;

}

/**
 *  Either sets the auto login cookies or expires them depending on the input
 *  
 * @param string $toDo - 'enable' if the autologin cookies are to be set, and
 * 'disable' if the auto login cookies are to be expired.
 * 
 * @return string - either 'enable' if the cookies were set, or 'disable' otherwise.
 */
function setAutoLoginCookie($toDo) {
global $db;

    //set default values for disabled auto login cookies
    $parts = parse_url(AT_BASE_HREF);
    $path = $parts['path'];
	$time = time() - 172800;
	$password = ""; 
	$login = "";
	$is_auto_login = 'disable';
	
	//if enable auto login, set actual cookie values
	if ($toDo == 'enable') {
		$time = time() + 172800;
		$sql	= "SELECT password FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);
		$row	= mysql_fetch_assoc($result);
		$password = $row["password"];
		$login = $_SESSION['login'];	
	}
	
	//set cookies and boolean value indicating cookies have been set.ies
	$is_cookie_login_set = ATutor.setcookie('ATLogin', $login, $time, $path);
	$is_cookie_pass_set = ATutor.setcookie('ATPass',  $password, $time, $path);
	if ($is_cookie_login_set && $is_cookie_pass_set) $is_auto_login = $toDo;
	return $is_auto_login;
}

/**
 * Sets $is_auto_login to 'enable' if both auto login cookies are set, otherwise
 * returns 'disable'
 * 
 * @return string
 */
function checkAutoLoginCookie() {
    $is_auto_login = 'disable';
    if (isset($_COOKIE['ATLogin']) && isset($_COOKIE['ATPass'])) {
        $is_auto_login = 'enable';
    }
    return $is_auto_login;
}

?>