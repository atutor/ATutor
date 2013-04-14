<?php

//
//  Copyright (c) 2011, Maths for More S.L. http://www.wiris.com
//  This file is part of WIRIS Plugin.
//
//  WIRIS Plugin is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  any later version.
//
//  WIRIS Plugin is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with WIRIS Plugin. If not, see <http://www.gnu.org/licenses/>.
//

include 'libwiris.php';

if (isset($_POST['service'])) {
	global $config;
	
	$config = wrs_loadConfig(WRS_CONFIG_FILE);
	$url = wrs_getImageServiceURL($config, $_POST['service']);
	$data = array();
	
	foreach ($_POST as $key => $value) {
		if ($key != 'service') {
			$data[$key] = $value;
		}
	}

	$response = wrs_getContents($config, $url, $data);

	if ($response !== false) {
		echo $response;
	}
	else {
		echo 'Error: the service is unavailable.';
	}
}
else {
	echo 'Error: undefined service.';
}
?>