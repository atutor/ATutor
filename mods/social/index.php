<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/Applications.class.php');
$_custom_css = $_base_path . 'mods/social/module.css'; // use a custom stylesheet

//Handles search queries from side menu
if (isset($_GET['searchFriends']) && $_GET['friendsName']!=''){
	$wanted = $addslashes($_GET['friendsName']);
	$friends = searchFriends($wanted, true);
}

//Handles remove request
if (isset($_GET['remove'])){
	$id = intval($_GET['id']);
//	if (isset($_GET['confirm_remove'])){
		removeFriend($id);
		header('Location: '.url_rewrite('mods/social/index.php', AT_PRETTY_URL_IS_HEADER));
		exit;
//	}
//	$msg->addConfirm("are_you_sure?");
//	header('Location: '.url_rewrite('mods/social/index.php?remove=yes'.SEP.'id='.$id.SEP.'confirm_remove=yes'));
}

//Handles request approval, and rejection
if (isset($_GET['approval'])){
	$id = intval($_GET['id']);
	if ($_GET['approval'] == 'y'){
		approveFriendRequest($id);
	} elseif ($_GET['approval'] == 'n'){
		rejectFriendRequest($id);
	}
	header('Location: '.url_rewrite('mods/social/index.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

include (AT_INCLUDE_PATH.'header.inc.php'); ?>

<div>
	<div style="float:left;">
		<?php
			//network updates
			$actvity_obj = new Activity();
			$savant->assign('activities', $actvity_obj->getFriendsActivities($_SESSION['member_id']));
			$savant->display('activities.tmpl.php');

			//applications/gagdets
			$applications_obj = new Applications();
			$savant->assign('list_of_my_apps', $applications_obj->listMyApplications());
			$savant->display('tiny_applications.tmpl.php');
//			echo '<div class="gadget_wrapper">';
//			echo '<div class="gadget_title_bar">Applications</div>';
//			echo '<div class="gadget_container">TODO: GADGETS/Applications</div>';
//			echo '</div>';
		?>
	</div>
	<div id="box" style="float:left; margin-left:1em;">
		<?php			
			//if friends array is not empty.
			if (!empty($friends)){
				$savant->assign('friends', $friends);
			} else {
				$savant->assign('friends', getFriends($_SESSION['member_id']));
			}
			$savant->assign('pendingRequests', getPendingRequests());			
			$savant->display('friend_list.tmpl.php'); 
		?>
	</div>
</div>

<?php include (AT_INCLUDE_PATH.'footer.inc.php'); ?>
