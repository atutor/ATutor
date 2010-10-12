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
require(INCLUDE_PATH.'header.inc.php'); ?>

<script language="javascript" type="text/javascript" src="js/preview.js"></script>
	
<div id="content">
	<div id="movie-container" style="margin-top:10px;padding-bottom:5em;"> 
	</div>
	
	<div id="info-container">
		<!-- div id="submenubar">
			<ul>
				<li><a id="preset-tab" href="#" onClick="displayPreset();">Preset</a></li>
				<li><a id="custom-tab" href="#" onClick="displayCustom()">Custom</a></li>
			</ul>
		</div -->
		
		<div id="layout" style="padding:5px; border-top:2px solid #ccc;">	
			<h3 style="font-size:small; margin:0px;margin-bottom:5px;">Layout Presets</h3>
			<form method="post" id="form" action="javascript:saveLayout();">
				<label><input type="radio" name="layout" value="0" /> Caption below</label><br />
				<label><input type="radio" name="layout" value="1" /> Caption on bottom</label><br />
				<label><input type="radio" name="layout" value="2" /> Caption only (audio podcast)</label><br /><br />			
				<div style="text-align:right"><input type="submit" name="submit" value="Apply" /></div>
			</form>
		</div>
	</div>

	</div>
<?php require(INCLUDE_PATH.'footer.inc.php'); ?>
