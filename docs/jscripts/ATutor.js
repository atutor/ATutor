/**
 * $Id: $
 * 
 * @author Laurel A. Williams
 * @copyright Copyright © 2010, ATutor, All rights reserved.
 */

var ATutor = ATutor || {};
ATutor.users = ATutor.users || {};
ATutor.users.preferences = ATutor.users.preferences || {};

(function() {
	
    ATutor.users.preferences.user_styles = 
    	'<style id="pref_style" type="text/css">' + 
    	'<!--' + 
        'body {' +
  	  	'{FG_COLOR} {BG_COLOR} {FONT_SIZE} {FONT}' +
  	  	'}' +
  	  	'h1, h2, h3, h4, h5, h6 {' +
  	  	'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'p.toc {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'a:link, a:visited, a:active { ' +
  		'{FG_COLOR} {BG_COLOR} ' +
  		'}' +
  		'a.dropdown-title {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'.button, .button2 {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'.editorsmallbox, .editorlargebox {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'.buttontab {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'.tab {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'.econtainer {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'.etabself {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'.unsaved {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'.saved {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'td.dropdown-heading {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'td.dropdown {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'td.dropdown a, td.dropdown a:visited  {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'td.dropdown strong {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'h5.search-results {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'small.search-info {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'p.search-description {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'.test-box {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'table.tabbed-table th.tab {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'table.tabbed-table th.selected {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div#sub-navigation {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div#sub-navigation strong {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div#help {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'h3#help-title {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'#jumpmenu:focus{' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'a#editor-link {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'table.data th {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'table.data th a {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'table.data tbody th {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'table.data tbody tr.selected {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'table.data tfoot {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'table.data tfoot tr:first-child td {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'table.data tfoot input {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div#error {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div#error h4 {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div#feedback {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div#help {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div#info {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div#warning {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.news span.date {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.dropdown {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.dropdown-heading {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.required {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'#header{' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'#header a{' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.tabs a {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.tabs a.selected {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.box {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'h5.box {' + 
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.box a:visited {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.box .even {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.box .odd {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.course {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'fieldset#shortcuts {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'a#guide {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div#content-test ol ul li{' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div#content-info {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.column h3 {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'#navlist li a {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'#forum-thread li {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'#forum-thread li.even {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'#forum-thread li.odd {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.forum-post-ctrl span {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.forum-post-content p.date {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div.forum-paginator{' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'div#topnavlistcontainer {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'ul#topnavlist li a {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'ul#topnavlist li a.selected {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'ol#tools>li:hover {' +
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
  		'li.top-tool {' + 
  		'{FG_COLOR} {BG_COLOR}' +
  		'}' +
	  	'dl.browse-course {' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'legend.group_form{' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'div.column_equivalent{' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'div.resource_box{' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'h2.alternatives_to{' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'div.alternative_box{' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'div.alternative_box legend {' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'div.resource_box legend {' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'label.primary a{' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'div.input-form {' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'div.input-form div.row {' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'div.input-form input[type=text], div.input-form input[type=password] {' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'input[type=checkbox]{' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'div.input-form div.buttons input {' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	  	'div.input-form div.row_alternatives {' +
	  	'{FG_COLOR} {BG_COLOR}' +
	  	'}' +
	
	  	'a:active, a:hover, a:focus, .highlight, a.highlight {' +
	  	'{HL_COLOR} {FG_COLOR} }' +
	  	'ul#topnavlist li a:hover, ul#topnavlist li a:focus, ul#topnavlist li a.active {' +
	  	'{HL_COLOR} {FG_COLOR}' +
	  	'}' +
	  	'.tab a:hover {' +
  		'background-color:  #FF0000;	; {FG_COLOR}' +
  		'}' +
  		'.button:focus, .button2:focus {' +
  		'background-color:  #FF0000;	; {FG_COLOR}' +
  		'}' +
  		'div.input-form textarea:focus, div.input-form  input[type=password]:focus, div.input-form  input[type=text]:focus{' +
  		'{HL_COLOR}; {FG_COLOR}' +
  		'}' +
  		'.formfield:focus{' +
  		'{HL_COLOR}; {FG_COLOR}' +
  		'}' +
  		'table.data tfoot input:focus {' +
  		'{HL_COLOR}; {FG_COLOR}' +
  		'}' +
  		'td.selected{' +
  		'{HL_COLOR}; {FG_COLOR}' +
  		'}' +
  		'.buttontab selected {' +
	  	'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'td.dropdown a:hover {' +
  		'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'table.tabbed-table th.tab:hover {' +
	  	'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'table.tabbed-table th.tab a:focus {' +
	  	'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'table.tabbed-table a, table.tabbed-table a:visited, table.tabbed-table a:hover {' +
	  	'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'div#top-links a:focus{' +
	  	'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'a#editor-link:hover {' +
	  	'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'table.data tbody tr:hover {' +
	  	'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'table.data tfoot input:focus {' +
	  	'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'div.home-link:hover {' +
	  	'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'#header a:hover {' +
	  	'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'div.tabs a:hover, div.tabs a.active {' +
  		'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'div.course:hover {' +
	  	'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
	  	'#navlist li a:hover, #navlist li a:active {' +
	  	'{HL_COLOR}; {FG_COLOR}' +
	  	'}' +
    	'-->' +
    	'</style>';

    ATutor.users.preferences.setStyles = function (bg_color, fg_color, hl_color, font, font_size) {
		var font_style = font ? 'font-family:' + font + ';\n' : '';
		var font_size_style = font ? 'font-size:' + font_size + 'em;\n' : '';
		var bg_color_style = bg_color ? 'background-color: #' + bg_color + ';\n' : '';
		var fg_color_style = fg_color ? 'color: #' + fg_color + ';\n' : '';
		var hl_color_style = hl_color ? 'background-color: #' + hl_color + ';\n' : '';
				
		var pref_style = ATutor.users.preferences.user_styles.replace('{FONT}', font_style)
			.replace('{FONT_SIZE}', font_size_style).replace('{BG_COLOR}', bg_color_style)
			.replace('{FG_COLOR}', fg_color_style).replace('{HL_COLOR}', hl_color_style);
	    jQuery('#pref_style').replaceWith(pref_style);
	};	
	
})();
