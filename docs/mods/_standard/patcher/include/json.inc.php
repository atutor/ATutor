<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

// my simple JSON encode/decode function, similar to PHP5.2 
function json_encode_result($phpdata) {
	if(gettype($phpdata) == "resource") 
		return php2js_sqlresult($phpdata);
	else if(is_array($phpdata)) 
		return php2js_array($phpdata);
	else
		return php2js_object($phpdata);
}


//function json_decode($jsonata) {
//	return $phpdata;
//}


// convert a PHP object to javascript object
function php2js_object($phpobj) {
	$str = ""; 
	
	if (!is_array($phpobj)) return "[]";
	
	foreach($phpobj as $col => $val) {
	  if($str == "")
	    $str = $col .":'" . escapeString($val) . "'";
	  else
	    $str = $str . "," . $col .":'" . escapeString($val) . "'";
	}
	
	return "{" . $str . "}";
}

// convert a PHP object to javascript object
function php2js_array($phparr) {
	$str = "";

  if (!is_array($phparr)) return "[]";

	foreach ($phparr as $e) {
	  if($str == "") 
			$str = php2js_object($e) ;
	  else
	    $str = $str . "," . php2js_object($e);
	}
	
	return "[" . $str . "]";
}

// convert a SQL result object to javascript object
function php2js_sqlresult($phpsql) {
	// Printing results
	$rows = array();
	while ($line = mysql_fetch_assoc($phpsql)) {
		$rows[] = $line;
	}
	mysql_free_result($phpsql);
	return php2js_array($rows);
}

function escapeString($string) {
    $escape = array(
    "\r\n" => '\n',
    "\r"   => '\n',
    "\n"   => '\n',
    "/"    => '\/'
    );

    return str_replace(array_keys($escape), array_values($escape), addslashes($string));
}	

?>