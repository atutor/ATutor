<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_LINKS);

require (AT_INCLUDE_PATH.'lib/links.inc.php');

if (!isset($_POST['url'])) {
	$_POST['url'] = "http://";
}

if (!isset($_POST['approved'])) {
	$_POST['approved'] = 1;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'tools/links/index.php');
	exit;
} 

if (isset($_POST['add_link']) && isset($_POST['submit'])) {

	if ($_POST['title'] == '') {		
		$msg->addError('TITLE_EMPTY');
	}
	if ($_POST['url'] == '') {		
		$msg->addError('URL_EMPTY');
	}
	if ($_POST['description'] == '') {		
		$msg->addError('DESCRIPTION_EMPTY');
	}

	if (!$msg->containsErrors() && isset($_POST['submit'])) {

		$_POST['cat'] = intval($_POST['cat']);
		$_POST['title']  = $addslashes($_POST['title']);
		$_POST['url'] == $addslashes($_POST['url']);
		$_POST['description']  = $addslashes($_POST['description']);

		$name = $_SESSION['login'];
		$email = '';

		$approved = 1; //auto-approve instructor submitted links

		$sql	= "INSERT INTO ".TABLE_PREFIX."resource_links VALUES (0, $_POST[cat], '$_POST[url]', '$_POST[title]', '$_POST[description]', $approved, '$name', '$email', NOW(), 0)";
		mysql_query($sql, $db);
	
		$msg->addFeedback('LINK_ADDED');

		header('Location: '.$_base_href.'tools/links/index.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

$categories = get_link_categories();

$msg->printErrors();

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_link" value="true" />

<div class="input-form">
	<div class="row">
		<label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" size="40" id="title" value="<?php echo $_POST['title']; ?>"/>
	</div>

	<div class="row">
		<label for="cat"><?php echo _AT('category'); ?></label><br />
		<select name="cat" id="cat"><?php
			if ($pcat_id) {
				$current_cat_id = $pcat_id;
				$exclude = false; /* don't exclude the children */
			} else {
				$current_cat_id = $cat_id;
				$exclude = true; /* exclude the children */
			}
			select_link_categories($categories, 0, $_POST['cat'], FALSE);
			?>
		</select>
	</div>
	
	<div class="row">
		<label for="url"><?php echo _AT('url'); ?></label><br />
		<input type="text" name="url" size="40" id="url" value="<?php echo $_POST['url']; ?>" />
	</div>

	<div class="row">
		<label for="description"><?php echo _AT('description'); ?></label><br />
		<textarea name="description" cols="55" rows="5" id="description" ><?php echo $_POST['description']; ?></textarea>
	</div>

	<div class="row">
		<?php echo _AT('approve'); ?><br />
		<?php
			if ($_POST['approved']) {
				$y = 'checked="checked"';
				$n = '';
			} else {
				$n = 'checked="checked"';
				$y = '';
			}
		?>
		<input type="radio" id="yes" name="approved" value="1" <?php echo $y; ?>><label for="yes"><?php echo _AT('yes1'); ?></label>  <input type="radio" id="no" name="approved" value="0" <?php echo $n; ?>><label for="no"><?php echo _AT('no1'); ?></label>
	</div>
	
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?> " />
	</div>
</div>
</form>

<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
?>
