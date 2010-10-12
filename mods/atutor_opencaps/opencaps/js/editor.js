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

/* GLOBALS */

var proj = proj || {};

var MIN_CLIP_DUR = 400;

var clip_playing = false;

var movie = movie || {};
var clip = clip || {};
var movieObj = movieObj || {};

num_clips = 0;
clips = new Array();

this_location = new Object;

var json;
var inClip;


/* ****** */
tab = 'clips';
lastplay = 0;
new_flag = 0;
clocktimer = 0;
curTime = 0;
clipTime = 0;
curClip = 0;
curIn = 0;
lastLoc = 1;

pace = 0;
timer = 0;
numclips = 0;
marker = "";
clip_marker = "";

//set values in seconds
onemin	= 60;
onehr	= onemin*60;

/* clip vars */
temp_in = 0;
temp_out = 0;
temp_caps = '';
dur = 0;
extend = false;


/*************************************** initialize */

$(document).ready(function () {
	$("#movie-controls").css("visibility", "hidden");
	$("#clip-controls").css("visibility", "hidden");
	$("#make-clip").css("visibility", "hidden");
	$("#clip-info").css("visibility", "hidden");

	$.get("include/workflow.php", { task: 'get_json' }, function(json) {
		if (json) {			
			proj = JSON.parse(json);
			startEditor();
		}
	});
	
	$("#caption-text").keyup( function(event) {		
		//if ($("#caption-text").text() != this_location.caption_text) {
			$("#makeclip").removeAttr("disabled");
			temp_caps = 1;
		//}
	});
});


function confirmDelete(clipnum) {
	if (confirm("Are you sure you want to delete Clip "+clipnum+"?")) {
		deleteClip(clipnum);
	}
}

function startEditor() {
	movieObj = document.mymovie;
	
	//register listeners
	RegisterListener('qt_play', 'mymovie', 'mymovie_embed', startTimeline);
	RegisterListener('qt_pause', 'mymovie', 'mymovie_embed', pauseTimeline);
	RegisterListener('qt_ended', 'mymovie', 'mymovie_embed', pauseTimeline);

	/* show or hide side bar action */
	$("#show_hide_caps").click(function() {
		if ($("#info-tab").css("display") == "none") {
			$("#info-tab").show();
			$("#info-container").css("margin-left", "71%");
			$("#movie-container").css("width", "70%");
			
			$(this).html('<h4 style="margin:0px">Clips <img style="float:right" src="images/application_get.png" alt="hide clips" title="hide clips" /></h4>');
		} else {
			$("#info-tab").hide();
			$("#info-container").css("margin-left", "97%");
			$("#movie-container").css("width", "96%");
			
			$(this).html('<img src="images/application_put.png" alt="show clips" title="show clips" />');	
		}
	});

	/* hide clip side bar */
	$("#info-tab").hide();
	$("#info-container").css("margin-left", "97%");
	$("#movie-container").css("width", "96%");
	$("#show_hide_caps").html('<img src="images/application_put.png" alt="show clips" title="show clips" />');	
		
	
	$("#editor-tab").addClass('current');
	$("#clips-subtab").addClass('current');
	
	/* global proj related vars */
	if (proj.clip_collection)
		num_clips = proj.clip_collection.clips.length;

	interval = window.setInterval("QTStatus()",100);	
}

function QTStatus() {
	try {
		if (movieObj.GetPluginStatus() == "Complete") {
			window.clearInterval(interval);
			$("#movie_status").html("");
			setDisplay();
		} else {
			$("#movie_status").html("<strong>Loading media...</strong>");
		}
	} catch (err) {
	}
}

function setDisplay() {
	$("#movie-controls").css("visibility", "visible");
	$("#clip-controls").css("visibility", "visible");
	$("#make-clip").css("visibility", "visible");
	$("#clip-info").css("visibility", "visible");
	
	$("#source-file").text(movieObj.GetURL());	

	$("#duration").text(getFormattedTime(movieObj.GetDuration()));	
	dur = parseInt(movieObj.GetDuration()/movieObj.GetTimeScale()*1000);

	var mysize = movieObj.GetRectangle().split(',');
	
	/* set if audio file: min 250x100 */
	if (mysize[3] >= 1)
		proj.media_height = mysize[3];
	else 
		proj.media_height = 1;
	
	if (mysize[2] >= 250)
		proj.media_width = mysize[2];
	else 
		proj.media_width = 250;

	proj.duration = getFormattedTime2(dur);
	clips = proj.clip_collection.clips;	
		
	$("#m_timeline").slider({ 
        min: 0, 
        max: dur,
    
        stop: function(e, ui) { 
           	$("#m_timeline").slider("value", ui.value);
        }, 

        slide: function(e, ui) { 
            $("#c_timeline").slider("value", ui.value); 
			moveMovieAll(ui.value);
        } 
    });    

	//$("#show_hide_caps").html('<img src="images/application_get.png" style="margin-bottom:-3px;" alt="hide clips" title="hide clips" /> Clips');

	pace = dur / parseInt($("#m_timeline").css("width"));

	//isn't it always 0?
	if (movieObj.GetTime() > 0) {
		curTime = calcTime(movieObj.GetTime());
	} else {
		curTime = getFormattedTime2(0);
	}
	
	$("#current-time").text(curTime);	
	$("#clip-time").text(curTime);	
	
	curTime = getMilliseconds(curTime);
	$("#makeclip").attr("disabled","disabled");

	saveJson();
}

function moveMovie(time) { //time in milliseconds
	movieObj.SetTime(time*movieObj.GetTimeScale()/1000);
	setCurClip(time);  
	    
   	$("#current-time").text(getFormattedTime2(time));	
   	
	clipTime = parseInt(time - this_location.inTimeMilli);
	$("#clip-time").text(getFormattedTime2(clipTime));	

	curTime = time;
}

function moveMovieAll(time) { //time in milliseconds
	movieObj.SetTime(time*movieObj.GetTimeScale()/1000);
	setCurClip(time);  
    	
	$("#c_timeline").slider("value", time);
    $("#m_timeline").slider("value", time); 
   	$("#current-time").text(getFormattedTime2(time));	
   	
	clipTime = parseInt(time - this_location.inTimeMilli);
	$("#clip-time").text(getFormattedTime2(clipTime));	
	curTime = time;
}

function startTimeline() {
	/*if (getMilliseconds(calcTime(movieObj.GetTime())) == curTime-1) {
		movieObj.SetTime((curTime*movieObj.GetTimeScale()/1000)+50);
	}*/
	runTimer();	
	
	this_location_dur = getMilliseconds($("#out-time").text())-getMilliseconds($("#in-time").text());
	clip_pace = this_location_dur / parseInt($("#c_timeline").css("width"));
	
	updateMovieMarker(curTime);	
	updateClipMarker(curTime);		
}

function pauseTimeline() {
	clearTimeout(clocktimer);
	clearTimeout(marker);
	clearTimeout(clip_marker);
}

function updateMovieMarker(time) {	
	$("#m_timeline").slider("value", time);
	marker = setTimeout("updateMovieMarker(curTime)", pace);
}

function updateClipMarker(time) {	
	$("#c_timeline").slider("value", time);
	clip_marker = setTimeout("updateClipMarker(curTime)", clip_pace);
}

function runTimer() {
	curTime = calcTime(movieObj.GetTime());

	$("#current-time").text(curTime); 	
	curTime = getMilliseconds(curTime);
		
	clipTime = parseInt(curTime - getMilliseconds($("#in-time").text()));

	if ( curTime==dur || curTime>getMilliseconds($("#out-time").text()) || curTime<getMilliseconds($("#in-time").text()) ) {	
		if (clip_playing || curTime >= dur) {
			clip.pressStop();
			moveMovieAll(getMilliseconds($("#out-time").text()));
			return;
		} else {
			setCurClip(curTime);
		}
	}
	$("#clip-time").text(getFormattedTime2(clipTime)); 	
	clocktimer = setTimeout("runTimer()",1);	
}

function setClipTimeline(cdur, cin, cout) { 
	//console.log('setClipTimeline', cin, cout);
	
	//destroy the old slider set up and create the new one
    $("#c_timeline").slider("destroy");

	$("#c_timeline").slider({ 
        min: cin, 
        max: cout,
                  
        stop: function(e, ui) { 
			$("#c_timeline").slider("value", ui.value);
        },
        slide: function(e, ui) { 
            $("#m_timeline").slider("value", ui.value); 
			moveMovieAll(ui.value);
        } 
    });    
}
	
function loadClips() {
	$('#numclips').text(num_clips);
	clips_html = '';
		
	if (num_clips > 0) {
		for (var i in clips) {
						
			//previous space
			if (clips[parseInt(i)-1] == undefined && clips[i].inTimeMilli>0) {
				space_dur = getFormattedTime2(clips[i].inTimeMilli-1);
				clips_html += '<div class="space"><span style="font-weight:bold;float:left;"><a href="#" onclick="javascript:resetClipWork();moveMovieAll(0);">Space before Clip 1</a></span><span style="float:right">'+ space_dur +'</span><span id="space'+i+'"></span><br style="clear:both;" /></div>';
			} else if ( clips[parseInt(i)-1] != undefined && (clips[i].inTimeMilli != parseInt(clips[parseInt(i)-1].outTimeMilli)+1)) { 
				space_dur = getFormattedTime2(clips[i].inTimeMilli - clips[parseInt(i)-1].outTimeMilli -2);
				clips_html += '<div class="space"><span style="font-weight:bold;float:left;"><a href="#" onclick="javascript:resetClipWork();moveMovieAll('+(clips[parseInt(i)-1].outTimeMilli+1)+');">Space after Clip '+i+'</a></span><span style="float:right">'+ space_dur +'</span><span id="space'+i+'"></span><br style="clear:both;" /></div>';
			} 
			
			//clip
			html_caption = clips[i].caption_text.replace(/\n/g, "<br />");			
			clips[i].name = 'Clip '+(parseInt(i)+1);
			clips_html += '<div style="float:right; padding-top:5px;margin-right:5px;"><a href="#" onclick="javascript:confirmDelete('+(parseInt(i)+1)+');">X</a></div><div class="clip"><span class="clip-title"><a name="clip'+i+'" href="#" onclick="javascript:resetClipWork();moveMovieAll('+clips[i].inTimeMilli+');">'+clips[i].name+'</a></span><span id="clip'+(parseInt(i)+1)+'"></span><br />' + html_caption + '<br /><div style="float:right"> '+ clips[i].duration + '</div><span style="font-size:smaller">in: ' + clips[i].inTime + ' <br />out: '+ clips[i].outTime +'</span></div>';
				
			//last space
			if (clips[parseInt(i)+1] == undefined && clips[i].outTimeMilli<dur) {
				space_dur = getFormattedTime2(dur - clips[i].outTimeMilli-1);
				clips_html += '<div class="space"><span style="font-weight:bold;float:left;"><a href="#" onclick="javascript:resetClipWork();moveMovieAll('+parseInt(clips[i].outTimeMilli+1)+');">Space after Clip '+(parseInt(i)+1)+'</a></span><span style="float:right">'+ space_dur +'</span><span id="space'+(parseInt(i)+1)+'"></span><br style="clear:both;" /></div>';
			}
			i++;
		}	
		
  		$("#info-tab").html(clips_html);		
	} else {
		//no clips
  		clips_html = '<div class="space" style="border-bottom:0px"><span style="font-weight:bold;float:left;"><a href="#" onclick="javascript:resetClipWork();moveMovieAll(0);">Space</a></span><span style="float:right">'+ $("#duration").text() +'</span><br style="clear:both;" /></div>';
  		$("#info-tab").html(clips_html);
  		
  		new_flag = true;
	}
	setCurClip(curTime);
}

/*
 * As the movie plays (or the playhead is moved), figure out what clip it's on 
 */ 
function setCurClip(time) {
	
	this_location = {};
	inClip = false;
	var asterisk = '';
			
	if (temp_in) {
		this_location.inTime = temp_in;
		this_location.inTimeMilli = getMilliseconds(temp_in);
	} else {
		this_location.inTime = $("#in-time").text();
		this_location.inTimeMilli = getMilliseconds(this_location.inTime);
	}
	if (temp_out) {
		this_location.outTime = temp_out;
		this_location.outTimeMilli = getMilliseconds(temp_out);
	} else {
		this_location.outTime = $("#out-time").text();
		this_location.outTimeMilli = getMilliseconds(this_location.outTime);
	}
	
	if (extend == true) {		
		if( (temp_in && time < getMilliseconds(temp_in)) || (temp_out && time > getMilliseconds(temp_out) )) {
			resetClipWork();
			extend = false;
		} else {
			return;
		}
	} else {
		lastLoc = curClip;
	}	
	
	if (num_clips == 0) {
		//no clips
		this_location.name = "Space";
		
		asterisk = "#space0";
		if (!temp_in) {
			this_location.inTimeMilli = 0;
			this_location.inTime = getFormattedTime2(this_location.inTimeMilli);
		}
		if (!temp_out) {
			this_location.outTimeMilli = dur;
			this_location.outTime = getFormattedTime2(dur);
		}
		
		this_location.caption_text = "";
		curClip = 0;
		$("#makeclip").val("Make Clip");
		
	} else {	
		for (var i in clips) {
			if (time < clips[i].inTimeMilli && clips[parseInt(i)-1] == undefined) { 
				//first space
				this_location.name = "Space before Clip 1";
				
				asterisk = "#space0";
								
				if (!temp_in) {
					this_location.inTimeMilli = 0;
					this_location.inTime = getFormattedTime2(this_location.inTimeMilli);
				}
				if (!temp_out) {
					this_location.outTimeMilli = parseInt(clips[i].inTimeMilli)-1;
					this_location.outTime = getFormattedTime2(this_location.outTimeMilli);
				}
				
				this_location.caption_text = "";
				curClip = 0;
				$("#makeclip").val("Make Clip");

				break;
			} else if ( (clips[parseInt(i)+1] == undefined || time < clips[parseInt(i)+1].inTimeMilli) && time > clips[i].outTimeMilli) {
				
				//space
				this_location.name = "Space after Clip "+ parseInt(parseInt(i)+1);
				
				if (!temp_in) {	
					this_location.inTimeMilli = clips[i].outTimeMilli+1;
					this_location.inTime = getFormattedTime2(this_location.inTimeMilli);
				}

				if (!temp_out) {
					if (clips[parseInt(i)+1] == undefined)
						this_location.outTimeMilli = dur;
					else
						this_location.outTimeMilli = clips[parseInt(i)+1].inTimeMilli-1;
					
					this_location.outTime = getFormattedTime2(this_location.outTimeMilli);
				}
				
				this_location.caption_text = "";
				curClip = parseInt(i)+1;
				asterisk = "#space"+curClip;

				$("#makeclip").val("Make Clip");
				
				break;
			} else if (time >= clips[i].inTimeMilli && time <= clips[i].outTimeMilli) {
				//clip
				curClip = parseInt(i)+1;
				this_location = clips[i];
				inClip = true;
				$("#makeclip").val("Update Clip");
				
				asterisk = "#clip"+curClip;
				break;
			} else {				
				//console.log("skipping this one", i, clips[i].inTimeMilli, clips[i].outTimeMilli);
			}
		}	
	}	
	
	// 'you are here' asterisk 	
	$("#space"+lastLoc).text("");				
	$("#clip"+lastLoc).text("");
		
	$(asterisk).html("&nbsp;<img src='images/asterisk_yellow.png' alt='current space' />");
	
	// in and out times
	if (!temp_in) 
		$("#in-time").text(this_location.inTime);
	if (!temp_out) 				
		$("#out-time").text(this_location.outTime);
			
	//duration
	this_location.durationMilli = this_location.outTimeMilli - this_location.inTimeMilli;
	this_location.duration = getFormattedTime2(this_location.durationMilli);		
	$("#clip-duration").text(this_location.duration);

	
	// moved into a different clip or space
	if ($("#clip-name").text() != this_location.name || (new_flag && !temp_in && !temp_out && !temp_caps)) {
		
		//caption text
		$("#caption-text").val(this_location.caption_text);			
		
		// user was working on a new clip but it gets reset when moving out of current clip
		if (temp_in || temp_out || temp_caps) {  
			resetClipWork();
		}
		
		//move scroll bar to clip
		if (curClip>2)
			$("#info-tab").scrollTop(curClip*70);
		else 
			$("#info-tab").scrollTop(0);
		
		// update the clip timeline and move the playhead
		setClipTimeline(this_location.durationMilli, this_location.inTimeMilli, this_location.outTimeMilli);
	}
	
	// name
	$("#clip-name").text(this_location.name);
}
/*************************************** movie controller buttons */

movie.normPlay = function() {
	if ($("#playButton").attr("src") == "images/play.png") {
		movieObj.Play();
		$("#playButton").attr("src", 'images/pause.png');
		$("#clip-playButton").attr("src", 'images/pause.png');

	} else {
		movie.pressStop();	
	}
}

movie.pressPlay = function() {
	movieObj.Play();
	$("#pressButton").attr("src", 'images/pause.png');
}
movie.pressStop = function() {
	movieObj.Stop();
	$("#playButton").attr("src", 'images/play.png');
	$("#clip-playButton").attr("src", 'images/play.png');

	$("#pressButton").attr("src", 'images/pressplay.png');
}


/*************************************** clip controller buttons */

clip.normPlay = function() {		
	if ($("#clip-playButton").attr("src") == "images/play.png") {
		lastplay = curTime;
		movieObj.Play();
		$("#clip-playButton").attr("src", 'images/pause.png');
		$("#playButton").attr("src", 'images/pause.png');
		clip_playing = true;
	} else {
		clip.pressStop();	
	}
}

clip.pressPlay = function() {
	lastplay = curTime;
	moveMovie(curTime);
	movieObj.Play();
	$("#clip-pressButton").attr("src", 'images/pause.png');
	clip_playing = true;
}
clip.pressStop = function() {
	movieObj.Stop();
	var max = $("#c_timeline").slider("option", "max");
	if (curTime > max)
		moveMovie(max-1000);
		
	$("#clip-playButton").attr("src", 'images/play.png');
	$("#playButton").attr("src", 'images/play.png');
	$("#clip-pressButton").attr("src", 'images/pressplay.png');
	clip_playing = false;	
}

function getIndex( myarray, item ) {
	for (var i=0; i<myarray.length; i++) {
		if (myarray[i] == item) {
			return i;
		}
	}
}

clip.previous = function() {	
	//if in a clip
	if (inClip && curClip > 1) {
		//go to prev space (prev clip's out time +1)
		if (clips[curClip-2].outTimeMilli+1 != clips[curClip-1].inTimeMilli) {
			moveMovieAll(clips[curClip-2].outTimeMilli+1);
			
		//if no space, go to prev clip
		} else {
			moveMovieAll(clips[curClip-2].inTimeMilli);		
		}
		
	//if in a space, go to prev clip	
	} else if (!inClip && curClip>0) {
		moveMovieAll(clips[curClip-1].inTimeMilli);
	//if in first space or clip, go to start	
	} else {	
		moveMovieAll(0);
	}
}
clip.next = function() {	
	//if in a clip
	if (inClip && clips[curClip] != undefined) {
		
		//go to next space (next clip's out time +1)
		if (clips[curClip].inTimeMilli != clips[curClip-1].outTimeMilli+1) {
			moveMovieAll(clips[curClip-1].outTimeMilli+1);
			
		//if no space, go to next clip
		} else  {
			moveMovieAll(clips[curClip].inTimeMilli);		
		}
		
	//if in a space, go to next clip	
	} else if (!inClip && clips[curClip] != undefined) {
		moveMovieAll(clips[curClip].inTimeMilli);
		
	//if at the end 
	} else if (clips[clips.length-1] != undefined) {
		if (clips[clips.length-1].outTimeMilli == dur)
			moveMovieAll(clips[clips.length-1].inTimeMilli);
		else
			moveMovieAll(clips[clips.length-1].outTimeMilli+1);
	}

}

clip.goToStart = function() {
	moveMovieAll(getMilliseconds($("#in-time").text()));
}
clip.goToEnd = function() {
	moveMovieAll(getMilliseconds($("#out-time").text()));
}
clip.stepBack = function(step) {
	if (curTime >= step && (curTime-step >= getMilliseconds($("#in-time").text())) )
		moveMovieAll(curTime - step);	
}
clip.stepForward = function(step) {		
	if (  curTime+step <= getMilliseconds($("#out-time").text()) ) 
		moveMovieAll(curTime + step);	
}

clip.lastPlay = function() {		
	moveMovieAll(lastplay);	
}

/*************** making a clip */

clip.newInTime = function() {
	var clip_dur = 0;
	clip_dur = this_location.outTimeMilli-curTime;	
	if (clip_dur <= MIN_CLIP_DUR) {
		alert("Too few frames to make a clip!");
	} else {
		$("#in").css("color","red");	
		$("#in-time").css("color","red");
		$("#clip-duration").css("color","red");	
		$("#makeclip").removeAttr("disabled");
		$("#clip-duration").text(getFormattedTime2(clip_dur));

		//old_in = getMilliseconds($("#in-time").text());
		
		temp_in = getFormattedTime2(curTime);
		$("#in-time").text(temp_in);
				
		setClipTimeline(clip_dur, curTime, this_location.outTimeMilli);
		moveMovieAll(curTime);
				
		/*if (new_flag)
			$("#in-undo").html("<a href='#' onclick='javascript:clip.undoIn(0)'><img src='images/bullet_delete.png' alt='cancel in-time' title='cancel in-time' /></a>");
		else if (inAClip())
			$("#in-undo").html("<a href='#' onclick='javascript:clip.undoIn("+milli_ins[curClip-1]+")'><img src='images/bullet_delete.png' alt='cancel in-time' title='cancel in-time' /></a>");
		else 	
			$("#in-undo").html("<a href='#' onclick='javascript:clip.undoIn("+milli_spaceins[curClip]+")'><img src='images/bullet_delete.png' alt='cancel in-time' title='cancel in-time' /></a>");
		*/
	}	
}


clip.newOutTime = function() {
	if (curTime != getMilliseconds($("#out-time").text())) {
		var clip_dur = 0;

		if (temp_in) {
			clip_dur = curTime-getMilliseconds(temp_in);
		} else {
			clip_dur = curTime-getMilliseconds($("#in-time").text());
		}		
		
		if (clip_dur <= MIN_CLIP_DUR) {
			alert("Too few frames to make a clip!");
		} else {

			$("#out").css("color","red");
			$("#out-time").css("color","red");
			$("#clip-duration").css("color","red");
			$("#makeclip").removeAttr("disabled");		
			$("#clip-duration").text(getFormattedTime2(clip_dur));			
			
			temp_out = getFormattedTime2(curTime);
			$("#out-time").text(temp_out);
			
			setClipTimeline(curTime - getMilliseconds($("#in-time").text()), getMilliseconds($("#in-time").text()), curTime);
			moveMovieAll(curTime);

			/*if (new_flag)
				$("#out-undo").html("<a href='#' onclick='javascript:clip.undoOut("+dur+")'><img src='images/bullet_delete.png' alt='cancel out-time' title='cancel out-time' /></a>");
			else if (inAClip())
				$("#out-undo").html("<a href='#' onclick='javascript:clip.undoOut("+milli_outs[curClip-1]+")'><img src='images/bullet_delete.png' alt='cancel out-time' title='cancel out-time' /></a>");
			else 	
				$("#out-undo").html("<a href='#' onclick='javascript:clip.undoOut("+milli_spaceouts[curClip]+")'><img src='images/bullet_delete.png' alt='cancel out-time' title='cancel out-time' /></a>");
			*/					
		}
	}
}

clip.addPrevSpace = function() {		
		
	//if the current clip's inTime is > 0 and also not equal to the prev clip's outTime
	if (inClip && clips[curClip-1].inTimeMilli > 0 && ( clips[curClip-2]==undefined || clips[curClip-1].inTimeMilli != clips[curClip-2].outTimeMilli) ) {
				
		var newin = 0;
		
		//make clip start = last clip's end	
		if (clips[curClip-2] != undefined) {
			newin = clips[curClip-2].outTimeMilli + 1;
		} else {
			newin = 0;
		}
		temp_in = getFormattedTime2(newin);
		var oldout = getMilliseconds($("#out-time").text())
		var caption_text = $("#caption-text").val();
				
		//set new temp values
		$("#in-time").css("color","red");
		$("#in-time").text(temp_in);
		
		$("#clip-duration").css("color","red");	
		$("#makeclip").removeAttr("disabled");
		$("#makeclip").val("Update Clip");
		
		//$("#caption-text").val(caption_text);				
		$("#clip-duration").text(getFormattedTime2(oldout - newin));		
		
		extend = true;		
		setClipTimeline(oldout - newin, newin, oldout);
		moveMovieAll(curTime);
		//$("#in-undo").html("<a href='#' onclick='javascript:clip.undoIn("+milli_ins[curClip-1]+")'><img src='images/bullet_delete.png' alt='cancel in-time' title='cancel in-time' /></a>");
	}
}

clip.addNextSpace = function() {		
	
	if (inClip && clips[curClip-1].outTimeMilli < dur && (clips[curClip]==undefined || clips[curClip-1].outTimeMilli != clips[curClip].inTimeMilli)) {
		var newout = 0;
		//make clip start = last clip's end	
		if (clips[curClip] != undefined) {
			newout = clips[curClip].inTimeMilli - 1;
		} else {
			newout = dur;
		}
		temp_out = getFormattedTime2(newout);

		var oldin = getMilliseconds($("#in-time").text())
		var caption_text = $("#caption-text").val();
		
		//set new temp values
		$("#out-time").css("color","red");
		$("#out-time").text(temp_out);
		
		$("#clip-duration").css("color","red");	
		$("#makeclip").removeAttr("disabled");
		$("#makeclip").val("Update Clip");
		
		//$("#caption-text").val(caption_text);				
		$("#clip-duration").text(getFormattedTime2(newout - oldin));		
				
		extend = true;		
		setClipTimeline(newout - oldin, oldin, newout);
		moveMovieAll(curTime);	
		//$("#out-undo").html("<a href='#' onclick='javascript:clip.undoOut("+milli_outs[curClip-1]+")'><img src='images/bullet_delete.png' alt='cancel out-time' title='cancel out-time' /></a>");
	}
}

/****** making/updating and saving clips **/

movie.saveClip = function() {
	var caption_text = $("#caption-text").val();
	var clipnum = 0;
		
	// button should be disabled but double check that at least one time has changed, or caption text has changed
	//if (temp_in || temp_out) {   
		resetClipWork();
		
		if (!temp_in) {
			temp_in = $("#in-time").text();
		}
		if (!temp_out) {
			temp_out = $("#out-time").text();
		}		
		
		this_location.inTime = temp_in;
		this_location.outTime = temp_out;
		this_location.inTimeMilli = getMilliseconds(temp_in);
		this_location.outTimeMilli = getMilliseconds(temp_out);
		
		this_location.durationMilli = this_location.outTimeMilli - this_location.inTimeMilli;
		this_location.duration = getFormattedTime2(this_location.durationMilli);
		this_location.caption_text = caption_text;
		
		if($("#makeclip").val() == "Make Clip") { 
			num_clips++;
			
			if (num_clips > 0) {
				//add to clips list
				clips[num_clips-1] = this_location;
				
				//sort clips by inTimeMilli
				clips.sort(function (a,b) { return a.inTimeMilli-b.inTimeMilli});
				
			} else {
				clips = new Array(); //clips
				clips[0] = this_location;
				
				new_flag = 0;
			}
		} else {
			clips[curClip-1] = this_location;
		}
											
		//loadClips();
	 	
		var move_to = getMilliseconds(temp_out) + 1;
		 if (move_to < (dur - MIN_CLIP_DUR) ) {
			 setTimeout("moveMovieAll("+move_to+")", 500);	
		 } else {
			 setTimeout("moveMovie(curTime)", 500);						 
		 }				

		
		temp_in = '';
		temp_out = '';
		
		saveJson();
	//}
}

function deleteClip(clipnum) {
	num_clips--;
	clips.splice(clipnum-1, 1);
	saveJson();	
}

function saveJson() {
	//save file
	json = JSON.stringify(proj);
	
	$.post("include/workflow.php", { task: 'save_json', json:json, pid:proj.id }, function(data) {
		if (!data) { 
			loadClips();
		} else {
			alert(data);
		}
		
	} );
	
	var d = new Date();

	var curr_hour = d.getHours();
	var curr_min = d.getMinutes();
	
	$("#last-saved").text('Last saved at '+curr_hour + ":" + ((curr_min < 10) ? "0" : "") + curr_min);
}

/****************reset UI */

function resetClipWork() {
	$("#in").css("color","black");	
	$("#out").css("color","black");	
	$("#in-time").css("color","black");
	$("#out-time").css("color","black");			
	$("#clip-duration").css("color","black");	
	temp_in = 0;
	temp_out = 0;
	temp_caps = '';	
	
	extend = false;
	$("#makeclip").attr("disabled","disabled");
	$("#in-undo").html("");
	$("#out-undo").html("");

}


