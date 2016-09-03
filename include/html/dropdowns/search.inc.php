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
global $_base_path, $include_all, $include_one, $stripslashes;
global $savant;

ob_start(); 

if (!isset($include_all, $include_one)) {
	$include_one = ' checked="checked"';
}
if (!isset($_GET['words'])) {
	$_GET['words'] = '';
} else {
	$_GET['words'] = stripslashes(htmlspecialchars($_GET['words']));
}
?>

<form action="<?php echo $_base_path; ?>search.php#search_results" method="get" name="searchform">
<input type="hidden" name="search" value="1" />
<input type="hidden" name="find_in" value="this" />
<input type="hidden" name="display_as" value="pages" />
<input type="hidden" name="search_within" value="all" />
<label for="words" style="visibility:hidden; font-size:0;"><?php echo _AT('search'); ?></label>
<input type="text" name="words" class="formfield" size="20" id="words" value="<?php echo htmlspecialchars($_GET['words'], ENT_QUOTES); ?>" title="<?php echo _AT('enter_search_terms'); ?>"/><br />
<fieldset>
<legend style="display:none;"><?php echo _AT('search'); ?></legend>
<small>
	<?php echo _AT('search_match'); ?>:<br />
	<input type="radio" name="include" value="all" id="all2"<?php echo $include_all; ?> /><label for="all2"><?php echo _AT('search_all_words'); ?></label><br />

	<input type="radio" name="include" value="one" id="one2"<?php echo $include_one; ?> /><label for="one2"><?php echo _AT('search_any_word'); ?></label><br />
</small>
</fieldset>
<input type="submit" name="submit" value="<?php echo _AT('search'); ?>" class="button" />
</form>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('search'));

$savant->display('include/box.tmpl.php');
?>