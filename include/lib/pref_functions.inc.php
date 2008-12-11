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

global $user_style_template;

$user_style_template = '
<STYLE TYPE="text/css"> 
<!-- 
body {
  {FG_COLOR} {BG_COLOR} {FONT_SIZE} {FONT} }
h1, h2, h3, h4, h5, h6 {
  {FG_COLOR} {BG_COLOR}
}
p.toc{
  {FG_COLOR} {BG_COLOR}
}
a:link, a:visited, a:active {
  {FG_COLOR} {BG_COLOR}
}
a.dropdown-title {
  {FG_COLOR} {BG_COLOR}
}
.button, .button2 {
  {FG_COLOR} {BG_COLOR}
}
.editorsmallbox, .editorlargebox {
  {FG_COLOR} {BG_COLOR}
}
.buttontab {
  {FG_COLOR} {BG_COLOR}
}
.tab {
  {FG_COLOR} {BG_COLOR}
}
.econtainer {
  {FG_COLOR} {BG_COLOR}
}
.etabself {
  {FG_COLOR} {BG_COLOR}
}
.unsaved {
  {FG_COLOR} {BG_COLOR}
}
.saved {
  {FG_COLOR} {BG_COLOR}
}
td.dropdown-heading {
  {FG_COLOR} {BG_COLOR}
}
td.dropdown {
  {FG_COLOR} {BG_COLOR}
}
td.dropdown a, td.dropdown a:visited  {
  {FG_COLOR} {BG_COLOR}
}
td.dropdown strong {
  {FG_COLOR} {BG_COLOR}
}
h5.search-results {
  {FG_COLOR} {BG_COLOR}
}
small.search-info {
  {FG_COLOR} {BG_COLOR}
}
p.search-description {
  {FG_COLOR} {BG_COLOR}
}
.test-box {
  {FG_COLOR} {BG_COLOR}
}
table.tabbed-table th.tab {
  {FG_COLOR} {BG_COLOR}
}
table.tabbed-table th.selected {
  {FG_COLOR} {BG_COLOR}
}
div#sub-navigation {
  {FG_COLOR} {BG_COLOR}
}
div#sub-navigation strong {
  {FG_COLOR} {BG_COLOR}
}
div#help {
  {FG_COLOR} {BG_COLOR}
}
h3#help-title {
  {FG_COLOR} {BG_COLOR}
}
#jumpmenu:focus{
  {FG_COLOR} {BG_COLOR}
}
a#editor-link {
  {FG_COLOR} {BG_COLOR}
}
table.data th {
  {FG_COLOR} {BG_COLOR}
}
table.data th a {
  {FG_COLOR} {BG_COLOR}
}
table.data tbody th {
  {FG_COLOR} {BG_COLOR}
}
table.data tbody tr.selected {
  {FG_COLOR} {BG_COLOR}
}
table.data tfoot {
  {FG_COLOR} {BG_COLOR}
}
table.data tfoot tr:first-child td {
  {FG_COLOR} {BG_COLOR}
}
table.data tfoot input {
  {FG_COLOR} {BG_COLOR}
}
div#error {
  {FG_COLOR} {BG_COLOR}
}
div#error h4 {
  {FG_COLOR} {BG_COLOR}
}
div#feedback {
  {FG_COLOR} {BG_COLOR}
}
div#help {
  {FG_COLOR} {BG_COLOR}
}
div#info {
  {FG_COLOR} {BG_COLOR}
}
div#warning {
  {FG_COLOR} {BG_COLOR}
}
div.news span.date {
  {FG_COLOR} {BG_COLOR}
}
div.dropdown {
  {FG_COLOR} {BG_COLOR}
}
div.dropdown-heading {
  {FG_COLOR} {BG_COLOR}
}
div.required {
  {FG_COLOR} {BG_COLOR}
}
#header{
  {FG_COLOR} {BG_COLOR}
}
#header a{
  {FG_COLOR} {BG_COLOR}
}
div.tabs a {
  {FG_COLOR} {BG_COLOR}
}
div.tabs a.selected {
  {FG_COLOR} {BG_COLOR}
}
div.box {
  {FG_COLOR} {BG_COLOR}
}
h5.box { 
  {FG_COLOR} {BG_COLOR}
}
div.box a:visited {
  {FG_COLOR} {BG_COLOR}
}
div.box .even {
  {FG_COLOR} {BG_COLOR}
}
div.box .odd {
  {FG_COLOR} {BG_COLOR}
}
div.course {
  {FG_COLOR} {BG_COLOR}
}
fieldset#shortcuts {
  {FG_COLOR} {BG_COLOR}
}
a#guide {
  {FG_COLOR} {BG_COLOR}
}
div#content-test ol ul li{
  {FG_COLOR} {BG_COLOR}
}
div#content-info {
  {FG_COLOR} {BG_COLOR}
}
div.column h3 {
  {FG_COLOR} {BG_COLOR}
}
#navlist li a {
  {FG_COLOR} {BG_COLOR}
}
#forum-thread li {
  {FG_COLOR} {BG_COLOR}
}
#forum-thread li.even {
  {FG_COLOR} {BG_COLOR}
}
#forum-thread li.odd {
  {FG_COLOR} {BG_COLOR}
}
div.forum-post-ctrl span {
  {FG_COLOR} {BG_COLOR}
}
div.forum-post-content p.date {
  {FG_COLOR} {BG_COLOR}
}
div.forum-paginator{
  {FG_COLOR} {BG_COLOR}
}
div#topnavlistcontainer {
  {FG_COLOR} {BG_COLOR}
}
ul#topnavlist li a {
  {FG_COLOR} {BG_COLOR}
}
ul#topnavlist li a.selected {
  {FG_COLOR} {BG_COLOR}
}
ol#tools>li:hover {
  {FG_COLOR} {BG_COLOR}
}
li.top-tool { 
  {FG_COLOR} {BG_COLOR}
}
dl.browse-course {
  {FG_COLOR} {BG_COLOR}
}
legend.group_form{
  {FG_COLOR} {BG_COLOR}
}
div.column_equivalent{
  {FG_COLOR} {BG_COLOR}
}
div.resource_box{
  {FG_COLOR} {BG_COLOR}
}
h2.alternatives_to{
  {FG_COLOR} {BG_COLOR}
}
div.alternative_box{
  {FG_COLOR} {BG_COLOR}
}
div.alternative_box legend {
  {FG_COLOR} {BG_COLOR}
}
div.resource_box legend {
  {FG_COLOR} {BG_COLOR}
}
label.primary a{
  {FG_COLOR} {BG_COLOR}
}
div.input-form {
  {FG_COLOR} {BG_COLOR}
}
div.input-form div.row {
  {FG_COLOR} {BG_COLOR}
}
div.input-form input[type=text], div.input-form input[type=password] {
  {FG_COLOR} {BG_COLOR}
}
input[type=checkbox]{
  {FG_COLOR} {BG_COLOR}
}
div.input-form div.buttons input {
  {FG_COLOR} {BG_COLOR}
}
div.input-form div.row_alternatives {
  {FG_COLOR} {BG_COLOR}
}

a:active, a:hover, a:focus, .highlight, a.highlight {
  {HL_COLOR} {FG_COLOR} }
ul#topnavlist li a:hover, ul#topnavlist li a:focus, ul#topnavlist li a.active {
  {HL_COLOR} {FG_COLOR}
}
.tab a:hover {
	background-color:  #FF0000;	; {FG_COLOR}
}
.button:focus, .button2:focus {
	background-color:  #FF0000;	; {FG_COLOR}
}
div.input-form textarea:focus, div.input-form  input[type=password]:focus, div.input-form  input[type=text]:focus{
	{HL_COLOR}; {FG_COLOR}
}
.formfield:focus{
	{HL_COLOR}; {FG_COLOR}
}
table.data tfoot input:focus {
	{HL_COLOR}; {FG_COLOR}
}
td.selected{
	{HL_COLOR}; {FG_COLOR}
}
.buttontab selected {
	{HL_COLOR}; {FG_COLOR}
}
td.dropdown a:hover {
	{HL_COLOR}; {FG_COLOR}
}
table.tabbed-table th.tab:hover {
	{HL_COLOR}; {FG_COLOR}
}
table.tabbed-table th.tab a:focus {
	{HL_COLOR}; {FG_COLOR}
}
table.tabbed-table a, table.tabbed-table a:visited, table.tabbed-table a:hover {
	{HL_COLOR}; {FG_COLOR}
}
div#top-links a:focus{
	{HL_COLOR}; {FG_COLOR}
}
a#editor-link:hover {
	{HL_COLOR}; {FG_COLOR}
}
table.data tbody tr:hover {
	{HL_COLOR}; {FG_COLOR}
}
table.data tfoot input:focus {
	{HL_COLOR}; {FG_COLOR}
}
div.home-link:hover {
	{HL_COLOR}; {FG_COLOR}
}
#header a:hover {
	{HL_COLOR}; {FG_COLOR}
}
div.tabs a:hover, div.tabs a.active {
	{HL_COLOR}; {FG_COLOR}
}
div.course:hover {
	{HL_COLOR}; {FG_COLOR}
}
#navlist li a:hover, #navlist li a:active {
	{HL_COLOR}; {FG_COLOR}
}

	--> 
</STYLE>
';

function get_user_style() 
{
	global $user_style_template;

	if (($_SESSION["prefs"]["PREF_FONT_FACE"] == "")
	  && ($_SESSION["prefs"]["PREF_FONT_TIMES"] == 0 || $_SESSION["prefs"]["PREF_FONT_TIMES"] == 1)
	  && ($_SESSION["prefs"]["PREF_FG_COLOUR"] == "")
	  && ($_SESSION["prefs"]["PREF_BG_COLOUR"] == "")
	  && ($_SESSION["prefs"]["PREF_HL_COLOUR"] == ""))
	{
		return "";
	}
	else
	{
		if ($_SESSION["prefs"]["PREF_FONT_FACE"] <> "")
			$font = "font-family: ". $_SESSION["prefs"]["PREF_FONT_FACE"] .";";

		if ($_SESSION["prefs"]["PREF_FONT_TIMES"] <> 0 && $_SESSION["prefs"]["PREF_FONT_TIMES"] <> 1)
			$font_size = "font-size: ". $_SESSION["prefs"]["PREF_FONT_TIMES"] ."em;";

		if ($_SESSION["prefs"]["PREF_FG_COLOUR"] <> "")
			$fg_color = "color: #". $_SESSION["prefs"]["PREF_FG_COLOUR"] .";";

		if ($_SESSION["prefs"]["PREF_BG_COLOUR"] <> "")
			$bg_color = "background-color: #". $_SESSION["prefs"]["PREF_BG_COLOUR"] .";";

		if ($_SESSION["prefs"]["PREF_HL_COLOUR"] <> "")
			$hl_color = "background-color: #". $_SESSION["prefs"]["PREF_HL_COLOUR"] .";";
	
		return str_replace(array("{FONT}", "{FONT_SIZE}", "{FG_COLOR}", "{BG_COLOR}", "{HL_COLOR}"),
											array($font, $font_size, $fg_color, $bg_color, $hl_color),
											$user_style_template);
	}
}

?>