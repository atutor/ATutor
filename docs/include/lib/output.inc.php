<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

/**********************************************************************************/
/* Output functions found in this file, in order:
/*
/*	- AT_date(format, timestamp, format_type)
/*
/*	- _AT([...])
/*	- AT_print(input, name, Boolean runtime_html)
/*
/*	- smile_replace(text)
/*	- myCodes(text)
/*	- make_clickable(text)
/*	- image_replace(text)
/*	- format_final_output(text, Boolean nl2br)
/*	- highlight (input, var)
/*	- format_content(input, Boolean html, glossary)
/*	- find_terms(find_text)
/*
/**********************************************************************************/


/**
* Returns a formatted date string - Uses the same options as date(), but requires a % infront of each argument and the
* textual values are language dependent (unlike date()).
* @access  public
* @param   string $format		preferred date format 
* @param   string $timestamp	value of timestamp
* @param   int $format_type		timestamp format, an AT_DATE constant
* @return  string				formatted date
* @see     AT_DATE constants	in include/lib/constants.inc.php
* @author  Joel Kronenberg
*/

/* 
	The following options were added as language dependant:
	%D: A textual representation of a week, three letters Mon through Sun
	%F: A full textual representation of a month, such as January or March January through December
	%l (lowercase 'L'): A full textual representation of the day of the week Sunday through Saturday
	%M: A short textual representation of a month, three letters Jan through Dec

	Support for the following maybe added later:
	?? %S: English ordinal suffix for the day of the month, 2 characters st, nd, rd or th. Works well with j
	?? %a: Lowercase Ante meridiem and Post meridiem am or pm 
	?? %A: Uppercase Ante meridiem and Post meridiem AM or PM 

	valid format_types:
	AT_DATE_MYSQL_DATETIME:		YYYY-MM-DD HH:MM:SS
	AT_DATE_MYSQL_TIMESTAMP_14:	YYYYMMDDHHMMSS
	AT_DATE_UNIX_TIMESTAMP:		seconds since epoch
	AT_DATE_INDEX_VALUE:		0-x, index into a date array
*/
function AT_date($format='%Y-%M-%d', $timestamp = '', $format_type=AT_DATE_MYSQL_DATETIME) {	
	static $day_name_ext, $day_name_con, $month_name_ext, $month_name_con;
	global $_config;

	if (!isset($day_name_ext)) {
		$day_name_ext = array(	'date_sunday', 
								'date_monday', 
								'date_tuesday', 
								'date_wednesday', 
								'date_thursday', 
								'date_friday',
								'date_saturday');

		$day_name_con = array(	'date_sun', 
								'date_mon', 
								'date_tue', 
								'date_wed',
								'date_thu', 
								'date_fri', 
								'date_sat');

		$month_name_ext = array('date_january', 
								'date_february', 
								'date_march', 
								'date_april', 
								'date_may',
								'date_june', 
								'date_july', 
								'date_august', 
								'date_september', 
								'date_october', 
								'date_november',
								'date_december');

		$month_name_con = array('date_jan', 
								'date_feb', 
								'date_mar', 
								'date_apr', 
								'date_may_short',
								'date_jun', 
								'date_jul', 
								'date_aug', 
								'date_sep', 
								'date_oct', 
								'date_nov',
								'date_dec');
	}

	if ($format_type == AT_DATE_INDEX_VALUE) {
		// apply timezone offset
//		$timestamp = apply_timezone($timestamp);
	
		if ($format == '%D') {
			return _AT($day_name_con[$timestamp-1]);
		} else if ($format == '%l') {
			return _AT($day_name_ext[$timestamp-1]);
		} else if ($format == '%F') {
			return _AT($month_name_ext[$timestamp-1]);
		} else if ($format == '%M') {
			return _AT($month_name_con[$timestamp-1]);
		}
	}

	if ($timestamp == '') {
		$timestamp = time();
		$format_type = AT_DATE_UNIX_TIMESTAMP;
	}

	/* convert the date to a Unix timestamp before we do anything with it */
	if ($format_type == AT_DATE_MYSQL_DATETIME) {
		$year	= substr($timestamp,0,4);
		$month	= substr($timestamp,5,2);
		$day	= substr($timestamp,8,2);
		$hour	= substr($timestamp,11,2);
		$min	= substr($timestamp,14,2);
		$sec	= substr($timestamp,17,2);
	    $timestamp	= mktime($hour, $min, $sec, $month, $day, $year);

	} else if ($format_type == AT_DATE_MYSQL_TIMESTAMP_14) {
	    $year		= substr($timestamp,0,4);
	    $month		= substr($timestamp,4,2);
	    $day		= substr($timestamp,6,2);
		$hour		= substr($timestamp,8,2);
	    $minute		= substr($timestamp,10,2);
	    $second		= substr($timestamp,12,2);
	    $timestamp	= mktime($hour, $minute, $second, $month, $day, $year);  
	}

	// apply timezone offset
	$timestamp = apply_timezone($timestamp);

	/* pull out all the %X items from $format */
	$first_token = strpos($format, '%');
	if ($first_token === false) {
		/* no tokens found */
		return $timestamp;
	} else {
		$tokened_format = substr($format, $first_token);
	}
	$tokens = explode('%', $tokened_format);
	array_shift($tokens);
	$num_tokens = count($tokens);

	$output = $format;
	for ($i=0; $i<$num_tokens; $i++) {
		$tokens[$i] = substr($tokens[$i],0,1);

		if ($tokens[$i] == 'D') {
			$output = str_replace('%D', _AT($day_name_con[date('w', $timestamp)]),$output);
		
		} else if ($tokens[$i] == 'l') {
			$output = str_replace('%l', _AT($day_name_ext[date('w', $timestamp)]),$output);
		
		} else if ($tokens[$i] == 'F') {
			$output = str_replace('%F', _AT($month_name_ext[date('n', $timestamp)-1]),$output);		
		
		} else if ($tokens[$i] == 'M') {
			$output = str_replace('%M', _AT($month_name_con[date('n', $timestamp)-1]),$output);

		} else {

			/* this token doesn't need translating */
			$value = date($tokens[$i], $timestamp);
			if ($value != $tokens[$i]) {
				$output = str_replace('%'.$tokens[$i], $value, $output);
			} /* else: this token isn't valid. so don't replace it. Eg. try %q */
		}
	}

	return $output;
}

/**
* Converts language code to actual language message, caches them according to page url
* @access	public
* @param	args				unlimited number of arguments allowed but first arg MUST be name of the language variable/term
*								i.e		$args[0] = the term to the format string $_template[term]
*										$args[1..x] = optional arguments to the formatting string 
* @return	string|array		full resulting message
* @see		$db			        in include/vitals.inc.php
* @see		cacheLite			in include/classes/cacheLite/cacheLite.inc.php
* @author	Joel Kronenberg, modified by Cindy Qi Li onDec 8, 2009 to replace phpCache with cacheLite
*/
function _AT() {
	global $lang_et, $_rel_url;
	
	$args = func_get_args();
	
	// a feedback msg
	if (!is_array($args[0])) {
		/**
		 * Added functionality for translating language code String (AT_ERROR|AT_INFOS|AT_WARNING|AT_FEEDBACK|AT_HELP).*
		 * to its text and returning the result. No caching needed.
		 * @author Jacek Materna
		 */

		// Check for specific language prefix, extendible as needed
		// 0002767:  a substring+in_array test should be faster than a preg_match test.
		// replaced the preg_match with a test of the substring.
		$sub_arg = substr($args[0], 0, 7); // 7 is the shortest type of msg (AT_HELP)
		if (in_array($sub_arg, array('AT_ERRO','AT_INFO','AT_WARN','AT_FEED','AT_HELP','AT_CONF'))) {
			global $db;
			global $_base_path, $addslashes;

			$args[0] = $addslashes($args[0]);
					
			/* get $_msgs_new from the DB */
			$sql	= 'SELECT text FROM '.TABLE_PREFIX.'language_text WHERE term="' . $args[0] . '" AND (variable="_msgs" OR variable="_c_msgs") AND language_code="'.$_SESSION['lang'].'" ORDER BY variable ASC LIMIT 1';

			$result	= @mysql_query($sql, $db);
			$i = 1;
			$msgs = '';
					
			if ($row = @mysql_fetch_assoc($result)) {
				// do not cache key as a digit (no contstant(), use string)
				$msgs = str_replace('SITE_URL/', $_base_path, $row['text']);
				if (defined('AT_DEVEL') && AT_DEVEL) {
					$msgs .= ' <small><small>('. $args[0] .')</small></small>';
				}
			}

			$sql = 'INSERT INTO '.TABLE_PREFIX.'language_pages (`term`, `page`) VALUES ("'.$args[0].'", "'.$_rel_url.'")';
			mysql_query($sql, $db);

			return $msgs;
		}
	}
	
	$num_args = func_num_args();
	if (is_array($args[0])) {
		$args = $args[0];
		$num_args = count($args);
	}
	$format	  = array_shift($args);
	$url_parts = parse_url(AT_BASE_HREF);
	$name = substr($_SERVER['PHP_SELF'], strlen($url_parts['path'])-1);
	$cache_group = $_SESSION['lang'].'_'.$name;

	$outString = cacheLite::get($format, $cache_group);
	if (!$outString) {
		
		global $db;
		$sql	= 'SELECT L.* FROM '.TABLE_PREFIX.'language_text L WHERE L.language_code="'.$_SESSION['lang'].'" AND L.variable<>"_msgs" AND L.term="'.$format.'"';

		$result	= mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);

		$text = stripslashes($row['text']);
		if (empty($text)) {
			return ('[ '.$format.' ]');
		}
		else
		{
			$outString = vsprintf($text, $args);
			// do NOT cache the string that needs pass-in parameter
			if (!strstr($format,'%') && !strstr($outString, '%') && !strstr($text, '%')) {
				cacheLite::save($outString, $format, $cache_group);
			}
		}

		/* update the locations */
		$sql = 'INSERT INTO '.TABLE_PREFIX.'language_pages (`term`, `page`) VALUES ("'.$format.'", "'.$_rel_url.'")';
		mysql_query($sql, $db);
	}

	return $outString;
}

/**********************************************************************************************************/
	/**
	* 	Transforms text based on formatting preferences.  Original $input is also changed (passed by reference).
	*	Can be called as:
	*	1) $output = AT_print($input, $name);
	*	   echo $output;
	*
	*	2) echo AT_print($input, $name); // prefered method
	*
	* @access	public
	* @param	string $input			text being transformed
	* @param	string $name			the unique name of this field (convension: table_name.field_name)
	* @param	boolean $runtime_html	forcefully disables html formatting for $input (only used by fields that 
	*									have the 'formatting' option
	* @return	string					transformed $input
	* @see		AT_FORMAT constants		in include/lib/constants.inc.php
	* @see		query_bit()				in include/vitals.inc.php
	* @author	Joel Kronenberg
	*/
	function AT_print($input, $name, $runtime_html = true) {
		global $_field_formatting, $_config;

		if (!isset($_field_formatting[$name])) {
			/* field not set, check if there's a global setting */
			$parts = explode('.', $name);
			
			/* check if wildcard is set: */
			if (isset($_field_formatting[$parts[0].'.*'])) {
				$name = $parts[0].'.*';
			} else {
				/* field not set, and there's no global setting */
				/* same as AT_FORMAT_NONE */
				return $input;
			}
		}

		if (query_bit($_field_formatting[$name], AT_FORMAT_QUOTES)) {
			$input = str_replace('"', '&quot;', $input);
		}

		if (query_bit($_field_formatting[$name], AT_FORMAT_CONTENT_DIR)) {
			$input = str_replace('CONTENT_DIR/', '', $input);
		}

		if (query_bit($_field_formatting[$name], AT_FORMAT_HTML) && $runtime_html) {
			/* what special things do we have to do if this is HTML ? remove unwanted HTML? validate? */
		} else {
			$input = str_replace('<', '&lt;', $input);
			$input = nl2br($input);
		}

		if (isset($_config['latex_server']) && $_config['latex_server']) {
			$input = preg_replace('/\[tex\](.*?)\[\/tex\]/sie', "'<img src=\"'.\$_config['latex_server'].rawurlencode('$1').'\" align=\"middle\">'", $input);
		}

		/* this has to be here, only because AT_FORMAT_HTML is the only check that has an else-block */
		if ($_field_formatting[$name] === AT_FORMAT_NONE) {
			return $input;
		}

		if (query_bit($_field_formatting[$name], AT_FORMAT_EMOTICONS)) {
			$input = smile_replace($input);
		}

		if (query_bit($_field_formatting[$name], AT_FORMAT_ATCODES)) {
			$input = trim(myCodes(' ' . $input . ' '));
		}

		if (query_bit($_field_formatting[$name], AT_FORMAT_LINKS)) {
			$input = trim(make_clickable(' ' . $input . ' '));
		}

		if (query_bit($_field_formatting[$name], AT_FORMAT_IMAGES)) {
			$input = trim(image_replace(' ' . $input . ' '));
		}

	
		return $input;
	}

/********************************************************************************************/
// Global variables for emoticons
 
global $smile_pics;
global $smile_codes;
if (!isset($smile_pics)) {
	$smile_pics[0] = $_base_path.'images/forum/smile.gif';
	$smile_pics[1] = $_base_path.'images/forum/wink.gif';
	$smile_pics[2] = $_base_path.'images/forum/frown.gif';
	$smile_pics[3] = $_base_path.'images/forum/ohwell.gif';
	$smile_pics[4] = $_base_path.'images/forum/tongue.gif';
	$smile_pics[5] = $_base_path.'images/forum/51.gif';
	$smile_pics[6] = $_base_path.'images/forum/52.gif';
	$smile_pics[7] = $_base_path.'images/forum/54.gif';
	$smile_pics[8] = $_base_path.'images/forum/27.gif';
	$smile_pics[9] = $_base_path.'images/forum/19.gif';
	$smile_pics[10] = $_base_path.'images/forum/3.gif';
	$smile_pics[11] = $_base_path.'images/forum/56.gif';
}

if (!isset($smile_codes)) {
	$smile_codes[0] = ':)';
	$smile_codes[1] = ';)';
	$smile_codes[2] = ':(';
	$smile_codes[3] = '::ohwell::';
	$smile_codes[4] = ':P';
	$smile_codes[5] = '::evil::';
	$smile_codes[6] = '::angry::';
	$smile_codes[7] = '::lol::';
	$smile_codes[8] = '::crazy::';
	$smile_codes[9] = '::tired::';
	$smile_codes[10] = '::confused::';
	$smile_codes[11] = '::muah::';
}

/**
* Replaces smile-code text into smilie image.
* @access	public
* @param	string $text		smile text to be transformed
* @return	string				transformed $text
* @see		$smile_pics			in include/lib/output.inc.php (above)
* @see		$smile_codes		in include/lib/output.inc.php (above)
* @author	Joel Kronenberg
*/
function smile_replace($text) {
	global $smile_pics;
	global $smile_codes;
	static $smiles;

	$smiles[0] = '<img src="'.$smile_pics[0].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_smile').'" />';
	$smiles[1] = '<img src="'.$smile_pics[1].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_wink').'" />';
	$smiles[2] = '<img src="'.$smile_pics[2].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_frown').'" />';
	$smiles[3]= '<img src="'.$smile_pics[3].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_oh_well').'" />';
	$smiles[4]= '<img src="'.$smile_pics[4].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_tongue').'" />';
	$smiles[5]= '<img src="'.$smile_pics[5].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_evil').'" />';
	$smiles[6]= '<img src="'.$smile_pics[6].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_angry').'" />';
	$smiles[7]= '<img src="'.$smile_pics[7].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_lol').'" />';
	$smiles[8]= '<img src="'.$smile_pics[8].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_crazy').'" />';
	$smiles[9]= '<img src="'.$smile_pics[9].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_tired').'" />';
	$smiles[10]= '<img src="'.$smile_pics[10].'" border="0" height="17" width="19" align="bottom" alt="'._AT('smile_confused').'" />';
	$smiles[11]= '<img src="'.$smile_pics[11].'" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_muah').'" />';

	$text = str_replace($smile_codes[0],$smiles[0],$text);
	$text = str_replace($smile_codes[1],$smiles[1],$text);
	$text = str_replace($smile_codes[2],$smiles[2],$text);
	$text = str_replace($smile_codes[3],$smiles[3],$text);
	$text = str_replace($smile_codes[4],$smiles[4],$text);
	$text = str_replace($smile_codes[5],$smiles[5],$text);
	$text = str_replace($smile_codes[6],$smiles[6],$text);
	$text = str_replace($smile_codes[7],$smiles[7],$text);
	$text = str_replace($smile_codes[8],$smiles[8],$text);
	$text = str_replace($smile_codes[9],$smiles[9],$text);
	$text = str_replace($smile_codes[10],$smiles[10],$text);
	$text = str_replace($smile_codes[11],$smiles[11],$text);

	return $text;
}


/* Used specifically for the visual editor
*/
function smile_javascript () {
	global $_base_path;
	global $smile_pics;
	global $smile_codes;

	static $i = 0;

	while ($smile_pics [$i]) {
		echo 'case "'.$smile_codes[$i].'":'."\n";
		echo 'pic = "'.$smile_pics[$i].'";'."\n";
		echo 'break;'."\n";
		$i++;
	}
}

function myCodes($text, $html = false) {
	global $_base_path;
	global $HTTP_USER_AGENT;

	if (substr($HTTP_USER_AGENT,0,11) == 'Mozilla/4.7') {
		$text = str_replace('[quote]','</p><p class="block">',$text);
		$text = str_replace('[/quote]','</p><p>',$text);

		$text = str_replace('[reply]','</p><p class="block">',$text);
		$text = str_replace('[/reply]','</p><p>',$text);
	} else {
		$text = str_replace('[quote]','<blockquote>',$text);
		$text = str_replace('[/quote]','</blockquote><p>',$text);

		$text = str_replace('[reply]','</p><blockquote class="block"><p>',$text);
		$text = str_replace('[/reply]','</p></blockquote><p>',$text);
	}

	$text = str_replace('[b]','<strong>',$text);
	$text = str_replace('[/b]','</strong>',$text);

	$text = str_replace('[i]','<em>',$text);
	$text = str_replace('[/i]','</em>',$text);

	$text = str_replace('[u]','<u>',$text);
	$text = str_replace('[/u]','</u>',$text);

	$text = str_replace('[center]','<center>',$text);
	$text = str_replace('[/center]','</center><p>',$text);

	/* colours */
	$text = str_replace('[blue]','<span style="color: blue;">',$text);
	$text = str_replace('[/blue]','</span>',$text);

	$text = str_replace('[orange]','<span style="color: orange;">',$text);
	$text = str_replace('[/orange]','</span>',$text);

	$text = str_replace('[red]','<span style="color: red;">',$text);
	$text = str_replace('[/red]','</span>',$text);

	$text = str_replace('[purple]','<span style="color: purple;">',$text);
	$text = str_replace('[/purple]','</span>',$text);

	$text = str_replace('[green]','<span style="color: green;">',$text);
	$text = str_replace('[/green]','</span>',$text);

	$text = str_replace('[gray]','<span style="color: gray;">',$text);
	$text = str_replace('[/gray]','</span>',$text);

	$text = str_replace('[op]','<span class="bigspacer"></span> <a href="',$text);
	$text = str_replace('[/op]','">'._AT('view_entire_post').'</a>',$text);

	$text = str_replace('[head1]','<h2>',$text);
	$text = str_replace('[/head1]','</h2>',$text);

	$text = str_replace('[head2]','<h3>',$text);
	$text = str_replace('[/head2]','</h3>',$text);

	$text = str_replace('[cid]',$_base_path.'content.php?cid='.$_SESSION['s_cid'],$text);

	global $sequence_links;
	if (isset($sequence_links['previous']) && $sequence_links['previous']['url']) {
		$text = str_replace('[pid]', $sequence_links['previous']['url'], $text);
	}
	if (isset($sequence_links['next']) && $sequence_links['next']['url']) {
		$text = str_replace('[nid]', $sequence_links['next']['url'], $text);
	}
	if (isset($sequence_links['resume']) && $sequence_links['resume']['url']) {
		$text = str_replace('[nid]', $sequence_links['resume']['url'], $text);
	}
	if (isset($sequence_links['first']) && $sequence_links['first']['url']) {
		$text = str_replace('[fid]', $sequence_links['first']['url'], $text);
	}

	/* contributed by Thomas M. Duffey <tduffey at homeboyz.com> */
	$html = !$html ? 0 : 1;

	// little hack added by greg to add syntax highlighting without using <?php \?\>

	$text = str_replace("[code]","[code]<?php",$text);
	$text = str_replace("[/code]","?>[/code]",$text);

	$text = preg_replace("/\[code\]\s*(.*)\s*\[\\/code\]/Usei", "highlight_code(fix_quotes('\\1'), $html)", $text);
	// now remove the <?php added above and leave the syntax colour behind.
	$text = str_replace("&lt;?php", "", $text);
	$text = str_replace("?&gt;", "", $text);

	return $text;
}

/* contributed by Thomas M. Duffey <tduffey at homeboyz.com> */
function highlight_code($code, $html) {
	// XHTMLize PHP highlight_string output until it gets fixed in PHP
	static $search = array(
		'<br>',
		'<font',
		'</font>',
		'color="');

	static $replace = array(
		'<br />',
		'<span',
		'</span>',
		'style="color:');
	if (!$html) {
		$code = str_replace('&lt;', '<', $code);
		$code = str_replace("\r", '', $code);
	}

	return str_replace($search, $replace, highlight_string($code, true));
}

/* contributed by Thomas M. Duffey <tduffey at homeboyz.com> */
function fix_quotes($text){
	return str_replace('\\"', '"', $text);
}

function embed_media($text) {
	if (preg_match("/\[media(\|[0-9]+\|[0-9]+)?\]*/", $text)==0){
		return $text;
	}

	$media_matches = Array();
	
	/*
		First, we search though the text for all different kinds of media defined by media tags and store the results in $media_matches.
		
		Then the different replacements for the different media tags are stored in $media_replace.
		
		Lastly, we loop through all $media_matches / $media_replaces. (We choose $media_replace as index because $media_matches is multi-dimensioned.) It is important that for each $media_matches there is a $media_replace with the same index. For each media match we check the width/height, or we use the default value of 425x350. We then replace the height/width/media1/media2 parameter placeholders in $media_replace with the correct ones, before running a str_replace on $text, replacing the given media with its correct replacement.
		
	*/
	
	// youtube videos
	preg_match_all("#\[media[0-9a-z\|]*\]http://([a-z0-9\.]*)?youtube.com/watch\?v=([a-z0-9_-]+)\[/media\]#i",$text,$media_matches[1],PREG_SET_ORDER);
	$media_replace[1] = '<object width="##WIDTH##" height="##HEIGHT##"><param name="movie" value="http://##MEDIA1##youtube.com/v/##MEDIA2##"></param><embed src="http://##MEDIA1##youtube.com/v/##MEDIA2##" type="application/x-shockwave-flash" width="##WIDTH##" height="##HEIGHT##"></embed></object>';
		
	// .mpg
	preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+).mpg\[/media\]#i",$text,$media_matches[2],PREG_SET_ORDER);
	$media_replace[2] = "<object data=\"##MEDIA1##.mpg\" type=\"video/mpeg\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.mpg\"><param name=\"autoplay\" value=\"false\"><param name=\"autoStart\" value=\"0\"><a href=\"##MEDIA1##.mpg\">##MEDIA1##.mpg</a></object>";
	
	// .avi
	preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+).avi\[/media\]#i",$text,$media_matches[3],PREG_SET_ORDER);
	$media_replace[3] = "<object data=\"##MEDIA1##.avi\" type=\"video/x-msvideo\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.avi\"><param name=\"autoplay\" value=\"false\"><param name=\"autoStart\" value=\"0\"><a href=\"##MEDIA1##.avi\">##MEDIA1##.avi</a></object>";
	
	// .wmv
	preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+).wmv\[/media\]#i",$text,$media_matches[4],PREG_SET_ORDER);
	$media_replace[4] = "<object data=\"##MEDIA1##.wmv\" type=\"video/x-ms-wmv\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.wmv\"><param name=\"autoplay\" value=\"false\"><param name=\"autoStart\" value=\"0\"><a href=\"##MEDIA1##.wmv\">##MEDIA1##.wmv</a></object>";
	
	// .mov
	preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+).mov\[/media\]#i",$text,$media_matches[5],PREG_SET_ORDER);
	$media_replace[5] = "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.mov\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><!--[if gte IE 7]> <!--><object type=\"video/quicktime\" data=\"##MEDIA1##.mov\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><a href=\"##MEDIA1##.mov\">##MEDIA1##.mov</a></object><!--<![endif]--><!--[if lt IE 7]><a href=\"##MEDIA1##.mov\">##MEDIA1##.mov</a><![endif]--></object>";
	
	// .swf
	preg_match_all("#\[media[0-9a-z\|]*\]([.\w\d]+[^\s\"]+).swf\[/media\]#i",$text,$media_matches[6],PREG_SET_ORDER);
	$media_replace[6] = "<object type=\"application/x-shockwave-flash\" data=\"##MEDIA1##.swf\" width=\"##WIDTH##\" height=\"##HEIGHT##\">  <param name=\"movie\" value=\"##MEDIA1##.swf\"><param name=\"loop\" value=\"false\"><a href=\"##MEDIA1##.swf\">##MEDIA1##.swf</a></object>";
	
	// .mp3
	preg_match_all("#\[media[0-9a-z\|]*\](.+[^\s\"]+).mp3\[/media\]#i",$text,$media_matches[7],PREG_SET_ORDER);
	$media_replace[7] = "<object type=\"audio/mpeg\" data=\"##MEDIA1##.mp3\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.mp3\"><param name=\"autoplay\" value=\"false\"><param name=\"autoStart\" value=\"0\"><a href=\"##MEDIA1##.mp3\">##MEDIA1##.mp3</a></object>";
	
	// .wav
	preg_match_all("#\[media[0-9a-z\|]*\](.+[^\s\"]+).wav\[/media\]#i",$text,$media_matches[8],PREG_SET_ORDER);
	$media_replace[8] ="<object type=\"audio/x-wav\" data=\"##MEDIA1##.wav\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.wav\"><param name=\"autoplay\" value=\"false\"><param name=\"autoStart\" value=\"0\"><a href=\"##MEDIA1##.wav\">##MEDIA1##.wav</a></object>";
	
	// .ogg
	preg_match_all("#\[media[0-9a-z\|]*\](.+[^\s\"]+).ogg\[/media\]#i",$text,$media_matches[9],PREG_SET_ORDER);
	$media_replace[9] ="<object type=\"application/ogg\" data=\"##MEDIA1##.ogg\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.ogg\"><a href=\"##MEDIA1##.ogg\">##MEDIA1##.ogg</a></object>";
	
	// .mid
	preg_match_all("#\[media[0-9a-z\|]*\](.+[^\s\"]+).mid\[/media\]#i",$text,$media_matches[10],PREG_SET_ORDER);
	$media_replace[10] ="<object type=\"application/x-midi\" data=\"##MEDIA1##.mid\" width=\"##WIDTH##\" height=\"##HEIGHT##\"><param name=\"src\" value=\"##MEDIA1##.mid\"><a href=\"##MEDIA1##.mid\">##MEDIA1##.mid</a></object>";
	
	$text = preg_replace("#\[media[0-9a-z\|]*\](.+[^\s\"]+).mid\[/media\]#i", "<object type=\"application/x-midi\" data=\"\\1.mid\" width=\"".$width."\" height=\"".$height."\"><param name=\"src\" value=\"\\1.mid\"><a href=\"\\1.mid\">\\1.mid</a></object>", $text);

	// Executing the replace
	for ($i=1;$i<=count($media_replace);$i++){
		foreach($media_matches[$i] as $media)
		{
			
			//find width and height for each matched media
			if (preg_match("/\[media\|([0-9]*)\|([0-9]*)\]*/", $media[0], $matches)) 
			{
				$width = $matches[1];
				$height = $matches[2];
			}
			else
			{
				$width = 425;
				$height = 350;
			}
			
			//replace media tags with embedded media for each media tag
			$media_input = $media_replace[$i];
			$media_input = str_replace("##WIDTH##","$width",$media_input);
			$media_input = str_replace("##HEIGHT##","$height",$media_input);
			$media_input = str_replace("##MEDIA1##","$media[1]",$media_input);
			$media_input = str_replace("##MEDIA2##","$media[2]",$media_input);
			$text = str_replace($media[0],$media_input,$text);
		}
	}
		
	return $text;
}

function make_clickable($text) {
	$text = embed_media($text);

//	$text = eregi_replace("([[:space:]])(http[s]?)://([^[:space:]<]*)([[:alnum:]#?/&=])", "\\1<a href=\"\\2://\\3\\4\">\\3\\4</a>", $text);
//
//	$text = eregi_replace(	'([_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]+)*'.
//							'\@'.'[_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]+)*'.'(\.[a-zA-Z]{1,6})+)',
//							"<a href=\"mailto:\\1\">\\1</a>",
//							$text);

	$text = preg_replace("/([\s])(http[s]?):\/\/([\^\s\<]*)([a-zA-Z0-9\#\?\/\&\=])/i", 
	                     "\\1<a href=\"\\2://\\3\\4\">\\3\\4</a>", $text);
	
	$text = preg_replace('/([_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]+)*'.
						'\@'.'[_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]+)*'.'(\.[a-zA-Z]{1,6})+)/i',
						"<a href=\"mailto:\\1\">\\1</a>",
						$text);
	return $text;
}

function image_replace($text) {
	/* image urls do not require http:// */
	
//	$text = eregi_replace("\[image(\|)?([[:alnum:][:space:]]*)\]" .
//						 "[:space:]*" .
//						 "([[:alnum:]#?/&=:\"'_.-]+)" .
//						 "[:space:]*" .
//						 "((\[/image\])|(.*\[/image\]))",
//				  "<img src=\"\\3\" alt=\"\\2\" />",
//				  $text);
	 
	$text = preg_replace("/\[image(\|)?([a-zA-Z0-9\s]*)\]".
	                     "[\s]*".
	                     "([a-zA-Z0-9\#\?\/\&\=\:\\\"\'\_\.\-]+)[\s]*".
	                     "((\[\/image\])|(.*\[\/image\]))/i",
				  "<img src=\"\\3\" alt=\"\\2\" />",
				  $text);
				  
	return $text;
}

function format_final_output($text, $nl2br = true) {
	global $_base_path;

	$text = str_replace('CONTENT_DIR/', '', $text);

	if ($nl2br) {
		return nl2br(image_replace(make_clickable(myCodes(' '.$text, false))));
	}
	return image_replace(make_clickable(myCodes(' '.$text, true)));
}

// 
function apply_customized_format($input) {
	global $_input, $moduleFactory;
	
	$_input = $input;
	$enabled_modules = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED);

	if (is_array($enabled_modules))
	{
		foreach ($enabled_modules as $dir_name => $mod)
		{
			$module_content_format_file = AT_INCLUDE_PATH . '../mods/'.$dir_name.'/module_format_content.php';
			if (file_exists($module_content_format_file))
			{
				include($module_content_format_file);
			}
		}
	}
	
	return $_input;
}
/****************************************************************************************/
/* @See: ./user/search.php & ./index.php */
function highlight($input, $var) {//$input is the string, $var is the text to be highlighted
	if ($var != "") {
		$xtemp = "";
		$i=0;
		/*
			The following 'if' statement is a check to ensure that the search term is not part of the tag, '<strong class="highlight">'.  Words within this string are avoided in case a previously highlighted string is used for the haystack, $input.  To avoid any html breaks in the highlighted string, the search word is avoided completely.
		*/
		if (strpos('<strong class="highlight">', $var) !== false) {
			return $input;
		}
		while($i<strlen($input)){
			if((($i + strlen($var)) <= strlen($input)) && (strcasecmp($var, substr($input, $i, strlen($var))) == 0)) {
				$xtemp .= '<strong class="highlight">' . substr($input, $i , strlen($var)) . '</strong>';
				$i += strlen($var);
			}
			else {
				$xtemp .= $input{$i};
				$i++;
			}
		}
		$input = $xtemp;
	}
	return $input;
}


/* @See: ./index.php */
function format_content($input, $html = 0, $glossary, $simple = false) {
	global $_base_path, $_config;

      	if (!$html) {
		$input = str_replace('<', '&lt;', $input);
		$input = str_replace('&lt;?php', '<?php', $input); // for bug #2087
	} elseif ($html==2) {
		$output = '<iframe width="100%" frameborder="0" id="content_frame" marginheight="0" marginwidth="0" src="'.$input.'"></iframe>';
		$output .=	'<script type="text/javascript">
					function resizeIframe() {
						var height = document.documentElement.clientHeight;
						
						// not sure how to get this dynamically
						height -= 20; /* whatever you set your body bottom margin/padding to be */
						
						document.getElementById(\'content_frame\').style.height = height +"px";
						
					};
					document.getElementById(\'content_frame\').onload = resizeIframe;
					window.onresize = resizeIframe;
					</script>';
		return $output;
	}

	/* do the glossary search and replace: */
	if (is_array($glossary)) {
		foreach ($glossary as $k => $v) {
			$k = urldecode($k);
			$v = str_replace("\n", '<br />', $v);
			$v = str_replace("\r", '', $v);

			/* escape special characters */
			$k = preg_quote($k);

			$k = str_replace('&lt;', '<', $k);
			$k = str_replace('/', '\/', $k);

			$original_term = $k;
			$term = $original_term;

	 		$term = '(\s*'.$term.'\s*)';
			$term = str_replace(' ','((<br \/>)*\s*)', $term); 

			$def = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');		
			if ($simple) {
				$input = preg_replace
						("/(\[\?\])$term(\[\/\?\])/i",
						'<a href="'.$simple.'glossary.html#'.urlencode($original_term).'" target="body" class="at-term">\\2</a>',
						$input);
			} else {
				$input = preg_replace
						("/(\[\?\])$term(\[\/\?\])/i",
						'\\2<sup><a class="tooltip" href="'.$_base_path.'glossary/index.php?g_cid='.$_SESSION['s_cid'].htmlentities(SEP).'w='.urlencode($original_term).'#term" title="'.addslashes($original_term).': '.$def.'"><span style="color: blue; text-decoration: none;font-size:small; font-weight:bolder;">?</span></a></sup>',$input);
			}
		}
	} else if (!$user_glossary) {
		$input = str_replace(array('[?]','[/?]'), '', $input);
	}
        
	$input = str_replace('CONTENT_DIR', '', $input);

	if (isset($_config['latex_server']) && $_config['latex_server']) {
		// see: http://www.forkosh.com/mimetex.html
		$input = preg_replace('/\[tex\](.*?)\[\/tex\]/sie', "'<img src=\"'.\$_config['latex_server'].rawurlencode('$1').'\" align=\"middle\">'", $input);
	}

	if ($html) {
		$x = apply_customized_format(format_final_output($input, false));
		return $x;
	}

	$output = apply_customized_format(format_final_output($input));

	$output = '<p>'.$output.'</p>';

	return $output;
}

function get_content_table($content)
{
	preg_match_all("/<(h[\d]+)[^>]*>(.*)<\/(\s*)\\1(\s*)>/i", $content, $found_headers, PREG_SET_ORDER);
	
	if (count($found_headers) == 0) return array("", $content);
	else
	{
		$num_of_headers = 0;

		for ($i = 0; $i < count($found_headers); $i++)
		{
			$div_id = "_header_" . $num_of_headers++;
			
			if ($i == 0)
			{
				$content_table = "<div id=\"toc\">\n<fieldset id=\"toc\"><legend>". _AT("table_of_contents")."</legend>\n";
			}

			$content = str_replace($found_headers[$i][0], '<div id="'.$div_id.'">'.$found_headers[$i][0].'</div>', $content);
			$content_table .= '<a href="'.$_SERVER["REQUEST_URI"].'#'.$div_id.'" class="'.$found_headers[$i][1].'">'. $found_headers[$i][2]."</a>\n";

			if ($i == count($found_headers) - 1)
			{
				$content_table .= "</fieldset></div><br />";
			}
		}
		return array($content_table, $content);
	}
}

function find_terms($find_text) {
	preg_match_all("/(\[\?\])(.[^\?]*)(\[\/\?\])/i", $find_text, $found_terms, PREG_PATTERN_ORDER);
	return $found_terms;
}

/***********************************************************************
	@See /include/Classes/Message/Message.class.php
	Jacek Materna
*/

function html_get_list($array) {
	$list = '';
	foreach ($array as $value) {
		$list .= '<li>'.$value.'</li>';
	}
	return $list;
}

/**
 * print_paginator
 *
 * print out list of page links
 */
function print_paginator($current_page, $num_rows, $request_args, $rows_per_page = 50, $window = 5) {
	$num_pages = ceil($num_rows / $rows_per_page);
	$request_args = '?'.$request_args;

    if ($num_rows) {
		echo '<div class="paging">';
	    echo '<ul>';
		
		$i=max($current_page-$window - max($window-$num_pages+$current_page,0), 1);

		if ($i > 1) {
			echo '<li><a href="'.$_SERVER['PHP_SELF'].$request_args.htmlspecialchars(SEP).'p=1">1</a></li>';
			if ($i > 2) {
		        echo '<li>&hellip;</li>';
			}
		}

		for ($i; $i<= min($current_page+$window -min($current_page-$window,0),$num_pages); $i++) {
			if ($current_page == $i) {
				echo '<li><a href="'.$_SERVER['PHP_SELF'].$request_args.htmlspecialchars(SEP).'p='.$i.'" class="current"><em>'.$current_page.'</em></a></li>';
			} else {
				echo '<li><a href="'.$_SERVER['PHP_SELF'].$request_args.htmlspecialchars(SEP).'p='.$i.'">'.$i.'</a></li>';
			}
		}
        if ($i <= $num_pages) {
			if ($i < $num_pages) {
		        echo '<li>&hellip;</li>';
	        }
			echo '<li><a href="'.$_SERVER['PHP_SELF'].$request_args.htmlspecialchars(SEP).'p='.$num_pages.'">'.$num_pages.'</a></li>';
		}
		echo '</ul>';
		echo '</div>';
	}
}


/**
* According to user's preferences, it provides appropriated resources in the content page.
* @access	public
* @param	$cid: 				content id.
* @param	$content_page: 		the original content page ($content_row['text'], from content.php).
* @return	string|array		$content: the content page with the appropriated resources.
* @see		$db			        in include/vitals.inc.php
* @author	Silvia Mirri
*/
function provide_alternatives1($cid, $content_page){
	global $db;
	
	$vidoe_exts = array("mpg", "avi", "wmv", "mov", "swf", "mp3", "wav", "ogg", "mid");

	$content = $content_page;
	
	if (($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT']==0) && ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_AUDIO']==0) && ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_VISUAL']==0)) 
	{
		//No user's preferences related to content format are declared
		return $content;
	}
	/*else if ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT']==1){

		$sql_primary = "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id=".$cid." and resource='".mysql_real_escape_string($content_page)."'";

		$result = mysql_query($sql_primary, $db);
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_assoc($result)) {
			$sql_type 	 = "SELECT * FROM ".TABLE_PREFIX."primary_resources_types WHERE primary_resource_id=$row[primary_resource_id]";	
			$result_type = mysql_query($sql_type, $db);
				if (mysql_num_rows($result_type) > 0) {
					while ($row_type = mysql_fetch_assoc($result_type)){
						if (($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TEXT']==1) && ($row_type[type_id]==3)){
								$sql_text	  = "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id=$row[primary_resource_id] and language_code='".$_SESSION['prefs']['PREF_ALT_TEXT_PREFER_LANG']."'";	
							$result_text = mysql_query($sql_text, $db);
							if (mysql_num_rows($result_text) > 0) {
								while ($row_text = mysql_fetch_assoc($result_text)){
									$sql_text_alt 	  = "SELECT * FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id=$row_text[secondary_resource_id]";	
									$result_text_alt = mysql_query($sql_text_alt, $db);
									if (mysql_num_rows($result_text_alt) > 0) {
										while ($row_text_alt = mysql_fetch_assoc($result_text_alt)){
											if ((($_SESSION['prefs']['PREF_ALT_TO_TEXT']==visual) && ($row_text_alt[type_id]==4)) || (($_SESSION['prefs']['PREF_ALT_TO_TEXT']==audio) && ($row_audio_alt[type_id]==1)) || (($_SESSION['prefs']['PREF_ALT_TO_TEXT']==sign_lang) && ($row_text_alt[type_id]==2))) {
												if (($_SESSION['prefs']['PREF_ALT_TO_TEXT_APPEND_OR_REPLACE']=='replace'))
													$content = $row_text_alt['secondary_resource'];
												else 
													$content = $content.'<br/>'.$row_text_alt['secondary_resource'];
											}
										}	
									}
								}
							}
						}
					}
				}
			}

		}
		return $content;								
	}*/
	else
	{
	$sql_primary = "SELECT * FROM ".TABLE_PREFIX."primary_resources WHERE content_id=".$cid." ORDER BY primary_resource_id";
	$result		 = mysql_query($sql_primary, $db);
	
	if (mysql_num_rows($result) > 0) 
	{
		while ($row = mysql_fetch_assoc($result)) 
		{
			$sql_type 	 = "SELECT * FROM ".TABLE_PREFIX."primary_resources_types WHERE primary_resource_id=$row[primary_resource_id]";	
			$result_type = mysql_query($sql_type, $db);
			
			if (mysql_num_rows($result_type) > 0) 
			{
				while ($row_type = mysql_fetch_assoc($result_type))
				{
					if (($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_AUDIO']==1) && ($row_type[type_id]==1))
					{
						$sql_audio	  = "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id=$row[primary_resource_id] and language_code='".$_SESSION['prefs']['PREF_ALT_AUDIO_PREFER_LANG']."'";	
						$result_audio = mysql_query($sql_audio, $db);
						if (mysql_num_rows($result_audio) > 0) 
						{
							while ($row_audio = mysql_fetch_assoc($result_audio))
							{
								$sql_audio_alt 	  = "SELECT * FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id=$row_audio[secondary_resource_id]";	
								$result_audio_alt = mysql_query($sql_audio_alt, $db);
								if (mysql_num_rows($result_audio_alt) > 0) 
								{
									while ($row_audio_alt = mysql_fetch_assoc($result_audio_alt))
									{
										if ((($_SESSION['prefs']['PREF_ALT_TO_AUDIO']=="visual") && ($row_audio_alt[type_id]==4)) || (($_SESSION['prefs']['PREF_ALT_TO_AUDIO']==text) && ($row_audio_alt[type_id]==3)) || (($_SESSION['prefs']['PREF_ALT_TO_AUDIO']==sign_lang) && ($row_audio_alt[type_id]==2))) 
										{
											if (($_SESSION['prefs']['PREF_ALT_TO_AUDIO_APPEND_OR_REPLACE']=='replace'))
											{
												$before  = explode($row['resource'], $content);
												$last_c  = substr($before[0], -1, 1);
												if ($last_c=="]")
													$shift   = strripos($before[0], '[');
												else
													$shift   = strripos($before[0], '<');

												$len     = strlen($before[0]);
												$shift   = $len-$shift;
												$first   = substr($before[0], 0, -$shift);
												$ext     = substr($row_audio['secondary_resource'], -3);
												if (in_array($ext, $vidoe_exts))
												{
													$content = $first.'[media]'.$row_audio['secondary_resource'];
													if ($last_c=="]")
													{
														$after 	 = substr($before[1], 8);
														$after   = '[/media]'.$after;
													}
													else
													{
														$shift 	 = strpos($before[1], '</');
														$after 	 = substr($before[1], $shift);
														$after 	 = substr($after, 4);
														$after 	 = '[/media]'.$after;
													}
												}
												else
												{
													$new 	 = '<a href="';
													$content = $first.$new.$row_audio['secondary_resource'].'">'.$row_audio['secondary_resource'];
													if ($last_c=="]")
													{
														$after 	 = substr($before[1], 8);
														$after   = '</a>'.$after;
													}
													else
													{
														$shift 	 = strpos($before[1], '</');
														$after 	 = substr($before[1], $shift);
													}
												}
												$content = $content.$after;
											}
											else
											{
												$before = explode($row['resource'], $content);
												$content   = $before[0].$row['resource'];
												$last_c  = substr($before[0], -1, 1);
												$ext     = substr($row_audio['secondary_resource'], -3);
												if (in_array($ext, $vidoe_exts))
												{
													if ($last_c=="]")
													{
														$after 	   = substr($before[1], 8);
														$content   = $content.'[/media][media]'.$row_audio['secondary_resource'].'[/media]'.$after;
													}
													else
													{
														$shift 	   = strpos($before[1], '</a>');
														$alt_shift = $len-$shift;
														$res       = substr($before[1], 0, -$alt_shift);
														$shift     = $shift+4;
														$after 	   = substr($before[1], $shift);
														$content   = $content.$res.'</a><br/>[media]'.$row_audio['secondary_resource'].'[/media]'.$after;
													}
												}
												else 
												{
													if ($last_c=="]")
													{
														$after 	   = substr($before[1], 8);
														$content   = $content.'[/media]'.'<p><a href="'.$row_audio['secondary_resource'].'">'.$row_audio['secondary_resource'].'</a></p>'.$after;
													}
													else
													{
														$shift 	   = strpos($before[1], '</a>');
														$alt_shift = $len-$shift;
														$res       = substr($before[1], 0, -$alt_shift);
														$shift     = $shift+4;
														$after 	   = substr($before[1], $shift);
														$content   = $content.$res.'</a><p><a href="'.$row_audio['secondary_resource'].'">'.$row_audio['secondary_resource'].'</a></p>'.$after;
													}
												}	
											}
										}
									}	
								}
							}
						}
					}
					if (($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT']==1) && ($row_type[type_id]==3))
					{
						$sql_text	   = "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id=$row[primary_resource_id] and language_code='".$_SESSION['prefs']['PREF_ALT_VISUAL_PREFER_LANG']."'";	
						$result_text = mysql_query($sql_text, $db);
						if (mysql_num_rows($result_text) > 0) 
						{
							while ($row_text = mysql_fetch_assoc($result_text))
							{
								$sql_text_alt 	 = "SELECT * FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id=$row_text[secondary_resource_id]";	
								$result_text_alt	 = mysql_query($sql_text_alt, $db);
								if (mysql_num_rows($result_text_alt) > 0) 
								{
									while ($row_text_alt = mysql_fetch_assoc($result_text_alt))
									{
										if ((($_SESSION['prefs']['PREF_ALT_TO_TEXT']==audio) && ($row_text_alt[type_id]==1)) || 
										    (($_SESSION['prefs']['PREF_ALT_TO_TEXT']==visual) && ($row_text_alt[type_id]==4)) || 
										    (($_SESSION['prefs']['PREF_ALT_TO_TEXT']==sign_lang) && ($row_text_alt[type_id]==2)))
										{
											if ($_SESSION['prefs']['PREF_ALT_TO_TEXT_APPEND_OR_REPLACE']=='replace')
											{
												$before  = explode($row['resource'], $content);
												$shift   = strripos($before[0], '<');
												$len     = strlen($before[0]);
												$shift   = $len-$shift;
												$first   = substr($before[0], 0, -$shift);
												$ext     = substr($row_text['secondary_resource'], -3);
												if (in_array($ext, $vidoe_exts))
												{
													$content = $first.'[media]'.$row_text['secondary_resource'];
													if ($last_c=="]")
													{
														$after 	 = substr($before[1], 8);
														$after   = '[/media]'.$after;
													}
													else
													{
														$shift 	 = strpos($before[1], '</');
														$after 	 = substr($before[1], $shift);
														$after 	 = substr($after, 4);
														$after 	 = '[/media]'.$after;
													}
												}
												else
												{
													if (($_SESSION['prefs']['PREF_ALT_TO_TEXT']==visual) && ($row_text_alt[type_id]==4))
													{
														$new     = '<img border="0" alt="Alternate Text" src="';
														$content = $first.$new.$row_text['secondary_resource'].'"/>';
														$shift 	 = strpos($before[1], '</');
														$after 	 = substr($before[1], $shift);
														$media	 = substr($after, 0, 8);
														if ($media == '[/media]')
															$after 	 = substr($after, 8);
														else
															$after 	 = substr($after, 4);
													}
													else
													{
														$new 	 = '<a href="';
														$content = $first.$new.$row_text['secondary_resource'].'">'.$row_text['secondary_resource'];
														$shift 	 = strpos($before[1], '</');
														$after 	 = substr($before[1], $shift);
													}
												}
												$content = $content.$after;
											}
											else 
											{
												$before    = explode($row['resource'], $content);
												$content   = $before[0].$row['resource'];
												$ext       = substr($row_text['secondary_resource'], -3);
												if (in_array($ext, $vidoe_exts))
												{
//													$shift 	   = strpos($before[1], '</a>');
													$shift 	   = strpos($before[1], '>') + 1;
													$alt_shift = $len-$shift;
													$res       = substr($before[1], 0, -$alt_shift);
													//$shift     = $shift;
													$after 	   = substr($before[1], $shift);
													$af 	   = strpos($after, '<');
													$str       = substr($after, $af, 4);
													if ($str != '</a>')
														$content   = $content.$res.'<br/>[media]'.$row_text['secondary_resource'].'[/media]'.$after;
													else 
													{
														$shift 	   = strpos($before[1], '</a>');
														$alt_shift = $len-$shift;
														$res       = substr($before[1], 0, -$alt_shift);
														$shift     = $shift+4;
														$after 	   = substr($before[1], $shift);
														$content   = $content.$res.'</a><br/>[media]'.$row_text['secondary_resource'].'[/media]'.$after;
													}
												}
												else 
												{
													if (($_SESSION['prefs']['PREF_ALT_TO_TEXT']==visual) && ($row_text_alt[type_id]==4))
													{
														$shift 	   = strpos($before[1], '</a>');
														$alt_shift = $len-$shift;
														$res       = substr($before[1], 0, -$alt_shift);
														$shift     = $shift+4;
														$after 	   = substr($before[1], $shift);
														$content   = $content.$res.'</a><img border="0" alt="Alternate Text" src="'.$row_text['secondary_resource'].'"/>'.$after;
													}
													else 
													{
														$shift 	   = strpos($before[1], '</a>');
														$alt_shift = $len-$shift;
														$res       = substr($before[1], 0, -$alt_shift);
														$shift     = $shift+4;
														$after 	   = substr($before[1], $shift);
														$content   = $content.$res.'</a><p><a href="'.$row_text['secondary_resource'].'">'.$row_text['secondary_resource'].'</a></p>'.$after;
													}
												}
											}
										}
									}
								} 
							}
						}
					}
					if (($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_VISUAL']==1) && ($row_type[type_id]==4))
					{
						$sql_visual	   = "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id=$row[primary_resource_id] and language_code='".$_SESSION['prefs']['PREF_ALT_VISUAL_PREFER_LANG']."'";	
						$result_visual = mysql_query($sql_visual, $db);
						
						if (mysql_num_rows($result_visual) > 0) 
						{
							while ($row_visual = mysql_fetch_assoc($result_visual))
							{
								$sql_visual_alt 	 = "SELECT * FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id=$row_visual[secondary_resource_id]";	
								$result_visual_alt	 = mysql_query($sql_visual_alt, $db);
								
								if (mysql_num_rows($result_visual_alt) > 0) 
								{
									while ($row_visual_alt = mysql_fetch_assoc($result_visual_alt))
									{
										if ((($_SESSION['prefs']['PREF_ALT_TO_VISUAL']==audio) && ($row_visual_alt[type_id]==1)) || 
										    (($_SESSION['prefs']['PREF_ALT_TO_VISUAL']==text) && ($row_visual_alt[type_id]==3)) || 
										    (($_SESSION['prefs']['PREF_ALT_TO_VISUAL']==sign_lang) && ($row_visual_alt[type_id]==2)))
										{
											if ($_SESSION['prefs']['PREF_ALT_TO_VISUAL_APPEND_OR_REPLACE']=='replace')
											{
												$before  = explode($row['resource'], $content);
												$last_c  = substr($before[0], -1, 1);
												if ($last_c=="]"){
													$shift   = strripos($before[0], '[');
												}
												else
												{
													$shift   = strripos($before[0], '<');
												}
												$len     = strlen($before[0]);
												$shift   = $len-$shift;
												$first   = substr($before[0], 0, -$shift);
												$ext     = substr($row_visual['secondary_resource'], -3);
												if (in_array($ext, $vidoe_exts))
												{
													$content = $first.'[media]'.$row_visual['secondary_resource'];
													if ($last_c=="]")
													{
														$after 	 = substr($before[1], 8);
														$after   = '[/media]'.$after;
													}
													else
													{
														$shift 	 = strpos($before[1], '/>');
														$after 	 = substr($before[1], $shift);
														$after 	 = substr($after, 2);
														$after 	 = '[/media]'.$after;
													}
												}
												else
												{
													$new 	 = '<a href="';
													$content = $first.$new.$row_visual['secondary_resource'].'">'.$row_visual['secondary_resource'].'</a>';
													if ($last_c=="]")
													{
														$after 	 = substr($before[1], 8);
														$content = $content.$after;
													}
													else
													{
														$shift 	   = strpos($before[1], '/>');
														$alt_shift = $len-$shift;
														$res       = substr($before[1], 0, -$alt_shift);
														$shift     = $shift+2;
														$after 	   = substr($before[1], $shift);
													}
												}
												$content = $content.$after;
											}
											else 
											{
												$before    = explode($row['resource'], $content);
												$content   = $before[0].$row['resource'];
												$last_c    = substr($before[0], -1, 1);
 												$ext       = substr($row_visual['secondary_resource'], -3);
												if (in_array($ext, $vidoe_exts))
												{
													if ($last_c=="]")
													{
														$after 	   = substr($before[1], 8);
														$content   = $content.'[/media][media]'.$row_visual['secondary_resource'].'[/media]'.$after;
													}
													else
													{
														$shift 	   = strpos($before[1], '/>');
														$alt_shift = $len-$shift;
														$res       = substr($before[1], 0, -$alt_shift);
														$shift     = $shift+2;
														$after 	   = substr($before[1], $shift);
														$content   = $content.$res.'/>[media]'.$row_visual['secondary_resource'].'[/media]'.$after;
													}
												}
												else 
												{
													if ($last_c=="]")
													{
														$after 	   = substr($before[1], 8);
														$content   = $content.'[/media]'.'<p><a href="'.$row_visual['secondary_resource'].'">'.$row_visual['secondary_resource'].'</a></p>'.$after;
													}
													else
													{
														$shift 	   = strpos($before[1], '/>');
														$alt_shift = $len-$shift;
														$res       = substr($before[1], 0, -$alt_shift);
														$shift     = $shift+2;
														$after 	   = substr($before[1], $shift);
														$content   = $content.$res.'/><p><a href="'.$row_visual['secondary_resource'].'">'.$row_visual['secondary_resource'].'</a></p>'.$after;
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		
		return $content;
		}
		else 
		{
			//No alternatives are declared by content authors
			$content=$content_page;
			return $content;
		}
	}
}	
	
/**
* replace source object with alternatives according to user's preferences
* @access	public
* @param	$cid: 				content id.
* @param	$content:	 		the original content page ($content_row['text'], from content.php).
* @return	string				$content: the content page with the appropriated resources.
* @see		$db			        from include/vitals.inc.php
* @author	Cindy Qi Li
*/
function provide_alternatives($cid, $content){
	global $db;
	
	$vidoe_exts = array("mpg", "avi", "wmv", "mov", "swf", "mp3", "wav", "ogg", "mid");
	$txt_exts = array("txt", "html", "htm");
	
	if (($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT']==0) && ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_AUDIO']==0) && ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_VISUAL']==0)) 
	{
		//No user's preferences related to content format are declared
		return $content;
	}

	// get all relations between primary resources and their alternatives
	$sql = "SELECT pr.resource, prt.type_id primary_type, sr.secondary_resource, srt.type_id secondary_type
	          FROM ".TABLE_PREFIX."primary_resources pr, ".
	                 TABLE_PREFIX."primary_resources_types prt,".
	                 TABLE_PREFIX."secondary_resources sr,".
	                 TABLE_PREFIX."secondary_resources_types srt
	         WHERE pr.content_id=".$cid."
		       AND pr.primary_resource_id = prt.primary_resource_id
		       AND pr.primary_resource_id = sr.primary_resource_id
		       AND sr.language_code='".$_SESSION['prefs']['PREF_ALT_AUDIO_PREFER_LANG']."'
		       AND sr.secondary_resource_id = srt.secondary_resource_id
		     ORDER BY pr.primary_resource_id, prt.type_id";
	$result = mysql_query($sql, $db);

	if (mysql_num_rows($result) == 0) return $content;
	
	while ($row = mysql_fetch_assoc($result)) 
	{
		if (($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT']==1 && $row['primary_type']==3 &&
		    ($_SESSION['prefs']['PREF_ALT_TO_TEXT']=="audio" && $row['secondary_type']==1 || 
		     $_SESSION['prefs']['PREF_ALT_TO_TEXT']=="visual" && $row['secondary_type']==4 || 
		     $_SESSION['prefs']['PREF_ALT_TO_TEXT']=="sign_lang" && $row['secondary_type']==2)) ||
		     
		     ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_AUDIO']==1 && $row['primary_type']==1 &&
		     ($_SESSION['prefs']['PREF_ALT_TO_AUDIO']=="visual" && $row['secondary_type']==4 || 
		      $_SESSION['prefs']['PREF_ALT_TO_AUDIO']=="text" && $row['secondary_type']==3 || 
		      $_SESSION['prefs']['PREF_ALT_TO_AUDIO']=="sign_lang" && $row['secondary_type']==2)) ||
		      
		     ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_VISUAL']==1 && $row['primary_type']==4 &&
		     ($_SESSION['prefs']['PREF_ALT_TO_VISUAL']=="audio" && $row['secondary_type']==1 || 
		      $_SESSION['prefs']['PREF_ALT_TO_VISUAL']=="text" && $row['secondary_type']==3 || 
		      $_SESSION['prefs']['PREF_ALT_TO_VISUAL']=="sign_lang" && $row['secondary_type']==2))
		    )
		{
			$ext = substr($row['secondary_resource'], strrpos($row['secondary_resource'], '.')+1);
			
			// alternative is video
			if (in_array($ext, $vidoe_exts))
				$target = '[media]'.$row['secondary_resource'].'[/media]';
			// a text primary to be replaced by a visual alternative 
			else if (in_array($ext, $txt_exts))
			{
				if (substr($row['secondary_resource'], 0, 2) == '..') 
					$file_location = substr($row['secondary_resource'], 3);
				else 
					$file_location = $row['secondary_resource'];
				$file .= $file_location;
				
				$file = AT_CONTENT_DIR.$_SESSION['course_id'] . '/'.$file_location;
				$target = file_get_contents($file);
				
				// check whether html file
				if (preg_match('/.*\<html.*\<\/html\>.*/s', $target))
				{ // is a html file, use iframe to display
					// get real path to the text file
					if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
						$course_base_href = 'get.php/';
					} else {
						$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
					}
	
					$file = AT_BASE_HREF . $course_base_href.$file_location;
						
					$target = '<iframe width="100%" frameborder="0" class="autoHeight" scrolling="auto" src="'.$file.'"></iframe>';
				}
				else
				{ // is a text file, insert/replace into content
					$target = nl2br($target);
				}
			} 
			else if ($_SESSION['prefs']['PREF_USE_ALTERNATIVE_TO_TEXT']==1 
			         && $_SESSION['prefs']['PREF_ALT_TO_TEXT']=="visual")
				$target = '<img border="0" alt="Alternate Text" src="'.$row['secondary_resource'].'"/>';
			// otherwise
			else
				$target = '<p><a href="'.$row['secondary_resource'].'">'.$row['secondary_resource'].'</a></p>';
			
			// replace or append the target alternative to the source
			if (($row['primary_type']==3 && $_SESSION['prefs']['PREF_ALT_TO_TEXT_APPEND_OR_REPLACE'] == 'replace') ||
				($row['primary_type']==1 && $_SESSION['prefs']['PREF_ALT_TO_AUDIO_APPEND_OR_REPLACE']=='replace') ||
				($row['primary_type']==4 && $_SESSION['prefs']['PREF_ALT_TO_VISUAL_APPEND_OR_REPLACE']=='replace'))
				$pattern_replace_to = '${1}'.$target.'${3}';
			else
				$pattern_replace_to = '${1}${2}'.$target.'${3}';
				
			// append/replace target alternative to [media]source[/media]
			$content = preg_replace("/(.*)(".preg_quote("[media]".$row['resource']."[/media]", "/").")(.*)/s", 
			             $pattern_replace_to, $content);
			
			// append/replace target alternative to <a>...source...</a> or <a ...source...>...</a>
			if (preg_match("/\<a.*".preg_quote($row['resource'], "/").".*\<\/a\>/s", $content))
			{
				$content = preg_replace("/(.*)(\<a.*".preg_quote($row['resource'], "/").".*\<\/a\>)(.*)/s", 
		                                $pattern_replace_to, $content);
			}

			// append/replace target alternative to <img ... src="source" ...></a>
			if (preg_match("/\<img.*src=\"".preg_quote($row['resource'], "/")."\".*\/\>/s", $content))
			{
				$content = preg_replace("/(.*)(\<img.*src=\"".preg_quote($row['resource'], "/")."\".*\/\>)(.*)/s", 
		                                $pattern_replace_to, $content);
			}
			
			// append/replace target alternative to <object ... source ...></object>
			if (preg_match("/\<object.*".preg_quote($row['resource'], "/").".*\<\/object\>/s", $content))
			{
				$content = preg_replace("/(.*)(\<object.*".preg_quote($row['resource'], "/").".*\<\/object\>)(.*)/s", 
		                                $pattern_replace_to, $content);
			}

			// append/replace target alternative to <embed ... source ...>
			if (preg_match("/\<embed.*".preg_quote($row['resource'], "/").".*\>/s", $content))
			{
				$content = preg_replace("/(.*)(\<embed.*".preg_quote($row['resource'], "/").".*\>)(.*)/s", 
		                                $pattern_replace_to, $content);
			}
		}
	}
	return $content;
}	
		
/**
* apply_timezone
* converts a unix timestamp into another UNIX timestamp with timezone offset added up.
* Adds the user's timezone offset, then converts back to a MYSQL timestamp
* Available both as a system config option, and a user preference, if both are set
* they are added together
* @param   date	 MYSQL timestamp.
* @return  date  MYSQL timestamp plus user's and/or system's timezone offset.
* @author  Greg Gay  .
*/
function apply_timezone($timestamp){
	global $_config;

	if($_config['time_zone']){
		$timestamp = ($timestamp + ($_config['time_zone']*3600));
	}

	if(isset($_SESSION['prefs']['PREF_TIMEZONE'])){
		$timestamp = ($timestamp + ($_SESSION['prefs']['PREF_TIMEZONE']*3600));
	}

	return $timestamp;
}
?>
