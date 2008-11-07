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
$default_num_of_results = 25;

//global $search;
$advanced = intval($advanced);
$browse =  intval($browse);

if (!isset($_POST["search_type"])) $_POST["search_type"] = 0;
if (!isset($_POST["num_of_results"])) $_POST["num_of_results"] = $default_num_of_results;

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

			<div>
			<img src="<?php echo $_base_path; ?>mods/merlot/merlot.gif" height="50" width="50" style="margin-right:3px;float:left;text-align:right;" alt="<?php  echo _AT('merlot'); ?>" />
			<?php  echo _AT('merlot_howto'); ?>
			</div>
			<table>
				<tr>
					<td colspan="2">
						<input type="radio" name="search_type" value="0" id="allKeyWords" <?php if ($_POST["search_type"] == 0) echo 'checked="checked"'; ?> /><label for="allKeyWords">All Words</label>
						<input type="radio" name="search_type" value="1" id="anyKeyWords" <?php if ($_POST["search_type"] == 1) echo 'checked="checked"'; ?> /><label for="anyKeyWords">Any Word</label>
						<input type="radio" name="search_type" value="2"  id="exactPhraseKeyWords" <?php if ($_POST["search_type"] == 2) echo 'checked="checked"'; ?> /><label for="exactPhraseKeyWords">Exact Phrase</label>
					</td>
				</tr>
					
				<tr>
					<td><label for="words2"><?php echo _AT('keywords'); ?></label></td>
					<td><input type="text" name="keywords" size="100" id="words2" value="<?php echo $_POST['keywords']; ?>" /></td>
				</tr>

				<tr>
					<td><label for="title"><?php echo _AT('title'); ?></label></td>
					<td><input type="text" name="title" size="100" id="title" value="<?php echo $_POST['title']; ?>" /></td>
				</tr>

				<tr>
					<td><label for="description"><?php echo _AT('description'); ?></label></td>
					<td><input type="text" name="description" size="100" id="description" value="<?php echo $_POST['description']; ?>" /></td>
				</tr>

				<tr>
					<td><label for="author"><?php echo _AT('author'); ?></label></td>
					<td><input type="text" name="author" size="100" id="author" value="<?php echo $_POST['author']; ?>" /></td>
				</tr>

				<tr>
					<td colspan="2">
						<input type="checkbox" name="creativeCommons" value="true" id="creativeCommons" <?php if ($_POST["creativeCommons"] == "true") echo 'checked="checked"'; ?> /><label for="creativeCommons"><?php echo _AT("merlot_creative_commons"); ?></label>
					</td>
				</tr>

				<tr>
					<td colspan="2">
						<label for="num_of_results"><?php echo _AT('merlot_num_of_results'); ?></label>
						<input type="text" name="num_of_results" size="20" id="num_of_results" value="<?php echo $_POST['num_of_results']; ?>" />
					</td>
				</tr>
			</table>
			
			<div class="row buttons">
					<input type="submit" name="submit" value="<?php echo _AT('merlot_search'); ?>" />
			</div>
		</div>
	</form>
<?php } ?>


<br /> 

<?php
	//if ($_REQUEST['submit']){
	//	require(AT_INCLUDE_PATH.'../mods/merlot/merlot_soap.php');
	//}
	if ($_REQUEST['submit']){
		require(AT_INCLUDE_PATH.'../mods/merlot/merlot_rest.php');
	}

}
?>
<!--
<iframe src="http://localhost/docs/content/tmp_merlot_results.xml" width="90%">

</iframe>
//-->

<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
?>