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

//$_custom_css = $_base_path . 'mods/merlot/module.css'; // use a custom stylesheet

$add_to_links = intval($_GET['add_to_links']);
$title = stripslashes(htmlspecialchars($_GET['title']));
$description = stripslashes(htmlspecialchars($_GET['desc']));
$url = stripslashes(htmlspecialchars($_GET['url']));

require (AT_INCLUDE_PATH.'lib/links.inc.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'mods/merlot/index.php');
	exit;
}else if (isset($_POST['add_link']) && isset($_POST['submit'])) {
	$missing_fields = array();
	if ($_POST['cat'] == 0 || $_POST['cat'] == '') {
		$missing_fields[] = _AT('category');
	}
	if (trim($_POST['title']) == '') {
		$missing_fields[] = _AT('title');
	}
	if (trim($_POST['url']) == '' || $_POST['url'] == 'http://') {
		$missing_fields[] = _AT('url');
	}
	if (trim($_POST['description']) == '') {
		$missing_fields[] = _AT('description');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors() && isset($_POST['submit'])) {

		$_POST['cat'] = intval($_POST['cat']);
		$_POST['title']  = $addslashes($_POST['title']);
		$_POST['url'] == $addslashes($_POST['url']);
		$_POST['description']  = $addslashes($_POST['description']);

		$name = get_display_name($_SESSION['member_id']);
		$email = '';

		// approve link if submitter is a group member or instructor
		$sql = "SELECT * from ".TABLE_PREFIX."links_categories WHERE  cat_id='$_POST[cat]' AND owner_type='2' ";
		$result = mysql_query($sql, $db);

		while($row = mysql_fetch_assoc($result)){
			$sql2 = "SELECT * from ".TABLE_PREFIX."groups_members WHERE member_id= '$_SESSION[member_id]' AND group_id = '$row[owner_id]' ";

 			if($result2 = mysql_query($sql2, $db)){
 				$group_member = true;
  			}
		}

		if($_SESSION['is_admin']){
			$approved = 1;		//approved for instructor submissions
		}else if($group_member){
			$approved = 1;  		//approved for group member submissions to group links
		}else if(authenticate(AT_PRIV_LINKS, true)){
			$approved = 1;  		//approved for privileged user submissions
		}else{
			$approved = 0;		//not approved for student submissions to course links
		}
		/////
		$sql	= "INSERT INTO ".TABLE_PREFIX."links VALUES (NULL, $_POST[cat], '$_POST[url]', '$_POST[title]', '$_POST[description]', $approved, '$name', '$email', NOW(), 0)";
		mysql_query($sql, $db);
	
		$msg->addFeedback('LINK_ADDED');
		header('Location: '.$_base_href.'mods/merlot/index.php');
		exit;
	} else {
		$_POST['title']  = stripslashes($_POST['title']);
		$_POST['url'] == stripslashes($_POST['url']);
		$_POST['description']  = stripslashes($_POST['description']);
	}
}

$onload = 'document.form.title.focus();';
$categories = get_link_categories();

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<div>
<h3><?php echo _AT('merlot_add_link'); ?></h3>

</div>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>#search_results" method="post" name="form">
		<div class="input-form" style="width: 60%;padding:5px;">
			<div>
			<?php echo _AT('merlot_add_to_link_howto'); ?>
			</div>
			<div class="row">
				<input type="hidden" name="add_link" value="true" />
				<label for="title"><?php echo _AT('merlot_title'); ?></label><br />
				<input type="text" id="title" name="title"  size="70" value="<?php  echo $title; ?>" /><br />

				<label for="description"><?php echo _AT('merlot_description'); ?></label><br />
				<textarea id="description" name="description" rows="4" cols="60"><?php  echo $description; ?></textarea><br />
				<label for="url"><?php echo _AT('merlot_url'); ?></label>	<br />
				<input type="text" id="url" name="url" size="70" value="<?php echo $url;  ?>" /><br />
				<label for="cat"><?php echo _AT('merlot_category'); ?></label><br />
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
	
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('merlot_submit'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('merlot_cancel'); ?> " />
	</div>
		</div>
	</form>


<br /> 

<?php

require(AT_INCLUDE_PATH.'footer.inc.php');

?>