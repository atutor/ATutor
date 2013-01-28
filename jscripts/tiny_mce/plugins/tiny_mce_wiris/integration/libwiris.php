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

define('WRS_DEFAULT_CONFIG_FILE', dirname(__FILE__) . '/../default_configuration.ini');
define('WRS_CONFIG_FILE', dirname(__FILE__) . '/../configuration.ini');
define('WRS_CACHE_DIRECTORY', dirname(__FILE__) . '/../cache');
define('WRS_FORMULA_DIRECTORY', dirname(__FILE__) . '/../formulas');

global $wrs_imageConfigProperties, $wrs_xmlFileAttributes;

$wrs_imageConfigProperties = array(
	'bgColor' => 'wirisimagebgcolor',
	'backgroundColor' => 'wirisimagebackgroundcolor',
	'symbolColor' => 'wirisimagesymbolcolor',
	'transparency' => 'wiristransparency',
	'fontSize' => 'wirisimagefontsize',
	'numberColor' => 'wirisimagenumbercolor',
	'identColor' => 'wirisimageidentcolor',
	'identMathvariant' => 'wirisimageidentmathvariant',
	'numberMathvariant' => 'wirisimagenumbermathvariant',
	'fontIdent' => 'wirisimagefontident',
	'fontNumber' => 'wirisimagefontnumber',
	'version' => 'wirisimageserviceversion',
	'color' => 'wirisimagecolor',
	'dpi' => 'wirisdpi',
	'fontFamily' => 'wirisfontfamily'
);

$wrs_xmlFileAttributes = array(
	'bgColor',
	'symbolColor',
	'transparency',
	'fontSize',
	'numberColor',
	'identColor',
	'identMathvariant',
	'numberMathvariant',
	'fontIdent',
	'fontNumber',
	'zoom',
	'dpi', 
	'color',
	'backgroundColor',
	'fontFamily'
);

function wrs_applyConfigRetrocompatibility($config) {
	if (isset($config['']['wirisimageserviceprotocol']) && isset($config['']['wirisimageservicehost']) && isset($config['']['wirisimageserviceport']) && isset($config['']['wirisimageservicepath'])) {
		$config['main']['editor.serviceURL'] = $config['']['wirisimageserviceprotocol'] . '://' . $config['']['wirisimageservicehost'] . rtrim($config['wirisimageservicepath'], '/render');
	}
	
	if (isset($config['']['wirismathmltolatexurl'])) {
		$config['main']['editor.serviceURL.mathml2latex'] = $config['']['wirismathmltolatexurl'];
	}
	
	if (isset($config['']['wirislatextomathmlurl'])) {
		$config['main']['editor.serviceURL.latex2mathml'] = $config['']['wirislatextomathmlurl'];
	}
	
	if (isset($config['']['wirisimageserviceversion'])) {
		$config['main']['editor.version'] = $config['']['wirisimageserviceversion'];
	}
	
	if (isset($config['']['wirisformulaeditorlang'])) {
		$config['main']['editor.language'] = $config['']['wirisformulaeditorlang'];
	}

	if (isset($config['']['wiriscascodebase'])) {
		$config['main']['cas.serviceURL'] = $config['']['wiriscascodebase'];
	}
	
	if (isset($config['']['wiriscascodebase']) && isset($config['']['wiriscasarchive'])) {
		$config['main']['cas.serviceURL.archive'] = $config['']['wiriscascodebase'] . '/' . $config['']['wiriscasarchive'];
	}
	
	if (isset($config['']['wiriscasclass'])) {
		$config['main']['cas.class'] = $config['']['wiriscasclass'];
	}
	
	if (isset($config['']['wiriscaslanguages'])) {
		$config['main']['cas.languages'] = $config['']['wiriscaslanguages'];
	}
	
	if (isset($config['']['CAS_width'])) {
		$config['main']['cas.width'] = $config['']['CAS_width'];
	}

	if (isset($config['']['CAS_height'])) {
		$config['main']['cas.height'] = $config['']['CAS_height'];
	}
	
	if (isset($config['']['wiriscachedirectory'])) {
		$config['main']['plugin.cacheDirectory'] = $config['']['wiriscachedirectory'];
	}
	
	if (isset($config['']['wirisformuladirectory'])) {
		$config['main']['plugin.formulaDirectory'] = $config['']['wirisformuladirectory'];
	}
	
	if (isset($config['']['wirisproxy_host']) && isset($config['']['wirisproxy_port'])) {
		$config['main']['plugin.proxy'] = $config['']['wirisproxy_host'] . ':' . $config['']['wirisproxy_port'];
	}
	
	if (isset($config['']['wirisstorageclass'])) {
		$config['main']['plugin.storageClass'] = $config['']['wirisstorageclass'];
	}
	
	if (isset($config['']['wiriscontainerstorageclass'])) {
		$config['main']['plugin.containerStorageClass'] = $config['']['wiriscontainerstorageclass'];
	}

	if (isset($config['']['wirisconfigurationclass'])) {
		$config['main']['plugin.configurationClass'] = $config['']['wirisconfigurationclass'];
	}
	
	if (isset($config['']['wirisconfigurationrefreshtime'])) {
		$config['main']['plugin.refreshInterval'] = $config['']['wirisconfigurationrefreshtime'];
	}
}

function wrs_createIni($properties) {
	$ini = '';
	
	foreach ($properties as $key => $value) {
		$ini .= $key . '=' . $value . "\r\n";
	}
	
	return $ini;
}

function wrs_deduceConfigUndefinedValues($config) {
	$deduction = array(
		'plugin.cacheDirectory' => $config['main']['plugin.php.cacheDirectory'],
		'plugin.formulaDirectory' => $config['main']['plugin.php.formulaDirectory'],
		'editor.enabled' => $config['main']['plugin.enabled'],
		'editor.saveMode' => $config['main']['plugin.saveMode'],
		'editor.codeAttribute' => $config['main']['plugin.codeAttribute'],
		'editor.serviceURL' => $config['main']['plugin.serviceURL'] . '/editor',
		'editor.serviceURL.render' => $config['main']['editor.serviceURL'] . '/render',
		'editor.serviceURL.mathml2latex' => $config['main']['editor.serviceURL'] . '/mathml2latex',
		'editor.serviceURL.latex2mathml' => $config['main']['editor.serviceURL'] . '/latex2mathml',
		'editor.cacheDirectory' => $config['main']['plugin.cacheDirectory'],
		'editor.formulaDirectory' => $config['main']['plugin.formulaDirectory'],
		'cas.enabled' => $config['main']['plugin.enabled'],
		'cas.saveMode' => $config['main']['plugin.saveMode'],
		'cas.codeAttribute' => $config['main']['plugin.codeAttribute'],
		'cas.serviceURL' => $config['main']['plugin.serviceURL'] . '/cas',
		'cas.serviceURL.archive' => $config['main']['cas.serviceURL'] . '/wrs_net_%LANG.jar',
		'cas.cacheDirectory' => $config['main']['plugin.cacheDirectory'],
		'cas.formulaDirectory' => $config['main']['plugin.formulaDirectory']
	);
	
	foreach ($deduction as $key => $value) {
		if (!isset($config['main'][$key])) {
			$config['main'][$key] = $value;
		}
	}
	
	foreach ($config['main'] as $key => $value) {
		if (substr($key, 0, 4) == 'php.') {
			$config['main'][substr($key, 4)] = $value;
		}
	}
}

function wrs_getAvailableCASLanguages($languageString) {
	$availableLanguages = explode(',', $languageString);
		
	for ($i = count($availableLanguages) - 1; $i >= 0; --$i) {
		$availableLanguages[$i] = trim($availableLanguages[$i]);
	}

	// At least we should accept an empty language.
	
	if (!isset($availableLanguages[0])) {
		$availableLanguages[] = '';
	}
	
	return $availableLanguages;
}

function wrs_getCacheDirectory($config) {
	$cacheDirectory = (isset($config['wiriscachedirectory'])) ? $config['wiriscachedirectory'] : WRS_CACHE_DIRECTORY;
	@mkdir($cacheDirectory, 0755, true);
	return $cacheDirectory;
}

function wrs_getContents($config, $url, $postVariables = NULL) {

	$reqURI = $_SERVER['REQUEST_URI'];
	if (substr($reqURI, 0, 1) == '/'){
		$referer = ((isset($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}else{
		$referer = ((isset($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI'];    
	}

	//If cURL is used it's possible to disable the directive allow_url_fopen
	if (function_exists('curl_init')){
		return wrs_fileGetContentsCurl($url, $postVariables, $config, $referer);
	}
	
	if (is_null($postVariables)) {
		$httpConfiguration = array(
			'method' => 'GET',
			'header' => 'Referer: ' . $referer
		);
	}
	else {
		$httpConfiguration = array(
			'method'  => 'POST',
			'header'  => 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8' . "\r\n" . 'Referer: ' . $referer,
			'content' => http_build_query($postVariables, '', '&')
		);
	}
	
	$contextArray = array('http' =>	$httpConfiguration);

	if (isset($config['wirisproxy']) && $config['wirisproxy'] == 'true') {
		$contextArray['http']['proxy'] = 'tcp://' . $config['wirisproxy_host'] . ':' . $config['wirisproxy_port'];
		$contextArray['http']['request_fulluri'] = true;
	}
	
	$context = stream_context_create($contextArray);
	
	return file_get_contents($url, false, $context);
}

function wrs_fileGetContentsCurl($url, $postVariables, $config, $referer) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_REFERER, $referer);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	//POST method
	if (!is_null($postVariables)) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded; charset=UTF-8'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postVariables, '', '&'));
	}
	
	//PROXY is used	
	if (isset($config['wirisproxy']) && $config['wirisproxy'] == 'true') {
		curl_setopt($ch, CURLOPT_PROXY, $config['wirisproxy_host']);
		curl_setopt($ch, CURLOPT_PROXYPORT, $config['wirisproxy_port']);
	}
	
	$data = curl_exec($ch);	
	curl_close($ch);

	return $data;
}

function wrs_getFormulaDirectory($config) {
	$formulaDirectory = (isset($config['wirisformuladirectory'])) ? $config['wirisformuladirectory'] : WRS_FORMULA_DIRECTORY;
	if (!is_dir($formulaDirectory)){
		@mkdir($formulaDirectory, 0755, true);    
	}
	return $formulaDirectory;
}

function wrs_getImageServiceURL($config, $service) {
	if ($service == 'latex2mathml' && isset($config['wirislatextomathmlurl'])) {
		return $config['wirislatextomathmlurl'];
	}
	
	if ($service == 'mathml2latex' && isset($config['wirismathmltolatexurl'])) {
		return $config['wirismathmltolatexurl'];
	}

	// Protocol 
	if (isset($config['wirisimageserviceprotocol'])){
		$protocol = $config['wirisimageserviceprotocol'];
	}else{
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'){
			$protocol = 'https';
		}else{
			$protocol = 'http';
		}
	}
	
	// Port
	if (isset($config['wirisimageserviceport'])){
		$port = ':' . $config['wirisimageserviceport'];
	}
	else {
		$port = '';
	}

	// Domain.
	$domain = $config['wirisimageservicehost'];

	// Path.
	$path = $config['wirisimageservicepath'];

	if (!is_null($service)) {
		$path = dirname($path) . '/' . $service;
	}

	return $protocol . '://' . $domain . $port . $path;
}

function wrs_loadConfig($filePath) {
	$config = wrs_parseIni($filePath, false, array());
	
	
	if (isset($config['wirisconfigurationclass'])) {
		$parts = explode(';', $config['wirisconfigurationclass'], 2);
		
		if (isset($parts[0]) && isset($parts[1])) {
			$file = trim($parts[0]);
			require_once(dirname(__FILE__) . '/ConfigurationUpdater.php');
			require_once(dirname(__FILE__) . '/' . $file);
			
			$className = trim($parts[1]);
			$configurationUpdater = new $className();
			$configurationUpdater->init();
			$configurationUpdater->updateConfiguration($config);
		}
	}

	return $config;
	
	/*
	TODO: implement the new configuration system.
	
	$defaultConfig = wrs_parseIni(WRS_DEFAULT_CONFIG_FILE, true);
	$config = wrs_parseIni(WRS_CONFIG_FILE, true, $wrs_defaultConfig);
	$config = wrs_applyConfigRetrocompatibility($config);
	$config = wrs_deduceConfigUndefinedValues($config);
	return $config;*/
}

function wrs_parseIni($filePath, $parseSections = false, $properties = null) {
	$handle = fopen($filePath, 'r');
	
	if ($handle === false) {
		return $properties;
	}
	
	if (is_null($properties)) {
		$properties = array();
	}
	
	$lastSection = '';
	$properties[$lastSection] = array();
	
	while (($line = fgets($handle)) !== false) {
		$line = trim($line);
		$lineLength = mb_strlen($line);
		
		if ($lineLength > 0 && $line[0] == '[' && mb_substr($line, -1) == ']') {
			$lastSection = mb_substr($line, 1, $lineLength - 2);
			
			if (!isset($properties[$lastSection])) {
				$properties[$lastSection] = array();
			}
		}
		else {
			$lineWords = explode('=', $line, 2);
			
			if (isset($lineWords[1])) {
				$key = trim($lineWords[0]);
				$value = trim($lineWords[1]);
				$properties[$lastSection][$key] = $value;
			}
		}
	}
	
	fclose($handle);
	
	if (!$parseSections) {
		return $properties[''];
	}
	
	return $properties;
}

function wrs_replaceVariable($value, $variableName, $variableValue) {	
	return str_replace('%' . $variableName, $variableValue, $value);
}

function wrs_secureStripslashes($element) {
	if (is_array($element)) {
		return array_map('wrs_secureStripslashes', $element);
	}

	return stripslashes($element);
}

if (get_magic_quotes_runtime()) {
    @set_magic_quotes_runtime(0);
}

if (get_magic_quotes_gpc() == 1) {
	$_REQUEST = array_map('wrs_secureStripslashes', $_REQUEST);
	$_GET = array_map('wrs_secureStripslashes', $_GET);
	$_POST = array_map('wrs_secureStripslashes', $_POST);
}
?>