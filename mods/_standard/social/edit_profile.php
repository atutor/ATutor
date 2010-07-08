<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
$_custom_css = $_base_path . AT_SOCIAL_BASENAME . 'module.css'; // use a custom stylesheet

if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

// Initiates member
$member = new Member($_SESSION['member_id']);

// Handles social profile 
if ($_POST['social_profile']){
	//update database from here
	header('Location: sprofile.php');
}


// Handles Saving
if (isset($_POST['submit'])){
	//where was this request sent from
	if (isset($_POST['edit'])){
			$id		 = intval($_POST['id']);
		if ($_POST['edit']=='position'){
			$company	 = $_POST['company'];
			$title		 = $_POST['title'];
			$from		 = $_POST['from'];
			$to			 = $_POST['to'];
			$description = $_POST['description'];
			$member->updatePosition($id, $company, $title, $from, $to, $description);			
		} elseif ($_POST['edit']=='education'){
			$university	 = $_POST['university'];
			$country	 = $_POST['country'];
			$province	 = $_POST['province'];
			$degree		 = $_POST['degree'];
			$field		 = $_POST['field'];
			$from		 = $_POST['from'];
			$to			= $_POST['to'];
			$description = $_POST['description'];
			$member->updateEducation($id, $university, $from, $to, $country, $province, $degree, $field, $description);
		} elseif ($_POST['edit']=='websites'){
			$url		= $_POST['url'];
			$site_name	= $_POST['site_name'];
			$member->updateWebsite($id, $url, $site_name);
		} elseif ($_POST['edit']=='interests' || $_POST['edit']=='associations' || $_POST['edit']=='awards' || $_POST['edit']=='expertise' 
					|| $_POST['edit']=='others'){
			$interests		= isset($_POST['interests'])&&$_POST['interests']==''? _AT('na'): $_POST['interests'];
			$associations	= isset($_POST['associations'])&&$_POST['associations']==''? _AT('na'): $_POST['associations'];
			$awards			= isset($_POST['awards'])&&$_POST['awards']==''? _AT('na'): $addslashes($_POST['awards']);
			$expertise		= isset($_POST['expertise'])&&$_POST['expertise']==''? _AT('na'): $_POST['expertise'];
			$others			= isset($_POST['others'])&&$_POST['others']==''? _AT('na'): $_POST['others'];
			$member->updateAdditionalInformation($interests, $associations, $awards, $expertise, $others);
		} elseif ($_POST['edit']=='representation'){
			$rep_name 		= $_POST['rep_name'];
			$rep_title		= $_POST['rep_title'];
			$rep_phone		= $_POST['rep_phone'];
			$rep_email		= $_POST['rep_email'];
			$rep_address		= $_POST['rep_address'];
			$member->updateRepresentation($id, $rep_name, $rep_title, $rep_phone, $rep_email, $rep_address);
		} elseif ($_POST['edit']=='contact'){
			$rep_name 		= $_POST['con_name'];
			$rep_phone		= $_POST['con_phone'];
			$rep_email		= $_POST['con_email'];
			$rep_address		= $_POST['con_address'];
			$member->updateContact($id, $con_name, $con_phone, $con_email, $con_address);
		} elseif ($_POST['edit']=='personal'){
			$per_weight		= $_POST['per_weight'];
			$per_height		= $_POST['per_height'];
			$per_hair		= $_POST['per_hair'];
			$per_eyes 		= $_POST['per_eyes'];
			$per_ethnicity		= $_POST['per_ethnicity'];
			$per_languages		= $_POST['per_languages'];
			$per_disabilities	= $_POST['per_disabilities'];
			$member->updatePersonal($per_weight, $per_height, $per_hair, $per_eyes, $per_ethnicity, $per_languages, $per_disabilities);
		}
	} 
	elseif (isset($_POST['add'])) {
		if ($_POST['add']=='position'){
			$company	 = $_POST['company'];
			$title		 = $_POST['title'];
			$from		 = $_POST['from'];
			$to			 = $_POST['to'];
			$description = $_POST['description'];
			$member->addPosition($company, $title, $from, $to, $description);
		} elseif ($_POST['add']=='education'){
			$university	 = $_POST['university'];
			$country	 = $_POST['country'];
			$province	 = $_POST['province'];
			$degree		 = $_POST['degree'];
			$field		 = $_POST['field'];
			$from		 = $_POST['from'];
			$to			 = $_POST['to'];
			$description = $_POST['description'];
			$member->addEducation($university, $from, $to, $country, $province, $degree, $field, $description);
		} elseif ($_POST['add']=='websites'){
			$url		= $_POST['url'];
			$site_name	= $_POST['site_name'];
			$member->addWebsite($url, $site_name);
		} elseif ($_POST['add']=='interests'){
			$interests	= $_POST['interests'];
			$member->addInterests($interests);
		} elseif ($_POST['add']=='associations'){
			$associations = $_POST['associations'];
			$member->addAssociations($associations);
		} elseif ($_POST['add']=='awards'){
			$awards		= $_POST['awards'];
			$member->addAwards($awards);
		} elseif ($_POST['add']=='representation'){
			$rep_name 		= $_POST['rep_name'];
			$rep_title		= $_POST['rep_title'];
			$rep_phone		= $_POST['rep_phone'];
			$rep_email		= $_POST['rep_email'];
			$rep_address		= $_POST['rep_address'];
			$member->addRepresentation( $rep_name, $rep_title, $rep_phone, $rep_email, $rep_address);
		} elseif ($_POST['add']=='contact'){
			$con_name 		= $_POST['con_name'];
			$con_phone		= $_POST['con_phone'];
			$con_email		= $_POST['con_email'];
			$con_address		= $_POST['con_address'];
			$member->addContact($con_name, $con_phone, $con_email, $con_address);
		} elseif ($_POST['add']=='personal'){
			$per_weight		= $_POST['per_weight'];
			$per_height		= $_POST['per_height'];
			$per_hair		= $_POST['per_hair'];
			$per_eyes 		= $_POST['per_eyes'];
			$per_ethnicity		= $_POST['per_ethnicity'];
			$per_languages		= $_POST['per_languages'];
			$per_disabilities		= $_POST['per_disabilities'];
			$member->addPersonal($per_weight, $per_height, $per_hair, $per_eyes, $per_ethnicity, $per_languages, $per_disabilities);
		}
	}
}


// Handles Adding
if (isset($_GET['add'])){
	//header starts here.
	include(AT_INCLUDE_PATH.'header.inc.php');
	if ($_GET['add']=='position'){
		$savant->display('social/edit_profile/edit_position.tmpl.php');
	} elseif ($_GET['add']=='education'){
		$savant->display('social/edit_profile/edit_education.tmpl.php');
	} elseif ($_GET['add']=='websites'){
		$savant->display('social/edit_profile/edit_websites.tmpl.php');
	} elseif ($_GET['add']=='interests' || $_GET['add']=='associations' || $_GET['add']=='awards'){
		$savant->assign('title', $_GET['add']);
		$savant->display('social/edit_profile/edit_additional.tmpl.php');
	} elseif ($_GET['add']=='representation'){
		$savant->display('social/edit_profile/edit_representation.tmpl.php');
	} elseif ($_GET['add']=='contact'){
		$savant->display('social/edit_profile/edit_contact.tmpl.php');
	} elseif ($_GET['add']=='personal'){
		$savant->display('social/edit_profile/edit_personal.tmpl.php');
	}
	//footer
	include(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

// Handles Editing
if (isset($_GET['edit']) && isset($_GET['id']) && (intval($_GET['id']) > 0)){
	$id = intval($_GET['id']);

	//header starts here.
	include(AT_INCLUDE_PATH.'header.inc.php');
	$savant->assign('id', $id);
	if ($_GET['edit']=='position'){
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_member_position WHERE id='.$id;
		$rs = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($rs);
		
		//Template
		$savant->assign('company', $row['company']);
		$savant->assign('profile_title', $row['title']);
		$savant->assign('from', $row['from']);
		$savant->assign('to', $row['to']);
		$savant->assign('description', $row['description']);
		$savant->display('social/edit_profile/edit_position.tmpl.php');
	} elseif ($_GET['edit']=='education'){
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_member_education WHERE id='.$id;
		$rs = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($rs);

		//Template
		$savant->assign('university', $row['university']);
		$savant->assign('country', $row['country']);
		$savant->assign('province', $row['province']);
		$savant->assign('degree', $row['degree']);
		$savant->assign('field', $row['field']);
		$savant->assign('from', $row['from']);
		$savant->assign('to', $row['to']);
		$savant->assign('description', $row['description']);
		$savant->display('social/edit_profile/edit_education.tmpl.php');
	} elseif ($_GET['edit']=='websites'){
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_member_websites WHERE id='.$id;
		$rs = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($rs);

		//Template
		$savant->assign('url', $row['url']);
		$savant->assign('site_name', $row['site_name']);
		$savant->display('social/edit_profile/edit_websites.tmpl.php');

	} elseif ($_GET['edit']=='interests'){
		$sql = 'SELECT interests FROM '.TABLE_PREFIX.'social_member_additional_information WHERE member_id='.$_SESSION['member_id'];
		$rs = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($rs);

		//Template
		$savant->assign('interests', $row['interests']);
		$savant->assign('title', 'interests');
		$savant->display('social/edit_profile/edit_additional.tmpl.php');
	} elseif ($_GET['edit']=='associations'){
		$sql = 'SELECT associations FROM '.TABLE_PREFIX.'social_member_additional_information WHERE member_id='.$_SESSION['member_id'];
		$rs = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($rs);

		//Template
		$savant->assign('associations', $row['associations']);
		$savant->assign('title', 'associations');
		$savant->display('social/edit_profile/edit_additional.tmpl.php');
	} elseif ($_GET['edit']=='awards'){
		$sql = 'SELECT awards FROM '.TABLE_PREFIX.'social_member_additional_information WHERE member_id='.$_SESSION['member_id'];
		$rs = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($rs);

		//Template
		$savant->assign('awards', $row['awards']);
		$savant->assign('title', 'awards');
		$savant->display('social/edit_profile/edit_additional.tmpl.php');
	} elseif ($_GET['edit']=='representation'){
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_member_representation WHERE member_id='.$_SESSION['member_id'];
		$rs = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($rs);

		//Template
		$savant->assign('rep_name', $row['rep_name']);
		$savant->assign('rep_title', $row['rep_title']);
		$savant->assign('rep_phone', $row['rep_phone']);
		$savant->assign('rep_email', $row['rep_email']);
		$savant->assign('rep_address', $row['rep_address']);
		$savant->display('social/edit_profile/edit_representation.tmpl.php');

	}elseif ($_GET['edit']=='contact'){
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_member_contact WHERE member_id='.$_SESSION['member_id'];
		$rs = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($rs);

		//Template
		$savant->assign('con_name', $row['con_name']);
		$savant->assign('con_title', $row['con_title']);
		$savant->assign('con_phone', $row['con_phone']);
		$savant->assign('con_email', $row['con_email']);
		$savant->assign('con_address', $row['con_address']);
		$savant->display('social/edit_profile/edit_contact.tmpl.php');

	}elseif ($_GET['edit']=='personal'){
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_member_personal WHERE member_id='.$_SESSION['member_id'];
		$rs = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($rs);

		//Template
		$savant->assign('per_weight', $row['per_weight']);
		$savant->assign('per_height', $row['per_height']);
		$savant->assign('per_hair', $row['per_hair']);
		$savant->assign('per_eyes', $row['per_eyes']);
		$savant->assign('per_ethnicity', $row['per_ethnicity']);
		$savant->assign('per_languages', $row['per_languages']);
		$savant->assign('per_disabilities', $row['per_disabilities']);
		$savant->display('social/edit_profile/edit_personal.tmpl.php');
	}

	//footer
	include(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}


// Handles Deleting
if (isset($_GET['delete'])){
	$id	= intval($_GET['id']);
	if ($_GET['delete']=='position'){
		$member->deletePosition($id);
	} elseif ($_GET['delete']=='education'){
		$member->deleteEducation($id);
	} elseif ($_GET['delete']=='websites'){
		$member->deleteWebsite($id);
	} elseif ($_GET['delete']=='interests'){
		$member->deleteInterests($id);
	} elseif ($_GET['delete']=='associations'){
		$member->deleteAssociations($id);
	} elseif ($_GET['delete']=='awards'){
		$member->deleteAwards($id);
	} elseif ($_GET['delete']=='representation'){
		$member->deleteRepresentation($id);
	} elseif ($_GET['delete']=='contact'){
		$member->deleteContact($id);
	} elseif ($_GET['delete']=='personal'){
		$member->deletePersonal($id);
	}
}


// Member object
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('social/pubmenu.tmpl.php');
$savant->assign('profile', $member->getDetails());
$savant->assign('position', $member->getPosition());
$savant->assign('education', $member->getEducation());
$savant->assign('websites', $member->getWebsites());
$savant->assign('representation', $member->getRepresentation());
$savant->assign('contact', $member->getContact());
$savant->assign('personal', $member->getPersonal());
$savant->display('social/edit_profile.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>
