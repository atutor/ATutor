<?php
/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2010 Heidi Hazelton
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

if (!defined('INCLUDE_PATH')) {
	define('INCLUDE_PATH', 'include/');
}


/* digest auth */
function matterhornAuth($rid, $uri, $content='') {
	global $remote_systems, $this_proj;
		
    $username = $remote_systems[$rid]['username'];
    $password = $remote_systems[$rid]['password'];
		
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    curl_setopt($ch, CURLOPT_URL, $remote_systems[$rid]['url'].$uri);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);   
   	curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-Auth: Digest")); 
	
    if ($content == "media") {
    	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    	
    	$temp_file = 'projects/'.$this_proj->id.'/media'; //use tempname();
    	$fh = fopen($temp_file, 'w');
		curl_setopt($ch, CURLOPT_FILE, $fh);
		curl_exec($ch);
		curl_close($ch);
		fclose($fh); 
		return $temp_file;
		
    } else if (!empty($content)) {
	    curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $content);   
    }
        
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 

    curl_close($ch);

	return $response;

}


?>