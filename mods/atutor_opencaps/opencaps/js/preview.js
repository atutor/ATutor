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

var layout = 0;

$(document).ready(function () {
	$.get("include/workflow.php", { task: 'get_json' }, function(json) {
		if (json) {
			proj = JSON.parse(json);			
			startPreview();
		 }
	});	
});

function startPreview() {
	$("#preview-tab").addClass('current');
	layout = proj.layout;
	if (layout == undefined)
		layout = 0;
	
	//set selected layout	
	$('input[name="layout"]')[layout].checked = true;
	
	embedPreview();
	
}

function saveLayout() {

	for(var i=0; i<document.forms[0].layout.length; i++) {
		if (document.forms[0].layout[i].checked == true ) {
			proj.layout = document.forms[0].layout[i].value;
			break;
		}
	}
	
	json = JSON.stringify(proj);
	$.get("include/workflow.php", { task: 'save_json', json:json, pid:proj.id }, function(data) {		
		startPreview();
	});
}

 
function QTStatus() {
	var status = movieObj.GetPluginStatus();

	if (status == "Complete") {
		window.clearInterval(QTinterval);
		setDisplay();
	}
}

function embedPreview() {
	if (proj.media_width <= 0) {
		layout = 2;
		proj.media_width = 250;
		$('input[name="layout"]')[layout].checked = true;
	}
	
	//preview on the server creates the qttext and smil files
	$.get("include/workflow.php", { task:'preview', layout: layout }, function(obj_height) {
		
		if (obj_height) {			
			smil_loc = "projects/"+proj.id+"/smil_"+layout+".mov";
	
			//embed smil file
			var embed = '<object width="'+(parseInt(proj.media_width)+80)+'" height="'+obj_height+'" classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0" id="mymovie">';
			embed += '<param name="src" value="'+smil_loc+'" /><param name="enablejavascript" value="true" />';
			embed += '<param name="postdomevents" value="true" /><param name="autoplay" value="false" />';
			embed += '<param name="cache" value="false" />';
			embed += '<embed src="'+smil_loc+'" width="'+(parseInt(proj.media_width)+80)+'" height="'+obj_height+'" cache="false" pluginspage="http://www.apple.com/quicktime/download/" name="mymovie" enablejavascript="true" id="mymovie_embed" postdomevents="true" autoplay="false" /></object>';
			$("#movie-container").html(embed);			
		}			
	});	
	
}