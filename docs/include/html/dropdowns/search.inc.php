<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

if ($_SESSION['prefs'][PREF_SEARCH] == 1){
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="catd" valign="top">';
	print_popup_help(AT_HELP_SEARCH_MENU);
	if($_GET['menu_jump']){
		echo '<a name="menu_jump7"></a>';
	}
	echo '<a class="white" href="'.$_my_uri.'disable='.PREF_SEARCH.SEP.'menu_jump=7">';
	echo _AT('close_search');
	echo '</a>';
	echo '</td></tr>';
	echo '<tr>';
	echo '<td class="row1" align="left">';

	if (!isset($include_all, $include_one)) {
		$include_one = ' checked="checked"';
	}
?>
	<form action="<?php echo $_base_path; ?>tools/search.php#search_results" method="get" name="searchform">
	<input type="hidden" name="search" value="1" />
	<input type="text" name="words" class="formfield" size="20" id="words" value="<?php echo stripslashes(htmlspecialchars($_GET['words'])); ?>" /><br />
	<small><?php echo _AT('search_match'); ?>: <input type="radio" name="include" value="all" id="all2"<?php echo $include_all; ?> /><label for="all2"><?php echo _AT('search_all_words'); ?></label>, <input type="radio" name="include" value="one" id="one2"<?php echo $include_one; ?> /><label for="one2"><?php echo _AT('search_any_word'); ?></label><br /></small>

	<input type="submit" name="submit" value="  <?php echo _AT('search'); ?>  " class="button" />
	</form>

<?php
	echo '</td></tr></table>';

} else {
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="catd" valign="top">';
	print_popup_help(AT_HELP_SEARCH_MENU);
	if($_GET['menu_jump']){
		echo '<a name="menu_jump7"></a>';
	}
	echo '<a class="white" href="'.$_my_uri.'enable='.PREF_SEARCH.SEP.'menu_jump=7">';
	echo _AT('open_search');
	echo '</a>';
	echo '</td></tr></table>';
}

?>
