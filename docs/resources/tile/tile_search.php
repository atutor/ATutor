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

define('AT_INCLUDE_PATH', '../include/');

require (AT_INCLUDE_PATH.'vitals.inc.php');
$_section[0][0] = _AT('resources');
$_section[0][1] = 'resources/';
$_section[1][0] = _AT('tile_search');
$_section[1][1] = 'resources/tile_search.php';

require (AT_INCLUDE_PATH.'header.inc.php');
	
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<h2><img src="images/icons/default/square-large-resources.gif" class="menuimage" vspace="2" width="42" height="38" border="0" alt="" /> <a href="resources/index.php?g=11">'._AT('resources').'</a></h2>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1 && $_SESSION['prefs'][PREF_CONTENT_ICONS] == 2) {
	echo '<h2><a href="resources/index.php?g=11">'._AT('resources').'</a></h2>';
}

if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<h3><img src="images/icons/default/links-large.gif" width="42" height="38"  class="menuimageh3" border="0" alt="" /> <a href="resources/tile_search.php?g=11">'._AT('tile_search').'</a></h3>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1 && $_SESSION['prefs'][PREF_CONTENT_ICONS] == 2) {
	echo '<h3><a href="resources/tile_search.php?g=11"><a href="resources/tile_search.php?g=11">'._AT('tile_search').'</a></h3>';
}

?>
<br />
<form action="<?php echo $_SERVER['PHP_SELF']; ?>#search_results" method="get" name="form">
<input type="hidden" name="search" value="1" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2"><?php print_popup_help(AT_HELP_SEARCH); ?><?php echo _AT('tile_search'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><b><label for="words2"><?php echo _AT('search_words'); ?>:</label></b>
	</td>
	<td class="row1"><input type="text" name="words" class="formfield" size="40" id="words2" value="<?php echo stripslashes(htmlspecialchars($_GET['words'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><label for="words2">Search In:</label></b>
	</td>
	<td class="row1">
		<select name="over">   
		<option selected="selected" value="8128">Any Field</option>   
		<option value="64">Title</option>   
		<option value="128">Author</option>   
		<option value="4096">Keyword</option>    
		<option value="1024">Description</option>   
		<option value="2048">Technical Format</option> 
	</select> 
	</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><label for="words2">Content Type:</label></b>
	</td>
	<td class="row1">
		<select name="ctype">   
			<option value="1">Any Content</option>   
			<option selected="selected" value="2">IMS Content Packages</option>   
			<option value="4">Web Content Files</option>   
			<option value="8">Image Files</option>   
			<option value="16">Audio Files</option>   
			<option value="32">Video Files</option> 
		</select> 
	</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('search_match'); ?>:</b></td>
	<td class="row1"><input type="radio" name="include" value="all" id="all"<?php echo $include_all; ?> checked="checked" /><label for="all"><?php echo _AT('search_all_words'); ?></label><br />
	<input type="radio" name="include" value="one" id="one"<?php echo $include_one; ?> /><label for="one"><?php echo _AT('search_any_word'); ?></label></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><input type="submit" name="submit" value="  <?php echo _AT('search'); ?>  " class="button" /></td>
</tr>
</table>
</form>
<br />

<h4>Search Results for &quot;&quot;  over   <span class="searchField">Any Field</span>    of Type   <span class="searchField">IMS Content Packages</span>   </h4>
<div class="results">
<p class="small">Results <span class="bold">1 - 7</span> of   <span class="bold">7</span></p>  
<p><a class="larger" href="/tile/servlet/view?view=item&amp;cp=urn:e0991540-0be3-11d8-afb0-0002b3af6db8&amp;item=urn:e0991540-0be3-11d8-afb0-0002b3af6db8">Simple Manifest</a><small> - <span class="small">[<a href="/tile/servlet/advsearch?query=&amp;over=8128&amp;ctype=2&amp;add=urn:e0991540-0be3-11d8-afb0-0002b3af6db8&amp;page=0&amp;field0=AggregationLevel&amp;value0=0">Add to Modules</a>]</span>              
<br /> <span class="TILEDescription">Representative content to assist development.</span><br />     <span class="small">     <span class="searchField">Authors:</span>      <a href="javascript:authorPopUp(800,600,'/tile/servlet/authview?loid=urn:e0991540-0be3-11d8-afb0-0002b3af6db8&amp;popup=true','authors');">David Weinkauf, Simon Bates, Cynick Young, Joseph Scheuhammer, Anastasia Cheetham, Mike Lam</a>  </small></p>   
</div>


<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>