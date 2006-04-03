<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

function createToken ($token, $sharedSecret) {
	//String time = Long.toString(System.currentTimeMillis());
	$time = mktime() . '000';

	//String toMd5 = token+time+sharedSecret;
	$toMd5 = $token . $time . $sharedSecret;

	//String enctoken = URLEncoder.encode(token, "UTF-8");
	$enctoken = urlencode($token);

	//byte[] bytes = new Md5(toMd5).getDigest();
	$bytes = pack('H*', md5($toMd5));
	
	//BASE64Encoder encoder = new BASE64Encoder();
	//String fullToken = enctoken+':'+time+':'+encoder.encode(bytes);

	$fullToken = $enctoken . ':' . $time . ':' . base64_encode($bytes);

	return $fullToken;
}

require (AT_INCLUDE_PATH.'header.inc.php');

if (!isset($_config['tle_server']) || !$_config['tle_server']) {
	$msg->printErrors('TLE_MISSING_SERVER');
} else {
	$callback = $_base_href . 'mods/tle/import.php?';

	$url = $_config['tle_server'] . '?method=lms'
				. '&returnurl=' . urlencode($callback)
				. '&action=searchResources'
				. '&returnprefix=tle'
				. '&template=standard'
				. '&token=' . urlencode(createToken($_config['tle_username'], $_config['tle_secret']));

	echo '<iframe src="'. $url . '" width="620" height="600" frameborder="0" /></iframe>';
}
require(AT_INCLUDE_PATH.'footer.inc.php');
?>