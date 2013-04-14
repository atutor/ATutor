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

// Retrocompatibility: there was a time that the files had another format.

function getConfigurationAndFonts($config, $formulaPath) {
	if (is_file($formulaPath) && ($handle = fopen($formulaPath, 'r')) !== false) {
		$fonts = array();
		
		if (($line = fgets($handle)) !== false) {
			$mathml = trim($line);

			global $wrs_imageConfigProperties, $wrs_xmlFileAttributes;
			$i = 0;
			$wrs_xmlFileAttributesCount = count($wrs_xmlFileAttributes);
			
			while (($line = fgets($handle)) !== false && $i < $wrs_xmlFileAttributesCount) {
				$config[$wrs_imageConfigProperties[$wrs_xmlFileAttributes[$i]]] = trim($line);
				++$i;
			}
			
			$i = 0;
			
			while (($line = fgets($handle)) !== false) {
				$line = trim($line);
				
				if (isset($line[0])) {
					$fonts['font' . $i] = $line;
					++$i;
				}
			}
		}
		else {
			$mathml = '';
		}
		
		fclose($handle);
		
		return array(
			'mathml' => $mathml,
			'config' => $config,
			'fonts' => $fonts
		);
	}
	
	return false;
}

function getConfigurationAndFontsFromIni($config, $formulaPath) {
	$formulaConfig = wrs_parseIni($formulaPath);

	if ($formulaConfig === false) {
		return false;
	}
	
	$fonts = array();
	global $wrs_imageConfigProperties;
	
	foreach ($formulaConfig as $key => $value) {
		if ($key != 'mml') {
			if (substr($key, 0, 4) == 'font') {
				$fonts[$key] = trim($value);
			}
			else {
				$config[$wrs_imageConfigProperties[$key]] = trim($value);
			}
		}
	}
	
	return array(
		'mathml' => trim($formulaConfig['mml']),
		'config' => $config,
		'fonts' => $fonts
	);
}

function createImage($config, $formulaPath, $formulaPathExtension, $useParams) {
	$configAndFonts = ($formulaPathExtension == 'ini') ? getConfigurationAndFontsFromIni($config, $formulaPath . '.ini') : getConfigurationAndFonts($config, $formulaPath . '.xml');
	
	if ($configAndFonts !== false) {
		$config = $configAndFonts['config'];
		
		// Retrocompatibility: when wirisimagenumbercolor is not defined
		
		if (!isset($config['wirisimagenumbercolor']) && isset($config['wirisimagesymbolcolor'])) {
			$config['wirisimagenumbercolor'] = $config['wirisimagesymbolcolor'];
		}
		
		// Retrocompatibility: when wirisimageidentcolor is not defined
		
		if (!isset($config['wirisimageidentcolor']) && isset($config['wirisimagesymbolcolor'])) {
			$config['wirisimageidentcolor'] = $config['wirisimagesymbolcolor'];
		}
		
		// Converting configuration to parameters.
		global $wrs_imageConfigProperties;
		$properties = array('mml' => $configAndFonts['mathml']);
		
		foreach ($wrs_imageConfigProperties as $serverParam => $configKey) {
			if (isset($config[$configKey])) {
				$properties[$serverParam] = trim($config[$configKey]);
			}
		}
		
		// Converting fonts to parameters.
		
		if (isset($config['wirisimagefontranges'])) {
			$carry = count($configAndFonts['fonts']);
			$fontRanges = explode(',', $config['wirisimagefontranges']);
			$fontRangesCount = count($fontRanges);
			$j = 0;
			
			for ($i = 0; $i < $fontRangesCount; ++$i) {
				$rangeName = trim($fontRanges[$i]);
				
				if (isset($config[$rangeName])) {
					$configAndFonts['fonts']['font' . ($carry + $j)] = trim($config[$rangeName]);
					++$j;
				}
			}
		}
		
		// User params.
		
		if ($useParams) {
			global $wrs_xmlFileAttributes;
			
			foreach ($_GET as $key => $value) {
				if (in_array($key, $wrs_xmlFileAttributes) || substr($key, 0, 4) == 'font') {
					$properties[$key] = $value;
				}
			}
		}
		
		// Query.
		$response = wrs_getContents($config, wrs_getImageServiceURL($config, NULL), array_merge($configAndFonts['fonts'], $properties));
		
		if ($response === false) {
			return null;
		}

		return $response;
	}
	
	return null;
}

function createAndSaveImage($config, $formulaPath, $formulaPathExtension, $imagePath) {
	$imageStream = createImage($config, $formulaPath, $formulaPathExtension, false);
	
	if (is_null($imageStream)) {
		return false;
	}
	
	file_put_contents($imagePath, $imageStream);
	return true;
}

function mustBeCached() {
	global $wrs_xmlFileAttributes;
	
	foreach ($_GET as $key => $value) {
		if (in_array($key, $wrs_xmlFileAttributes) || substr($key, 0, 4) == 'font') {
			return false;
		}
	}
	
	return true;
}

if (!empty($_GET['formula'])){	
	$config = wrs_loadConfig(WRS_CONFIG_FILE);
	$formula = rtrim(basename($_GET['formula']), '.png');
	$formulaPath = wrs_getFormulaDirectory($config) . '/' . $formula;
	$extension = (is_file($formulaPath . '.ini')) ? 'ini' : 'xml';
	
	if (mustBeCached()) {
		$imagePath = wrs_getCacheDirectory($config) . '/' . $formula . '.png';
		
		if (is_file($imagePath) || createAndSaveImage($config, $formulaPath, $extension, $imagePath)) {
			header('Content-Type: image/png');
			readfile($imagePath);
		}
		else {
			echo 'Error creating the image.';
		}
	}
	else {
		$imageStream = createImage($config, $formulaPath, $extension, true);

		if (is_null($imageStream)) {
				echo 'Error creating the image.';
		}
		else {
				header('Content-Type: image/png');
				echo $imageStream;
		}
	}
}else if(!empty($_GET['mml'])){
    $config = wrs_loadConfig(WRS_CONFIG_FILE);
	$properties = array('mml' => $_GET['mml']);
	$imageStream = wrs_getContents($config, wrs_getImageServiceURL($config, NULL), $properties, null);

	if (is_null($imageStream)) {
		echo 'Error creating the image.';
	}
	else {
		header('Content-Type: image/png');
		echo $imageStream;
	}	
}else{
	echo 'Error: no digest or mathml has been sent.';
}
?>