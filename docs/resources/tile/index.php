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

define('AT_INCLUDE_PATH', '../../include/');

require (AT_INCLUDE_PATH.'vitals.inc.php');
$_section[0][0] = _AT('resources');
$_section[0][1] = 'resources/index.php';
$_section[1][0] = _AT('tile_search');
$_section[1][1] = 'resources/tile/index.php';

	$path = array();

	/* called at the start of en element */
	/* builds the $path array which is the path from the root to the current element */
	function startElement($parser, $name, $attrs) {
		global $path;
		array_push($path, $name);
	}

	/* called when an element ends */
	/* removed the current element from the $path */
	function endElement($parser, $name) {
		global $my_data, $path, $tile_title, $tile_description, $tile_identifier;

		if ($path == array('lom', 'general', 'title', 'langstring')) {
			$tile_title = $my_data;
		} else if ($path == array('lom', 'general', 'description', 'langstring')) {
			$tile_description = $my_data;
		} else if ($path == array('lom', 'general', 'identifier')) {
			$tile_identifier = $my_data;
		}

		$my_data = '';
		array_pop($path);
	}

	/* called when there is character data within elements */
	/* constructs the $items array using the last entry in $path as the parent element */
	function characterData($parser, $data){
		global $my_data;
		$my_data .= $data;
	}

require (AT_INCLUDE_PATH.'header.inc.php');
	
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<h2><img src="images/icons/default/square-large-resources.gif" class="menuimage" vspace="2" width="42" height="38" border="0" alt="" /> <a href="resources/index.php?g=11">'._AT('resources').'</a></h2>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1 && $_SESSION['prefs'][PREF_CONTENT_ICONS] == 2) {
	echo '<h2><a href="resources/index.php?g=11">'._AT('resources').'</a></h2>';
}

if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<h3><img src="images/icons/default/search_tile-large.gif" width="42" height="38"  class="menuimageh3" border="0" alt="" /> '._AT('tile_search').'</h3>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1 && $_SESSION['prefs'][PREF_CONTENT_ICONS] == 2) {
	echo '<h3>'._AT('tile_search').'</h3>';
}

require(AT_INCLUDE_PATH.'html/feedback.inc.php'); 

?>
<br />
<form action="<?php echo $_SERVER['PHP_SELF']; ?>#search_results" method="get" name="form">

	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr>
		<th colspan="2" class="cyan"><?php print_popup_help(AT_HELP_SEARCH); ?><?php echo _AT('tile_search'); ?></th>
	</tr>
	<tr>
		<td class="row1" align="right"><b><label for="words2"><?php echo _AT('search_words'); ?>:</label></b>
		</td>
		<td class="row1"><input type="text" name="query" class="formfield" size="40" id="words2" value="<?php echo stripslashes(htmlspecialchars($_GET['query'])); ?>" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right"><b><label for="words2"><?php echo _AT('search_in'); ?>:</label></b>
		</td>
		<td class="row1">
			<select name="field">   
				<option selected="selected" value="anyField"><?php echo _AT('tile_any_field'); ?></option>   
				<option value="title"><?php echo _AT('tile_title'); ?></option>
				<option value="author"><?php echo _AT('tile_author'); ?></option>
				<option value="subject"><?php echo _AT('tile_keyword'); ?></option>
				<option value="description"><?php echo _AT('tile_description'); ?></option>
				<option value="technicalFormat"><?php echo _AT('tile_technical_format'); ?></option> 
			</select></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" colspan="2" align="center"><input type="submit" name="submit" value="<?php echo _AT('search'); ?>" class="button" /></td>
	</tr>
	</table>
</form>
<br />
<?php

if (isset($_GET['query'])) {

	require(AT_INCLUDE_PATH . 'classes/nusoap.php');

	// Create the client instance
	$client = new soapclient(AT_TILE_WSDL, true);

	// Check for an error
	$err = $client->getError();
	if ($err) {
		// Display the error

		$errors[] = AT_ERRORS_TILE_UNAVAILABLE;
		require(AT_INCLUDE_PATH.'html/feedback.inc.php');

		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	// Create the proxy
	$proxy = $client->getProxy();

	$search_input = array('query' => $_GET['query'], 'field' => $_GET['field'], 'content' => 'contentPackage');

	$results = $proxy->doSearch($search_input);

	if ($results) {
		$num_results = count($results);
	} else {
		$num_results = 0;
	}
	echo '<h2>'. _AT('results_found', $num_results).'</h2>';
	echo '<ol>';
	if ($num_results) {
		foreach ($results as $result) {

			$xml_parser = xml_parser_create();

			xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
			xml_set_element_handler($xml_parser, 'startElement', 'endElement');
			xml_set_character_data_handler($xml_parser, 'characterData');

			if (!xml_parse($xml_parser, $result, true)) {
				die(sprintf("XML error: %s at line %d",
							xml_error_string(xml_get_error_code($xml_parser)),
							xml_get_current_line_number($xml_parser)));
			}

			xml_parser_free($xml_parser);

			$tile_title = str_replace('<', '&lt;', $tile_title);

			echo '<li><strong>' . $tile_title . '</strong> - <a href="'.AT_TILE_EXPORT.'?cp='.$tile_identifier.'">'._AT('download').'</a>';
			if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
				echo ' | <a href="resources/tile/import.php?cp='.$tile_identifier.SEP.'title='.urlencode($tile_title).'">'._AT('import').'</a>';
			}
			echo '<br />';
			if (strlen($tile_description) > 200) {
				echo '<small>' . $tile_description  . '</small>';
			} else {
				echo $tile_description;
			}

			echo '<br /></li>';

			unset($tile_title);
			unset($tile_description);
			unset($tile_identifier);
		}
	}
	echo '</ol>';
}
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>