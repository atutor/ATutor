<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
/****************************************************************/
/* These functions were written by Joel Kronenberg for          */
/* purerave.com, and used as-is for ATutor.                     */
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

function smile_replace($text) {
	global $_base_path;
	$smiles = array();

	$smiles[0] = '<img src="'.$_base_path.'images/forum/smile.gif" border="0" height="15" width="15" align="bottom" alt="smile" />';
	$smiles[1] = '<img src="'.$_base_path.'images/forum/wink.gif" border="0" height="15" width="15" align="bottom" alt="wink" />';
	$smiles[2] = '<img src="'.$_base_path.'images/forum/frown.gif" border="0" height="15" width="15" align="bottom" alt="frown" />';
	// removed 1.3, interferes with Flash object code
	//$smiles[4]= '<img src="'.$_base_path.'images/forum/happy.gif" border="0" height="15" width="15" align="bottom" alt="happy" />';
	$smiles[5]= '<img src="'.$_base_path.'images/forum/ohwell.gif" border="0" height="15" width="15" align="bottom" alt="oh well" />';
	$smiles[6]= '<img src="'.$_base_path.'images/forum/tongue.gif" border="0" height="15" width="15" align="bottom" alt="tongue" />';
	$smiles[7]= '<img src="'.$_base_path.'images/forum/51.gif" border="0" height="15" width="15" align="bottom" alt="evil" />';
	$smiles[8]= '<img src="'.$_base_path.'images/forum/52.gif" border="0" height="15" width="15" align="bottom" alt="angry" />';
	$smiles[9]= '<img src="'.$_base_path.'images/forum/54.gif" border="0" height="15" width="15" align="bottom" alt="lol" />';
	$smiles[10]= '<img src="'.$_base_path.'images/forum/55.gif" border="0" height="15" width="15" align="bottom" alt="wow" />';
	$smiles[11]= '<img src="'.$_base_path.'images/forum/17.gif" border="0" height="21" width="37" align="bottom" alt="finger" />';
	$smiles[12]= '<img src="'.$_base_path.'images/forum/37.gif" border="0" height="23" width="42" align="bottom" alt="angel" />';
	$smiles[13]= '<img src="'.$_base_path.'images/forum/27.gif" border="0" height="15" width="15" align="bottom" alt="crazy" />';
	$smiles[14]= '<img src="'.$_base_path.'images/forum/26.gif" border="0" height="15" width="60" align="bottom" alt="puke" />';
	$smiles[15]= '<img src="'.$_base_path.'images/forum/30.gif" border="0" height="15" width="15" align="bottom" alt="love" />';
	$smiles[16]= '<img src="'.$_base_path.'images/forum/19.gif" border="0" height="15" width="15" align="bottom" alt="tired" />';
	$smiles[17]= '<img src="'.$_base_path.'images/forum/3.gif" border="0" height="17" width="19" align="bottom" alt="confused" />';
	$smiles[18]= '<img src="'.$_base_path.'images/forum/56.gif" border="0" height="15" width="15" align="bottom" alt="muah" />';
	$smiles[19]= '<img src="'.$_base_path.'images/forum/57.gif" border="0" height="15" width="15" align="bottom" alt="roll eyes" />';
	$smiles[20]= '<img src="'.$_base_path.'images/forum/58.gif" border="0" height="15" width="15" align="bottom" alt="licks" />';

	// this MUST be before anything else b/c the :/ is used in http://
	$text = str_replace(':\\',$smiles[5],$text);

	$text = str_replace(':)',$smiles[0],$text);
	$text = str_replace('=)',$smiles[0],$text);

	$text = str_replace(';)',$smiles[1],$text);
	$text = str_replace(':(',$smiles[2],$text);
	//$text = str_replace(':D',$smiles[4],$text);
	$text = str_replace(':P',$smiles[6],$text);

	$text = str_replace('::evil::',$smiles[7],$text);
	$text = str_replace('::angry::',$smiles[8],$text);
	$text = str_replace('::lol::',$smiles[9],$text);
	$text = str_replace('::wow::',$smiles[10],$text);
	$text = str_replace('::finger::',$smiles[11],$text);
	$text = str_replace('::angel::',$smiles[12],$text);
	$text = str_replace('::crazy::',$smiles[13],$text);
	$text = str_replace('::puke::',$smiles[14],$text);
	$text = str_replace('::love::',$smiles[15],$text);

	$text = str_replace('::tired::',$smiles[16],$text);
	$text = str_replace('::zzz::',$smiles[16],$text);

	$text = str_replace('::confused::',$smiles[17],$text);

	$text = str_replace('::muah::',$smiles[18],$text);
	$text = str_replace('::kiss::',$smiles[18],$text);

	$text = str_replace('::rolleyes::',$smiles[19],$text);

	$text = str_replace('::licks::',$smiles[20], $text);
	$text = str_replace('::lix::',$smiles[20], $text);

	return $text;
}

function myCodes($text) {
	global $_base_path;
	global $HTTP_USER_AGENT;
	global $learning_concept_tags;
	//$text = str_replace('[quote]','<blockquote><hr>',$text);
	//$text = str_replace('[/quote]','<hr></blockquote><p>',$text);

	if (substr($HTTP_USER_AGENT,0,11)== 'Mozilla/4.7') {
		$text = str_replace('[quote]','</p><p class="block">',$text);
		$text = str_replace('[/quote]','</p><p>',$text);

		$text = str_replace('[reply]','</p><p class="block">',$text);
		$text = str_replace('[/reply]','</p><p>',$text);
	} else {
		$text = str_replace('[quote]','<blockquote>',$text);
		$text = str_replace('[/quote]','</blockquote><p>',$text);

		$text = str_replace('[reply]','</p><blockquote class="block" title="quoted post"><p>',$text);
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
		

	return ($text);
}

function make_clickable($text) {
	$ret = eregi_replace("([[:space:]])http://([^[:space:]]*)([[:alnum:]#?/&=])", "\\1<a href=\"http://\\2\\3\">\\2\\3</a>", $text);

	//$ret = eregi_replace("(([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)([[:alnum:]-]))", "<a href=\"mailto:\\1\">\\1</a>", $ret);

	$ret = eregi_replace(	'([_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]+)*'.
							'\@'.'[_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9\-]+)*'.'(\.[a-zA-Z]{1,5})+)',
							"<a href=\"mailto:\\1\">\\1</a>",
							$ret);

	return($ret);
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

?>