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
// $Id: scaffolds.php 10142 2010-08-17 19:17:26Z hwong $ $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_ADMIN);


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'mods/_core/courses/admin/courses.php');
	exit;
}

$_POST['encyclopedia'] == $addslashes($_POST['encyclopedia']);
$_POST['dictionary'] == $addslashes($_POST['dictionary']);
$_POST['thesaurus'] == $addslashes($_POST['thesaurus']);
$_POST['atlas'] == $addslashes($_POST['atlas']);
$_POST['calculator'] == $addslashes($_POST['calculator']);
$_POST['abacus'] == $addslashes($_POST['abacas']);
$_POST['note_taking'] == $addslashes($_POST['note_taking']);

if (isset($_POST['submit'])) {
	foreach ($_POST as $key => $value){
		if($key != "submit"){
		$sql    = "REPLACE INTO ".TABLE_PREFIX."config VALUES('$key', '$value')";
		$result = mysql_query($sql, $db);
		};
	}
		
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location:'. $_SERVER[PHP_SELF]);
	exit;

}
require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="scaffolds">
<div class="input-form">
 <fieldset class="group_form"> <legend class="group_form"><strong><?php echo _AT("support_tools"); ?></strong>  </legend>  
	<div class="row">
		<p><?php echo _AT('scaffold_text'); ?></p>
	</div>
	<div class="row">

		<label for="encyclopedia"><?php echo _AT('encyclopedia'); ?></label><br /><input type="text" id="encyclopedia"  name="encyclopedia" value="<?php echo $_config['encyclopedia']; ?>"  size="60"/><br />
		<label for="dictionary"><?php echo _AT('dictionary'); ?></label><br /><input type="text" id="dictionary"  name="dictionary" value="<?php echo $_config['dictionary']; ?>"  size="60"/><br />
		<label for="thesaurus"><?php echo _AT('thesaurus'); ?></label><br /><input type="text" id="thesaurus"  name="thesaurus" value="<?php echo $_config['thesaurus']; ?>" size="60"/><br />
		<label for="atlas"><?php echo _AT('atlas'); ?></label><br /><input type="text" id="atlas"  name="atlas" value="<?php echo $_config['atlas']; ?>"  size="60"/><br />
		<label for="calculator"><?php echo _AT('calculator'); ?></label><br /><input type="text" id="calculator"  name="calculator" value="<?php echo $_config['calculator']; ?>"  size="60"/><br />
		<label for=""><?php echo _AT('note_taking'); ?></label><br /><input type="text" id="note_taking"  name="note_taking" value="<?php echo $_config['note_taking']; ?>"  size="60"/>	<br />
		<label for="abacas"><?php echo _AT('abacus'); ?></label><br /><input type="text" id="abacas"  name="abacas" value="<?php echo $_config['abacas']; ?>"  size="60"/><br />
	</div>
	<div class="buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
	</div>
</fieldset>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>