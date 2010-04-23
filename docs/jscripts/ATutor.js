/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2010 by Laurel Williams                           */
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: $

var ATutor = ATutor || {};
ATutor.users = ATutor.users || {};
ATutor.users.preferences = ATutor.users.preferences || {};

(function() {
	
	ATutor.poptastic = function (url) {
		var newwindow=window.open(url,'popup','height=600,width=600,scrollbars=yes,resizable=yes');
		if (window.focus) {
			newwindow.focus();
		}
	};

	
	//styles block for user preferences
	//used by ATutor.users.preferences.setStyles
    ATutor.users.preferences.user_styles = 
    	'<style id="pref_style" type="text/css">' + 
    	'<!--' + 
        'body {' +
  	  	'FG_COLOR BG_COLOR FONT_SIZE FONT_FAMILY' +
  	  	'}' +
  	  	'h1, h2, h3, h4, h5, h6 {' +
  	  	'FG_COLOR BG_COLOR' +
  		'}' +
  		'.tab {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.editor_tab {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.editor_buttontab {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.editor_tab_selected {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'#contentwrapper {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.test_instruction {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.contentbox {' +
  	  	'FG_COLOR BG_COLOR FONT_SIZE FONT_FAMILY'+
  		'}' +
  		'#footer {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'p.toc {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'a:link, a:visited, a:active { ' +
  		'FG_COLOR BG_COLOR ' +
  		'}' +
  		'a.dropdown-title {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.button, .button2 {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.editorsmallbox, .editorlargebox {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.buttontab {' +
  		'FG_COLOR BG_COLOR FONT_SIZE FONT_FAMILY' +
  		'}' +
  		'.prefs_buttontab {' +
  		'FG_COLOR BG_COLOR FONT_SIZE FONT_FAMILY' +
  		'}' +
  		'.prefs_tab {' +
  		'FG_COLOR BG_COLOR FONT_FAMILY' +
  		'}' +
  		'.prefs_tab_selected {' +
  		'FG_COLOR BG_COLOR FONT_SIZE FONT_FAMILY' +
  		'}' +
  		'.even {' +
  		'FG_COLOR BG_COLOR FONT_SIZE FONT_FAMILY' +
  		'}' +
  		'.odd {' +
  		'FG_COLOR BG_COLOR FONT_SIZE FONT_FAMILY' +
  		'}' +
  		'.tab {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.active {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.econtainer {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.etabself {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.unsaved {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.saved {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'td.dropdown-heading {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'td.dropdown {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'td.dropdown a, td.dropdown a:visited  {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'td.dropdown strong {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'h5.search-results {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'small.search-info {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'p.search-description {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.test-box {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'table.tabbed-table th.tab {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'table.tabbed-table th.selected {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div#sub-navigation {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div#sub-navigation strong {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'#subnavlist li{' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.logoutbar{' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div#help {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'h3#help-title {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'#jumpmenu:focus{' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'a#editor-link {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'table.data th {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'table.data th a {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'table.data tbody th {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'table.data tbody tr.selected {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'table.data tfoot {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'table.data tfoot tr:first-child td {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'table.data tfoot input {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div#error {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div#error h4 {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div#feedback {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div#help {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div#info {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div#warning {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.news span.date {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.dropdown {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.dropdown-heading {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.required {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'#header{' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'#header a{' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.tabs a {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.tabs a.selected {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.box {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'h5.box {' + 
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.box a:visited {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.box .even {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.box .odd {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.course {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'fieldset#shortcuts {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'a#guide {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div#content-test ol ul li{' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div#content-info {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.column h3 {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'#navlist li a {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'#forum-thread li {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'#forum-thread li.even {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'#forum-thread li.odd {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.forum-post-ctrl span {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.forum-post-content p.date {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div.forum-paginator{' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'div#topnavlistcontainer {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'ul#topnavlist li a {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'#breadcrumbs {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.crumbcontainer {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'.wizscreen {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'ul#topnavlist li a.selected {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'ol#tools>li:hover {' +
  		'FG_COLOR BG_COLOR' +
  		'}' +
  		'li.top-tool {' + 
  		'FG_COLOR BG_COLOR' +
  		'}' +
	  	'dl.browse-course {' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'legend.group_form{' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'div.column_equivalent{' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'div.resource_box{' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'h2.alternatives_to{' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'div.alternative_box{' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'div.alternative_box legend {' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'div.resource_box legend {' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'label.primary a{' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'div.input-form {' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'div.input-form div.row {' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'input[type=checkbox]{' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'div.input-form div.buttons input {' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'div.input-form div.row_alternatives {' +
	  	'FG_COLOR BG_COLOR' +
	  	'}' +
	  	'a:active, a:hover, a:focus, .highlight, a.highlight {' +
	  	'HL_COLOR FG_COLOR }' +
	  	'ul#topnavlist li a:hover, ul#topnavlist li a:focus, ul#topnavlist li a.active {' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'.tab a:hover {' +
  		'background-color:  #FF0000;	; FG_COLOR' +
  		'}' +
  		'.button:focus, .button2:focus {' +
  		'background-color:  #FF0000;	; FG_COLOR' +
  		'}' +
  		'table.data tfoot {' +
  		'HL_COLOR FG_COLOR' +
  		'}' +
  		'td.selected{' +
  		'HL_COLOR FG_COLOR FONT_SIZE FONT_FAMILY' +
  		'}' +
  		'.buttontab selected {' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'td.dropdown a:hover {' +
  		'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'table.tabbed-table th.tab:hover {' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'table.tabbed-table th.tab a:focus {' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'table.tabbed-table a, table.tabbed-table a:visited, table.tabbed-table a:hover {' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'div#top-links a:focus{' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'a#editor-link:hover {' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'table.data tbody tr:hover {' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'table.data tfoot input:focus {' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'div.home-link:hover {' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'#header a:hover {' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'div.tabs a:hover, div.tabs a.active {' +
  		'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'div.course:hover {' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
	  	'#navlist li a:hover, #navlist li a:active {' +
	  	'HL_COLOR FG_COLOR' +
	  	'}' +
    	'-->' +
    	'</style>';

    /**
     * Substitutes styles into styles block above and then places those styles on the page
     */
    ATutor.users.preferences.setStyles = function (bg_color, fg_color, hl_color, font, font_size) {
		var font_style = font ? 'font-family:' + font + ' !important;\n' : '';
		var font_size_style = font_size ? 'font-size:' + font_size + 'em !important;\n' : '';
		var bg_color_style = bg_color ? 'background-color: #' + bg_color + ' !important;\n' : '';
		var fg_color_style = fg_color ? 'color: #' + fg_color + ' !important;\n' : '';
		var hl_color_style = hl_color ? 'background-color: #' + hl_color + '! important;\n' : '';
				
		var pref_style = ATutor.users.preferences.user_styles.replace(/FONT_FAMILY/g, font_style).replace(/FONT_SIZE/g, font_size_style).replace(/BG_COLOR/g, bg_color_style).replace(/FG_COLOR/g, fg_color_style).replace(/HL_COLOR/g, hl_color_style);
	    jQuery('#pref_style').replaceWith(pref_style);
	    if (window.opener) jQuery('#pref_style', window.opener.document).replaceWith(pref_style);
	};
	
	/**
	 * Adds click hander to links with id pref_wiz_launcher
	 */
	ATutor.users.preferences.addPrefWizClickHandler = function () {
    	var launcherArray = jQuery(".pref_wiz_launcher");   	
    	launcherArray.click(function() {
    		var query_string = "";
    		if (ATutor.users.preferences.course_id !== "") {
    			query_string = 'course_id=' + ATutor.users.preferences.course_id;
    		}
    		if (query_string !== "") {
    			query_string = "?" + query_string;
    		}
    		window.open(ATutor.base_href + 'users/pref_wizard/index.php' + query_string,'newWin1','menubar=0,scrollbars=1,resizable=1,width=640,height=580');
    		return false;
    	});
    };
    
})();
