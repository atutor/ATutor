<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008                                      */
/* Written by Greg Gay, Joel Kronenberg & Chris Ridpath         */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_GET['submit'])) { // was the 'back' button pressed?
	header('Location: '.url_rewrite('mods/_standard/reading_list/index.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$id = intval ($_GET['id']);

$sql = "SELECT * FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] AND resource_id=$id";
$result = mysql_query($sql, $db);
if (!$row = mysql_fetch_assoc($result)) {
	// can't get resource from database
	$msg->addError('ITEM_NOT_FOUND');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$row['type']		= intval($row['type']);
$row['title']		= htmlentities_utf8($row['title']);
$row['author']		= htmlentities_utf8($row['author']);
$row['publisher']	= htmlentities_utf8($row['publisher']);
$row['date']		= htmlentities_utf8($row['date']);
$row['id']			= htmlentities_utf8($row['id']);
$row['comments']	= htmlentities_utf8($row['comments']);

?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get" name="form">
<div class="input-form">

	<?php if ($row['type'] == RL_TYPE_BOOK): ?>
		<div class="row">
			<?php  echo _AT('title'). ": <strong>". $row['title']. "</strong>"; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('rl_type_of_resource'). ": ". _AT($_rl_types[$row['type']]); ?> 
		</div>
		<div class="row">
			<?php  echo _AT('author'). ": ". $row['author']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('rl_publisher'). ": ". $row['publisher']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('date'). ": ". $row['date']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('rl_isbn_number'). ": ". $row['id']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('comment'). ": ". $row['comments']; ?> 
		</div>

	<?php elseif ($row['type'] == RL_TYPE_URL): ?>
		<div class="row">
			<?php  echo _AT('title'). ": <strong>". $row['title']. "</strong>"; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('rl_type_of_resource'). ": ". _AT($_rl_types[$row['type']]); ?> 
		</div>
		<div class="row">
			<?php echo _AT('location'). ": " ?><a href="<?php echo $row['url']?>"><?php echo $row['url']; ?></a> 
		</div>
		<div class="row">
			<?php  echo _AT('author'). ": ". $row['author']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('comment'). ": ". $row['comments']; ?> 
		</div>

	<?php elseif ($row['type'] == RL_TYPE_HANDOUT): ?>
		<div class="row">
			<?php  echo _AT('title'). ": <strong>". $row['title']. "</strong>"; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('rl_type_of_resource'). ": ". _AT($_rl_types[$row['type']]); ?> 
		</div>
		<div class="row">
			<?php  echo _AT('author'). ": ". $row['author']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('date'). ": ". $row['date']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('comment'). ": ". $row['comments']; ?> 
		</div>

	<?php elseif ($row['type'] == RL_TYPE_AV): ?>
		<div class="row">
			<?php  echo _AT('title'). ": <strong>". $row['title']. "</strong>" ; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('rl_type_of_resource'). ": ". _AT($_rl_types[$row['type']]); ?> 
		</div>
		<div class="row">
			<?php  echo _AT('author'). ": ". $row['author']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('date'). ": ". $row['date']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('comment'). ": ". $row['comments']; ?> 
		</div>

	<?php elseif ($row['type'] == RL_TYPE_FILE): ?>
		<div class="row">
			<?php  echo _AT('title'). ": <strong>". $row['title']. "</strong>"; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('rl_type_of_resource'). ": ". _AT($_rl_types[$row['type']]); ?> 
		</div>
		<div class="row">
			<?php  echo _AT('author'). ": ". $row['author']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('rl_publisher'). ": ". $row['publisher']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('date'). ": ". $row['date']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('rl_id'). ": ". $row['id']; ?> 
		</div>
		<div class="row">
			<?php  echo _AT('comment'). ": ". $row['comments']; ?> 
		</div>
	<?php endif; ?>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('back'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>