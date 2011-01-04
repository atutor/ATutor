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


/**
* Class for creating and accessing an archive zip file
* @access	public
* @link		http://www.pkware.com/products/enterprise/white_papers/appnote.html	for the specs
* @author	Joel Kronenberg
*/
class zipfile {

	/**
	* string $files_data - stores file information like the header and description 
	* @access  public 
	*/
	var $files_data;

	/**
	* string $central_directory_headers - headers necessary for including file in central record
	* @access  public 
	*/
	var $central_directory_headers; 

	/**
	* int $num_entries - a counter for the number of entries in the archive
	* @access  public 
	*/
	var $num_entries = 0;

	/**
	* string $zip_file - complete contents of file
	* @access  public 
	*/
	var $zip_file;

	/**
	* boolean $is_closed - flag set to true if file is closed, false if still open
	* @access  private
	*/
	var $is_closed; 


	/**
	* Constructor method.  Initialises variables.
	* @access	public
	* @author	Joel Kronenberg
	*/
	function zipfile() {
		$this->files_data = '';
		$this->central_directory_headers = '';
		$this->num_entries = 0;
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

		while (($file = readdir($dh)) !== false) {
			/* skip directories */
			if ($file == '.' || $file == '..') {
				continue;
			}
			/* skip potential harmful files/directories */
			if ( (strpos($file, '..') !== false) || (strpos($file, '/') !== false)) {
				continue;
			}

			$file_info = stat( $dir . $pre_pend_dir . $file );

			if (is_dir( $dir . $pre_pend_dir . $file )) {
				/* create this dir in the zip */
				$this->priv_add_dir( $zip_prefix_dir . $pre_pend_dir . $file . '/',
									 $file_info['mtime'] );

				/* continue recursion, going down this dir */
				$this->add_dir(	$dir,
								$zip_prefix_dir,
								$pre_pend_dir . $file . '/' );

			} else {
				/* add this file to the zip */
				$this-> add_file( file_get_contents($dir . $pre_pend_dir . $file),
								  $zip_prefix_dir . $pre_pend_dir . $file,
								  $file_info['mtime'] );
			}
		}
		closedir($dh);
	}

	/**
	* Adding a dir to the archive 
	* @access  private
	* @param   string $name				directory name
	* @param   string $timestamp		time, default=''
	* @author  Joel Kronenberg
	*/
    function priv_add_dir($name, $timestamp = '') {   
        $name = str_replace("\\", "/", $name);   
		$old_offset = strlen($this->files_data);

        $local_file_header  = "\x50\x4b\x03\x04";												// local file header signature 4 bytes (0x04034b50) 
        $local_file_header .= "\x0a\x00";    // ver needed to extract							// version needed to extract 2 bytes
        $local_file_header .= "\x00\x00";    // gen purpose bit flag							// general purpose bit flag 2 bytes
        $local_file_header .= "\x00\x00";    // compression method								// compression method 2 bytes
        $local_file_header .= "\x00\x00\x00\x00"; // last mod time and date					// last mod file time 2 bytes & last mod file date 2 bytes 
        $local_file_header .= pack("V",0); // crc32											// crc-32 4 bytes
        $local_file_header .= pack("V",0); //compressed filesize								// compressed size 4 bytes 
        $local_file_header .= pack("V",0); //uncompressed filesize								// uncompressed size 4 bytes
        $local_file_header .= pack("v", strlen($name) ); //length of pathname					// file name length 2 bytes 
        $local_file_header .= pack("v", 0 ); //extra field length								// extra field length 2 bytes		
        $local_file_header .= $name;															// file name (variable size)  & extra field (variable size)
        // end of "local file header" segment 

        // no "file data" segment for path 

        // add this entry to array 
        $this->files_data .= $local_file_header;

        // ext. file attributes mirrors MS-DOS directory attr byte, detailed 
        // at http://support.microsoft.com/support/kb/articles/Q125/0/19.asp 

		if ($timestamp) {
			$v_date = getdate($timestamp);
		} else {
			$v_date = getdate();
		}
		$time = ($v_date['hours']<<11) + ($v_date['minutes']<<5) + $v_date['seconds']/2;
		$date = (($v_date['year']-1980)<<9) + ($v_date['mon']<<5) + $v_date['mday'];

        // now add to central record 
        $central_directory = "\x50\x4b\x01\x02";											// central file header signature 4 bytes (0x02014b50)
        $central_directory .="\x14\x00";    // version made by								// version made by 2 bytes
        $central_directory .="\x14\x00";    // version needed to extract					// version needed to extract 2 bytes
        $central_directory .="\x00\x00";    // gen purpose bit flag							// general purpose bit flag 2 bytes
        $central_directory .="\x00\x00";    // compression method							// compression method 2 bytes
		$central_directory .= pack("v",$time); // time										// last mod file time 2 bytes
        $central_directory .= pack("v",$date); // date										// last mod file date 2 bytes
        $central_directory .= pack("V", 0); // crc32										// crc-32 4 bytes
        $central_directory .= pack("V", 0); // compressed filesize							// compressed size 4 bytes
        $central_directory .= pack("V", 0); // uncompressed filesize						// uncompressed size 4 bytes
        $central_directory .= pack("v", strlen($name) ); //length of filename				// file name length 2 bytes
        $central_directory .= pack("v", 0); // extra field length							// extra field length 2 bytes
        $central_directory .= pack("v", 0); // file comment length							// file comment length 2 bytes 
        $central_directory .= pack("v", 0); // disk number start							// disk number start 2 bytes
        $central_directory .= pack("v", 0); // internal file attributes						// internal file attributes 2 bytes
        $central_directory .= pack("V", 16+32); //external file attributes  - 'directory' 'archive' bit set // external file attributes 4 bytes
        $central_directory .= pack("V", $old_offset); //relative offset of local header // relative offset of local header 4 bytes
        $central_directory .= $name;														// file name (variable size)

    	$this->central_directory_headers .= $central_directory;

		$this->num_entries++;
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

		if (substr($name, -1) != '/') {
			/* add the trailing slash */
			$name .= '/';
		}

		$this->priv_add_dir($name, $timestamp = '');
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
        $crc = crc32($file_data);
        $uncompressed_size = strlen($file_data);
		$file_data = substr(gzcompress($file_data, 9), 2, -4);
        $compressed_size = strlen($file_data);
		$old_offset = strlen($this->files_data);

		/* local file header */
        $local_file_header = "\x50\x4b\x03\x04";								// local file header signature 4 bytes (0x04034b50) 
        $local_file_header .= "\x14\x00";    // ver needed to extract			// version needed to extract 2 bytes 
        $local_file_header .= "\x00\x00";    // gen purpose bit flag			// general purpose bit flag 2 bytes 
        $local_file_header .= "\x08\x00";    // compression method				// compression method 2 bytes 
        $local_file_header .= "\x00\x00\x00\x00"; // last mod time and date	// last mod file time 2 bytes & last mod file date 2 bytes 
        $local_file_header .= pack("V",$crc); // crc32							// crc-32 4 bytes 
        $local_file_header .= pack("V",$compressed_size); //compressed filesize			// compressed size 4 bytes 
        $local_file_header .= pack("V",$uncompressed_size); //uncompressed filesize		// uncompressed size 4 bytes 
        $local_file_header .= pack("v", strlen($name) ); //length of filename  // file name length 2 bytes 
        $local_file_header .= "\x00\x00"; //extra field length				// extra field length 2 bytes 
        $local_file_header .= $name;											// file name (variable size)  & extra field (variable size) 
		/* end of local file header */
          
		$this->files_data .= $local_file_header . $file_data; // . $data_descriptor;;

		/* create the central directory */
		$central_directory = '';
		if ($timestamp) {
			$v_date = getdate($timestamp);
		} else {
			$v_date = getdate();
		}
		$time = ($v_date['hours']<<11) + ($v_date['minutes']<<5) + $v_date['seconds']/2;
		$date = (($v_date['year']-1980)<<9) + ($v_date['mon']<<5) + $v_date['mday'];

        // now add to central directory record 
        $central_directory = "\x50\x4b\x01\x02";											// central file header signature 4 bytes (0x02014b50)
        $central_directory .="\x14\x00";    // version made by								// version made by 2 bytes 
        $central_directory .="\x14\x00";    // version needed to extract					// version needed to extract 2 bytes 
        $central_directory .="\x00\x00";    // gen purpose bit flag							// general purpose bit flag 2 bytes 
        $central_directory .="\x08\x00";    // compression method							// compression method 2 bytes         
        $central_directory .= pack("v",$time); // time										// last mod file time 2 bytes 
		$central_directory .= pack("v",$date); // date										// last mod file date 2 bytes 
		$central_directory .= pack("V",$crc); // crc32										// crc-32 4 bytes 
        $central_directory .= pack("V",$compressed_size); //compressed filesize						// compressed size 4 bytes 
        $central_directory .= pack("V",$uncompressed_size); //uncompressed filesize					// uncompressed size 4 bytes 
        $central_directory .= pack("v", strlen($name) ); //length of filename				// file name length 2 bytes 
        $central_directory .= "\x00\x00"; //extra field length							// extra field length 2 bytes 
        $central_directory .= "\x00\x00"; //file comment length							// file comment length 2 bytes 
        $central_directory .= "\x00\x00"; //disk number start							// disk number start 2 bytes 
        $central_directory .= "\x00\x00"; //internal file attributes						// internal file attributes 2 bytes 
        $central_directory .= pack("V", 32); //external file attributes - 'archive' bit set // external file attributes 4 bytes 
		$central_directory .= pack("V", $old_offset);

        $central_directory .= $name;														// file name (variable size)

		$this->central_directory_headers .= $central_directory;
	
		$this->num_entries++;
    } 

	/**
	* Closes archive, sets $is_closed to true
	* @access  public
	* @param   none
	* @author  Joel Kronenberg
	*/
	function close() {
		$this->files_data .= $this->central_directory_headers . "\x50\x4b\x05\x06\x00\x00\x00\x00" .   
            pack("v", $this->num_entries).     // total # of entries "on this disk" 
            pack("v", $this->num_entries).     // total # of entries overall 
            pack("V", strlen($this->central_directory_headers)).             // size of central dir 
            pack("V", strlen($this->files_data)).                 // offset to start of central dir 
            "\x00\x00"; 

		unset($this->central_directory_headers);
		unset($this->num_entries);

		$this->zip_file =& $this->files_data;
		$this->is_closed = true;
	}

    /**
	* Gets size of new archive
	* Only call this after calling close() - will return false if the zip wasn't close()d yet
	* @access  public
	* @return  int	size of file
	* @author  Joel Kronenberg
	*/
	function get_size() {
		if (!$this->is_closed) {
			return false;
		}
		return strlen($this->zip_file);
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
		return $this->zip_file;
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
		if (function_exists('file_put_contents')) {
			file_put_contents($file, $this->zip_file);
		} else {
			$fp = fopen($file, 'wb+');
			fwrite($fp, $this->zip_file);
			fclose($fp);
		}
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

		header('Content-Type: application/x-zip');
		header('Content-transfer-encoding: binary'); 
		header('Content-Disposition: attachment; filename="'.htmlspecialchars($file_name).'.zip"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: '.$this->get_size());

		echo $this->get_file();

		exit;
	}
}

?>