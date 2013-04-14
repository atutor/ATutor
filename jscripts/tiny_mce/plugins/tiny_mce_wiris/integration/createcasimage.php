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

$currentPath = dirname($_SERVER['PHP_SELF']) . '/';

if (isset($_POST['image'])) {
	$fileName = md5($_POST['image']);
	$config = wrs_loadConfig(WRS_CONFIG_FILE);
	$formulaPath = wrs_getFormulaDirectory($config) . '/' . $fileName . '.xml';
	
	if (isset($_POST['mml']) && !is_file($formulaPath)) {
		file_put_contents($formulaPath, $_POST['mml']);
	}
	
	$url = $currentPath . 'showcasimage.php?formula=' . $fileName . '.png';
	$imagePath = wrs_getCacheDirectory($config) . '/' . $fileName . '.png';
	
	if (!is_file($imagePath)) {
		if (file_put_contents($imagePath, base64_decode($_POST['image'])) !== false) {
			echo $url;
		}
		else {
			echo $currentPath . '../core/cas.gif';
		}
	}
	else {
		echo $url;
	}
}
else {
	echo $currentPath . '../core/cas.gif';
}
?>