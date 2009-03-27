<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');


// Get activities	
$act_obj = new Activity();
$activities = $act_obj->getActivities($id);

// Get social group class
$social_groups = new SocialGroups();
$my_groups = $social_groups->getMemberGroups($_SESSION['member_id']);


if (isset($_POST['create'])){
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
		$isSucceded = $social_groups->addGroup($_POST['group_type'], $_POST['group_name'], $_POST['description'], $_POST['logo']);

		if($isSucceded){
			$msg->addFeedback('GROUP_CREATED');
			header('Location: index.php');
		} else {
			//Something went bad in the backend, contact admin?
			$msg->addError('GROUP_CREATION_FAILED');
		}
	}
}

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('my_groups', $my_groups);
$savant->assign('group_types', $social_groups->getAllGroupType());
$savant->display('sgroup_create.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>