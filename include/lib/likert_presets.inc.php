<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }


$_likert_preset = array();

$_likert_preset[] = array(_AT('lk_always'),_AT('lk_very_frequently'),_AT('lk_occasionally'),_AT('lk_rarely'),_AT('lk_very_rarely'),_AT('lk_never'));

$_likert_preset[] = array(_AT('lk_excellent'),_AT('lk_very_good'),_AT('lk_good'),_AT('lk_fair'),_AT('lk_poor'),_AT('lk_very_poor'));

$_likert_preset[] = array(_AT('lk_strongly_agree'),_AT('lk_agree'),_AT('lk_undecided'),_AT('lk_disagree'),_AT('lk_strongly_disagree'));

$_likert_preset[] = array(_AT('lk_very_important'),_AT('lk_important'),_AT('lk_mod_important'),_AT('lk_little_importance'),_AT('lk_unimportant'));


?>