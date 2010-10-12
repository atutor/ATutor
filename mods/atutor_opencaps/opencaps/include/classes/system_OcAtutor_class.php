<?php
/****************************************************************/
/* OpenCaps Module						
/****************************************************************/
/* Copyright (c) 2010                           
/* Written by Antonio Gamba						
/* Adaptive Technology Resource Centre / University of Toronto
/*
/* This program is free software. You can redistribute it and/or
/* modify it under the terms of the GNU General Public License
/* as published by the Free Software Foundation.
/****************************************************************/

class OcAtutor
{
	public static function putCaps($uri, $action, $id, $ccData)
	{
		if ($ccData!='The format of source Caption was not recognized.')
		{
			//$uri = '../../service.php?';
			$uri .= '?';
			$ch = curl_init($uri);
			$encoded = 'id='.$id.'&action='.$action.'&cc='.''.urlencode($ccData).'';
			curl_setopt($ch, CURLOPT_POSTFIELDS,  $encoded);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_exec($ch);
			curl_close($ch);
			if(OC_DEBUG_MODE_ON)
			{
				echo '<br/> Encoded Data sent to Server:<br/> '.$encoded.'<br/>';
			}
		} // end putCaps
	
	} // end class
}
?>