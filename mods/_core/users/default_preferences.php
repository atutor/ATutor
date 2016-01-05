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

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/themes/lib/themes.inc.php'); //update when themes are moved to mod/
require(AT_INCLUDE_PATH.'../mods/_core/users/lib/pref_tab_functions.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: users.php');
	exit;
}

if (isset($_POST['submit']) || isset($_POST["set_default"])) {
	if (isset($_POST['submit']))
	{
		/* custom prefs */
		// atutor settings (tab 0)
		// capture the current theme before updating prefs_defaults
		// Then re apply the theme after leaving the page
		
		$current_theme = $_SESSION['PREF_THEME'];
		
		$pref_defaults['PREF_NUMBERING']      = intval($_POST['numbering']);
		if (isset($_POST['theme']) && $_POST['theme'] != '') {
			$pref_defaults['PREF_THEME']          = $addslashes($_POST['theme']);
		}
		if (isset($_POST['mobile_theme']) && $_POST['mobile_theme'] != '') {
			$pref_defaults['PREF_MOBILE_THEME'] = $addslashes($_POST['mobile_theme']);
		}
		$pref_defaults['PREF_TIMEZONE']	     = $addslashes($_POST['time_zone']);
		$pref_defaults['PREF_JUMP_REDIRECT']  = intval($_POST['use_jump_redirect']);
    $pref_defaults['PREF_HIDE_FEEDBACK']  = intval($_POST['hide_feedback']);
		$pref_defaults['PREF_FORM_FOCUS']     = intval($_POST['form_focus']);
		$pref_defaults['PREF_CONTENT_EDITOR'] = intval($_POST['content_editor']);
		$pref_defaults['PREF_SHOW_GUIDE']     = intval($_POST['show_guide']);
		
		// display settings (tab 1)
		$pref_defaults['PREF_FONT_FACE']	   = $addslashes($_POST['fontface']);
		$pref_defaults['PREF_FONT_TIMES']	   = $addslashes($_POST['font_times']);
		$pref_defaults['PREF_FG_COLOUR']	   = $addslashes($_POST['fg']);
		$pref_defaults['PREF_BG_COLOUR']	   = $addslashes($_POST['bg']);
		$pref_defaults['PREF_HL_COLOUR']	   = $addslashes($_POST['hl']);
	
		// content settings (tab 2)
		$pref_defaults['PREF_USE_ALTERNATIVE_TO_TEXT'] = intval($_POST['use_alternative_to_text']);
		$pref_defaults['PREF_ALT_TO_TEXT'] = $addslashes($_POST['preferred_alt_to_text']);
		$pref_defaults['PREF_ALT_TO_TEXT_APPEND_OR_REPLACE'] = $addslashes($_POST['alt_to_text_append_or_replace']);
		$pref_defaults['PREF_ALT_TEXT_PREFER_LANG'] = $addslashes($_POST['alt_text_prefer_lang']);
		$pref_defaults['PREF_USE_ALTERNATIVE_TO_AUDIO'] = intval($_POST['use_alternative_to_audio']);
		$pref_defaults['PREF_ALT_TO_AUDIO'] = $addslashes($_POST['preferred_alt_to_audio']);
		$pref_defaults['PREF_ALT_TO_AUDIO_APPEND_OR_REPLACE'] = $addslashes($_POST['alt_to_audio_append_or_replace']);
		$pref_defaults['PREF_ALT_AUDIO_PREFER_LANG'] = $addslashes($_POST['alt_audio_prefer_lang']);
		$pref_defaults['PREF_USE_ALTERNATIVE_TO_VISUAL'] = intval($_POST['use_alternative_to_visual']);
		$pref_defaults['PREF_ALT_TO_VISUAL'] = $addslashes($_POST['preferred_alt_to_visual']);
		$pref_defaults['PREF_ALT_TO_VISUAL_APPEND_OR_REPLACE'] = $addslashes($_POST['alt_to_visual_append_or_replace']);
		$pref_defaults['PREF_ALT_VISUAL_PREFER_LANG'] = $addslashes($_POST['alt_visual_prefer_lang']);
	
		// tool settings (tab 3)
		$pref_defaults['PREF_DICTIONARY'] = intval($_POST['dictionary_val']);
		$pref_defaults['PREF_THESAURUS'] = intval($_POST['thesaurus_val']);
		$pref_defaults['PREF_NOTE_TAKING'] = intval($_POST['note_taking_val']);
		$pref_defaults['PREF_CALCULATOR'] = intval($_POST['calculator_val']);
		$pref_defaults['PREF_ABACUS'] = intval($_POST['abacus_val']);
		$pref_defaults['PREF_ATLAS'] = intval($_POST['atlas_val']);
		$pref_defaults['PREF_ENCYCLOPEDIA'] = intval($_POST['encyclopedia_val']);	
	
		// control settings (tab 4)
		$pref_defaults['PREF_SHOW_CONTENTS'] = intval($_POST['show_contents']);
		$pref_defaults['PREF_SHOW_NEXT_PREVIOUS_BUTTONS'] = intval($_POST['show_next_previous_buttons']);
		$pref_defaults['PREF_SHOW_BREAD_CRUMBS'] = intval($_POST['show_bread_crumbs']);

		$pref_defaults = serialize($pref_defaults);
		
		$mnot = intval($_POST["mnot"]);
		$auto_login = $addslashes($_POST["auto"]);
	}
	else
	{
	print_r("anything");
		$pref_defaults = $_config_defaults['pref_defaults'];
		$mnot = $_config_defaults['pref_inbox_notify'];
		$auto_login = $_config_defaults['pref_is_auto_login'];
	}
    //WHY IS THIS CONDITION HERE
	if (!($_config_defaults['pref_defaults'] == $pref_defaults)) {
		$sql    = "REPLACE INTO %sconfig VALUES ('pref_defaults','%s')";
	} else if ($_config_defaults['pref_defaults'] == $pref_defaults) {
		$sql    = "REPLACE INTO %sconfig VALUES ('pref_defaults','%s')";
	}

    $result = queryDB($sql, array(TABLE_PREFIX, $pref_defaults));

	$sql    = "REPLACE INTO %sconfig VALUES ('pref_inbox_notify','%s')";
	$result = queryDB($sql, array(TABLE_PREFIX, $mnot));

	$sql    = "REPLACE INTO %sconfig VALUES ('pref_is_auto_login','%s')";
	$result = queryDB($sql, array(TABLE_PREFIX, $auto_login));
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.$_SERVER['PHP_SELF'].'?current_tab='.$_POST['current_tab']);
	exit;
} else {
	// save the current mobile theme that's calculated by vitals.inc.php
	// so it can be restored into $_SESSION['prefs']['PREF_THEME'] before 
	// headers.inc.php gets called. This session var will be overwritten by
	// the following config calculation
	if (is_mobile_device()) {
		$current_theme = $_SESSION['prefs']['PREF_THEME'];
	}
	
	// Calculates the actual default theme
	// set defaults with the $_config_defaults and overwrite them with what's defined in table `config`
	$pref_defaults = unserialize($_config_defaults['pref_defaults']);
	
	if (is_array(unserialize($_config['pref_defaults']))) {
		foreach (unserialize($_config['pref_defaults']) as $name => $value) {
			$pref_defaults[$name] = $value;
		}
	}
	
	// Save the desktop theme
	$desktop_theme = $pref_defaults['PREF_THEME'];
	
	// $_SESSION['prefs']['PREF_THEME'] is overwritten here into the desktop theme
	assign_session_prefs($pref_defaults);
	
	// restore the mobile theme
	if (is_mobile_device()) {
		$_SESSION['prefs']['PREF_THEME'] = $current_theme;
	}

	$sql	= "SELECT value FROM %sconfig WHERE name='pref_inbox_notify'";
	$row_notify = queryDB($sql, array(TABLE_PREFIX), TRUE);
    
    if(count($row_notify) > 0){
		$notify = $row_notify['value'];
	}
	else
		$notify = $_config_defaults['pref_inbox_notify'];
	
	$sql	= "SELECT value FROM %sconfig WHERE name='pref_is_auto_login'";
	$row_is_auto_login = queryDB($sql, array(TABLE_PREFIX), TRUE);
	
    if(count($row_is_auto_login) > 0){

		$auto_login = $row_is_auto_login["value"];
	}
	else
		$auto_login = $_config_defaults['pref_is_auto_login'];
}

$languages = $languageManager->getAvailableLanguages();

$savant->assign('notify', $notify);
$savant->assign('languages', $languages);
$savant->assign('is_auto_login', $auto_login);
$savant->assign('desktop_theme', $desktop_theme);

$savant->display('users/preferences.tmpl.php');
// Allow the new default theme top display on this page, then reset back to the systems default
$_SESSION['prefs']['PREF_THEME'] = $current_theme;
?>
