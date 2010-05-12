<?php
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
// $Id: zipfile.class.php 7208 2008-01-09 16:07:24Z greg $

//loads the pclzip library.
define('PCLZIP_TEMPORARY_DIR', AT_CONTENT_DIR.'export'.DIRECTORY_SEPARATOR);
include(AT_INCLUDE_PATH.'/classes/pclzip.lib.php');
include(AT_INCLUDE_PATH.'..//mods/_core/file_manager/filemanager.inc.php');	//copy/delete folder

/**
* Class for creating and accessing an archive zip file
* @access	public
* @link		http://www.pkware.com/products/enterprise/white_papers/appnote.html	for the specs
* @author	Joel Kronenberg
*/
class zipfile {


	/**
	 *
	 */
	var $zipfile_dir;

	/**
	* boolean $is_closed - flag set to true if file is closed, false if still open
	* @access  private
	*/
	var $is_closed; 

	/** File name */
	var $filename; 


	/**
	* Constructor method.  Initialises variables.
	* @access	public
	* @author	Joel Kronenberg
	*/
	function zipfile() {
		//create the 
		if (!is_dir(PCLZIP_TEMPORARY_DIR)){
			mkdir(PCLZIP_TEMPORARY_DIR);
			copy(PCLZIP_TEMPORARY_DIR.'../index.html', PCLZIP_TEMPORARY_DIR.'index.html');
		}

		//generate a random hash 
		$this->filename = substr(md5(rand()), 0, 5);

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
debug($dir, 'add dir');
		//copy folder recursively
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
		//deprecated.
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

		if(is_dir(dirname($this->zipfile_dir.$name)) && $name != '.'){
			$this->create_dir(dirname($name));
		} elseif ($name == '' || $name ='.') {
			return;
		}

		//mkdir 

debug($this->zipfile_dir.$name, 'create dir');
		mkdir($this->zipfile_dir.$name);
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
debug($name);
		if (!is_dir(dirname($this->zipfile_dir.$name))){
			$this->create_dir(dirname($this->zipfile_dir.$name));
		}

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
		//save file 

		$archive = new PclZip($this->zipfile_dir.$this->filename.'.zip');
		$v_list = $archive->create($this->zipfile_dir, 
							PCLZIP_OPT_REMOVE_PATH, $this->zipfile_dir);

		//error info
		if ($v_list == 0) {
		die ("Error: " . $archive->errorInfo(true));
		}

//		debug($v_list);		
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
return;
		if (!$this->is_closed) {
			$this->close();
		}
		$file_name = str_replace(array('"', '<', '>', '|', '?', '*', ':', '/', '\\'), '', $file_name);

		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment; filename=$file_name.zip");
		readfile($this->zipfile_dir.$this->filename.'.zip');
		exit;
	}

	/**
	 * Destructor - removes temporary folder and its content.
	 */
	function __destruct(){
//		clr_dir($this->zipfile_dir);
	}
}

?>