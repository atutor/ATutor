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
$digest = NULL;

if (isset($_POST['md5']) && mb_strlen($_POST['md5']) == 32) {		// Support for "generic simple" integration.
	$digest = $_POST['md5'];
}
else if (isset($_POST['digest'])) {		// Support for future integrations (where maybe they aren't using md5 sums).
	$digest = $_POST['digest'];
}

if (!is_null($digest)) {
	$config = wrs_loadConfig(WRS_CONFIG_FILE);
	$filePath = wrs_getFormulaDirectory($config) . '/' . basename($digest);

	if (is_file($filePath . '.ini')) {
		$formula = wrs_parseIni($filePath . '.ini');
		
		if ($formula !== false) {
			if (isset($formula['mml'])) {
				echo $formula['mml'];
			}
		}
		else {
			echo 'Error: could not read the formula. Check your file permissions.';
		}
	}
	else if (is_file($filePath . '.xml')) {
		if (($handle = fopen($filePath, 'r')) !== false) {
			if (($line = fgets($handle)) !== false) {
				echo $line;
			}
			
			fclose($handle);
		}
		else {
			echo 'Error: could not read the formula. Check your file permissions.';
		}
	}
	else {
		echo 'Error: formula not found.';
	}
}
else {
	echo 'Error: no digest has been sent.';
}
?>