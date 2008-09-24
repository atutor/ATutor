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

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/themes.inc.php');
require(AT_INCLUDE_PATH.'lib/pref_tab_functions.inc.php');
/* whether or not, any settings are being changed when this page loads. */
/* ie. is ANY action being performed right now?							*/

$action = false;

if (!$_SESSION['valid_user']) {				
	/* we're not logged in */
	$msg->addFeedback('PREFS_LOGIN');
}

if (isset($_POST['submit'])) {
	if (isset($_POST['caption_rate']) && (intval($_POST['caption_rate'])>300 || intval($_POST['caption_rate'])<1))
		$msg->addError('WRONG_CAPTION_RATE');
	
	if (!$msg->containsErrors()) 
	{
		/* custom prefs */
		// atutor settings (tab 0)
		$temp_prefs['PREF_NUMBERING']      = intval($_POST['numbering']);
		$temp_prefs['PREF_THEME']          = $addslashes($_POST['theme']);
		$temp_prefs['PREF_TIMEZONE']	     = $addslashes($_POST['time_zone']);
		$temp_prefs['PREF_JUMP_REDIRECT']  = intval($_POST['use_jump_redirect']);
		$temp_prefs['PREF_FORM_FOCUS']     = intval($_POST['form_focus']);
		$temp_prefs['PREF_CONTENT_EDITOR'] = intval($_POST['content_editor']);
		$temp_prefs['PREF_SHOW_GUIDE']     = intval($_POST['show_guide']);
		
		// display settings (tab 1)
		$temp_prefs['PREF_FONT_FACE']	   = $addslashes($_POST['fontface']);
		$temp_prefs['PREF_FONT_SIZE']	   = intval($_POST['fontsize']);
		$temp_prefs['PREF_FG_COLOUR']	   = $addslashes($_POST['fg']);
		$temp_prefs['PREF_BG_COLOUR']	   = $addslashes($_POST['bg']);
		$temp_prefs['PREF_HL_COLOUR']	   = $addslashes($_POST['hl']);
		$temp_prefs['PREF_INVERT_COLOUR_SELECTION'] = intval($_POST['invert_colour_selection']);
		$temp_prefs['PREF_AVOID_RED']	         = intval($_POST['avoid_red']);
		$temp_prefs['PREF_AVOID_RED_GREEN']	   = intval($_POST['avoid_red_green']);
		$temp_prefs['PREF_AVOID_BLUE_YELLOW']	 = intval($_POST['avoid_blue_yellow']);
		$temp_prefs['PREF_AVOID_GREEN_YELLOW'] = intval($_POST['avoid_green_yellow']);
		$temp_prefs['PREF_USE_MAX_CONTRAST']	 = intval($_POST['use_max_contrast']);
	
		// content settings (tab 2)
//		$temp_prefs['PREF_USE_ALTERNATE_TEXT'] = intval($_POST['use_alternate_text']);
//		$temp_prefs['PREF_ALT_TEXT_LANG']	     = $addslashes($_POST['alt_text_lang']);
//		$temp_prefs['PREF_LONG_DESC_LANG']	   = $addslashes($_POST['long_desc_lang']);
//		$temp_prefs['PREF_USE_GRAPHIC_ALTERNATIVE'] = intval($_POST['use_graphic_alternative']);
//		$temp_prefs['PREF_USE_SIGN_LANG'] = intval($_POST['use_sign_lang']);
//		$temp_prefs['PREF_SIGN_LANG']	    = $addslashes($_POST['sign_lang']);
//		$temp_prefs['PREF_USE_VIDEO']     = intval($_POST['use_video']);
//		$temp_prefs['PREF_PREFER_LANG']	  = $addslashes($_POST['prefer_lang']);
//		$temp_prefs['PREF_DESC_TYPE']	    = $addslashes($_POST['description_type']);
//		$temp_prefs['PREF_ENABLE_CAPTIONS'] = intval($_POST['enable_captions']);
//		$temp_prefs['PREF_CAPTION_TYPE']	  = $addslashes($_POST['caption_type']);
//		$temp_prefs['PREF_CAPTION_LANG']	  = $addslashes($_POST['caption_lang']);
//		$temp_prefs['PREF_ENHANCED_CAPTIONS'] = intval($_POST['enhanced_captions']);
//		$temp_prefs['PREF_REQUEST_CAPTION_RATE'] = intval($_POST['request_caption_rate']);
//		$temp_prefs['PREF_CAPTION_RATE']	   = intval($_POST['caption_rate']);
		$temp_prefs['PREF_USE_ALTERNATIVE_TO_TEXT'] = intval($_POST['use_alternative_to_text']);
		$temp_prefs['PREF_ALT_TO_TEXT'] = $addslashes($_POST['preferred_alt_to_text']);
		$temp_prefs['PREF_ALT_TO_TEXT_APPEND_OR_REPLACE'] = $addslashes($_POST['alt_to_text_append_or_replace']);
		$temp_prefs['PREF_ALT_TEXT_PREFER_LANG'] = $addslashes($_POST['alt_text_prefer_lang']);
		$temp_prefs['PREF_USE_ALTERNATIVE_TO_AUDIO'] = intval($_POST['use_alternative_to_audio']);
		$temp_prefs['PREF_ALT_TO_AUDIO'] = $addslashes($_POST['preferred_alt_to_audio']);
		$temp_prefs['PREF_ALT_TO_AUDIO_APPEND_OR_REPLACE'] = $addslashes($_POST['alt_to_audio_append_or_replace']);
		$temp_prefs['PREF_ALT_AUDIO_PREFER_LANG'] = $addslashes($_POST['alt_audio_prefer_lang']);
		$temp_prefs['PREF_USE_ALTERNATIVE_TO_VISUAL'] = intval($_POST['use_alternative_to_visual']);
		$temp_prefs['PREF_ALT_TO_VISUAL'] = $addslashes($_POST['preferred_alt_to_visual']);
		$temp_prefs['PREF_ALT_TO_VISUAL_APPEND_OR_REPLACE'] = $addslashes($_POST['alt_to_visual_append_or_replace']);
		$temp_prefs['PREF_ALT_VISUAL_PREFER_LANG'] = $addslashes($_POST['alt_visual_prefer_lang']);
	
		// tool settings (tab 3)
		$temp_prefs['PREF_DICTIONARY'] = intval($_POST['dictionary_val']);
		$temp_prefs['PREF_THESAURUS'] = intval($_POST['thesaurus_val']);
		$temp_prefs['PREF_NOTE_TAKING'] = intval($_POST['note_taking_val']);
		$temp_prefs['PREF_CALCULATOR'] = intval($_POST['calculator_val']);
		//$temp_prefs['PREF_PEER_INTERACTION'] = intval($_POST['peer_interaction_val']);
		$temp_prefs['PREF_ABACUS'] = intval($_POST['abacus_val']);
		$temp_prefs['PREF_ATLAS'] = intval($_POST['atlas_val']);
		$temp_prefs['PREF_ENCYCLOPEDIA'] = intval($_POST['encyclopedia_val']);	

		// control settings (tab 4)
		$temp_prefs['PREF_SHOW_CONTENTS'] = intval($_POST['show_contents']);
		$temp_prefs['PREF_SHOW_NEXT_PREVIOUS_BUTTONS'] = $addslashes($_POST['show_next_previous_buttons']);
		$temp_prefs['PREF_SHOW_BREAD_CRUMBS'] = $addslashes($_POST['show_bread_crumbs']);
//		$temp_prefs['PREF_SHOW_NOTES'] = intval($_POST['show_notes']);
//		$temp_prefs['PREF_LEVEL_OF_DETAIL'] = $addslashes($_POST['level_of_detail']);
//		$temp_prefs['PREF_CONTENT_VIEWS'] = $addslashes($_POST['content_views']);
//		$temp_prefs['PREF_SHOW_SEPARATE_LINKS'] = intval($_POST['show_separate_links']);
//		$temp_prefs['PREF_SHOW_TRANSCRIPT'] = intval($_POST['show_transcript']);
//		$temp_prefs['PREF_WINDOW_LAYOUT'] = $addslashes($_POST['window_layout']);
	
		/* we do this instead of assigning to the $_SESSION directly, b/c	*/
		/* assign_session_prefs functionality might change slightly.		*/
		assign_session_prefs($temp_prefs);
	
		/* save as pref for ALL courses */
		save_prefs();
	
		//update auto-login settings
		if (isset($_POST['auto']) && ($_POST['auto'] == 'disable')) {
			$parts = parse_url(AT_BASE_HREF);
			$is_cookie_login_set = setcookie('ATLogin', '', time()-172800, $parts['path']);
			$is_cookie_paa_set = setcookie('ATPass',  '', time()-172800, $parts['path']);

			// The usage of flag $is_auto_login is because the set cookies are only accessible at the next page reload
			if ($is_cookie_login_set && $is_cookie_pass_set) $is_auto_login = 0;
		} else if (isset($_POST['auto']) && ($_POST['auto'] == 'enable')) {
			$parts = parse_url(AT_BASE_HREF);
			$sql	= "SELECT password FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
			$result = mysql_query($sql, $db);
			$row	= mysql_fetch_assoc($result);
			$password = $row["password"];
			$is_cookie_login_set = setcookie('ATLogin', $_SESSION['login'], time()+172800, $parts['path']);
			$is_cookie_pass_set = setcookie('ATPass',  $password, time()+172800, $parts['path']);
			
			if ($is_cookie_login_set && $is_cookie_pass_set) $is_auto_login = 1;
		}
	
		/* also update message notification pref */
		$_POST['mnot'] = intval($_POST['mnot']);
		$sql = "UPDATE ".TABLE_PREFIX."members SET inbox_notify = $_POST[mnot], creation_date=creation_date, last_login=last_login WHERE member_id = $_SESSION[member_id]";
		$result = mysql_query($sql, $db);
	
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}
}

$sql	= "SELECT inbox_notify FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
$result = mysql_query($sql, $db);
$row_notify = mysql_fetch_assoc($result);

$languages = $languageManager->getAvailableLanguages();

/* page contents starts here */
$savant->assign('notify', $row_notify['inbox_notify']);
$savant->assign('languages', $languages);
$savant->assign('is_auto_login', $is_auto_login);

$savant->display('users/preferences.tmpl.php');

?>