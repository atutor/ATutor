<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

/* 

v.1.2 Sep 10, 2003
- Major reworking by Joel Kronenberg/ATRC UofT - joel.kronenberg@utoronto.ca
- Fixed unzip warnings and directory problems.
- Made code more efficient and faster
- added timestamp option
- added recursive directory compression

Zip file creation class 
makes zip files on the fly... 

use the functions add_dir() and add_file() to build the zip file; 
see example code below 

by Eric Mueller 
http://www.themepark.com 

v1.1 9-20-01 
  - added comments to example 

v1.0 2-5-01 

initial version with: 
  - class appearance 
  - add_file() and file() methods 
  - gzcompress() output hacking 
by Denis O.Philippov, webmaster@atlant.ru, http://www.atlant.ru 

*/ 

set_time_limit(0);

class zipfile
{
    var $datasec		= array(); // array to store compressed data 
    var $ctrl_dir		= array(); // central directory    
    var $eof_ctrl_dir	= "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record
    var $old_offset = 0;

	/* public interface to adding a dir */
	/* $dir: the real system directory that contains the files to add to the zip */
	/* $zip_prefix_dir: the zip dir where the contents of $dir will be put in */
	/* $pre_pend_dir: used during the recursion to keep track of the path */
	function add_dir ( $dir, $zip_prefix_dir, $pre_pend_dir='' ) {
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

	// private add_dir
    // adds "directory" to archive - do this before putting any files in directory! 
    // $name - name of directory... like this: "path/" 
    // ...then you can add files using add_file with names like "path/file.txt" 
    function priv_add_dir($name, $timestamp = '')    
    {   
        $name = str_replace("\\", "/", $name);   

        $fr  = "\x50\x4b\x03\x04"; 
        $fr .= "\x0a\x00";    // ver needed to extract 
        $fr .= "\x00\x00";    // gen purpose bit flag 
        $fr .= "\x00\x00";    // compression method 
        $fr .= "\x00\x00\x00\x00"; // last mod time and date 

        $fr .= pack("V",0); // crc32 
        $fr .= pack("V",0); //compressed filesize 
        $fr .= pack("V",0); //uncompressed filesize 
        $fr .= pack("v", strlen($name) ); //length of pathname 
        $fr .= pack("v", 0 ); //extra field length 
        $fr .= $name;   
        // end of "local file header" segment 

        // no "file data" segment for path 

        // "data descriptor" segment (optional but necessary if archive is not served as file) 
        $fr .= pack("V",$crc); //crc32 
        $fr .= pack("V",$c_len); //compressed filesize 
        $fr .= pack("V",$unc_len); //uncompressed filesize 

        // add this entry to array 
        $this->datasec[] = $fr; 

        //$new_offset = strlen(implode("", $this->datasec)); 
		$new_offset = $this->old_offset + strlen($fr) ;

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
        $cdrec = "\x50\x4b\x01\x02"; 
        $cdrec .="\x00\x00";    // version made by 
        $cdrec .="\x0a\x00";    // version needed to extract 
        $cdrec .="\x00\x00";    // gen purpose bit flag 
        $cdrec .="\x00\x00";    // compression method 

		//$cdrec .="\x00\x00\x00\x00"; // last mod time & date 
		$cdrec .= pack("v",$time); // time
        $cdrec .= pack("v",$date); // date

        $cdrec .= pack("V", 0); // crc32 
        $cdrec .= pack("V", 0); // compressed filesize 
        $cdrec .= pack("V", 0); // uncompressed filesize 
        $cdrec .= pack("v", strlen($name) ); //length of filename 
        $cdrec .= pack("v", 0); // extra field length    
        $cdrec .= pack("v", 0); // file comment length 
        $cdrec .= pack("v", 0); // disk number start 
        $cdrec .= pack("v", 0); // internal file attributes 
        $ext = "\x00\x00\x10\x00"; 
        $ext = "\xff\xff\xff\xff";   
        $cdrec .= pack("V", 16); //external file attributes  - 'directory' bit set 
        $cdrec .= pack("V", $this->old_offset); //relative offset of local header 
        $this->old_offset = $new_offset; 

        $cdrec .= $name;   
        // optional extra field, file comment goes here 
        // save to array 
        $this->ctrl_dir[] = $cdrec;   
    } 


    // adds "file" to archive    
    // $data - file contents 
    // $name - name of file in archive. Add path if your want 
    function add_file($data, $name, $timestamp = '')    
    {   
        $name = str_replace("\\", "/", $name);   

        $fr = "\x50\x4b\x03\x04"; 
        $fr .= "\x14\x00";    // ver needed to extract 
        $fr .= "\x00\x00";    // gen purpose bit flag 
        $fr .= "\x08\x00";    // compression method 
        $fr .= "\x00\x00\x00\x00"; // last mod time and date 

        $unc_len = strlen($data);
        $crc = crc32($data);

		$zdata = substr(gzcompress($data, 9), 2, -4);

        $c_len = strlen($zdata);
        $fr .= pack("V",$crc); // crc32
        $fr .= pack("V",$c_len); //compressed filesize
        $fr .= pack("V",$unc_len); //uncompressed filesize
        $fr .= pack("v", strlen($name) ); //length of filename
        $fr .= pack("v", 0 ); //extra field length
        $fr .= $name;
        // end of "local file header" segment
          
        // "file data" segment 
        $fr .= $zdata;   

        // "data descriptor" segment (optional but necessary if archive is not served as file) 
        $fr .= pack("V",$crc); //crc32 
        $fr .= pack("V",$c_len); //compressed filesize 
        $fr .= pack("V",$unc_len); //uncompressed filesize 

        // add this entry to array 
        $this -> datasec[] = $fr; 

        $new_offset = strlen(implode("", $this->datasec)); 

		if ($timestamp) {
			$v_date = getdate($timestamp);
		} else {
			$v_date = getdate();
		}
		$time = ($v_date['hours']<<11) + ($v_date['minutes']<<5) + $v_date['seconds']/2;
		$date = (($v_date['year']-1980)<<9) + ($v_date['mon']<<5) + $v_date['mday'];

        // now add to central directory record 
        $cdrec = "\x50\x4b\x01\x02"; 
        $cdrec .="\x00\x00";    // version made by 
        $cdrec .="\x14\x00";    // version needed to extract 
        $cdrec .="\x00\x00";    // gen purpose bit flag 
        $cdrec .="\x08\x00";    // compression method 
        
		//$cdrec .="\x00\x00\x00\x00"; // last mod time & date 
        $cdrec .= pack("v",$time); // time
        $cdrec .= pack("v",$date); // date
		
		
		$cdrec .= pack("V",$crc); // crc32 
        $cdrec .= pack("V",$c_len); //compressed filesize 
        $cdrec .= pack("V",$unc_len); //uncompressed filesize 
        $cdrec .= pack("v", strlen($name) ); //length of filename 
        $cdrec .= pack("v", 0 ); //extra field length    
        $cdrec .= pack("v", 0 ); //file comment length 
        $cdrec .= pack("v", 0 ); //disk number start 
        $cdrec .= pack("v", 0 ); //internal file attributes 
        $cdrec .= pack("V", 32 ); //external file attributes - 'archive' bit set 

        $cdrec .= pack("V", $this -> old_offset ); //relative offset of local header 
        $this -> old_offset = $new_offset; 

        $cdrec .= $name;   
        // optional extra field, file comment goes here 
        // save to central directory 
        $this -> ctrl_dir[] = $cdrec;   
    } 

    function file() { // dump out file    
        $data = implode("", $this -> datasec);   
        $ctrldir = implode("", $this -> ctrl_dir);   

        return    
            $data.   
            $ctrldir.   
            $this -> eof_ctrl_dir.   
            pack("v", sizeof($this -> ctrl_dir)).     // total # of entries "on this disk" 
            pack("v", sizeof($this -> ctrl_dir)).     // total # of entries overall 
            pack("V", strlen($ctrldir)).             // size of central dir 
            pack("V", strlen($data)).                 // offset to start of central dir 
            "\x00\x00";                             // .zip file comment length 
    } 
}   

?>