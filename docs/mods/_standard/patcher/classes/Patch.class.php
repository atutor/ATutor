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
// $Id: Patch.class.php 7208 2008-02-08 16:07:24Z cindy $

/**
* Patch
* Class for patch installation
* @access	public
* @author	Cindy Qi Li
* @package	Patch
*/

define('AT_INCLUDE_PATH', '../../../include/');

require_once(AT_INCLUDE_PATH. "../mods/_standard/patcher/include/common.inc.php");

class Patch {

	// all private
	var $patch_array = array();           // the patch data
	var $patch_summary_array = array();   // patch summary information 
	var $patch_id;                        // current patches.patches_id
	var $patch_file_id;                   // current patches_files.patches_files_id
	
	var $need_access_to_folders = array();// folders that need to have write permission
	var $need_access_to_files = array();  // files that need to have write permission
	var $backup_files = array();          // backup files
	var $patch_files = array();           // patch files

	var $errors = array();                // error messages
	var $baseURL;                         // patch folder at update.atutor.ca
	var $backup_suffix;                   // suffix appended for backup files
	var $patch_suffix;                    // suffix appended for patch files copied from update.atutor.ca
	var $skipFilesModified = false;       // if set to true, report error for files that have been modified by user
	var $module_content_dir;              // content folder used to create patch.sql
	var $svn_server_connected;            // flag indicating if can connect to svn server, if not, consider all files manipulated by patch as modified

	// constant, URL of user's ATutor release version in SVN 
	var $svn_tag_folder = 'http://atutorsvn.atrc.utoronto.ca/repos/atutor/tags/';
	var $sql_file = 'patch.sql';
	var $relative_to_atutor_root = '../../../';   // relative path from mods/_standard/patcher to root

	/**
	* Constructor: Initialize object members
	* @access  public
	* @param   $patch_array	The name of the file to find charset definition
	*          $patch_summary_array
	*          $skipFilesModified
	* @author  Cindy Qi Li
	*/
	function Patch($patch_array, $patch_summary_array, $skipFilesModified, $patch_folder) 
	{
		// add relative path to move to ATutor root folder
		for ($i = 0; $i < count($patch_array[files]); $i++)
		{
			$patch_array[files][$i]['location'] = $this->relative_to_atutor_root . $patch_array[files][$i]['location'];
		}
		
		$this->patch_array = $patch_array; 
		$this->patch_summary_array = $patch_summary_array;

		$this->baseURL = $patch_folder;
		$this ->backup_suffix = $patch_array['atutor_patch_id'] . ".old";
		$this ->patch_suffix = $patch_array['atutor_patch_id'];
		$this->skipFilesModified = $skipFilesModified;
		
		$this->module_content_dir = AT_CONTENT_DIR . "patcher";

		session_start();
		
		if (!is_array($_SESSION['remove_permission'])) $_SESSION['remove_permission']=array();
		
	}

	/**
	* Main process to apply patch.
	* @access  public
	* @return  true  if patch is successfully applied
	*          false if failed
	* @author  Cindy Qi Li
	*/
	function applyPatch() 
	{
		global $msg;
		
		// Checks on 
		// 1. if svn server is up. If not, consider all files manipulated by patch as modified
		// 2. if the local file is customized by user
		// 3. if script has write priviledge on local file/folder
		// 4. if dependent patches have been installed
		if (!$this->pingDomain($this->svn_tag_folder)) 
		{
			$msg->addInfo('CANNOT_CONNECT_SVN_SERVER');
			$msg->printInfos();
			$this->svn_server_connected = false;
		}
		else
			$this->svn_server_connected = true;
		
		if (!$this->checkDependentPatches()) return false;

		if (!$this->checkAppliedVersion()) return false;

		if (!$this->skipFilesModified && $this->hasFilesModified()) return false;
		
		if (!$this->checkPriviledge()) return false;
		// End of check

		if (strlen(trim($this->patch_array['sql'])) > 0) $this->runSQL();

		// Start applying patch
		$this->createPatchesRecord($this->patch_summary_array);

		// if no file action defined, update database and return true
		if (!is_array($this->patch_array[files])) 
		{
			$updateInfo = array("status"=>"Installed");
			updatePatchesRecord($this->patch_id, $updateInfo);
	
			return true;
		}
		
		foreach ($this->patch_array[files] as $row_num => $patch_file)
		{
			$this->createPatchesFilesRecord($this->patch_array['files'][$row_num]);

			if ($patch_file['action'] == 'alter')
			{
				$this->alterFile($row_num);
			}
			else if ($patch_file['action'] == 'add')
			{
				$this->addFile($row_num);
			}
			else if ($patch_file['action'] == 'delete')
			{
				$this->deleteFile($row_num);
			}
			else if ($patch_file['action'] == 'overwrite')
			{
				$this->overwriteFile($row_num);
			}
		}
		
		// if only has backup files info, patch is considered successfully installed
		// if has permission to remove, considered partly installed
		$updateInfo = array();

		if (count($this->backup_files) > 0)
		{
			foreach($this->backup_files as $backup_file)
				$backup_files .= $backup_file. '|';
		
			$updateInfo = array("backup_files"=>mysql_real_escape_string($backup_files));
		}
	
		if (count($this->patch_files) > 0)
		{
			foreach($this->patch_files as $patch_file)
				$patch_files .= $patch_file. '|';
		
			$updateInfo = array_merge($updateInfo, array("patch_files"=>mysql_real_escape_string($patch_files)));
		}
	
		if (is_array($_SESSION['remove_permission']) && count($_SESSION['remove_permission']))
		{
			foreach($_SESSION['remove_permission'] as $remove_permission_file)
				$remove_permission_files .= $remove_permission_file. '|';

			$updateInfo = array_merge($updateInfo, array("remove_permission_files"=>mysql_real_escape_string($remove_permission_files), "status"=>"Partly Installed"));
		}
		else
		{
			$updateInfo = array_merge($updateInfo, array("status"=>"Installed"));
		} 

		updatePatchesRecord($this->patch_id, $updateInfo);
		
		unset($_SESSION['remove_permission']);

		return true;
	}

	/**
	* return patch array
	* @access  public
	* @return  patch array
	* @author  Cindy Qi Li
	*/
	function getPatchArray() 
	{
		return $this->patch_array;
	}
	
	/**
	* return patch id processed by this object
	* @access  public
	* @return  patch id
	* @author  Cindy Qi Li
	*/
	function getPatchID() 
	{
		return $this->patch_id;
	}
	
	/**
	* Check if script has write permission to the files and folders that need to be written
	* if no permission, warn user to give permission
	* @access  private
	* @return  true  if there are files or folders that script has no permission
	*          false if permissions are in place
	* @author  Cindy Qi Li
	*/
	function checkPriviledge()
	{
		global $id, $who;
		
		// no file action is defined, return true;
		if (!is_array($this->patch_array[files])) return true;
		
		foreach ($this->patch_array[files] as $row_num => $patch_file)
		{
			$real_location = realpath($patch_file['location']);
			if (!is_writable($patch_file['location']) && !in_array($real_location, $this->need_access_to_folders))
			{
				$this->need_access_to_folders[] = $real_location;

				if (!in_array($real_location, $_SESSION['remove_permission']))
					$_SESSION['remove_permission'][] = $real_location;
			}

			if ($patch_file['action'] == 'alter' || $patch_file['action'] == 'delete' || $patch_file['action'] == 'overwrite')
			{
				$file = $patch_file['location'] . "/" . $patch_file['name'];

				$real_file = realpath($file);
				if (file_exists($file) && !is_writable($file) && !in_array($real_file, $this->need_access_to_files))
				{
					$this->need_access_to_files[] = $real_file;

					if (!in_array($real_file, $_SESSION['remove_permission']) && $patch_file['action'] <> 'delete')
						$_SESSION['remove_permission'][] = $real_file;
				}
			}
		}
		
		if (count($this->need_access_to_folders) > 0 || count($this->need_access_to_files) > 0)
		{
			$this->errors[] = _AT('grant_write_permission');
			
			foreach($this->need_access_to_folders as $folder)
			{
				$this->errors[0] .= '<strong>'. $folder . "</strong><br />";
			}

			foreach($this->need_access_to_files as $file)
			{
				$this->errors[0] .= '<strong>'. $file . "</strong><br />";
			}

			$notes = '<form action="'. $_SERVER['PHP_SELF'].'?id='.$id.'&who='. $who .'" method="post" name="skip_files_modified">
		  <div class="row buttons">
				<input type="submit" name="yes" value="'._AT('continue').'" accesskey="y" />
				<input type="submit" name="no" value="'. _AT('cancel'). '" />
				<input type="hidden" name="install" value="' . $_POST['install'] . '" />
				<input type="hidden" name="install_upload" value="' . $_POST['install_upload'] . '" />
				<input type="hidden" name="ignore_version" value="' . $_POST['ignore_version'] . '" />
			</div>
			</form>';
			
			print_errors($this->errors, $notes);
		
			unset($this->errors);
			return false;
		}
		
		return true;
	}
	
	/**
	* Check if ATutor version is same as "applied version" defined in the patch.
	* @access  private
	* @return  true  if versions match
	*          false if versions don't match
	* @author  Cindy Qi Li
	*/
	function checkAppliedVersion()
	{
		global $msg;
		
		if ($this->patch_summary_array["applied_version"] <> VERSION)
		{
				$this->errors[] = _AT("version_not_match", $this->patch_summary_array["applied_version"]);
				
				$notes = '
			  <form action="'. $_SERVER['PHP_SELF'].'?id='.$_POST['id'].'&who='. $_POST['who'] .'" method="post" name="skip_files_modified">
			  <div class="row buttons">
					<input type="submit" name="ignore_version" value="'._AT('yes').'" accesskey="y" />
					<input type="submit" name="not_ignore_version" value="'. _AT('no'). '" />
					<input type="hidden" name="install" value="' . $_POST['install'] . '" />
					<input type="hidden" name="install_upload" value="' . $_POST['install_upload'] . '" />
				</div>
				</form>';

			print_errors($this->errors, $notes);
		
			unset($this->errors);
			
			return false;
		}

		return true;
	}

	/**
	* Check if all the dependent patches have been installed.
	* @access  private
	* @return  true  if all the dependent patches have been installed
	*          false if any dependent patch has not been installed.
	* @author  Cindy Qi Li
	*/
	function checkDependentPatches()
	{
		global $msg;
		
		$dependent_patches_installed = true;
		
		// if no dependent patch defined, return true
		if (!is_array($this->patch_summary_array["dependent_patches"])) return true;
		
		foreach($this->patch_summary_array["dependent_patches"] as $num => $dependent_patch)
		{
			if (!is_patch_installed($dependent_patch))
			{
				$dependent_patches_installed = false;
				$dependent_patches .= $dependent_patch. ", ";
			}
		}
		
		if (!$dependent_patches_installed)
		{
			$errors = array('PATCH_DEPENDENCY', substr($dependent_patches, 0, -2));
			$msg->addError($errors);
			return false;
		}
		
		return true;
	}
	
	/**
	* Loop thru all the patch files that will be overwitten or altered, 
	* to find out if they are modified by user. If it's modified, warn user.
	* @access  private
	* @return  true  if there are files being modified
	*          false if no file is modified
	* @author  Cindy Qi Li
	*/
	function hasFilesModified()
	{
		$overwrite_modified_files = false;
		$alter_modified_files = false;
		$has_not_exist_files = false;
		
		// no file action is defined, return nothing is modified (false)
		if (!is_array($this->patch_array[files])) return false;
		
		foreach ($this->patch_array[files] as $row_num => $patch_file)
		{
			if ($patch_file["action"]=='alter' || $patch_file["action"]=='overwrite')
			{
				if (!file_exists($patch_file['location'] . $patch_file['name']))
				{
					$not_exist_files .= $patch_file['location'] . $patch_file['name'] . '<br />';
					$has_not_exist_files = true;
				}
				else if ($this->isFileModified($patch_file['location'], $patch_file['name']))
				{
					if ($patch_file['action']=='overwrite')
					{
						$overwrite_files .= realpath($patch_file['location'] . $patch_file['name']) . '<br />';
						$overwrite_modified_files = true;
					}
					if ($patch_file['action']=='alter')
					{
						$alter_files .= realpath($patch_file['location'] . $patch_file['name']) . '<br />';
						$alter_modified_files = true;
					}
				}
			}
		}

		if ($has_not_exist_files) $this->errors[] = _AT('patch_local_file_not_exist'). $not_exist_files;
		if ($overwrite_modified_files)    $this->errors[] = _AT('patcher_overwrite_modified_files') . $overwrite_files;
		if ($alter_modified_files)    $this->errors[] = _AT('patcher_alter_modified_files') . $alter_files;
		if (count($this->errors) > 0)
		{
			if ($has_not_exist_files)
				$notes = '';
			else
				$notes = '
			  <form action="'. $_SERVER['PHP_SELF'].'?id='.$_POST['id'].'&who='. $_POST['who'] .'" method="post" name="skip_files_modified">
			  <div class="row buttons">
					<input type="submit" name="yes" value="'._AT('yes').'" accesskey="y" />
					<input type="submit" name="no" value="'. _AT('no'). '" />
					<input type="hidden" name="install" value="' . $_POST['install'] . '" />
					<input type="hidden" name="install_upload" value="' . $_POST['install_upload'] . '" />
					<input type="hidden" name="ignore_version" value="' . $_POST['ignore_version'] . '" />
				</div>
				</form>';

			print_errors($this->errors, $notes);
		
			unset($this->errors);
			return true;
		}
		
		return false;
	}

	/**
	* Compare user's local file with SVN backup for user's ATutor version,
	* if different, check table at_patches_files to see if user's local file
	* was altered by previous patch installation. If it is, return false 
	* (not modified), otherwise, return true (modified).
	* @access  private
	* @param   $folder  folder of the file to be compared
	*          $file    name of the file to be compared
	* @return  true     if the file is modified
	*          false    if the file is not modified
	* @author  Cindy Qi Li
	*/
	function isFileModified($folder, $file)
	{
		global $db;

		if (!$this->svn_server_connected) return true;
		
		$svn_file = $this->svn_tag_folder . 'atutor_' . str_replace('.', '_', VERSION) .
		            str_replace(substr($this->relative_to_atutor_root, 0, -1), '' , $folder) .$file;
		$local_file = $folder.$file;

		// if svn script does not exist, consider the script is modified
		if (!file_get_contents($svn_file)) return true;

		// check if the local file has been modified by user. if it is, don't overwrite
		if ($this->compareFiles($svn_file, $local_file) <> 0)
		{
			// check if the file was changed by previous installed patches
			$sql = "SELECT count(*) num_of_updates FROM " . TABLE_PREFIX. "patches patches, " . TABLE_PREFIX."patches_files patches_files " .
			       "WHERE patches.applied_version = '" . VERSION . "' ".
			       "  AND patches.status = 'Installed' " .
			       "  AND patches.patches_id = patches_files.patches_id " .
			       "  AND patches_files.name = '" . $file . "'";
			
			$result = mysql_query($sql, $db) or die(mysql_error());
			$row = mysql_fetch_assoc($result);
			
			if ($row["num_of_updates"] == 0) return true;
		}
		return false;
	}

	/**
	* Run SQL defined in patch.xml
	* @access  private
	* @author  Cindy Qi Li
	*/
	function runSQL()
	{
		// run sql
		// As sqlutility.class.php reads sql from a file, write sql to module content folder
		$patch_sql_file = $this->module_content_dir . '/' . $this->sql_file;

		$fp = fopen($patch_sql_file, 'w');
		fwrite($fp, trim($this->patch_array['sql']));
		fclose($fp);

		require(AT_INCLUDE_PATH . 'classes/sqlutility.class.php');
		$sqlUtility =& new SqlUtility();
	
		$sqlUtility->queryFromFile($patch_sql_file, TABLE_PREFIX);
		
		@unlink($patch_sql_file);
		
		return true;
	}
		
	/**
	* Copy file from update.atutor.ca to user's computer
	* @access  private
	* @param   $row_num	row number of patch record to be processed
	* @author  Cindy Qi Li
	*/
	function addFile($row_num)
	{
		$this->copyFile($this->baseURL . preg_replace('/.php$/', '.new', $this->patch_array['files'][$row_num]['name']), $this->patch_array['files'][$row_num]['location'].$this->patch_array['files'][$row_num]['name']);
		
		return true;
	}
	
	/**
	* Delete file, backup before deletion
	* @access  private
	* @param   $row_num	row number of patch record to be processed
	* @author  Cindy Qi Li
	*/
	function deleteFile($row_num)
	{
		$local_file = $this->patch_array['files'][$row_num]['location'].$this->patch_array['files'][$row_num]['name'];
		$backup_file = $local_file . "." . $this->backup_suffix;
		
		if (file_exists($local_file))
		{
			// move file to backup
			$this->copyFile($local_file, $backup_file);
			$this->backup_files[] = realpath($backup_file);
			@unlink($local_file);
		}
		
		return true;
		
	}
	
	/**
	* Alter file based on <action_detail>
	* If user's local file is modified and user agrees to proceed with applying patch,
	* alter user's local file.
	* @access  private
	* @param   $row_num	row number of patch record to be processed
	* @author  Cindy Qi Li
	*/
	function alterFile($row_num)
	{
		$local_file = $this->patch_array['files'][$row_num]['location'].$this->patch_array['files'][$row_num]['name'];
		
		// backup user's file
		$backup_file = $local_file . "." . $this->backup_suffix;
		
		// Checking existence of $backup_file is to fix the bug when there are multiple alter/delete actions 
		// on the same file, the following backups overwrite the first backup which results in the loss of the
		// original code.
		if (!file_exists($backup_file))
		{
			$this->copyFile($local_file, $backup_file);
			$this->backup_files[] = realpath($backup_file);
		}
		
		$local_file_content = file_get_contents($local_file);

		// Modify user's file
		foreach ($this->patch_array['files'][$row_num]['action_detail'] as $garbage => $alter_file_action)
		{
			if ($alter_file_action['type'] == 'delete')
				$modified_local_file_content = $this->strReplace($alter_file_action['code_from'], '', $local_file_content);

			if ($alter_file_action['type'] == 'replace')
				$modified_local_file_content = $this->strReplace($alter_file_action['code_from'], $alter_file_action['code_to'], $local_file_content);
				
			// when code_from is not found, add in warning
			if ($modified_local_file_content == $local_file_content)  
			{
				for ($i = 0; $i < count($this->backup_files); $i++)
					if ($this->backup_files[$i] == realpath($backup_file))
						$this->backup_files[$i] .= ' '._AT("chunks_not_found");
			}
			else
				$local_file_content = $modified_local_file_content;

			$this->createPatchesFilesActionsRecord($alter_file_action);
		}

		$fp = fopen($local_file, 'w');
		fwrite($fp, $local_file_content);
		fclose($fp);

		return true;
	}
	
	/**
	* Fetch file from update.atutor.ca and overwrite user's local file if the local file is not modified
	* If user's local file is modified and user agrees to proceed with applying patch,
	* copy the new file to user's local for them to merge manually.
	* @access  private
	* @param   $row_num	row number of patch record to be processed
	* @author  Cindy Qi Li
	*/
	function overwriteFile($row_num)
	{
		$local_file = $this->patch_array['files'][$row_num]['location'].$this->patch_array['files'][$row_num]['name'];
		$patch_file = $this->baseURL . preg_replace('/.php$/', '.new', $this->patch_array['files'][$row_num]['name']);
		
		// if local file is modified and user agrees to proceed with applying patch,
		// copy the new file to user's local for them to merge manually
		if ($this->skipFilesModified && $this->isFileModified($this->patch_array['files'][$row_num]['location'], $this->patch_array['files'][$row_num]['name']))
		{
			$local_patch_file = $local_file . "." . $this->patch_suffix;

			$this->copyFile($patch_file, $local_patch_file);
			
			$this->patch_files[] = realpath($local_patch_file);
		}
		else
		{
			$backup_file = $local_file . "." . $this->backup_suffix;
			
			// backup user's file
			$this->copyFile($local_file, $backup_file);
			$this->backup_files[] = realpath($backup_file);
			
			// overwrite user's file
			$this->copyFile($patch_file, $local_file);
		}
		
		return true;
	}
	
	/**
	* Copy file $src to $dest. $src can be a local file or a remote file
	* @access  private
	* @param   $src	location of the source file
	*          $dest	location of the destination file
	* @author  Cindy Qi Li
	*/
	function copyFile($src, $dest)
	{
		$content = file_get_contents($src);
		$fp = fopen($dest, 'w');
		fwrite($fp, $content);
		fclose($fp);
		
		return true;
	}
	
	/**
	* Compare files $src against $dest
	* @access  private
	* @param   $src	location of the source file
	*          $dest	location of the destination file
	* @return  Returns < 0 if $src is less than $dest ; > 0 if $src is greater than $dest, and 0 if they are equal.
	* @author  Cindy Qi Li
	*/
	function compareFiles($src, $dest)
	{
		// use preg_replace to delete 
		// 1. the line starting with // $Id:
		// 2. the line starting with $lm = '$LastChangedDate, ending with ;
		// These lines are created by SVN. It could be different in different copies of the same file.
		$pattern = '/\/\/ \$Id.*\$|\$lm = \'\$LastChangedDate.*;/';
		
		$src_content = preg_replace($pattern, '', file_get_contents($src));
		$dest_content = preg_replace($pattern, '', file_get_contents($dest));

		return strcasecmp($src_content, $dest_content);
	}
	
	/**
	* Replace single/multiple lines of string. 
	* This function handles different new line character at windows/unix platform
	* @access  private
	* @param   $search	String to replace from
	*          $replace	String to replace to
	*          $subject Subject to be handled  
	* @return  return replaced string, if nothing is replaced, return original subject
	* @author  Cindy Qi Li
	*/
	function strReplace($search, $replace, $subject)
	{
		// Note: DO NOT change the order of the array elements. 
		// "\n\r", "\r\n" must come before "\n", "\r" in the array, 
		// otherwise, the new line replace underneath would wrongly replace "\n\r" to "\r\r" or "\n\n"
		$new_line_array = array("\n\r", "\r\n", "\r", "\n");
		
		foreach ($new_line_array as $new_line)
		{
			if (preg_match('/'.preg_quote($new_line).'/', $search) > 0)   $search_new_lines[] = $new_line;
			if (preg_match('/'.preg_quote($new_line).'/', $replace) > 0)   $replace_new_lines[] = $new_line;
			if (preg_match('/'.preg_quote($new_line).'/', $subject) > 0)   $subject_new_lines[] = $new_line;
		}

		// replace new line chars in $search, $replace, $subject to the last new line in $subject
		if (is_array($subject_new_lines)) $new_line_replace_to = array_pop(&$subject_new_lines);

		if ($new_line_replace_to <> '')
		{
			if (count($search_new_lines) > 0)
				foreach ($search_new_lines as $new_line)
					if ($new_line <> $new_line_replace_to)
						$search = preg_replace('/'.preg_quote($new_line).'/', $new_line_replace_to, $search);
			
			if (count($replace_new_lines) > 0)
				foreach ($replace_new_lines as $new_line)
					if ($new_line <> $new_line_replace_to)
						$replace = preg_replace('/'.preg_quote($new_line).'/', $new_line_replace_to, $replace);
			
			if (count($subject_new_lines) > 0)
				foreach ($subject_new_lines as $new_line)
					$subject = preg_replace('/'.preg_quote($new_line).'/', $new_line_replace_to, $subject);
		}
		
		return preg_replace('/'. preg_quote($search, '/') .'/', $replace, $subject);
	}
	
	/**
	* Check if the server is down
	* @access  private
	* @param   $domain	Server Domain
	* @return  return false if server is down, otherwise, return true
	* @author  Cindy Qi Li
	*/
	function pingDomain($domain)
	{
    $file = @fopen ($domain, 'r');

    if (!$file) 
    	return false;

    return true;
	}

	/**
	* Insert record into table patches
	* @access  private
	* @param   $patch_summary_array	Patch summary information
	* @author  Cindy Qi Li
	*/
	function createPatchesRecord($patch_summary_array)
	{
		global $db;
		
		$sql = "INSERT INTO " . TABLE_PREFIX. "patches " .
					 "(atutor_patch_id, 
					   applied_version,
					   patch_folder,
					   description,
					   available_to,
					   sql_statement,
					   status,
					   remove_permission_files,
					   backup_files,
					   patch_files,
					   author,
					   installed_date)
					  VALUES
					  ('".$patch_summary_array["atutor_patch_id"]."',
					   '".$patch_summary_array["applied_version"]."',
					   '".mysql_real_escape_string($patch_summary_array["patch_folder"])."',
					   '".mysql_real_escape_string($patch_summary_array["description"])."',
					   '".$patch_summary_array["available_to"]."',
					   '".mysql_real_escape_string($patch_summary_array["sql"])."',
					   '".$patch_summary_array["status"]."',
					   '',
					   '',
					   '',
					   '".mysql_real_escape_string($patch_summary_array["author"])."',
					   now()
					   )";

		$result = mysql_query($sql, $db) or die(mysql_error());
		
		$this->patch_id = mysql_insert_id();
		
		return true;
	}

	/**
	* Insert record into table patches_files
	* @access  private
	* @param   $patch_files_array	Patch information
	* @author  Cindy Qi Li
	*/
	function createPatchesFilesRecord($patch_files_array)
	{
		global $db;
		
		$sql = "INSERT INTO " . TABLE_PREFIX. "patches_files " .
					 "(patches_id, 
					   action,
					   name,
					   location)
					  VALUES
					  (".$this->patch_id.",
					   '".$patch_files_array['action']."',
					   '".mysql_real_escape_string($patch_files_array['name'])."',
					   '".mysql_real_escape_string($patch_files_array['location'])."'
					   )";

		$result = mysql_query($sql, $db) or die(mysql_error());
		
		$this->patch_file_id = mysql_insert_id();

		return true;
	}

	/**
	* Insert record into table patches_files_actions
	* @access  private
	* @param   $patch_files_actions_array	alter file actions and contents
	* @author  Cindy Qi Li
	*/
	function createPatchesFilesActionsRecord($patch_files_actions_array)
	{
		global $db;
		
		$sql = "INSERT INTO " . TABLE_PREFIX. "patches_files_actions " .
					 "(patches_files_id, 
					   action,
					   code_from,
					   code_to)
					  VALUES
					  (".$this->patch_file_id.",
					   '".$patch_files_actions_array['type']."',
					   '".mysql_real_escape_string($patch_files_actions_array['code_from'])."',
					   '".mysql_real_escape_string($patch_files_actions_array['code_to'])."'
					   )";

		$result = mysql_query($sql, $db) or die(mysql_error());
		
		return true;
	}
}

?>