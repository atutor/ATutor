<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/* [Modified version of the phpMyAdmin Language Loading File]   */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

	 $available_languages = array(
        'en'=> array('en([-_][[:alpha:]]{2})?|english',  'iso-8859-1', 'en', 'English'),
        'es'=> array('es([-_][[:alpha:]]{2})?|spanish', 'iso-8859-1', 'es', 'Español'),
        'fr'=> array('fr([-_][[:alpha:]]{2})?|french', 'iso-8859-1', 'fr', 'Français'),

		'af'=> array('af|afrikaans', 'iso-8859-1', 'af', 'Afrikaans'),
		'bg'=> array('bg|bulgarian', 'koi8-r', 'bg', 'Bulgarian'),
		'ca'=> array('ca|catalan', 'iso-8859-1', 'ca', 'Catalana'),
        'cs'=> array('cs|czech', 'iso-8859-2', 'cs', 'Czech'),
        'da'=> array('da|danish', 'iso-8859-1', 'da', 'Dansk'),
        'de'=> array('de([-_][[:alpha:]]{2})?|german', 'iso-8859-1', 'de', 'Deutsch'),
        'ar'=> array('ar([-_][[:alpha:]]{2})?|arabic', 'windows-1256', 'ar', 'Arabic'),
		'et'=> array('et|estonian', 'iso-8859-1', 'et', 'Estonian'),
        'fi'=> array('fi|finnish', 'iso-8859-1', 'fi', 'Finnish'),
		'gl'=> array('gl|galician', 'iso-8859-1', 'gl', 'Galician'),
        'he'=> array('he|hebrew', 'iso-8859-8-i', 'he', 'Hebrew'),
        'hr'=> array('hr|croatian', 'iso-8859-2', 'hr', 'Croatian'),
        'id'=> array('id|indonesian', 'iso-8859-1', 'id', 'Indonesian'),
        'ja'=> array('ja|japanese', 'euc', 'ja', 'Japanese'),
        'ko'=> array('ko|korean', 'ks_c_5601-1987', 'ko', 'Korean'),
        'lt'=> array('lt|lithuanian', 'windows-1257', 'lt', 'Lithuanian'),
        'lv'=> array('lv|latvian', 'windows-1257', 'lv', 'Lativian'),
        'ms'=> array('ms|malay', 'iso-8859-1', 'ms', 'Malay'),
        'nl'=> array('nl([-_][[:alpha:]]{2})?|dutch', 'iso-8859-1', 'nl', 'Dutch'),
        'no'=> array('no|norwegian', 'iso-8859-1', 'no', 'Norwegian'),
        'pl'=> array('pl|polish', 'iso-8859-2', 'pl', 'Polish'),
		'ro'=> array('ro|romanian', 'iso-8859-2', 'ro', 'Romanian'),
        'ru'=> array('ru|russian', 'russian-koi8-r', 'ru', 'Russian'),
        'sk'=> array('sk|slovak', 'iso-8859-2', 'sk', 'Slovak'),
        'sl'=> array('sl|slovenian', 'iso-8859-2', 'sl', 'Slovenian'),
        'sq'=> array('sq|albanian', 'iso-8859-1', 'sq', 'Albanian'),
        'sr'=> array('sr|serbian', 'windows-1250', 'sr', 'Serbian'),
        'sv'=> array('sv|swedish', 'iso-8859-1', 'sv', 'Swedish'),
		'tr'=> array('tr|turkish', 'iso-8859-9', 'tr', 'Turkish'),
        'uk'=> array('uk|ukrainian', 'windows-1251', 'uk', 'Ukrainian'),
        'zh'=> array('zh[-_]tw|chinese traditional', 'big5', 'zh', 'Chinese'),
		'zhs'=> array('zh|chinese simplified', 'gb2312', 'zhs', 'Chinese Simplified'),
        'el'=> array('el|greek',  'iso-8859-7', 'el', 'Greek'),
		'fa'=> array('fa|farsi',  'windows-1256', 'fa', 'Farsi'),
		'hu'=> array('hu|hungarian', 'iso-8859-2', 'hu', 'Hungarian'),
		'it'=> array('it|italian', 'iso-8859-1', 'it', 'Italiano'),
        'th'=> array('th|thai', 'TIS-620', 'th', 'Thai'),
        'pt'=> array('pt([-_][[:alpha:]]{2})?|portuguese', 'iso-8859-15', 'pt', 'Portuguese'),
		'ur'=> array('ur|urdu', 'windows-1256', 'ur', 'Urdu'),
		'ptb'=> array('ptb([-_][[:alpha:]]{2})?|portuguese brazil', 'iso-8859-1', 'ptb', 'Portuguese Brazil'),
		'vi'=> array('vi|vietnamese', 'VISCII', 'vi', 'Vietnamese'),
		'is'=> array('is|icelandic', 'iso-8859-1', 'is', 'Icelandic'),
		'cy'=> array('cy|cymraeg', 'iso-8859-14', 'cy', 'Cymraeg (Welsh)'),
    );

if ( !($et_l = cache(0, 'system_langs', 'system_langs')) ) {
	$temp_langs = array();

	$sql	= 'SELECT DISTINCT lang FROM '.TABLE_PREFIX_LANG.'lang2';
	$result = mysql_query($sql, $lang_db);
	$temp_langs['en'] = $available_languages['en'];  /* English is always included */
	while($row = mysql_fetch_assoc($result)){
		$temp_langs[$row['lang']] = $available_languages[$row['lang']];
	}

	$available_languages = $temp_langs;

	cache_variable('available_languages');
	endcache(true, false);
}

        /**
         * Analyzes some PHP environment variables to find the most probable language
         * that should be used
         *
         * @param   string   string to analyze
         * @param   integer  type of the PHP environment variable which value is $str
         *
         * @global  array    the list of available translations
         * @global  string   the retained translation keyword
         *
         * @access  private
         */
	function PMA_langDetect($str = '', $envType = '')
    {
		global $available_languages;
        global $temp_lang;

        reset($available_languages);
        while (list($key, $value) = each($available_languages)) {
			// $envType =  1 for the 'HTTP_ACCEPT_LANGUAGE' environment variable,
            //             2 for the 'HTTP_USER_AGENT' one
            if (($envType == 1 && eregi('^(' . $value[0] . ')(;q=[0-9]\\.[0-9])?$', $str))
                 || ($envType == 2 && eregi('(\(|\[|;[[:space:]])(' . $value[0] . ')(;|\]|\))', $str))) {
                $temp_lang     = $key;
				break;
			}
		}
	} // end of the 'PMA_langDetect()' function

	if (isset($_GET) && !empty($_GET['lang'])) {
		$temp_lang = $_GET['lang'];
    } else if (isset($_POST) && !empty($_POST['lang'])) {
        $temp_lang = $_POST['lang'];
    } else if (isset($_SESSION) && !empty($_SESSION['lang'])) {
		$temp_lang = $_SESSION['lang'];
    }

    // Language is not defined yet :
    // 1. try to findout user's language by checking its HTTP_ACCEPT_LANGUAGE
    //    variable
    if (empty($temp_lang) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $accepted    = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $acceptedCnt = count($accepted);
        reset($accepted);
        for ($i = 0; $i < $acceptedCnt && empty($temp_lang); $i++) {
            PMA_langDetect($accepted[$i], 1);
        }
    }

	// 2. try to findout user's language by checking its HTTP_USER_AGENT variable
    if (empty($temp_lang) && !empty($_SERVER['HTTP_USER_AGENT'])) {
        PMA_langDetect($_SERVER['HTTP_USER_AGENT'], 2);
    }

    // 3. Didn't catch any valid lang : we use the default settings
    if ($temp_lang == '') {
		if (isset($available_languages[DEFAULT_LANGUAGE])) {
	        $temp_lang = DEFAULT_LANGUAGE;
		} else {
			$temp_lang = 'en'; /* fail safe */
		}
	}


/* check if this language is supported: */

if (!isset($available_languages[$temp_lang])) {
	$errors[] = AT_ERROR_NO_LANGUAGE;
} else if (($temp_lang != '') && ($available_languages[$temp_lang] != '') && ($_SESSION['lang'] != $temp_lang)) {
	$_SESSION['lang'] = $temp_lang;
}
header('Content-Type: text/html; charset='.$available_languages[$_SESSION['lang']][1]);

/* set right-to-left language */
$rtl = '';
if (in_array($_SESSION['lang'], $_rtl_languages)) {
	$rtl = 'rtl_'; /* basically the prefix to a rtl variant directory/filename. rtl_tree */
}


?>