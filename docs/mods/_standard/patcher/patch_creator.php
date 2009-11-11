<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: index_admin.php 7208 2008-03-13 16:07:24Z cindy $

define('AT_INCLUDE_PATH', '../../../include/');
require_once (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_PATCHER);

if ($_POST['create'] || $_POST['save'])
{
	if (isset($_REQUEST["myown_patch_id"])) $patch_id = $_REQUEST["myown_patch_id"];
	else $patch_id = 0;
	
	// check missing fields
	if (!isset($_POST["atutor_patch_id"]) || trim($_POST["atutor_patch_id"]) == "")
		$missing_fields[] = _AT("atutor_patch_id");

	if (!isset($_POST["atutor_version_to_apply"]) || trim($_POST["atutor_version_to_apply"]) == "")
		$missing_fields[] = _AT("atutor_version_to_apply");

	// only check missing upload file when creating a patch. don't check when save
	if (is_array($_POST['rb_action']) && $_POST['create'])
	{
		foreach ($_POST['rb_action'] as $i=>$action)
		{
			// must upload a file if action is add or overwrite
			if ($action == "add" && $_FILES['add_upload_file']['name'][$i] == "" && $_POST['add_uploaded_file'] == "")
				$missing_fields[] = _AT("upload_file") . " for ". _AT("file_name") . " <strong>" . $_POST['add_filename'][$i] . "</strong>";
	
			if ($action == "overwrite" && $_FILES['overwrite_upload_file']['name'][$i] == "" && $_POST['overwrite_uploaded_file'] == "")
				$missing_fields[] = _AT("upload_file") . " for ". _AT("file_name") . " <strong>" . $_POST['overwrite_filename'][$i] . "</strong>";
		}
	}
	// end of checking missing fields

	if ($missing_fields) 
	{
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!(preg_match("/^[a-zA-Z0-9_.-]([a-zA-Z0-9_.-])*$/i", $_POST['atutor_patch_id'])))
		$msg->addError('LOGIN_CHARS');

	// main process
	if (!$msg->containsErrors()) 
	{
		$patch_info = array("atutor_patch_id"=>$_POST["atutor_patch_id"],
	                      "atutor_version_to_apply"=>$_POST["atutor_version_to_apply"],
	                      "description"=>$_POST["description"],
	                      "sql_statement"=>$_POST["sql_statement"]);

		// remove empty dependent patches
		if (is_array($_POST["dependent_patch"]))
		{
			foreach ($_POST["dependent_patch"] as $dependent_patch)
				if (trim($dependent_patch) <> "")
					$dependent_patches[] = $dependent_patch;
		}
		
		if (is_array($dependent_patches))
			$patch_info["dependent_patches"] = $dependent_patches;
			
		if (is_array($_POST['rb_action']))
		{
			foreach ($_POST['rb_action'] as $i=>$action)
			{
				if ($action == "add" && $_POST['add_filename'][$i] <> "")
				{
					if ($_FILES['add_upload_file']['tmp_name'][$i] <> "")
						$upload_file = $_FILES['add_upload_file']['tmp_name'][$i];
					else
						$upload_file = $_POST['add_uploaded_file'][$i];
					
					$patch_info["files"][] = array("action"=>$action,
					                             "file_name"=>$_POST['add_filename'][$i],
				                               "directory"=>$_POST['add_dir'][$i],
				                               "upload_tmp_name"=>$upload_file);
				}
				
				if ($action == "alter" && $_POST['alter_filename'][$i] <> "")
					$patch_info["files"][] = array("action"=>$action,
								                       "file_name"=>$_POST['alter_filename'][$i],
				                               "directory"=>$_POST['alter_dir'][$i],
				                               "code_from"=>$_POST['alter_code_from'][$i],
				                               "code_to"=>$_POST['alter_code_to'][$i]);
	
				if ($action == "delete" && $_POST['delete_filename'][$i] <> "")
					$patch_info["files"][] = array("action"=>$action,
					                             "file_name"=>$_POST['delete_filename'][$i],
				                               "directory"=>$_POST['delete_dir'][$i]);
	
				if ($action == "overwrite" && $_POST['overwrite_filename'][$i] <> "")
				{
					if ($_FILES['overwrite_upload_file']['tmp_name'][$i] <> "")
						$upload_file = $_FILES['overwrite_upload_file']['tmp_name'][$i];
					else
						$upload_file = $_POST['overwrite_uploaded_file'][$i];
					
					$patch_info["files"][] = array("action"=>$action,
					                             "file_name"=>$_POST['overwrite_filename'][$i],
				                               "directory"=>$_POST['overwrite_dir'][$i],
				                               "upload_tmp_name"=>$upload_file);
				}
			}
		}

		require_once("classes/PatchCreator.class.php");
		
		$patch_creator = new PatchCreator($patch_info, $patch_id);
		
		if ($_POST['create'])
			$patch_creator->create_patch();
		else if ($_POST['save'])
		{
			$patch_creator->saveInfo();
			header('Location: myown_patches.php');
		}

	}
}

$msg->printErrors();
?>
