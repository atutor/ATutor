<?php 
/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2009 Heidi Hazelton
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['remote_submit'])) {
	//$this_proj->remoteUpdate($_SESSION['rid']);
	$this_system->putCaps($this_proj->id);
} else if (isset($_POST['format'])) {
	$this_proj->exportCaption($_POST['format']);
}

require(INCLUDE_PATH.'header.inc.php'); 

?>

<script language="javascript" type="text/javascript" src="js/export.js"></script>

<div id="content">
	<form action="export.php" method="post" id="form">

	<?php if (isset($systems) && isset($_SESSION['rid'])) { ?>
	<h3>Send Captions to <?php echo $systems[$_SESSION['rid']]['name']; ?></h3>					
		<p>If you are finished captioning this project, use the button below to update the system with your caption file.</p>
		<p><input type="submit" name="remote_submit" value="Submit Captions to <?php echo $systems[$_SESSION['rid']]['name']; ?>" /></p>
	<?php }  ?>
	
		<h3>Export Caption File</h3>
		<p>You may export your captions in a variety of formats. Choose a format to receive a download of that caption file:</p>	

		<input type="hidden" name="format" />
		<ul id="export-list">
			<!--  li><a href="javascript:set_format('json')">Complete Package</a><br />Zip of original movie, caption file, SMIL file, and accessible html player</li -->
			<li><a href="javascript:set_format('DFXP')">Timed Text (DFXP)</a></li>
			<li><a href="javascript:set_format('DvdStl')">DVD STL</a></li>
			<li><a href="javascript:set_format('MicroDvd')">MicroDVD</a></li>
			<!-- li><a href="javascript:set_format('MPlayer')">MPlayer</a></li -->
			<li><a href="javascript:set_format('QTtext')">QT text</a></li>
			<!-- li><a href="javascript:set_format('RealText')">Real Text</a></li -->
			<li><a href="javascript:set_format('Sami')">SAMI</a></li>
			<li><a href="javascript:set_format('SubRipSrt')">SubRip</a></li>
			<li><a href="javascript:set_format('SubViewer')">SubViewer</a></li>
			<li><a href="javascript:set_format('json')">JSON for OpenCaps</a></li>				
			<!-- li><a href="javascript:set_format('Scc')">SCC</a></li -->											
		</ul>

		<h3>Export Transcript</h3>
		<p>Coming soon.</p>
		<!--  ul id="export-list">
			<li><a href="javascript:set_format('plain')">Transcript</a> - a plain text file of the captions, separated by new lines</li>										
		</ul -->
		
		<?php 
		if (extension_loaded('zip')) { ?>
		<h3>Export Complete Project</h3>
		<ul id="export-list">
			<li><a href="javascript:set_format('all')">Complete Package</a> - .zip of original movie, caption file, SMIL file (layout in preview), and accessible html player</li>										
		</ul>
		<?php } ?>
	</form>
	
	<h3>Close</h3>		
	<p>Once you have exported your work to save it, you may now <a href="start.php"><img style="margin-bottom:-3px;" src="images/cross.png" alt="" /> <strong>Close</strong></a> this project.</p>
</div>
<?php require(INCLUDE_PATH.'footer.inc.php'); ?>
