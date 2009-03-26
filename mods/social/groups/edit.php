<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');

// Get social group class
$social_groups = new SocialGroups();

// Get this group
$id = intval($_REQUEST['id']);  //make sure $_GET and $_POST don't overlap the use of 'id'

//validate if this script is being run by the group admin
//validate the group_admin is indeed a group member
//TODO

// Update group
if (isset($_POST['save'])){
	//check if fields are empty
	if ($_POST['group_name']==''){
		$missing_fields[] = _AT('group_name');
	} elseif (intval($_POST['group_type'])<=0){
		$missing_fields[] = _('group_type');
	}
	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	} else {
		$isSucceded = $social_groups->updateGroup($id, $_POST['group_admin'], $_POST['group_type'], $_POST['group_name'], $_POST['description'], $_POST['logo']);

		if($isSucceded){
			$msg->addFeedback('group_updated');
			header('Location: '.url_rewrite('mods/social/groups/index.php', AT_PRETTY_URL_HEADER));
			exit;
		} else {
			//Something went bad in the backend, contact admin?
			$msg->addFeedback('group_edit_failed');
		}
	}
}

$group = new SocialGroup($id);

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('group_obj', $group);
$savant->assign('group_types', $social_groups->getAllGroupType());
$savant->display('sgroup_edit.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>