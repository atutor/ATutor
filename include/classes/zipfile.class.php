<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id: zipfile.class.php 10290 2010-10-05 16:02:43Z cindy $

define('PCLZIP_TEMPORARY_DIR', AT_CONTENT_DIR.'export'.DIRECTORY_SEPARATOR);  //constant for the temp folder.
include(AT_INCLUDE_PATH.'classes/pclzip.lib.php');	 //loads the pclzip library.
include_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');	//copy/delete folder

/**
* Class for creating and accessing an archive zip file.  Originally written by Joel Kronenberg,
* edited by Harris Wong to use the PCLZIP library (http://www.phpconcept.net)
*
* As of ATutor 2.0, this file will extend the pclzip library functions instead of using Joel's.  
* The function preconditions and postconditions will remain the same however. 
*
* @access	public
* @link		http://www.pkware.com/documents/casestudies/APPNOTE.TXT	for the specs
* @author	Joel Kronenberg
*/
class zipfile {

	/**
	 * string $zipfile_dir - the actual system directory that stores the temporary files for archiving.
	 * @access	private
	 */
	var $zipfile_dir;

	/**
	* boolean $is_closed - flag set to true if file is closed, false if still open
	* @access  private
	*/
	var $is_closed; 

	/** 
	 * string $filename -	randomized filename of this zip instance.  It also shares the same name
	 *						as the folder under the export/ folder.  
	 */
	var $filename; 


	/**
	* Constructor method.  Initialises variables.
	* @access	public
	* @author	Joel Kronenberg
	*/
	function zipfile() {
		//create the temp folder for export if it hasn't been created.
		if (!is_dir(PCLZIP_TEMPORARY_DIR)){
			mkdir(PCLZIP_TEMPORARY_DIR);
			copy(PCLZIP_TEMPORARY_DIR.'../index.html', PCLZIP_TEMPORARY_DIR.'index.html');
		}

		//generate a random hash 
		$this->filename = substr(md5(rand()), 0, 5);

		//create a temporary folder for this zip instance
		$this->zipfile_dir = PCLZIP_TEMPORARY_DIR.$this->filename.DIRECTORY_SEPARATOR;
		mkdir($this->zipfile_dir);
		$this->is_closed = false;
	}


	/**
	* Public interface for adding a dir and its contents recursively to zip file
	* @access  public
	* @param   string $dir				the real system directory that contains the files to add to the zip		 
	* @param   string $zip_prefix_dir	the zip dir where the contents of $dir will be put in
	* @param   string $pre_pend_dir		used during the recursion to keep track of the path, default=''
	* @see     $_base_path				in include/vitals.inc.php
	* @see     priv_add_dir()			in include/classes/zipfile.class.php
	* @see     add_file()				in include/classes/zipfile.class.php
	* @author  Joel Kronenberg
	*/
	function add_dir($dir, $zip_prefix_dir, $pre_pend_dir='') {
		if (!($dh = @opendir($dir.$pre_pend_dir))) {
			echo 'cant open dir: '.$dir.$pre_pend_dir;
			exit;		
		}
		//copy folder recursively into the temp folder.
		copys($dir, $this->zipfile_dir.DIRECTORY_SEPARATOR.$zip_prefix_dir);
	}

	/**
	* Adding a dir to the archive 
	* @access  private
	* @param   string $name				directory name
	* @param   string $timestamp		time, default=''
	* @author  Joel Kronenberg
	*/
    function priv_add_dir($name, $timestamp = '') {   
		//deprecated as of ATutor 2.0
    } 
	
	/**
	* Public interface to create a directory in the archive.
	* @access  public
	* @param   string $name				directory name
	* @param   string $timestamp		time of creation, default=''
	* @see     $_base_path				in include/vitals.inc.php
	* @see     priv_add_dir()			in include/zipfile.class.php
	* @author  Joel Kronenberg
	*/
	function create_dir($name, $timestamp='') {
		$name = trim($name);
		//don't create a folder if it is itself
		if ($name=='' || $name=='.'){
			return;
		}

		$parent_folder = dirname($name);
		if (!is_dir($this->zipfile_dir.$name) && ($parent_folder=='.' || $parent_folder=='')){
			//base case
			mkdir($this->zipfile_dir.$name);
			return;
		} else {
			//recursion step
			$this->create_dir(dirname($name));
		}

		//returned stack. continue from where it left off.  		
		if (!is_dir($this->zipfile_dir.$name)){
			//the parent folder should be created at this point, create itself
			mkdir($this->zipfile_dir.$name);
		}
	}


	/**
	* Adds a file to the archive.
	* @access  public
	* @param   string $file_data		file contents
	* @param   string $name				name of file in archive (add path if your want)
	* @param   string $timestamp		time of creation, default=''
	* @see     $_base_path				in include/vitals.inc.php
	* @see     priv_add_dir()			in include/zipfile.class.php
	* @author  Joel Kronenberg
	*/
    function add_file($file_data, $name, $timestamp = '') {
        $name = str_replace("\\", "/", $name);

		//check if folder exists, if not, create it.
		if (!is_dir($this->zipfile_dir.dirname($name))){
			$this->create_dir(dirname($name));
		}

		//write to file
		$fp = fopen($this->zipfile_dir.$name, 'w');
		fwrite($fp, $file_data);
		fclose($fp);		
    } 

	/**
	* Closes archive, sets $is_closed to true
	* @access  public
	* @param   none
	* @author  Joel Kronenberg
	*/
	function close() {
		//use pclzip to compress the file, and save it in the temp folder.
		$archive = new PclZip($this->zipfile_dir.$this->filename.'.zip');
		$v_list = $archive->create($this->zipfile_dir, 
							PCLZIP_OPT_REMOVE_PATH, $this->zipfile_dir);

		//error info
		if ($v_list == 0) {
		die ("Error: " . $archive->errorInfo(true));
		}
		
		$this->is_closed = true;
	}

    /**
	* Gets size of new archive
	* Only call this after calling close() - will return false if the zip wasn't close()d yet
	* @access  public
	* @return  int	size of file in byte.
	* @author  Joel Kronenberg
	*/
	function get_size() {
		if (!$this->is_closed) {
			return false;
		}

		//file path
		$filepath = $this->zipfile_dir.$this->filename.'.zip';
		if (file_exists($filepath)){
			return filesize($filepath);
		} 

		return false;
	}


    /**
	* Returns binary file
	* @access	public
	* @see		get_size()		in include/classes/zipfile.class.php
	* @author  Joel Kronenberg
	*/	
	function get_file() {
		if (!$this->is_closed) {
			$this->close();
		}
		return file_get_contents($this->zipfile_dir.$this->filename.'.zip');
    }

	/**
	* Writes the file to disk.
	* Similar to get_file(), but instead of returning the file, it saves it to disk.
	* @access  public
	* @author  Joel Kronenberg
	* @param  $file The full path and file name of the destination file.
	*/
	function write_file($file) {
		if (!$this->is_closed) {
			$this->close();
		}		
		copy($this->zipfile_dir.$this->filename.'.zip', $file);
	}


    /**
	* Outputs the file - sends headers to browser to force download
	* Only call this after calling close() - will return false if the zip wasn't close()d yet
	* @access	public
	* @see		get_size()		in include/classes/zipfile.class.php
	* @author  Joel Kronenberg
	*/
	function send_file($file_name) {
		if (!$this->is_closed) {
			$this->close();
		}
		$file_name = str_replace(array('"', '<', '>', '|', '?', '*', ':', '/', '\\'), '', $file_name);
		
		header("Content-type: archive/zip");
		header("Content-disposition: attachment; filename=$file_name.zip");
		
		readfile_in_chunks($this->zipfile_dir.$this->filename.'.zip');
		exit;
	}

	/**
	 * Destructor - removes temporary folder and its content.
	 * Should self-destruct automatically for PHP 5.0+; otherwise developers should call this function
	 * to clean up.
	 * @access	public
	 * @author	Harris Wong
	 */
	function __destruct(){
		clr_dir($this->zipfile_dir);
	}
}

?>