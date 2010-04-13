<?php 

$tabs = get_tabs();	
$num_tabs = count($tabs);

$current_tab = 0;  // set default tab
$switch_tab = false;

for ($i=0; $i < $num_tabs; $i++) 
{
	if (isset($_POST['button_'.$i]) && ($_POST['button_'.$i] != -1)) 
	{ 
		$current_tab = $i;
		$switch_tab = true;
		break;
	}
}

if (!$switch_tab && isset($_POST['current_tab'])) {
	$current_tab = intval($_POST['current_tab']);
}

if ($current_tab == 1)
{
	global $_custom_head, $onload;
	
	$_custom_head = "<script language=\"JavaScript\" src=\"jscripts/TILE.js\" type=\"text/javascript\"></script>";
	$onload = "setPreviewFace(); setPreviewSize(); setPreviewColours();";
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

if($_SESSION['course_id'] == "-1"){
echo '<div id="container"><br />';
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" name="form" enctype="multipart/form-data">

	<div align="center" style="width:90%; margin-left:auto; margin-right:auto;">
		<?php output_tabs($current_tab, $changes_made); ?>
	</div>

	<div class="input-form">
		<input type="hidden" name="current_tab" value="<?php echo $current_tab; ?>" />
<?php
	if ($current_tab != 0) 
	{
		// save selected options on tab 0 (ATutor settings)
		if (isset($_POST['theme']))
			echo '	<input type="hidden" name="theme" value="'.$_POST['theme'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_THEME']))
			echo '	<input type="hidden" name="theme" value="'.$_SESSION['prefs']['PREF_THEME'].'" />'."\n\r";
		
		if (isset($_POST['mnot']))
			echo '	<input type="hidden" name="mnot" value="'.$_POST['mnot'].'" />'."\n\r";
		else if (isset($this->notify))
			echo '	<input type="hidden" name="mnot" value="'.$this->notify.'" />'."\n\r";

		if (isset($_POST['time_zone']))
			echo '	<input type="hidden" name="time_zone" value="'.$_POST['time_zone'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_TIMEZONE']))
			echo '	<input type="hidden" name="time_zone" value="'.$_SESSION['prefs']['PREF_TIMEZONE'].'" />'."\n\r";
		
		if (isset($_POST['numbering']))
			echo '	<input type="hidden" name="numbering" value="'.$_POST['numbering'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_NUMBERING']))
			echo '	<input type="hidden" name="numbering" value="'.$_SESSION['prefs']['PREF_NUMBERING'].'" />'."\n\r";
		
		if (isset($_POST['use_jump_redirect']))
			echo '	<input type="hidden" name="use_jump_redirect" value="'.$_POST['use_jump_redirect'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_JUMP_REDIRECT']))
			echo '	<input type="hidden" name="use_jump_redirect" value="'.$_SESSION['prefs']['PREF_JUMP_REDIRECT'].'" />'."\n\r";
		
		if (isset($_POST['auto']))
			echo '	<input type="hidden" name="auto" value="'.$_POST['auto'].'" />'."\n\r";
		else if (isset($this->is_auto_login))
			echo '	<input type="hidden" name="auto" value="'.$this->is_auto_login.'" />'."\n\r";
		
		if (isset($_POST['form_focus']))
			echo '	<input type="hidden" name="form_focus" value="'.$_POST['form_focus'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_FORM_FOCUS']))
			echo '	<input type="hidden" name="form_focus" value="'.$_SESSION['prefs']['PREF_FORM_FOCUS'].'" />'."\n\r";
		
		if (isset($_POST['show_guide']))
			echo '	<input type="hidden" name="show_guide" value="'.$_POST['show_guide'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_SHOW_GUIDE']))
			echo '	<input type="hidden" name="show_guide" value="'.$_SESSION['prefs']['PREF_SHOW_GUIDE'].'" />'."\n\r";
		
		if (isset($_POST['content_editor']))
			echo '	<input type="hidden" name="content_editor" value="'.$_POST['content_editor'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_CONTENT_EDITOR']))
			echo '	<input type="hidden" name="content_editor" value="'.$_SESSION['prefs']['PREF_CONTENT_EDITOR'].'" />'."\n\r";
	}

	if ($current_tab != 1) 
	{
		// save selected options on tab 1 (display settings)
		if (isset($_POST['fontface']))
			echo '	<input type="hidden" name="fontface" value="'.$_POST['fontface'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_FONT_FACE']))
			echo '	<input type="hidden" name="fontface" value="'.$_SESSION['prefs']['PREF_FONT_FACE'].'" />'."\n\r";

		if (isset($_POST['font_times']))
			echo '	<input type="hidden" name="font_times" value="'.$_POST['font_times'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_FONT_TIMES']))
			echo '	<input type="hidden" name="font_times" value="'.$_SESSION['prefs']['PREF_FONT_TIMES'].'" />'."\n\r";

		if (isset($_POST['fg']))
			echo '	<input type="hidden" name="fg" value="'.$_POST['fg'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_FG_COLOUR']))
			echo '	<input type="hidden" name="fg" value="'.$_SESSION['prefs']['PREF_FG_COLOUR'].'" />'."\n\r";

		if (isset($_POST['bg']))
			echo '	<input type="hidden" name="bg" value="'.$_POST['bg'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_BG_COLOUR']))
			echo '	<input type="hidden" name="bg" value="'.$_SESSION['prefs']['PREF_BG_COLOUR'].'" />'."\n\r";

		if (isset($_POST['hl']))
			echo '	<input type="hidden" name="hl" value="'.$_POST['hl'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_HL_COLOUR']))
			echo '	<input type="hidden" name="hl" value="'.$_SESSION['prefs']['PREF_HL_COLOUR'].'" />'."\n\r";
	}
		
	if ($current_tab != 2) 
	{
		// save selected options on tab 2 (content settings)
		if (isset($_POST['use_alternative_to_text']))
			echo '	<input type="hidden" name="use_alternative_to_text" value="'.$_POST['use_alternative_to_text'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT']))
			echo '	<input type="hidden" name="use_alternative_to_text" value="'.$_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT'].'" />'."\n\r";

		if (isset($_POST['preferred_alt_to_text']))
			echo '	<input type="hidden" name="preferred_alt_to_text" value="'.$_POST['preferred_alt_to_text'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_ALT_TO_TEXT']))
			echo '	<input type="hidden" name="preferred_alt_to_text" value="'.$_SESSION['prefs']['PREF_ALT_TO_TEXT'].'" />'."\n\r";
		
		if (isset($_POST['alt_to_text_append_or_replace']))
		echo '	<input type="hidden" name="alt_to_text_append_or_replace" value="'.$_POST['alt_to_text_append_or_replace'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_ALT_TO_TEXT_APPEND_OR_REPLACE']))
		echo '	<input type="hidden" name="alt_to_text_append_or_replace" value="'.$_SESSION['prefs']['PREF_ALT_TO_TEXT_APPEND_OR_REPLACE'].'" />'."\n\r";
		
		if (isset($_POST['alt_text_prefer_lang']))
		echo '	<input type="hidden" name="alt_text_prefer_lang" value="'.$_POST['alt_text_prefer_lang'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_ALT_TEXT_PREFER_LANG']))
		echo '	<input type="hidden" name="alt_text_prefer_lang" value="'.$_SESSION['prefs']['PREF_ALT_TEXT_PREFER_LANG'].'" />'."\n\r";
		
		if (isset($_POST['use_alternative_to_audio']))
		echo '	<input type="hidden" name="use_alternative_to_audio" value="'.$_POST['use_alternative_to_audio'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_AUDIO']))
		echo '	<input type="hidden" name="use_alternative_to_audio" value="'.$_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_AUDIO'].'" />'."\n\r";
		
		if (isset($_POST['preferred_alt_to_audio']))
		echo '	<input type="hidden" name="preferred_alt_to_audio" value="'.$_POST['preferred_alt_to_audio'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_ALT_TO_AUDIO']))
		echo '	<input type="hidden" name="preferred_alt_to_audio" value="'.$_SESSION['prefs']['PREF_ALT_TO_AUDIO'].'" />'."\n\r";
		
		if (isset($_POST['alt_to_audio_append_or_replace']))
		echo '	<input type="hidden" name="alt_to_audio_append_or_replace" value="'.$_POST['alt_to_audio_append_or_replace'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_ALT_TO_AUDIO_APPEND_OR_REPLACE']))
		echo '	<input type="hidden" name="alt_to_audio_append_or_replace" value="'.$_SESSION['prefs']['PREF_ALT_TO_AUDIO_APPEND_OR_REPLACE'].'" />'."\n\r";
		
		if (isset($_POST['alt_audio_prefer_lang']))
		echo '	<input type="hidden" name="alt_audio_prefer_lang" value="'.$_POST['alt_audio_prefer_lang'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_ALT_AUDIO_PREFER_LANG']))
		echo '	<input type="hidden" name="alt_audio_prefer_lang" value="'.$_SESSION['prefs']['PREF_ALT_AUDIO_PREFER_LANG'].'" />'."\n\r";
		
		if (isset($_POST['use_alternative_to_visual']))
		echo '	<input type="hidden" name="use_alternative_to_visual" value="'.$_POST['use_alternative_to_visual'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_VISUAL']))
		echo '	<input type="hidden" name="use_alternative_to_visual" value="'.$_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_VISUAL'].'" />'."\n\r";
		
		if (isset($_POST['preferred_alt_to_visual']))
		echo '	<input type="hidden" name="preferred_alt_to_visual" value="'.$_POST['preferred_alt_to_visual'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_ALT_TO_VISUAL']))
		echo '	<input type="hidden" name="preferred_alt_to_visual" value="'.$_SESSION['prefs']['PREF_ALT_TO_VISUAL'].'" />'."\n\r";
		
		if (isset($_POST['alt_to_visual_append_or_replace']))
		echo '	<input type="hidden" name="alt_to_visual_append_or_replace" value="'.$_POST['alt_to_visual_append_or_replace'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_ALT_TO_VISUAL_APPEND_OR_REPLACE']))
		echo '	<input type="hidden" name="alt_to_visual_append_or_replace" value="'.$_SESSION['prefs']['PREF_ALT_TO_VISUAL_APPEND_OR_REPLACE'].'" />'."\n\r";
		
		if (isset($_POST['alt_visual_prefer_lang']))
		echo '	<input type="hidden" name="alt_visual_prefer_lang" value="'.$_POST['alt_visual_prefer_lang'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_ALT_VISUAL_PREFER_LANG']))
		echo '	<input type="hidden" name="alt_visual_prefer_lang" value="'.$_SESSION['prefs']['PREF_ALT_VISUAL_PREFER_LANG'].'" />'."\n\r";
	}

	if ($current_tab != 3) 
	{
		// save selected options on tab 3 (tool settings)
		if (isset($_POST['dictionary_val']))
			echo '	<input type="hidden" name="dictionary_val" value="'.$_POST['dictionary_val'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_DICTIONARY']))
			echo '	<input type="hidden" name="dictionary_val" value="'.$_SESSION['prefs']['PREF_DICTIONARY'].'" />'."\n\r";

		if (isset($_POST['thesaurus_val']))
			echo '	<input type="hidden" name="thesaurus_val" value="'.$_POST['thesaurus_val'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_THESAURUS']))
			echo '	<input type="hidden" name="thesaurus_val" value="'.$_SESSION['prefs']['PREF_THESAURUS'].'" />'."\n\r";

		if (isset($_POST['encyclopedia_val']))
			echo '	<input type="hidden" name="encyclopedia_val" value="'.$_POST['encyclopedia_val'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_ENCYCLOPEDIA']))
			echo '	<input type="hidden" name="encyclopedia_val" value="'.$_SESSION['prefs']['PREF_ENCYCLOPEDIA'].'" />'."\n\r";

		if (isset($_POST['atlas_val']))
			echo '	<input type="hidden" name="atlas_val" value="'.$_POST['atlas_val'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_ATLAS']))
			echo '	<input type="hidden" name="atlas_val" value="'.$_SESSION['prefs']['PREF_ATLAS'].'" />'."\n\r";

		if (isset($_POST['note_taking_val']))
			echo '	<input type="hidden" name="note_taking_val" value="'.$_POST['note_taking_val'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_NOTE_TAKING']))
			echo '	<input type="hidden" name="note_taking_val" value="'.$_SESSION['prefs']['PREF_NOTE_TAKING'].'" />'."\n\r";

		if (isset($_POST['calculator_val']))
			echo '	<input type="hidden" name="calculator_val" value="'.$_POST['calculator_val'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_CALCULATOR']))
			echo '	<input type="hidden" name="calculator_val" value="'.$_SESSION['prefs']['PREF_CALCULATOR'].'" />'."\n\r";

		if (isset($_POST['abacus_val']))
			echo '	<input type="hidden" name="abacus_val" value="'.$_POST['abacus_val'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_ABACUS']))
			echo '	<input type="hidden" name="abacus_val" value="'.$_SESSION['prefs']['PREF_ABACUS'].'" />'."\n\r";
	}
	
	if ($current_tab != 4) 
	{
		// save selected options on tab 4 (control settings)
		if (isset($_POST['show_contents']))
			echo '	<input type="hidden" name="show_contents" value="'.$_POST['show_contents'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_SHOW_CONTENTS']))
			echo '	<input type="hidden" name="show_contents" value="'.$_SESSION['prefs']['PREF_SHOW_CONTENTS'].'" />'."\n\r";

		if (isset($_POST['show_next_previous_buttons']))
			echo '	<input type="hidden" name="show_next_previous_buttons" value="'.$_POST['show_next_previous_buttons'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_SHOW_NEXT_PREVIOUS_BUTTONS']))
			echo '	<input type="hidden" name="show_next_previous_buttons" value="'.$_SESSION['prefs']['PREF_SHOW_NEXT_PREVIOUS_BUTTONS'].'" />'."\n\r";

		if (isset($_POST['show_bread_crumbs']))
			echo '	<input type="hidden" name="show_bread_crumbs" value="'.$_POST['show_bread_crumbs'].'" />'."\n\r";
		else if (isset($_SESSION['prefs']['PREF_SHOW_BREAD_CRUMBS']))
			echo '	<input type="hidden" name="show_bread_crumbs" value="'.$_SESSION['prefs']['PREF_SHOW_BREAD_CRUMBS'].'" />'."\n\r";
	}

	echo '<fieldset>';
	include(AT_INCLUDE_PATH .'../users/'.$tabs[$current_tab][1]);
	echo '</fieldset>';
//	include(getcwd().'/'.$tabs[$current_tab][1]);

?>
	<div class="row buttons">
<?php 
if ($_SESSION['course_id'] == -1) // admin login 
{
?>
		<input type="submit" name="set_default" value="<?php echo _AT('factory_default'); ?>" accesskey="d" />
<?php 
}
else  // user login 
{
?>
		<input type="submit" name="set_default" value="<?php echo _AT('reapply_default'); ?>" accesskey="d" title="<?php echo _AT('reapply_default'); ?> - Alt-d" style="float:left;"/>
<?php 
}
?>
		<input type="submit" name="submit" value="<?php echo _AT('apply'); ?>" accesskey="s" />
		<input type="reset" name="reset" value="<?php echo _AT('reset'); ?>" />
	</div>
</div>
</form>	
<?php
if($_SESSION['course_id'] == "-1"){
echo '</div>';
}

require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
