<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: zipfile.class.php,v 1.5 2004/02/18 16:00:04 joel Exp $

/* 

for the specs:
http://www.pkware.com/products/enterprise/white_papers/appnote.html


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

class zipfile
{
    var $datasec		= array(); // array to store compressed data 
    var $ctrl_dir		= array(); // central directory    
    var $eof_ctrl_dir	= "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record
    var $old_offset = 0;
	var $current_offset = 0;

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

        $fr  = "\x50\x4b\x03\x04";												// local file header signature 4 bytes (0x04034b50) 
        $fr .= "\x0a\x00";    // ver needed to extract							// version needed to extract 2 bytes
        $fr .= "\x00\x00";    // gen purpose bit flag							// general purpose bit flag 2 bytes
        $fr .= "\x00\x00";    // compression method								// compression method 2 bytes
        $fr .= "\x00\x00\x00\x00"; // last mod time and date					// last mod file time 2 bytes & last mod file date 2 bytes 

        $fr .= pack("V",0); // crc32											// crc-32 4 bytes
        $fr .= pack("V",0); //compressed filesize								// compressed size 4 bytes 
        $fr .= pack("V",0); //uncompressed filesize								// uncompressed size 4 bytes
        $fr .= pack("v", strlen($name) ); //length of pathname					// file name length 2 bytes 
        $fr .= pack("v", 0 ); //extra field length								// extra field length 2 bytes		
        $fr .= $name;															// file name (variable size)  & extra field (variable size)
        // end of "local file header" segment 

        // no "file data" segment for path 

        // "data descriptor" segment (optional but necessary if archive is not served as file) 
        //$fr .= pack("V",$crc); //crc32											// crc-32 4 bytes 
        //$fr .= pack("V",$c_len); //compressed filesize							// compressed size 4 bytes
        //$fr .= pack("V",$unc_len); //uncompressed filesize						// uncompressed size 4 bytes 

        // add this entry to array 
        $this->datasec[] = $fr; 

		//$new_offset = $this->old_offset + strlen($fr) ;

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
        $cdrec = "\x50\x4b\x01\x02";											// central file header signature 4 bytes (0x02014b50)
        $cdrec .="\x14\x00";    // version made by								// version made by 2 bytes
        $cdrec .="\x14\x00";    // version needed to extract					// version needed to extract 2 bytes
        $cdrec .="\x00\x00";    // gen purpose bit flag							// general purpose bit flag 2 bytes
        $cdrec .="\x00\x00";    // compression method							// compression method 2 bytes

		$cdrec .= pack("v",$time); // time										// last mod file time 2 bytes
        $cdrec .= pack("v",$date); // date										// last mod file date 2 bytes

        $cdrec .= pack("V", 0); // crc32										// crc-32 4 bytes
        $cdrec .= pack("V", 0); // compressed filesize							// compressed size 4 bytes
        $cdrec .= pack("V", 0); // uncompressed filesize						// uncompressed size 4 bytes
        $cdrec .= pack("v", strlen($name) ); //length of filename				// file name length 2 bytes
        $cdrec .= pack("v", 0); // extra field length							// extra field length 2 bytes
        $cdrec .= pack("v", 0); // file comment length							// file comment length 2 bytes 
        $cdrec .= pack("v", 0); // disk number start							// disk number start 2 bytes
        $cdrec .= pack("v", 0); // internal file attributes						// internal file attributes 2 bytes

        $cdrec .= pack("V", 16+32); //external file attributes  - 'directory' 'archive' bit set // external file attributes 4 bytes
        $cdrec .= pack("V", $this->old_offset); //relative offset of local header // relative offset of local header 4 bytes

        //$new_offset = strlen(implode('', $this->datasec)); 
        $this->old_offset += strlen($fr); //$new_offset; 

        $cdrec .= $name;														// file name (variable size)
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

        $fr = "\x50\x4b\x03\x04";								// local file header signature 4 bytes (0x04034b50) 
        $fr .= "\x14\x00";    // ver needed to extract			// version needed to extract 2 bytes 
        $fr .= "\x00\x00";    // gen purpose bit flag			// general purpose bit flag 2 bytes 
        $fr .= "\x08\x00";    // compression method				// compression method 2 bytes 
        $fr .= "\x00\x00\x00\x00"; // last mod time and date	// last mod file time 2 bytes & last mod file date 2 bytes 

        $unc_len = strlen($data);
        $crc = crc32($data);

		$zdata = substr(gzcompress($data, 9), 2, -4);

        $c_len = strlen($zdata);
        $fr .= pack("V",$crc); // crc32							// crc-32 4 bytes 
        $fr .= pack("V",$c_len); //compressed filesize			// compressed size 4 bytes 
        $fr .= pack("V",$unc_len); //uncompressed filesize		// uncompressed size 4 bytes 
        $fr .= pack("v", strlen($name) ); //length of filename  // file name length 2 bytes 
        $fr .= pack("v", 0); //extra field length				// extra field length 2 bytes 
        $fr .= $name;											// file name (variable size)  & extra field (variable size) 
        // end of "local file header" segment
          
        // "file data" segment 
        $fr .= $zdata;
		

        // "data descriptor" segment (optional but necessary if archive is not served as file) 
        //$fr .= pack("V",$crc); //crc32							// crc-32 4 bytes 
        //$fr .= pack("V",$c_len); //compressed filesize			// compressed size 4 bytes 
        //$fr .= pack("V",$unc_len); //uncompressed filesize		// uncompressed size 4 bytes 

        // add this entry to array 
        $this -> datasec[] = $fr;

		if ($timestamp) {
			$v_date = getdate($timestamp);
		} else {
			$v_date = getdate();
		}
		$time = ($v_date['hours']<<11) + ($v_date['minutes']<<5) + $v_date['seconds']/2;
		$date = (($v_date['year']-1980)<<9) + ($v_date['mon']<<5) + $v_date['mday'];

        // now add to central directory record 
        $cdrec = "\x50\x4b\x01\x02";											// central file header signature 4 bytes (0x02014b50)
        $cdrec .="\x14\x00";    // version made by								// version made by 2 bytes 
        $cdrec .="\x14\x00";    // version needed to extract					// version needed to extract 2 bytes 
        $cdrec .="\x00\x00";    // gen purpose bit flag							// general purpose bit flag 2 bytes 
        $cdrec .="\x08\x00";    // compression method							// compression method 2 bytes 
        
        $cdrec .= pack("v",$time); // time										// last mod file time 2 bytes 
        $cdrec .= pack("v",$date); // date										// last mod file date 2 bytes 
				
		$cdrec .= pack("V",$crc); // crc32										// crc-32 4 bytes 
        $cdrec .= pack("V",$c_len); //compressed filesize						// compressed size 4 bytes 
        $cdrec .= pack("V",$unc_len); //uncompressed filesize					// uncompressed size 4 bytes 
        $cdrec .= pack("v", strlen($name) ); //length of filename				// file name length 2 bytes 
        $cdrec .= pack("v", 0 ); //extra field length							// extra field length 2 bytes 
        $cdrec .= pack("v", 0 ); //file comment length							// file comment length 2 bytes 
        $cdrec .= pack("v", 0 ); //disk number start							// disk number start 2 bytes 
        $cdrec .= pack("v", 0 ); //internal file attributes						// internal file attributes 2 bytes 
        $cdrec .= pack("V", 32 ); //external file attributes - 'archive' bit set // external file attributes 4 bytes 

        $cdrec .= pack("V", $this -> old_offset); //relative offset of local header // relative offset of local header 4 bytes 

		//$new_offset = strlen(implode('', $this->datasec));
		//$new_offset += $this_entry_size;
		$this -> old_offset += strlen($fr);

        $cdrec .= $name;														// file name (variable size)
        // optional extra field, file comment goes here 
        // save to central directory 
        $this -> ctrl_dir[] = $cdrec;   
    } 

    function file() { // dump out file    
        $data = implode('', $this -> datasec);   
        $ctrldir = implode('', $this -> ctrl_dir);   

        $zip = $data. $ctrldir. $this -> eof_ctrl_dir.   
            pack("v", count($this -> ctrl_dir)).     // total # of entries "on this disk" 
            pack("v", count($this -> ctrl_dir)).     // total # of entries overall 
            pack("V", strlen($ctrldir)).             // size of central dir 
            pack("V", strlen($data)).                 // offset to start of central dir 
            "\x00\x00";                             // .zip file comment length 

		return $zip;
    } 
}   

?>