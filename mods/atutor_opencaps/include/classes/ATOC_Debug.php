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

class AtOpenCapsDebug
{

	/**
	 * Static function: shows AT session and cookie variables 
	 */
	static public function _seeAlSessionVars()
	{
		// trace $_SESSION
		
		if (count($_SESSION) == 0)
		{
		 echo '<br/> Noting in $_SESSION ';
		} else {
		
		echo '<h3>Displaying PHP $_SESSION and $_COOKIE variables</h3>';
		echo '<br/><p>$_SESSION vars</p>';
			while ($var = each($_SESSION))
		    {
			    printf ("Key <b>%s</b> = <b>%s</b><br>", $var['key'], $var['value']);
		    } 
		}
		
		if (count($_COOKIE) == 0)
		{
		 echo '<br/> No cookies set';
		} else {
		
		echo '<br/><br/><p>$_COOKIE vars</p>';
		while ($var1 = each($_COOKIE))
		    {
		    printf ("Key <b>%s</b> = <b>%s</b><br>", $var1['key'], $var1['value']);
		    } 
		}
		echo '<br/><br/>';
	} // end _seeAlSessionVars
	
} // end AtOpenCapsDebug class
?>