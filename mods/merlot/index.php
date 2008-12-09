<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: merlot.php 6614 2006-09-27 19:32:29Z greg $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/merlot/module.css'; // use a custom stylesheet
//$default_results_per_page = 25;
$default_results_per_page = 10;

$advanced = intval($advanced);
$browse =  intval($browse);

if (!isset($_REQUEST["search_type"])) $_REQUEST["search_type"] = 0;
if (!isset($_REQUEST["results_per_page"])) $_REQUEST["results_per_page"] = $default_results_per_page;

// check if merlot is configured
if(!isset($_config['merlot_key']) || !isset($_config['merlot_location']))
{
	$msg->addError('MERLOT_NOT_CONFIG');

	require (AT_INCLUDE_PATH.'header.inc.php');
	$msg->printAll();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
else
{
// If Merlot is configured, display the simple search form, and results
require (AT_INCLUDE_PATH.'header.inc.php');
?>
<script type="text/javascript" language="JavaScript" src="<?php echo $_base_path; ?>mods/merlot/merlot.js"></script>

<?php

if($_REQUEST['advanced']){
		require (AT_INCLUDE_PATH.'../mods/merlot/merlot_adv.php');

}else{?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>#search_results" method="post" name="form">
		<div class="input-form">

			<div style="padding:1em;">
			<a href="http://www.merlot.org">
				<img src="<?php echo $_base_path; ?>mods/merlot/merlotlogo.gif" height="53" width="187" style="float:right;text-align:right;border:0;" alt="<?php  echo _AT('merlot'); ?>" />
			</a>
			<?php  echo _AT('merlot_howto'); ?>
			</div>
			<table width="100%">
				<tr>
					<td colspan="2">
						<input type="radio" name="search_type" value="0" id="allKeyWords" <?php if ($_REQUEST["search_type"] == 0) echo 'checked="checked"'; ?> /><label for="allKeyWords">All Words</label>
						<input type="radio" name="search_type" value="1" id="anyKeyWords" <?php if ($_REQUEST["search_type"] == 1) echo 'checked="checked"'; ?> /><label for="anyKeyWords">Any Word</label>
						<input type="radio" name="search_type" value="2"  id="exactPhraseKeyWords" <?php if ($_REQUEST["search_type"] == 2) echo 'checked="checked"'; ?> /><label for="exactPhraseKeyWords">Exact Phrase</label>
					</td>
				</tr>
					
				<tr>
					<td width="20%"><label for="words2"><?php echo _AT('keywords'); ?></label></td>
					<td><input type="text" name="keywords" size="100" id="words2" value="<?php echo $_REQUEST['keywords']; ?>" /></td>
				</tr>

				<tr>
					<td><label for="title"><?php echo _AT('title'); ?></label></td>
					<td><input type="text" name="title" size="100" id="title" value="<?php echo $_REQUEST['title']; ?>" /></td>
				</tr>

				<tr>
					<td><label for="description"><?php echo _AT('description'); ?></label></td>
					<td><input type="text" name="description" size="100" id="description" value="<?php echo $_REQUEST['description']; ?>" /></td>
				</tr>

				<tr>
					<td><label for="author"><?php echo _AT('author'); ?></label></td>
					<td><input type="text" name="author" size="100" id="author" value="<?php echo $_REQUEST['author']; ?>" /></td>
				</tr>

				<tr>
					<td colspan="2">
						<input type="checkbox" name="creativeCommons" value="true" id="creativeCommons" <?php if ($_REQUEST["creativeCommons"] == "true") echo 'checked="checked"'; ?> /><label for="creativeCommons"><?php echo _AT("merlot_creative_commons"); ?></label>
					</td>
				</tr>
<!--
				<tr>
					<td colspan="2">
						<label for="results_per_page"><?php echo _AT('merlot_results_per_page'); ?></label>
						<select name="results_per_page">
							<option value="5" <?php if ($_REQUEST["results_per_page"] == 5) echo 'selected="selected"' ?>>5</option>
							<option value="10" <?php if ($_REQUEST["results_per_page"] == 10) echo 'selected="selected"' ?>>10</option>
							<option value="15" <?php if ($_REQUEST["results_per_page"] == 15) echo 'selected="selected"' ?>>15</option>
							<option value="20" <?php if ($_REQUEST["results_per_page"] == 20) echo 'selected="selected"' ?>>20</option>
							<option value="25" <?php if ($_REQUEST["results_per_page"] == 25) echo 'selected="selected"' ?>>25</option>
						</select>
					</td>
				</tr>
//-->
			</table>
			
			<div class="row buttons">
					<input type="submit" name="submit" value="<?php echo _AT('merlot_search'); ?>" />
			</div>
		</div>
	</form>
<?php } ?>

<div align="center">
	<small>
		<a href="http://about.merlot.org/wsrs.html"><?php echo _AT('merlot_web_service_agreement'); ?></a>&nbsp;
		<a href="http://taste.merlot.org/acceptableuserpolicy.html"><?php echo _AT('merlot_user_agreement'); ?></a>
	</small>
</div>

<br /> 

<?php
	if ($_REQUEST['submit'] || isset($_REQUEST['p']))
	{
		require(AT_INCLUDE_PATH.'../mods/merlot/merlot_rest.php');
	}

}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>
