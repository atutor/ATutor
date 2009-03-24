<?php 
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
	echo 'You don\'t have any friends in your network yet, <a href="'.url_rewrite('mods/social/add_friends.php').'">click here</a> to start adding friends to your networking';
}
*/
//search box
?>

<ul class="social_side_menu">
	<li><a href="<?php echo url_rewrite('mods/social/index.php', AT_PRETTY_URL_HEADER); ?>">Home</a></li>
	<li><a href="<?php echo url_rewrite('mods/social/add_friends.php', AT_PRETTY_URL_HEADER); ?>">Contacts</a></li>
	<li><a href="<?php echo url_rewrite('mods/social/sprofile.php', AT_PRETTY_URL_HEADER); ?>">My Social Profile</a></li>
	<li><a href="<?php echo url_rewrite('mods/social/applications.php', AT_PRETTY_URL_HEADER); ?>">Applications</a></li>
	<li><a href="<?php echo url_rewrite('mods/social/groups/index.php', AT_PRETTY_URL_HEADER); ?>">Social Groups</a></li>
</ul>

<div class="divider"></div>
<div><?php echo _AT('applications'); ?></div>
<?php 
	$applications_obj = new Applications();
	$myApplications = $applications_obj->listMyApplications();
?>
<ul class="social_side_menu">
	<?php 
	foreach ($myApplications as $id=>$app_obj){
		echo '<li><a href="'.url_rewrite('mods/social/applications.php?app_id='.$id, AT_PRETTY_URL_HEADER).'">'.$app_obj->title.'</a></li>';
	}
	?>
</ul>

<div class="divider"></div>
<ul class="social_side_menu">
	<li><a href="<?php echo url_rewrite('mods/social/privacy_settings.php', AT_PRETTY_URL_HEADER); ?>"><?php echo _AT('settings'); ?></a></li>
</ul>

<form action="<?php echo url_rewrite('mods/social/add_friends.php', AT_PRETTY_URL_HEADER);?>" method="POST">
	<input type="text" name="searchFriends" value="<?php echo urldecode($_POST['searchFriends']); ?>"/>
	<input type="submit" name="search" value="<?php echo _AT('search'); ?>" class="button" />
</form>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('social')); // the box title
$savant->display('include/box.tmpl.php');
?>