<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$


/**
* Class for dealing with files/directories in the course content directory
* @access	public
* @author	Joel Kronenberg
*/
class FileManager {

	var $contentDirectory; // path to content dir

	/**
	* Constructor method.  Initialises variables.
	* @access	public
	* @author	Joel Kronenberg
	*/
	function FileManager( ) {
		$this->contentDirectory = AT_CONTENT_DIR . $_SESSION['course_id'];
	}

	/**
	* Creates a directory
	* @access  public
	* @param   string $dir      relative path and name of the directory to create
	* @return  boolean			whether or not the directory was created
	* @author  Joel Kronenberg
	*/
	function createDirectory($dir) {
		// break $dir into the end part
		// check that the path to the new dir is safe
		// sanitise the dir name

		// Note: would it be easier to receive the path and directory name separately?

	}

	/**
	* Copies a file or directory
	* @access  public
	* @param   string $src	relative path to the source directory or file
	* @param   string $dst	relative path to the destination directory or file
	* @return  boolean      TRUE or FALSE whether or not the action was successful
	* @author  Joel Kronenberg
	*/
	function copy($src, $dst) {

	}

	/**
	* Moves a file or directory
	* @access  public
	* @param   string $src	relative path to the source directory or file
	* @param   string $dst	relative path to the destination directory or file
	* @return  boolean      TRUE or FALSE whether or not the action was successful
	* @author  Joel Kronenberg
	*/
	function move($src, $dst) {

	}

	/**
	* Rename a file or directory
	* @access  public
	* @param   string $old_name   relative path and old name of the directory or file to rename
	* @param   string $new_name   relative path and new name of the directory or file to rename
	* @return  boolean            TRUE or FALSE whether or not the action was successful
	* @author  Joel Kronenberg
	*/
	function rename($old_name, $new_name) {

	}

	/**
	* Delete a file or directory (recusively)
	* @access  public
	* @param   string $file   relative path and name of the file or directory to delete
	* @return  boolean        TRUE or FALSE whether or not the action was successful
	* @author  Joel Kronenberg
	*/
	function delete($file) {
		// if it's a dir, then call the $this->_deleteDir($file) private method
		// else if it's a file call the $this->_deleteFile($file) private method
	}

	/**
	* Extracts a zip archive
	* @access  public
	* @param   string $archive  relative path and name of the zip file to extract
	* @param   string $dst      relative path and name of the directory to extract the files into
	* @return  boolean          TRUE or FALSE whether or not the action was successful
	* @author  Joel Kronenberg
	*/
	function extract($archive, $dst) {

	}

	/**
	* Saves contents to a file
	* @access  public
	* @param   string $file       relative path to the file to save to
	* @param   string $contents   the contents of the file to save to
	* @param   boolean $overwrite whether or not to overwrite the file if it exists
	* @return  boolean          TRUE or FALSE whether or not the action was successful
	* @author  Joel Kronenberg
	*/
	function saveFile($file, $contents, $overwrite = FALSE) {

	}

	/**
	* Saves an uploded file
	* @access  public
	* @return  boolean          TRUE or FALSE whether or not the action was successful
	* @author  Joel Kronenberg
	*/
	function saveUploadFile( ) {

	}

	/**
	* Returns size of a directory (recursively)
	* @access  public
	* @param   string $dir     relative path to the directory
	* @return  int             size of directory in Bytes, FALSE on failure
	* @author  Joel Kronenberg
	*/
	function getDirectorySize($dir) {
		$dir = $this->_getRealPath($dir);

		if (($dir !== FALSE) && is_dir($dir)) {
			$dh = @opendir($dir);
		}
		if (!$dh) {
			return -1;
		}
		$size = 0;
		while (($file = readdir($dh)) !== false) {
			if (($file != '.') && ($file != '..')) {
				$path = $dir . $file;
				if (is_dir($path)) {
					$size += $this->getDirectorySize($path . DIRECTORY_SEPARATOR);
				} elseif (is_file($path)) {
					$size += filesize($path);
				}
			}
			
		}
		closedir($dh);
		return $size;
	}

	/**
	* Returns listing of files and directories
	* @access  public
	* @param   string $dir     relative path to the directory
	* @return  array           array of files and directories in $dir
	* @author  Joel Kronenberg
	*/
	function getDirectoryListing($dir) {

	}

	/**
	* Returns whether or not the $fileName is an editable type of file
	* @access  public
	* @param   string $fileName    name of the file to check
	* @return  boolean             TRUE if the file can be edited, FALSE otherwise
	* @author  Joel Kronenberg
	*/
	function isEditable($fileName) {
		// check if $fileName is in the list of editable files

	}

	/**
	* Returns whether or not the $fileName is an archive that can be extracted
	* @access  public
	* @param   string $fileName    name of the file to check
	* @return  boolean             TRUE if the file can be extracted, FALSE otherwise
	* @author  Joel Kronenberg
	*/
	function isExtractable($fileName) {
		// check if $fileName is in the list of extractable files

		// Note: could possibly call this isArchive() (but that doesn't directly imply extractability)
	}

	// -- private methods below

	/**
	* Returns a safe to use file or directory name
	* @access  private
	* @param   string $file		the file or directory name to sanitise
	* @return  string|boolean   the sanitised file/directory name, or FALSE if the result is empty
	* @author  Joel Kronenberg
	*/
	function _getCleanName($fileName) {
		$fileName = trim($fileName);
		$fileName = str_replace(' ', '_', $fileName);
		$fileName = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $fileName);

		return $fileName;
	}

	/**
	* Returns canonicalized absolute pathname
	* @access  private
	* @param   string $file		the relative path to a file or directory
	* @return  string|boolean   the canonicalized pathname, or FALSE if the file is not in the content directory
	* @author  Joel Kronenberg
	*/
	function _getRealPath($file) {
		// determine the real path of the file/directory
		$real = realpath($this->contentDirectory . DIRECTORY_SEPARATOR . $file);
		
		if (!file_exists($real)) {
			// the file or directory does not exist
			return FALSE;

		} else if (substr($real, 0, strlen($this->contentDirectory)) != $this->contentDirectory) {
			// the file or directory is not in the content path
			return FALSE;

		} else {
			// otherwise return the real path of the file
			return $real;
		}
	}

	/**
	* Delete a file
	* @access  private
	* @param   string $file   relative path and name of the file or directory to delete
	* @return  boolean        TRUE or FALSE whether or not the action was successful
	* @author  Joel Kronenberg
	*/
	function _deleteFile($file) {

	}

	/**
	* Delete this directory recursively
	* @access  private
	* @param   string $dir   relative path and name of the directory to delete
	* @return  boolean       TRUE or FALSE whether or not the action was successful
	* @author  Joel Kronenberg
	*/
	function _deleteDirectory($dir) {

	}
}
?>