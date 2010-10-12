<?php
/****************************************************************/
/* Atutor-OpenCaps Module						
/****************************************************************/
/* Copyright (c) 2010                           
/* Written by Antonio Gamba						
/* Adaptive Technology Resource Centre / University of Toronto
/*
/* This program is free software. You can redistribute it and/or
/* modify it under the terms of the GNU General Public License
/* as published by the Free Software Foundation.
/****************************************************************/

class ServerFiles
{
	
	/**
	 * @desc directoryToArray
	 * @param String $directory
	 * @param bool $recursive
	 * @return Array
	 */
	public function directoryToArray($directory, $recursive) 
	{
		global $ocAtSettings;
		
		$rex = '/^.*\.('.$ocAtSettings['supportedMedia'].')$/i';
		$sep = '\\';
		$array_items = array();
		if ($handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if (is_dir($directory. $sep . $file)) {
						if($recursive) 
						{
							$array_items = array_merge($array_items,$this->directoryToArray($directory. $sep . $file, $recursive));
						}
						//add if add only known files
						if (preg_match($rex, $file))
						{
							//$file = $directory . $sep . $file;
							$array_items[] = preg_replace("/\/\//si", $sep, $file);
							//echo '<br/>'.$file;	
						}
						
					} else {
						if (preg_match($rex, $file))
						{
							$file = $directory . $sep . $file;
							//$file = $sep . $file;
							$file = str_replace(AT_CONTENT_DIR.''.$_SESSION['course_id'].$sep, '', $file);
							$file = str_replace('\\', '/', $file);
							$array_items[] = preg_replace("/\/\//si", $sep, $file);
							//echo '<br/>'.$file;	
						}
					}
				}
			}
			closedir($handle);
		}
		return $array_items;
	} // end directoryToArray
	
} // end class ServerFiles
?>
