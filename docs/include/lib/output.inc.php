<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

/**********************************************************************************/
/* Output functions found in this file, in order:
/*
/*	- AT_date(format, timestamp, format_type)
/*
/*	- getMessage(array codes)
/*	- print_errors(array errors)
/*	- print_feedback(array feedback)
/*	- print_help (array help)
/*	- print_warnings (array warnings)
/*	- print_infos (array infos)
/*	- print_items (array items)
/*	- print_popup_help (array help, [left | right])
/*	- print_editor (array editor_links)
/*	- print_editorlg (array editor_links)
/*
/*	- _AC([...])
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

/* Uses the same options as date(), but require a % infront of each argument and the
/* textual values are language dependant ( unlike date() ).

	the following options were added as language dependant:

	%D: A textual representation of a week, three letters Mon through Sun
	%F: A full textual representation of a month, such as January or March January through December
	%l (lowercase 'L'): A full textual representation of the day of the week Sunday through Saturday
	%M: A short textual representation of a month, three letters Jan through Dec
	?? %S: English ordinal suffix for the day of the month, 2 characters st, nd, rd or th. Works well with j
	?? %a: Lowercase Ante meridiem and Post meridiem am or pm 
	?? %A: Uppercase Ante meridiem and Post meridiem AM or PM 

	valid format_types:
	AT_DATE_MYSQL_DATETIME:		YYYY-MM-DD HH:MM:SS
	AT_DATE_MYSQL_TIMESTAMP_14:	YYYYMMDDHHMMSS
	AT_DATE_UNIX_TIMESTAMP:		seconds since epoch
	AT_DATE_INDEX_VALUE:		0-x, index into a date array
*/
function AT_date($format='%Y-%M-%d', $timestamp = '', $format_type=AT_DATE_MYSQL_DATETIME)
{	
	static $day_name_ext, $day_name_con, $month_name_ext, $month_name_con;

	if (!isset($day_name_ext)) {
		$day_name_ext = array(	_AT('date_sunday'), 
								_AT('date_monday'), 
								_AT('date_tuesday'), 
								_AT('date_wednesday'), 
								_AT('date_thursday'), 
								_AT('date_friday'),
								_AT('date_saturday'));

		$day_name_con = array(	_AT('date_sun'), 
								_AT('date_mon'), 
								_AT('date_tue'), 
								_AT('date_wed'),
								_AT('date_thu'), 
								_AT('date_fri'), 
								_AT('date_sat'));

		$month_name_ext = array(_AT('date_january'), 
								_AT('date_february'), 
								_AT('date_march'), 
								_AT('date_april'), 
								_AT('date_may'),
								_AT('date_june'), 
								_AT('date_july'), 
								_AT('date_august'), 
								_AT('date_september'), 
								_AT('date_october'), 
								_AT('date_november'),
								_AT('date_december'));

		$month_name_con = array(_AT('date_jan'), 
								_AT('date_feb'), 
								_AT('date_mar'), 
								_AT('date_apr'), 
								_AT('date_may_short'),
								_AT('date_jun'), 
								_AT('date_jul'), 
								_AT('date_aug'), 
								_AT('date_sep'), 
								_AT('date_oct'), 
								_AT('date_nov'),
								_AT('date_dec'));
	}

	if ($format_type == AT_DATE_INDEX_VALUE) {
		if ($format == '%D') {
			return $day_name_con[$timestamp-1];
		} else if ($format == '%l') {
			return $day_name_ext[$timestamp-1];
		} else if ($format == '%F') {
			return $month_name_ext[$timestamp-1];
		} else if ($format == '%M') {
			return $month_name_con[$timestamp-1];
		}
	}

	if ($timestamp == '') {
		$timestamp = time();
		$format_type = AT_DATE_UNIX_TIMESTAMP;
	}

	/* 1. convert the date to a Unix timestamp before we do anything with it */
	if ($format_type == AT_DATE_MYSQL_DATETIME) {
		$year	= substr($timestamp,0,4);
		$month	= substr($timestamp,5,2);
		$day	= substr($timestamp,8,2);
		$hour	= substr($timestamp,11,2);
		$min	= substr($timestamp,14,2);
		$sec	= substr($timestamp,17,2);
	    $timestamp	= mktime($hour, $min, $sec, $month, $day, $year);

	} else if ($format_type == AT_DATE_MYSQL_TIMESTAMP_14) {
	    $hour		= substr($timestamp,8,2);
	    $minute		= substr($timestamp,10,2);
	    $second		= substr($timestamp,12,2);
	    $month		= substr($timestamp,4,2);
	    $day		= substr($timestamp,6,2);
	    $year		= substr($timestamp,0,4);
	    $timestamp	= mktime($hour, $minute, $second, $month, $day, $year);  
	}

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
			$output = str_replace('%D', $day_name_con[date('w', $timestamp)],$output);
		
		} else if ($tokens[$i] == 'l') {
			$output = str_replace('%l', $day_name_ext[date('w', $timestamp)],$output);
		
		} else if ($tokens[$i] == 'F') {
			$output = str_replace('%F', $month_name_ext[date('n', $timestamp)-1],$output);		
		
		} else if ($tokens[$i] == 'M') {
			$output = str_replace('%M', $month_name_con[date('n', $timestamp)-1],$output);

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

/********************************************************************************************/
function getMessage($codes) {
	/* this is where we want to get the msgs from the database inside a static variable */
	global $_cache_msgs;
	static $_msgs;

	if (!isset($_msgs)) {
		if ( !($lang_et = cache(120, 'msgs', $_SESSION['lang'])) ) {
			global $lang_db, $_base_path;
			/* get $_msgs from the DB */
			if ($_SESSION['lang'] == 'en') {
				$sql	= 'SELECT * FROM '.TABLE_PREFIX_LANG.'lang_base WHERE variable="_msgs"';
			} else {
				$sql	= 'SELECT * FROM '.TABLE_PREFIX_LANG.'lang2 WHERE variable="_msgs" AND lang="'.$_SESSION['lang'].'"';
			}
			$result	= @mysql_query($sql, $lang_db);
			$i = 1;
			while ($row = @mysql_fetch_assoc($result)) {
				$_cache_msgs[constant($row['key'])] = str_replace('SITE_URL/', $_base_path, $row['text']);
				if (AT_DEVEL) {
					$_cache_msgs[constant($row['key'])] .= '<small><small>('.$row['key'].')</small></small>';
				}

			}

			cache_variable('_cache_msgs');
			endcache(true, false);
		}
		$_msgs = $_cache_msgs;
	}

	if (is_array($codes)) {
		/* this is an array with terms to replace */
		$code		= array_shift($codes);
		$message	= $_msgs[$code];
		$terms		= $codes;

		/* replace the tokens with the terms */
		$message	= vsprintf($message, $terms);

	} else {
		$message = $_msgs[$codes];

		if ($message == '') {
			/* the language for this msg is missing: */
		
			$sql	= 'SELECT * FROM '.TABLE_PREFIX_LANG.'lang_base WHERE variable="_msgs"';
			$result	= @mysql_query($sql, $lang_db);
			$i = 1;
			while ($row = @mysql_fetch_assoc($result)) {
				if (constant($row['key']) == $codes) {
					$message = '['.$row['key'].']';
					break;
				}
			}
		}
		$code = $codes;
	}
	return $message;
}


function print_errors( $errors ) {
	if (empty($errors)) {
		return;
	}
	global $_base_path;

	?>	<br />
	<table border="0" class="errbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="errbox">
	<td>
		<h3><img src="<?php echo $_base_path; ?>images/error_x.gif" align="top" height="25" width="28" class="menuimage5" alt="<?php echo _AT('error'); ?>" /><?php echo _AT('error'); ?></h3><hr />
		<?php
			print_items($errors);

		?>
		</td>
	</tr>
	</table>
	<br />
<?php
}

function print_feedback( $feedback ) {
	if (empty($feedback)) {
		return;
	}

	global $_base_path;

	?>	<br />
	<table border="0" class="fbkbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="fbkbox">
	<td>
		<h3><img src="<?php echo $_base_path; ?>images/feedback_x.gif" align="top" alt="<?php echo _AT('feedback'); ?>" class="menuimage5" /><?php echo _AT('feedback'); ?></h3><hr />
		<?php

			print_items($feedback);

		?>
		</td>
	</tr>
	</table>
	<br />
<?php
}

function print_help( $help ) {
	if (empty($help)) {
		return;
	}
	global $_my_uri, $_base_path;
	echo '<a name="help"></a>';
	if (!isset($_GET['e']) && !$_SESSION['prefs']['PREF_HELP'] && !$_GET['h']) {
		if($_SESSION['prefs']['PREF_CONTENT_ICONS'] == 2){
			echo '<small>( <a href="'.$_my_uri.'e=1#help">'._AT('help').'</a> )</small><br /><br />';

		}else{
			echo '<a href="'.$_my_uri.'e=1#help"><img src="'.$_base_path.'images/help_open.gif" class="menuimage"  alt="'._AT('help').'" border="0" /></a><br />';
		}
		return;
	}
	?>	<br />
	<table border="0" class="hlpbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="hlpbox">
	<td>
		<h3><?php
			if (isset($_GET['e'])) {
				echo '<a href="'.$_my_uri.'#help">';
				echo '<img src="'.$_base_path.'images/help_close.gif" class="menuimage5" align="top" alt="'._AT('close_help').'" border="0" title="'._AT('close_help').'"/></a> ';
			} else {
				echo '<img src="'.$_base_path.'images/help.gif" class="menuimage5" align="top" alt="'._AT('help').'" border="0" /> ';
			}
		echo _AT('help').'</h3><hr />';

			print_items($help);
		if($_SESSION['course_id']){
		?>
		<div align="right"><br /><small><a href="<?php echo $_base_path; ?>help/about_help.php?h=1"><?php echo _AT('about_help'); ?></a>.</small></div>
		<?php } ?>
		</td>
	</tr>
	</table>
	<br />
<?php
}

function print_warnings( $warnings ) {
	if (empty($warnings)) {
		return;
	}

	global $_base_path;

	?>	<br />
	<table border="0" class="wrnbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">
	<tr class="wrnbox">
	<td>
		<h3><img src="<?php echo $_base_path; ?>images/warning_x.gif" align="top" class="menuimage5" alt="<?php echo _AT('warning'); ?>" /><?php echo _AT('warning'); ?></h3><hr />
		<?php

			print_items($warnings);


		?>
		</td>
	</tr>
	</table>
	<br />
<?php
}

function print_infos( $infos ) {
	if (empty($infos)) {
		return;
	}

	global $_base_path;
	
	?>
	<table border="0" cellpadding="3" cellspacing="2" width="90%" summary="" align="center"  class="hlpbox">
	<tr class="hlpbox">
	<td><h3><img src="<?php echo $_base_path;?>images/infos.gif" align="top" class="menuimage5" alt="<?php echo _AT('info'); ?>" /><?php echo _AT('info'); ?></h3><hr /><?php

	print_items($infos);
	?>
	</td>
	</tr></table>

<?php
}

function print_items( $items ) {
	if (!$items) {
		return;
	}

	if (is_object($items)) {
		/* this is a PEAR::ERROR object.	*/
		/* for backwards compatability.		*/
		echo $items->getMessage();
		echo '.<p>';
		echo '<small>';
		echo $items->getUserInfo();
		echo '</small></p>';

	} else if (is_array($items)) {
		/* this is an array of errors */
		echo '<ul>';
		foreach($items as $e => $info){
			echo '<li>'.getMessage($info).'</li>';
		}
		echo'</ul>';
	} else if (is_int($items)){
		/* this is a single error not an array of errors */
		echo '<ul>';
		echo '<li>'.getMessage($items).'</li>';
		echo '</ul>';
	
	} else {
		/* not really sure what this is.. some kind of string.	*/
		/* for backwards compatability							*/
		echo '<ul>';
		echo '<li>'.$items.'</li>';
		echo'</ul>';
	}
}

function print_popup_help($help, $align='left') {
	if (!$_SESSION['prefs'][PREF_MINI_HELP]) {
		return;
	}

	$text = getMessage($help);
	$text = str_replace('"','&quot;',$text);
	$text = str_replace("'",'&#8217;',$text);
	$text = str_replace('`','&#8217;',$text);
	$text = str_replace('<','&lt;',$text);
	$text = str_replace('>','&gt;',$text);

	global $_base_path;

	$help_link = urlencode(serialize(array($help)));
		
	if($_SESSION['prefs'][PREF_CONTENT_ICONS] == 2){
		echo '<span><a href="'.$_base_path.'popuphelp.php?h='.$help_link.'" target="help" onmouseover="return overlib(\'&lt;small&gt;'.$text.'&lt;/small&gt;\', CAPTION, \''._AT('help').'\', CSSCLASS, FGCLASS, \'row1\', BGCLASS, \'cat2\', TEXTFONTCLASS, \'row1\', CENTER);" onmouseout="return nd();"><small>('._AT('help').')</small> </a></span>';
	}else{
		echo '<a href="'.$_base_path.'popuphelp.php?h='.$help_link.'" target="help" onmouseover="return overlib(\'&lt;small&gt;'.$text.'&lt;/small&gt;\', CAPTION, \''._AT('help').'\', CSSCLASS, FGCLASS, \'row1\', BGCLASS, \'cat2\', TEXTFONTCLASS, \'row1\', CENTER);" onmouseout="return nd();"><img src="'.$_base_path.'images/help3.gif" border="0" class="menuimage10" align="'.$align.'" alt="'._AT('open_help').'" /></a>';
	}
}

function print_editor( $editor_links ) {
	$num_args = func_num_args();
	$args	  = func_get_args();

	if (!$num_args || !($_SESSION['is_admin'] && $_SESSION['prefs'][PREF_EDIT])) {
		return;
	}
	global $_base_path;

	echo ' <span class="editorsmallbox"><small>';
	if($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2){
		echo '<img src="'.$_base_path.'images/pen2.gif" border="0" class="menuimage12" alt="'._AT('editor_on').'" title="'._AT('editor_on').'" height="14" width="16" /> ';
	}
	for ($i=0; $i<$num_args; $i+=2) {
		echo '<a href="'.$args[$i+1].'">'.$args[$i].'</a>';
		if ($i+2 < $num_args){
			echo ' | ';
		}
	}
	echo '</small></span> '."\n";

	return;
}
function print_editorlg( $editor_links ) {
	$num_args = func_num_args();
	$args	  = func_get_args();

	if (!$num_args || !($_SESSION['is_admin'] && $_SESSION['prefs'][PREF_EDIT])) {
		return;
	}
	global $_base_path;

	echo '<p><span class="editorlargebox">';
	if($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2){
		echo '<img src="'.$_base_path.'images/pen3.gif" border="0" class="menuimage11" alt="'._AT('editor_on').'" title="'._AT('editor_on').'" height="28" width="32" /> ';
	}
	echo '<small>';
	for ($i=0; $i<$num_args; $i+=2) {
		echo '<a href="'.$args[$i+1].'">'.$args[$i].'</a>';
		if ($i+2 < $num_args){
			echo ' | ';
		}
	}
	echo '</small>';
	echo '</span></p>';

	return;
}

/****************************************************************************/
	/* _AC is from ACollab */
	function & _AC( ) {
		$args 	  = func_get_args();

		return _AT($args);
	}

	/*
		$args[0] = the key to the format string $_template[key]
		$args[1..x] = optional arguments to the formatting string 
	*/
	function & _AT( ) {
		global $_cache_template, $lang_et, $_rel_url;
		static $_template;

		if (!isset($_template)) {
			global $_base_href;
			$url_parts = parse_url($_base_href);
			$name = substr($_SERVER['PHP_SELF'], strlen($url_parts['path'])-1);

			if ( !($lang_et = cache(120, 'lang', $_SESSION['lang'].'_'.$name)) ) {
				global $lang_db;

				/* get $_template from the DB */
				if ($_SESSION['lang'] == 'en') {
					$sql	= 'SELECT L.* FROM '.TABLE_PREFIX_LANG.'lang_base L, '.TABLE_PREFIX_LANG.'lang_base_pages P WHERE L.variable="_template" AND L.key=P.key AND P.page="'.$_rel_url.'"';
				} else {
					$sql	= 'SELECT L.* FROM '.TABLE_PREFIX_LANG.'lang2 L, '.TABLE_PREFIX_LANG.'lang_base_pages P WHERE L.lang="'.$_SESSION['lang'].'" AND L.variable="_template" AND L.key=P.key AND P.page="'.$_rel_url.'"';
				}
				$result	= mysql_query($sql, $lang_db);
				while ($row = @mysql_fetch_assoc($result)) {
					$_cache_template[$row['key']] = stripslashes($row['text']);
				}
		
				cache_variable('_cache_template');
				endcache(true, false);
			}
			$_template = $_cache_template;
		}

		$num_args = func_num_args();
		$args 	  = func_get_args();

		/* fix for the _AC() wrapper: */
		if (is_array($args[0])) {
			$args = $args[0];
			$num_args = count($args);
		}

		$format		= array_shift($args);

		$c_error	= error_reporting(0);
		$outString	= vsprintf($_template[$format], $args);
		if ($outString === false) {
			return ('[Error parsing language. Variable: <code>'.$format.'</code>. Value: <code>'.$_template[$format].'</code>. Language: <code>'.$_SESSION['lang'].'</code> ]');
		}
		error_reporting($c_error);

		if (empty($outString) && ($_SESSION['lang'] == 'en')) {
			global $lang_db;
			$sql	= 'SELECT L.* FROM '.TABLE_PREFIX_LANG.'lang_base L WHERE L.variable="_template" AND `key`="'.$format.'"';
			$result	= @mysql_query($sql, $lang_db);
			$row = @mysql_fetch_array($result);

			$_template[$row['key']] = stripslashes($row['text']);
			$outString = $_template[$row['key']];
			if (empty($outString)) {
				return ('[ Variable: '.$format.']');
			}
			$outString = $_template[$row['key']];
			$outString = vsprintf($outString, $args);

			/* purge the language cache */
			/* update the locations */
			$sql = 'INSERT INTO '.TABLE_PREFIX_LANG.'lang_base_pages VALUES ("template", "'.$format.'", "'.$_rel_url.'")';
			@mysql_query($sql, $lang_db);

		} else if (empty($outString)) {
			global $lang_db;

			$sql	= 'SELECT L.* FROM '.TABLE_PREFIX_LANG.'lang2 L WHERE L.variable="_template" AND `key`="'.$format.'" AND lang="'.$_SESSION['lang'].'"';
			$result	= @mysql_query($sql, $lang_db);
			$row = @mysql_fetch_array($result);

			$_template[$row['key']] = stripslashes($row['text']);
			$outString = $_template[$row['key']];
			if (empty($outString)) {
				return ('[ Variable: '.$format.']');
			}
			$outString = vsprintf($outString, $args);

			/* purge the language cache */
			/* update the locations */
			$sql = 'INSERT INTO '.TABLE_PREFIX_LANG.'lang_base_pages VALUES ("template", "'.$format.'", "'.$_rel_url.'")';
			@mysql_query($sql, $lang_db);
		}

		return $outString;
	}

/**********************************************************************************************************/
	/*
		$output = AT_print($input, $name [, $runtime_html = false]);

		$input: the text being transformed
		$name: the unique name of this field (convension: table_name.field_name)
		$runtime_html: forcefully disables html formatting for $input (only used by fields that have the 'formatting' option

		returns transformed $input.
		original $input is also changed (passed by reference).

		can be called as:
		1) $output = AT_print($input, $name);
		   echo $output;

		2) echo AT_print($input, $name); // prefered method

		3) AT_print($input, $name);
		   echo $input;
	*/
	function &AT_print($input, $name, $runtime_html = true) {
		global $_field_formatting;

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
function smile_replace($text) {
	global $_base_path;
	static $smiles;

	if (!isset($smiles)) {
		$smiles[0] = '<img src="'.$_base_path.'images/forum/smile.gif" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_smile').'" />';
		$smiles[1] = '<img src="'.$_base_path.'images/forum/wink.gif" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_wink').'" />';
		$smiles[2] = '<img src="'.$_base_path.'images/forum/frown.gif" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_frown').'" />';
		$smiles[3]= '<img src="'.$_base_path.'images/forum/ohwell.gif" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_oh_well').'" />';
		$smiles[4]= '<img src="'.$_base_path.'images/forum/tongue.gif" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_tongue').'" />';
		$smiles[5]= '<img src="'.$_base_path.'images/forum/51.gif" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_evil').'" />';
		$smiles[6]= '<img src="'.$_base_path.'images/forum/52.gif" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_angry').'" />';
		$smiles[7]= '<img src="'.$_base_path.'images/forum/54.gif" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_lol').'" />';
		$smiles[8]= '<img src="'.$_base_path.'images/forum/27.gif" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_crazy').'" />';
		$smiles[9]= '<img src="'.$_base_path.'images/forum/19.gif" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_tired').'" />';
		$smiles[10]= '<img src="'.$_base_path.'images/forum/3.gif" border="0" height="17" width="19" align="bottom" alt="'._AT('smile_confused').'" />';
		$smiles[11]= '<img src="'.$_base_path.'images/forum/56.gif" border="0" height="15" width="15" align="bottom" alt="'._AT('smile_muah').'" />';
	}

	$text = str_replace(':)',$smiles[0],$text);
	$text = str_replace('=)',$smiles[0],$text);
	$text = str_replace(';)',$smiles[1],$text);
	$text = str_replace(':(',$smiles[2],$text);
	$text = str_replace(':\\',$smiles[3],$text);
	$text = str_replace(':P',$smiles[4],$text);
	$text = str_replace('::evil::',$smiles[5],$text);
	$text = str_replace('::angry::',$smiles[6],$text);
	$text = str_replace('::lol::',$smiles[7],$text);
	$text = str_replace('::crazy::',$smiles[8],$text);
	$text = str_replace('::tired::',$smiles[9],$text);
	$text = str_replace('::confused::',$smiles[10],$text);
	$text = str_replace('::muah::',$smiles[11],$text);

	return $text;
}

function myCodes($text) {
	global $_base_path;
	global $HTTP_USER_AGENT;
	global $learning_concept_tags;

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

	$text = str_replace('[cid]',$_base_path.'?cid='.$_SESSION['s_cid'],$text);

	//Replace learning concept codes with icons
	if (is_array($learning_concept_tags)) {
		foreach ($learning_concept_tags as $tag => $concept) {
			if ($tag == 'link') {
				$text = str_replace('['.$tag.']','<a href="'.$_base_path.'resources/links/"><img src="'.$_base_path.'images/concepts/'.$concept['icon_name'].'" alt="'.$concept['title'].'" border="0" /></a>', $text);
			} else if ($tag == 'discussion') {
				$text = str_replace('['.$tag.']','<a href="'.$_base_path.'forum/"><img src="'.$_base_path.'images/concepts/'.$concept['icon_name'].'" alt="'.$concept['title'].'" border="0" /></a>', $text);
			} else {
				$text = str_replace('['.$tag.']','<img src="'.$_base_path.'images/concepts/'.$concept['icon_name'].'" alt="'.$concept['title'].'" />', $text);
			}
		}
	}
		

	return $text;
}

function make_clickable($text) {
	$ret = eregi_replace("([[:space:]])http://([^[:space:]]*)([[:alnum:]#?/&=])", "\\1<a href=\"http://\\2\\3\">\\2\\3</a>", $text);

	$ret = eregi_replace(	'([_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]+)*'.
							'\@'.'[_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]+)*'.'(\.[a-zA-Z]{1,5})+)',
							"<a href=\"mailto:\\1\">\\1</a>",
							$ret);

	return $ret;
}

function image_replace($text) {
	/* image urls do not require http:// */
	$text = eregi_replace("(\[image)(\|)(([[:alnum:][:space:]])*)(\])(([^[:space:]]*)([[:alnum:]#?/&=]))(\[/image\])",
				  "<img src=\"\\6\" alt=\"\\3\" />",
				  $text);

	$text = str_replace('[image]','<img src="', $text);
	$text = str_replace('[/image]','" alt="" />', $text);
	
	return $text;
}

function format_final_output($text, $nl2br = true) {
	global $learning_concept_tags, $_base_path;

	/* search and replace the learning concepts: */
	if (is_array($learning_concept_tags)) {
		foreach ($learning_concept_tags as $tag) {
			if ($tag == 'link') {
				$text = str_replace('['.$tag.']','<a href="'.$_base_path.'resources/links/"><img src="'.$_base_path.'images/concepts/'.$tag.'.gif" alt="'._AT('lc_'.$tag.'_title').'" title="'._AT('lc_'.$tag.'_title').'" border="0" /></a>', $text);
			} else if ($tag == 'discussion') {
				$text = str_replace('['.$tag.']','<a href="'.$_base_path.'forum/"><img src="'.$_base_path.'images/concepts/'.$tag.'.gif" alt="'._AT('lc_'.$tag.'_title').'" title="'._AT('lc_'.$tag.'_title').'" border="0" /></a>', $text);
			} else {
				$text = str_replace('['.$tag.']','<img src="'.$_base_path.'images/concepts/'.$tag.'.gif" alt="'._AT('lc_'.$tag.'_title').'" title="'._AT('lc_'.$tag.'_title').'" />', $text);
			}
		}
	}
	$text = str_replace('CONTENT_DIR/', '', $text);

	if ($nl2br) {
		return nl2br(image_replace(make_clickable(myCodes(smile_replace(' '.$text)))));
	}
	return image_replace(make_clickable(myCodes(smile_replace(' '.$text))));
}

/****************************************************************************************/
/* @See: ./tools/search.php & ./index.php */
function &highlight(&$input, &$var) {//$input is the string, $var is the text to be highlighted
	if ($var != "") {
		$xtemp = "";
		$i=0;
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
function format_content($input, $html = 0, $glossary) {
	global $_base_path;

	if (!$html) {
		$input = str_replace('<', '&lt;', $input);
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

			$def = htmlspecialchars($v);		
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2){
				$input = preg_replace
							("/(\[\?\])$term(\[\/\?\])/i",
							'\\2<sup><a href="'.$_base_path.'glossary/index.php?g=24#'.urlencode($original_term).'" onmouseover="return overlib(\''.$def.'\', CAPTION, \''._AT('definition').'\', AUTOSTATUS);" onmouseout="return nd();"><img src="'.$_base_path.'images/glossary_small.gif" height="15" width="16" border="0" class="menuimage9" alt="glossary item"/></a></sup>',
							$input);
			} else {
				$input = preg_replace
							("/(\[\?\])$term(\[\/\?\])/i",
							'\\2<sup>[<a href="'.$_base_path.'glossary/index.php?g=24#'.urlencode($original_term).'" onmouseover="return overlib(\''.$def.'\', CAPTION, \''._AT('definition').'\', AUTOSTATUS);" onmouseout="return nd();">?</a>]</sup>',
							$input);
			}
		}
	} else if (!$user_glossary) {
		$input = str_replace(array('[?]','[/?]'), '', $input);
	}

	if (BACKWARDS_COMPATIBILITY) {
		$input = str_replace('CONTENT_DIR', 'content/'.$_SESSION['course_id'], $input);
	}

	if ($html) {
		return format_final_output($input, false);
	}

	$output = format_final_output($input);

	if (!$html) {
		$output = '<p>'.$output.'</p>';
	}

	return $output;
}

function find_terms($find_text) {
	preg_match_all("/(\[\?\])(.[^\?]*)(\[\/\?\])/i", $find_text, $found_terms, PREG_PATTERN_ORDER);
	return $found_terms;
}

?>