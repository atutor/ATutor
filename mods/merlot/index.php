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

$merlot_key = $_config['merlot_key'];
//$merlot_location =$_config['merlot_location'];

$merlot_location = "http://www.merlot.org/merlot/materials.rest";
//global $search;
$advanced = intval($advanced);
$browse =  intval($browse);

// See if merlot is configured
if(!$_config['merlot_key'] || !$_config['merlot_location']){
	$msg->addError('MERLOT_NOT_CONFIG');
}


if($msg->containsErrors()){
	require (AT_INCLUDE_PATH.'header.inc.php');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}else{

// If Merlot is configured, display the simple search form, and results
	require (AT_INCLUDE_PATH.'header.inc.php');





?>
<script type="text/javascript" language="JavaScript" src="<?php echo $_base_path; ?>mods/merlot/merlot.js"></script>



<?php

if($_REQUEST['advanced']){
		require (AT_INCLUDE_PATH.'../mods/merlot/merlot_adv.php');

}else{?>
	<!-- form action="<?php echo $_SERVER['PHP_SELF']; ?>#search_results" method="get" name="form" -->
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>#search_results" method="get" name="form">
		<div class="input-form" style="width: 40%;padding:5px;">

			<div>
			<img src="<?php echo $_base_path; ?>mods/merlot/merlot.gif" height="50" width="50" style="margin-right:3px;float:left;text-align:right;" alt="<?php  echo _AT('merlot'); ?>" />
			<?php  echo _AT('merlot_howto'); ?>
			</div>
			<div class="row">

					<label for="words2"><?php echo _AT('search_words'); ?></label><br />
					<input type="hidden" name="licenseKey" value="PZ8SRMDOFKA3I2DQF7ODJ9BUBJX7BPWGRQP" />
					<input type="text" name="keywords" size="40" id="words2" value="<?php echo stripslashes(htmlspecialchars($_GET['keywords'])); ?>" /><br />
<input type="hidden" name="totalCountOnly" value="20"/>
					<fieldset><legend>Filters</legend>

<input type="radio" name="allKeyWords" value="" id="allKeyWords"/><label for="allKeyWords">All Words</label><br />
<input type="radio" name="anyKeyWords" value="" id="anyKeyWords" /><label for="anyKeyWords">Any Word</label><br />
<input type="radio" name="exactPhraseKeyWords" value=""  id="exactPhraseKeyWords" /><label for="exactPhraseKeyWords">Exact Phrase</label><br />
<input type="radio" name="title" value=""  id="title" /><label for="title">Title</label><br />
<input type="radio" name="description" value=""  id="description" /><label for="description">Description</label><br />
<input type="radio" name="creativeCommons" value=""  id="creativeCommons" /><label for="creativeCommons">Creative Common</label><br />
<input type="radio" name="" value="author"  id="author" /><label for="author">Author</label><br />
					</fieldset>
			</div>
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
<iframe src="http://localhost/atutorsvn/mods/merlot/test.xml" width="90%">

</iframe>

<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
?>