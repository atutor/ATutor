<?php
// Emulate register_globals off. src: http://php.net/manual/en/faq.misc.php#faq.misc.registerglobals
function unregister_GLOBALS() {
   if (!ini_get('register_globals')) { return; }

   // Might want to change this perhaps to a nicer error
   if (isset($_REQUEST['GLOBALS'])) { die('GLOBALS overwrite attempt detected'); }

   // Variables that shouldn't be unset
   $noUnset = array('GLOBALS','_GET','_POST','_COOKIE','_REQUEST','_SERVER','_ENV', '_FILES');
   $input = array_merge($_GET,$_POST,$_COOKIE,$_SERVER,$_ENV,$_FILES,isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
  
   foreach ($input as $k => $v) {
       if (!in_array($k, $noUnset) && isset($GLOBALS[$k])) { unset($GLOBALS[$k]); }
   }
}

unregister_GLOBALS();

function debug($var, $title='') {

	echo '<pre style="border: 1px black solid; padding: 0px; margin: 10px;">';
	if ($title) {
		echo '<h4>'.$title.'</h4>';
	}
	
	ob_start();
	print_r($var);
	$str = ob_get_contents();
	ob_end_clean();

	$str = str_replace('<', '&lt;', $str);

	$str = str_replace('[', '<span style="color: red; font-weight: bold;">[', $str);
	$str = str_replace(']', ']</span>', $str);
	$str = str_replace('=>', '<span style="color: blue; font-weight: bold;">=></span>', $str);
	$str = str_replace('Array', '<span style="color: purple; font-weight: bold;">Array</span>', $str);
	echo $str;
	echo '</pre>';
}

function get_text($var, $return = FALSE) {
	global $req_lang, $lang, $section;

	static $req_lang_text, $lang_text;

	if (!isset($req_lang_text) && ($req_lang != 'en')) {
		$text = array();
		if (file_exists(dirname(__FILE__) . '/'.$req_lang.'/text.php')) {
			require(dirname(__FILE__) . '/'.$req_lang.'/text.php');
		}

		$req_lang_text = $text;
	} else if (!isset($lang_text)) {
		$text = array();
		require(dirname(__FILE__) . '/text.php');
		$lang_text = $text;
	}

	if (isset($req_lang_text[$var])) {
		if ($return) {
			return $req_lang_text[$var];
		}
		echo $req_lang_text[$var];
	} else if (isset($lang_text[$var])) {
		if ($return) {
			return $lang_text[$var];
		}
		echo $lang_text[$var];
	} else {
		if ($return) {
			return $var;
		}
		echo $var;
	}
}

function get_available_languages($section) {
	global $available_languages;

	$path = dirname(__FILE__);
	if (is_dir($path)) {
		$files = glob($path . '/??');
		if (is_array($files)) {
			foreach ($files as $filename) {
				$filename = basename($filename);
				$available_languages[$filename] = $filename;
			}
		}
	}
}

define('AT_HANDBOOK', true);
session_name('ATutorID');
session_start();
session_write_close();
// $lang is the language we've found to display
// $req_lang is the language we're requesting


$_available_sections = array('admin' => 'admin', 'instructor' => 'instructor', 'general' => 'general');
$available_languages = array('en' => 'en');

$parts = pathinfo($_SERVER['PHP_SELF']);
$this_page = $parts['basename'];

$dir_parts = explode('/', $parts['dirname']);
$last_dir_name = end($dir_parts);
$second_last_dir_name = prev($dir_parts);

if (isset($_available_sections[$second_last_dir_name])) {
	$lang = $req_lang = $last_dir_name;
	$section = $second_last_dir_name;
	$rel_path = '../../';
	get_available_languages($section);
} else if (isset($_available_sections[$last_dir_name])) {
	$section = $last_dir_name;
	$rel_path = '../';
	get_available_languages($section);
	foreach ($_GET as $lang_name => $garbage) {
		if (isset($available_languages[$lang_name])) {
			$lang = $req_lang = $lang_name;
			break;
		}
	}
	if (isset($_SESSION['lang']) && isset($available_languages[$_SESSION['lang']])) {
		$lang = $req_lang = $_SESSION['lang'];
	} else {
		$lang = $req_lang = 'en';
	}
} else {
	foreach ($_available_sections as $section_name) {
		if (isset($_GET[$section_name])) {
			$section = $section_name;
			unset($_GET[$section]);
			break;
		}
	}
	if ($section) {
		get_available_languages($section);
		foreach ($available_languages as $lang_name) {
			if (isset($_GET[$lang_name])) {
				$lang = $req_lang = $lang_name;
				break;
			}
		}
		if (!$lang && isset($_SESSION['lang']) && isset($available_languages[$_SESSION['lang']])) {
			$lang = $req_lang = $_SESSION['lang'];
		} else if (!$lang) {
			$lang = $req_lang = 'en';
		}
		$rel_path = '../';
	} else {
		$lang = $req_lang = 'en';
		$section = 'general';
		$rel_path = '../';
		get_available_languages($section);
	}
}

$lang = htmlspecialchars($lang);
$req_lang = htmlspecialchars($req_lang);

?>