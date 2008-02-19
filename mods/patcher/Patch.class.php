<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: Patch.class.php 7208 2008-02-08 16:07:24Z cindy $

/**
* Patch
* Class for patch installation
* @access	public
* @author	Cindy Qi Li
* @package	Patch
*/
require('common.inc.php');

class Patch {

	// all private
	var $patch_array = array();           // the patch data
	var $patch_summary_array = array();   // patch summary information 
	var $patch_id;                        // current patches.patches_id
	var $patch_file_id;                   // current patches_files.patches_files_id
	
	var $backup_files = array();          // names of all the backup files created in the script
	var $need_access_to_folders = array();// folders that need to have write permission
	var $need_access_to_files = array();  // files that need to have write permission

	var $errors = array();                // error messages
	var $feedbacks = array();             // feedback messages, displayed after patch installation
	var $baseURL;                         // patch folder at update.atutor.ca
	var $backup_suffix;                   // suffix appended for backup files
	var $patch_suffix;                    // suffix appended for patch files copied from update.atutor.ca
	var $skipFilesModified = false;       // if set to true, report error for files that have been modified by user
	var $module_content_dir;              // content folder used to create patch.sql

	// constant, URL of user's ATutor release version in SVN 
	var $svn_tag_folder = 'http://atutorsvn.atrc.utoronto.ca/repos/atutor/tags/';
	var $sql_file = 'patch.sql';
	var $relative_to_atutor_root = '../../';   // relative path from mods/patcher to root

	/**
	* Constructor: Initialize object members
	* @access  public
	* @param   $patch_array	The name of the file to find charset definition
	*          $patch_summary_array
	*          $skipFilesModified
	* @author  Cindy Qi Li
	*/
	function Patch($patch_array, $patch_summary_array, $skipFilesModified) 
	{
		// get rid of the current folder of 'mods/patcher'
		for ($i = 0; $i < count($patch_array[files]); $i++)
		{
			$patch_array[files][$i]['location'] = $this->relative_to_atutor_root . $patch_array[files][$i]['location'];
		}
		
		$this->patch_array = $patch_array; 
		$this->patch_summary_array = $patch_summary_array;
		
		$this->baseURL = 'http://update.atutor.ca/patch/' . str_replace('.', '_', VERSION) . '/' . $patch_summary_array['patch_folder']."/";
		$this ->backup_suffix = $patch_array['atutor_patch_id'] . ".old";
		$this ->patch_suffix = $patch_array['atutor_patch_id'];
		$this->skipFilesModified = $skipFilesModified;
		
		$this->module_content_dir = AT_CONTENT_DIR . "patcher";

		session_start();
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
		// Checks on 
		// 1. if svn server is up
		// 2. if the local file is customized by user
		// 3. if script has write priviledge on local file/folder
		if (!$this->pingDomain($this->svn_tag_folder)) return false;
		
		if (!$this->skipFilesModified && $this->hasFilesModified()) return false;
		
		if (!$this->checkPriviledge()) return false;
		// End of check

		if (strlen(trim($this->patch_array['sql'])) > 0) $this->runSQL();
		
		// Start applying patch
		$this->createPatchesRecord($this->patch_summary_array);

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

		if (count($this->backup_files) > 0)
		{
			$this->feedbacks[] = 'Below is the list of the backup files created by patch installation. 
														After ensuring ATutor works properly with the patch, you may want to 
														delete these files. If the ATutor does not work properly with the patch,
														you can always revert back to old ATutor by renaming the backup files 
														to original files. The renaming is to remove the suffix .'. 
														$this->backup_suffix.'<br>';
			
			foreach($this->backup_files as $backup_file)
				$this->feedbacks[count($this->feedbacks)-1] .= $backup_file . "<br>";
		}
		
		if (strlen($_SESSION['permission_msg']) > 0)
		{
			$this->feedbacks[] = 'Please <strong>REMOVE</strong> write permission on the listed folders and files for your security:<br>' . $_SESSION['permission_msg'];
			
			$notes = '<p><strong>Note:</strong> To remove permissions on Unix use <kbd>chmod 755</kbd> then the file name.</p>';
			
			unset($_SESSION['permission_msg']);
		}

		print_feedback($this->feedbacks, $notes);
	
		unset($this->feedbacks);
		
		return true;

	}

	/**
	* return patch array
	* @access  public
	* @return  true  if there are files or folders that script has no permission
	*          false if permissions are in place
	* @author  Cindy Qi Li
	*/
	function getPatchArray() 
	{
		return $this->patch_array;
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
		
		foreach ($this->patch_array[files] as $row_num => $patch_file)
		{
			if (!is_writable($patch_file['location']) && !in_array(realpath($patch_file['location']), $this->need_access_to_folders))
				$this->need_access_to_folders[] = realpath($patch_file['location']);
			

			if ($patch_file['action'] == 'alter' || $patch_file['action'] == 'delete' || $patch_file['action'] == 'overwrite')
			{
				$file = $patch_file['location'] . "/" . $patch_file['name'];

				if (file_exists($file))     @chmod($file, 0666);

				if (file_exists($file) && !is_writable($file) && !in_array(realpath($file), $this->need_access_to_files))
					$this->need_access_to_files[] = realpath($file);
			}
		}
		
		if (count($this->need_access_to_folders) > 0 || count($this->need_access_to_files) > 0)
		{
			$this->errors[] = 'Please give <strong>write</strong> permission to listed folders and files:<br>';
			
			foreach($this->need_access_to_folders as $folder)
			{
				$this->errors[0] .= '<strong>'. $folder . "</strong><br>";
				$_SESSION['permission_msg'] .= '<strong>'. $folder . "</strong><br>";
			}

			foreach($this->need_access_to_files as $file)
			{
				$this->errors[0] .= '<strong>'. $file . "</strong><br>";
				$_SESSION['permission_msg'] .= '<strong>'. $file . "</strong><br>";
			}

			$notes = '<p><strong>Note:</strong> To change permissions on Unix use <kbd>chmod a+rw</kbd> then the file name.</p>'.
		  '<form action="'. $_SERVER['PHP_SELF'].'?id='.$id.'&who='. $who .'" method="post" name="skip_files_modified">
		  <div class="row buttons">
				<input type="submit" name="yes" value="'._AT('continue').'" accesskey="y" />
				<input type="submit" name="no" value="'. _AT('cancel'). '" />
			</div>
			</form>';
			
			print_errors($this->errors, $notes);
		
			unset($this->errors);
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
		foreach ($this->patch_array[files] as $row_num => $patch_file)
		{
			if ($patch_file["action"]=='alter' || $patch_file["action"]=='overwrite')
			{
				if ($this->isFileModified($patch_file['location'], $patch_file['name']))
				{
					if ($patch_file['action']=='overwrite')
						$this->errors[] = '<strong>'. realpath($patch_file['location'] . $patch_file['name']) . 
						                  '</strong> is modified locally. If you choose to proceed with the installation, 
						                  the new file '.realpath($patch_file['location'] . $patch_file['name'].'.'.$this->patch_suffix) .
						                  ' will be copied to your local machine. You have to manually merge this file and your local copy.';
					if ($patch_file['action']=='alter')
						$this->errors[] = '<strong>'. realpath($patch_file['location'] . $patch_file['name']) . 
						                  '</strong> is modified locally. If you choose to proceed with the installation, 
						                  your local file will be modified by installation process. A backup on your original
						                  file will be created before the modification. The backup file will be ' .
						                  realpath($patch_file['location'] . $patch_file['name'].'.'.$this->backup_suffix) .
						                  '. Please note that the modification on your customized code may break your customization.';
				}
			}
		}

		if (count($this->errors) > 0)
		{
			$notes = '
		  <form action="'. $_SERVER['PHP_SELF'].'?id='.$_POST['id'].'&who='. $_POST['who'] .'" method="post" name="skip_files_modified">
		  <div class="row buttons">
				<input type="submit" name="yes" value="'._AT('yes').'" accesskey="y" />
				<input type="submit" name="no" value="'. _AT('no'). '" />
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

		$svn_file = $this->svn_tag_folder . 'atutor_' . str_replace('.', '_', VERSION) .
		            str_replace('../..', '', $folder) .$file;
		$local_file = $folder.$file;

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
			$this->backup_files[] = $backup_file;
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
		$this->copyFile($local_file, $backup_file);
		$this->backup_files[] = $backup_file;
		
		if ($this->skipFilesModified && $this->isFileModified($this->patch_array['files'][$row_num]['location'], $this->patch_array['files'][$row_num]['name']))
		{
			$this->feedbacks[] = '<strong>'.$local_file . '</strong> file has been customized by user. The installation may break your customization. Please test this file. 
			                      The file before the installation is backup to <strong>' . $backup_file . '</strong>';
		}
		
		$local_file_content = file_get_contents($local_file);

		// Modify user's file
		foreach ($this->patch_array['files'][$row_num]['action_detail'] as $garbage => $alter_file_action)
		{
			if ($alter_file_action['type'] == 'delete')
				$local_file_content = preg_replace('/'. preg_quote($alter_file_action['code_from'], '/') .'/', '', $local_file_content);

			if ($alter_file_action['type'] == 'replace')
				$local_file_content = preg_replace('/'. preg_quote($alter_file_action['code_from'], '/') .'/', $alter_file_action['code_to'], $local_file_content);

			$this->createPatchesFilesActionsRecord($alter_file_action);
		}

		$fp = fopen($local_file, 'w');
		fwrite($fp, $local_file_content);
		fclose($fp);

		// if file is modified and user agrees to proceed with applying patch,
		// copy the new file to user's local for them to merge manually
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
			
			$this->feedbacks[] = '<strong>'.$local_file . '</strong> file has been customized by user. The new file <strong>'.
			                     $local_patch_file .'</strong> has been copied to your computer. Please manually merge the change 
			                     between the new file and your local copy.';
		}
		else
		{
			$backup_file = $local_file . "." . $this->backup_suffix;
			
			// backup user's file
			$this->copyFile($local_file, $backup_file);
			$this->backup_files[] = $backup_file;
			
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
		// use preg_replace to delete the line starting with // $Id:
		// This line is created by SVN. It could be different in different copies of the same file.
		$pattern = '/\/\/ \$Id.*\$/';
		
		$src_content = preg_replace($pattern, '', file_get_contents($src));
		$dest_content = preg_replace($pattern, '', file_get_contents($dest));
		
		return strcasecmp($src_content, $dest_content);
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
    {
    	return false;
    }
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
					   sequence,
					   patch_folder,
					   description,
					   available_to,
					   sql_statement,
					   status)
					  VALUES
					  ('".$patch_summary_array["atutor_patch_id"]."',
					   '".$patch_summary_array["applied_version"]."',
					   ".$patch_summary_array["sequence"].",
					   '".$patch_summary_array["patch_folder"]."',
					   '".$patch_summary_array["description"]."',
					   '".$patch_summary_array["available_to"]."',
					   '',
					   'Installed'
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
					   '".$patch_files_array['name']."',
					   '".$patch_files_array['location']."'
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
					   '".preg_replace('/\'/', '\\\'', $patch_files_actions_array['code_from'])."',
					   '".preg_replace('/\'/', '\\\'', $patch_files_actions_array['code_to'])."'
					   )";

		$result = mysql_query($sql, $db) or die(mysql_error());
		
		return true;
	}

}

?>