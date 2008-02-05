<?php
exit('not yet complete');
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

/**
 *
 * This a class I'm writing to replace the un-cohesive operations of
 * the existing file manager.
 *
 * The focus of these methods is security. This file manager is written
 * to be much more secure and easier to maintain then the existing code.
 *
 **/

/**
* Class for dealing with files/directories in the course content directory
* @access	public
* @author	Joel Kronenberg
*/
class FileManager {

	/**
	* string $contentDirectory - the full path to this course's content directory
	* @access  private
	*/
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
	* Creates a directory recursivelly.
	* @access  public
	* @param   string $dir      relative path and name of the directory to create.
	*                           dir can be a full path of a dir structure to create.
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
	* @param   string $dir         relative path to the directory
	* @param   boolean $recursive  whether or not to recurse down directories
	* @return  int                 size of directory in Bytes, FALSE on failure
	* @author  Joel Kronenberg
	*/
	function getDirectorySize($dir, $recursive = TRUE) {
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
				if (is_dir($path) && ($recursive === TRUE)) {
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


/**
* FileManagerFactory
* Class for creating AbstractFileManager Objects
* @access	public
* @author	Joel Kronenberg
* @package	FileManager
*/
class FileManagerFactory {

	function FileManagerFactory() { }

	function createFileManagerFile($name) {
		$obj = new FileManagerFile($name);
		if ($obj->isOkay()) {
			return $obj;
		}
		return NULL;
	}
	function createFileManagerDirectory($name) {
		$obj= new FileManagerDirectory($name);
		if ($obj->isOkay()) {
			return $obj;
		}
		return NULL;
	}

	function open($name) {
		if (is_dir($name)) {
			$obj = new FileManagerDirectory($name);
		} else if (is_file($name)) {
			$obj = new FileManagerFile($name);
		} else {
			// file not found
			return NULL;
		}
		if ($obj->isOkay()) {
			return $obj;
		}
		return NULL;
	}
}

class AbstractFileManager {
	var $_type; // private
	var $_name; // private
	var $_path; // private
	var $_filename; // private
	var $_exists; // private

	// var $_old_filename;
	// var $_old_path; // maybe?

	var $_fp; // private, file/dir pointer

	function AbstractFileManager( ) {
		$this->contentDirectory = AT_CONTENT_DIR . $_SESSION['course_id'];
	}

	function create() { }

	function isOkay() {
		// this is where the important authentication check is done!
		echo 'authenticating '.$this->_filename.'<br>';
		if (file_exists($this->_path . DIRECTORY_SEPARATOR . $this->_filename)) {
			$this->_exists = TRUE;
		}
		$this->_exists = FALSE;
		if ($this->isIllegalType()) {
			return FALSE;
		}
		return TRUE;
	}
	
	function exists() {
		return $this->_exists;
	}

}

class FileManagerFile extends AbstractFileManager {
	var $_extension; // private

	function FileManagerFile($file) {
		$this->_type = 'file';

		$pathinfo = pathinfo($file);
		$this->_extension = $pathinfo['extension'];
		$this->_path      = $pathinfo['dirname'];
		$this->_filename  = $pathinfo['basename'];

		// set whether or not this file/dir is safe.
	}

	function rename($newName) {
		$return = FALSE;

		$fileManagerFactory = new FileManagerFactory();
		$fileObj = $fileManagerFactory->createFileManagerFile($this->_path . DIRECTORY_SEPARATOR . $newName);
		if (($fileObj !== NULL) && !$fileObj->exists()) {
			if (@rename($this->_path . DIRECTORY_SEPARATOR . $this->_filename, $this->_path . DIRECTORY_SEPARATOR . $newName)) {
				$this->_filename = $newName;
				$return = TRUE;
			}
		}
		return $return;
	}

	function delete() {
		return unlink($this->_path . DIRECTORY_SEPARATOR . $this->_filename);
	}

	function isIllegalType($name = '') {
		// get file extension
		if ($name) {
			$pathinfo = pathinfo($name);
			$ext = $pathinfo['extension'];
		} else {
			$ext = $this->_extension;
		}

		if (in_array($ext, array('txt', 'html'))) {
			return FALSE;
		}
		return TRUE;
	}

	function create($content) {
		if (!is_dir($this->_path)) {
			$fileManagerFactory = new FileManagerFactory();
			$dirObj = $fileManagerFactory->createFileManagerDirectory($this->_path);
			if ($dirObj !== NULL) {
				$dirObj->create(0666);
			}
		}

		// save $contents into $file
		$return = FALSE;
		if (($fp = @fopen($this->_path . DIRECTORY_SEPARATOR . $this->_filename, 'wb+')) !== FALSE) {
			$return = @fwrite($fp, $content, strlen($content));
			@fclose($fp);
		}
		return $return;
	}

}

class FileManagerDirectory extends AbstractFileManager {

	function FileManagerDirectory($dir) {
		$this->_type = 'directory';

		$pathinfo = pathinfo($dir);
		$this->_path     = $pathinfo['dirname'];
		$this->_filename = $pathinfo['basename'];
	}

	function isIllegalType() {
		return FALSE;
	}

	function getDirectoryListing() {

	}

	// creates dir
	function create($mode = 0666) {
		if (is_dir($this->_path)) {
			return @mkdir($this->_path . DIRECTORY_SEPARATOR . $this->_filename, $mode);
		} else {
			$fileManagerFactory = new FileManagerFactory();
			$dirObj = $fileManagerFactory->createFileManagerDirectory($this->_path);
			if ($dirObj !== NULL) {
				if ($dirObj->create(0666) !== FALSE) {
					return @mkdir($this->_path . DIRECTORY_SEPARATOR . $this->_filename, $mode);
				}
			}
		}
	}

	function delete() {}

	function getDirectorySize($recursive = TRUE) {

	}

	// private
	function _getDirectorySize($recursive = TRUE) {

	}

}

$fileManagerFactory = new FileManagerFactory();

$fileObj = $fileManagerFactory->createFileManagerFile('/content/meow.txt');

if ($fileObj !== NULL) {
	$data = 'stuff goes in here';
	if ($fileObj->create($data) !== FALSE) {
		echo 'create good: ' . $fileObj->_filename .' in '. $fileObj->_path;
	}
}
echo '<hr>';

$fileObj = $fileManagerFactory->open('/content/meow.txt');
if ($fileObj !== NULL) {
	if ($fileObj->rename('cow.txt') !== FALSE) {
		echo 'rename good: ' . $fileObj->_filename .' in '. $fileObj->_path;
	}
}

echo '<hr>';

$fileObj = $fileManagerFactory->open('/content/cow.txt');
if ($fileObj !== NULL) {
	if ($fileObj->delete() !== FALSE) {
		echo 'delete good: ' . $fileObj->_filename .' in '. $fileObj->_path;
	}
}

echo '<hr>';

$dirObj = $fileManagerFactory->createFileManagerDirectory('/content/test1/test2/test3/test4/');
if ($dirObj !== NULL) {
	if ($dirObj->create(0666) !== FALSE) {
		echo 'create good: ' . $dirObj->_filename .' in '. $dirObj->_path;
	} else {
		echo 'dir exists: ' . $dirObj->_filename .' in '. $dirObj->_path;
	}
}

echo '<hr>';


$fileObj = $fileManagerFactory->createFileManagerFile('/content/test3/test122/quack.txt');
if ($fileObj !== NULL) {
	$data = 'quack file goes here';
	if ($fileObj->create($data) !== FALSE) {
		echo 'create good: ' . $fileObj->_filename .' in '. $fileObj->_path;
	}
}

echo '<hr>';

/*
- create/overwrite file
- move file
- rename file
- delete file
- copy file


- create dir
- move dir (and its files)
- rename dir
- delete dir
- copy dir


*/
?>