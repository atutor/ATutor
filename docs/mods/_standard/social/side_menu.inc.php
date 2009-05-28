<?php 
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: side_menu.inc.php 8485 2009-05-25 20:50:55Z hwong $

global $savant, $_config, $stripslashes;

require_once('lib/friends.inc.php');
require_once('lib/classes/Application.class.php');
/* start output buffering: */
ob_start(); ?>

<?php
//Get the list of friends.
/*
$list_of_friends = getFriends($_SESSION['member_id']);
if (sizeof($list_of_friends) > 0){
	foreach ($list_of_friends as $id){
		//print links of friends
		//make a size limit so the rest will be "..."
	}
} else {
	echo 'You don\'t have any friends in your network yet, <a href="'.url_rewrite(AT_SOCIAL_BASENAME.'connections.php').'">click here</a> to start adding friends to your networking';
}
*/
//search box
?>

<ul class="social_side_menu">
	<li><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'index.php', AT_PRETTY_URL_HEADER); ?>"><?php echo _AT('network_home'); ?></a></li>
	<li><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'connections.php', AT_PRETTY_URL_HEADER); ?>"><?php echo _AT('connections'); ?></a></li>
	<li><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'sprofile.php', AT_PRETTY_URL_HEADER); ?>"><?php echo _AT('social_profile'); ?></a></li>
	<li><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'applications.php', AT_PRETTY_URL_HEADER); ?>"><?php echo _AT('applications'); ?></a></li>
	<li><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'groups/index.php', AT_PRETTY_URL_HEADER); ?>"><?php echo _AT('social_groups'); ?></a></li>
	<li><a href="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'settings.php', AT_PRETTY_URL_HEADER); ?>"><?php echo _AT('settings'); ?></a></li>
</ul>

<?php
$applications_obj = new Applications();
$myApplications = $applications_obj->listMyApplications();
 if (!empty($myApplications)): ?>
<div class="divider"></div>
<div><?php echo _AT('applications'); ?></div>
<ul class="social_side_menu">
	<?php 
	foreach ($myApplications as $id=>$app_obj){
		echo '<li><a href="'.url_rewrite(AT_SOCIAL_BASENAME.'applications.php?app_id='.$id, AT_PRETTY_URL_HEADER).'">'.$app_obj->title.'</a></li>';
	}
	?>
</ul>
<?php endif; ?>
<div class="divider"></div>

<form action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'connections.php', AT_PRETTY_URL_HEADER);?>" method="POST">
	<input type="text" name="search_friends_123" value="<?php echo urldecode($_POST['searchFriends']); ?>" title="<?php echo _AT('search_for_friends'); ?>" />
	<input type="hidden" name="rand_key" value="123"/>
	<input type="submit" name="search" value="<?php echo _AT('search_for_friends'); ?>" class="button" />
</form>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('social')); // the box title
$savant->display('include/box.tmpl.php');
?>