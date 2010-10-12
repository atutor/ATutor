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

require(INCLUDE_PATH.'header.inc.php'); 
?>	

<script language="javascript" type="text/javascript" src="js/editor.js"></script>					

	<div id="movie_status"></div>				
	
	<div id="movie-container">		
		<object id="mymovie" width="340" height="<?php echo $this_proj->media_height; ?>" classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0">
		<param name="src" value="<?php echo $this_proj->media_loc; ?>" />
		<param name="enablejavascript" value="true" />
		<param name="postdomevents" value="true" />
		<param name="controller" value="false" />
		<param name="autoplay" value="false" />
		<param name="scale" value="aspect" />
		<embed src="<?php echo $this_proj->media_loc; ?>" width="340" height="<?php echo $this_proj->media_height; ?>" pluginspage="http://www.apple.com/quicktime/download/" enablejavascript="true" name="mymovie" id="mymovie_embed" postdomevents="true" controller="false" autoplay="false" scale="aspect" />
		</object>		
				
		<div id="movie-controls">
			<div style="text-align:center; margin-top:-3px;">
				<div id="current-time"></div> / <div id="duration"></div>
			</div>
			
			<div id='m_timeline'>
				<div id="m_handle"></div>	
			</div>					
				
			<div style="float:left;"><a href="#" onclick="clip.previous();"><img src="images/clip_prev.png" alt="Previous Clip" title="Previous Clip" /></a></div> 
			<div style="float:right;"><a href="#" onclick="clip.next();"><img src="images/clip_next.png" alt="Next Clip" title="Next Clip" /></a></div>
	
			<div style="text-align:center;">
				<!--  a href="#" onmousedown="movie.pressPlay()" onmouseup="movie.pressStop()"><img src="images/pressplay.png" alt="Press Play" title="Press Play" id="pressButton" /></a>&nbsp; --> 
				<a href="#" onmousedown="movie.normPlay()"><img src="images/play.png" alt="Play" title="Play" id="playButton" /></a>
			</div>

		</div>
		
		<div id="clip-info">
			<div style="font-weight:bold;text-decoration:underline; text-align:left;" id="clip-name"></div><br />
			<!--  div style="text-align:left">Total Clips: <div id="numclips" style="display:inline"></div></div -->
		</div>

		
		<div id="make-clip">
			<input type="button" name="Make Clip" value="Make Clip" class="button" style="width:7em" onclick="movie.saveClip()" id="makeclip"  />
		</div>
					
		<div id="clip-controls">
			<!--  div id="clip-time"></div> / <div id="clip-duration"></div -->

			<div id="in-info">
				<div style="float:left" id="in-time"></div><div style="float:right" id="in-undo"></div><br />
				<a href="#" onclick="clip.addPrevSpace()" id="addprev"><img src="images/clip_leftedge.png" alt="Add previous space to clip" title="Add previous space to clip" style="padding-top:10px; padding-right:5px;" /></a> 
				<input type="button" id="in" name="in" value="In" class="button" onclick="clip.newInTime();" />
			</div>

			<div id="out-info">
				<div style="float:left" id="out-undo"></div><div style="float:right" id="out-time"></div><br />
				<input type="button" id="out" name="out" value="Out" class="button" onclick="clip.newOutTime();" /> 
				<a href="#" onclick="clip.addNextSpace()" id="addnext"><img src="images/clip_rightedge.png" alt="Add next space to clip" title="Add next space to clip" style="padding-top:10px; padding-left:5px;" /></a>
			</div>				

			<div id="clip-timeline">
				<div id='c_timeline' class='ui-slider-clip' >
					<div id="c_handle"></div>	
				</div>					
			</div>	
					
			<div id="clip-buttons">			
			<div style="float:left;margin-top:1.4em;">
				<a href="#" onclick="clip.goToStart()"><img src="images/start.png" alt="Go to start of clip" title="Go to start of clip" /></a>&nbsp;
				<a href="#" onclick="clip.stepBack(33)"><img src="images/rewind2.png" alt="Step back .033 second" title="Step back .033 seconds" /></a>&nbsp;
				<a href="#" onclick="clip.stepBack(100)"><img src="images/rewind.png" alt="Step back .100 second" title="Step back .100 second" /></a>
			</div>	

			<div style="float:right;margin-top:1.4em;">
				<a href="#" onclick="clip.stepForward(100)"><img src="images/ffwd.png" alt="Step forward .100 second" title="Step forward .100 second" /></a>&nbsp;
				<a href="#" onclick="clip.stepForward(33)"><img src="images/ffwd2.png" alt="Step forward .033 second" title="Step forward .033 second" /></a>&nbsp; 
				<a href="#" onclick="clip.goToEnd()"><img src="images/end.png" alt="Go to end of clip" title="Go to end of clip" /></a> 
			</div>						
									
			<div style="text-align:center; margin-top:5px;">		
				<!--  a href="#" onmousedown="clip.pressPlay()" onmouseup="clip.pressStop()"><img src="images/pressplay.png" alt="Press Play" title="Press Play" id="clip-pressButton" /></a>&nbsp; -->	
				<a href="#" onclick="clip.normPlay()"><img src="images/play.png" alt="Play Clip" title="Play Clip" id="clip-playButton" /></a>&nbsp;
				<a href="#" onclick="clip.lastPlay();"><img src="images/last_start.png" alt="Last play" title="Last play" /></a>
			</div>
			</div>
		</div>		
		<div id="captions">
			<textarea cols="5" rows="4" style="margin-left:-5px;width:250px; height:75px;font-size:15px;" id="caption-text"></textarea><br />
		</div>
	
	</div>

	<div id="info-container">
		<!-- div id="submenubar">
			<ul>
				<li id="clips-subtab"><a id="clip-tab" href="#" onclick="displayClips();">Clip Timeline</a></li>
				<li id="detail-subtab"><a id="detail-tab" href="#" onclick="displayDetails()">Details</a></li>
			</ul>
		</div -->
		<div style="background-color:#b5c3d9; padding:5px;"><div id="show_hide_caps"></div></div>	
		<div id="info-tab"></div>
	</div>

<?php require('include/footer.inc.php'); ?>