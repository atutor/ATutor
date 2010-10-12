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


function loadProject() {
	$.get("include/workflow.php", { task: 'get_json' }, function(json){
		alert(json);

		if (json) {
			proj = JSON.parse(json);
		 }
	});
}

/*************************************** listeners */
function myAddListener(obj, evt, handler, captures) {
	if (document.addEventListener) 
		obj.addEventListener(evt, handler, captures);
	else
		obj.attachEvent('on' + evt, handler);
}
function RegisterListener(eventName, objID, embedID, listenerFcn) {
	var obj = document.getElementById(objID);
	if ( !obj )
		obj = document.getElementById(embedID);
	if ( obj )
		myAddListener(obj, eventName, listenerFcn, false);
}

/*************************************** utils */
function sortNumber(a, b) {
	return a - b;
}

function calcTime(time) {
	var numhours = 0;
	var nummins = 0;
	var numsecs = 0;
	var nummils = 0;
		
	var t = time/movieObj.GetTimeScale();  

	if (t > onehr) {
		numhours = parseInt(t/onehr);
		t = t-(numhours*onehr);
	}
	if (t > onemin) {
		nummins = parseInt(t/onemin);
		t = t-(nummins*onemin);
	}

	//converts xx.xxxxxxxxxxx... to xxxxx since js doesn't round to a decimal
	t = Math.round(t*1000)+1;

	/*if (t > 0)
		t = t+1;*/

	var t2 = new String(t);
	if (t<1000) { //less than a sec
		numsecs = 0;
		nummils = t;
	} else if (t<10000) { //less than 10 secs
		numsecs = t2.substr(0,1);
		nummils = t2.substr(1);
	} else {
		numsecs = t2.substr(0,2);
		nummils = t2.substr(2);
	}

	nummils = nummils + "000";
	nummils = nummils.substr(0,3);

	return padDigits(numhours,2) + ':' + padDigits(nummins, 2) + ':' + padDigits(numsecs, 2) + '.' + nummils;
}
function getFormattedTime(gt) {
	var total = gt/movieObj.GetTimeScale();

	var gms = Math.round(total * 1000) % 1000;
	
	total = Math.floor(total);
	
	var gs = total % 60;
	total = Math.floor(total / 60);	
	
	var gm = total % 60;
	gh = Math.floor(total / 60);	
	
	var code = padDigits(gh, 2) + ":" + padDigits(gm, 2) + ":" + padDigits(gs, 2) + "." + padDigits(gms, 3);
	return code;
}

function getMilliseconds(ft) {
	var t = ft.split(':');
	
	//convert hours, minutes, seconds all to milliseconds and add
	var millis = (t[0]*60*60*1000) + (t[1]*60*1000) + (t[2]*1000);

	//millis = millis*(movieObj.GetTimeScale());
	
	return Math.round(millis);
}


function getFormattedTime2(total) {

	var milli = total % 1000;
	var sec = Math.floor(total/1000);	
	var min = 0;
	var hr = 0;
	
	if (sec > 59) {
		var bigsec = sec;
		sec = bigsec % 60;
		min = Math.floor(bigsec / 60);
		
		if (min > 59) {
			bigmin = min
			min = bigmin % 60;
			hr = Math.floor(bigmin / 60);
		}
	} 
	
	return padDigits(hr, 2) + ":" + padDigits(min, 2) + ":" + padDigits(sec, 2) + "." + padDigits(milli, 3);	
}

function getClipDuration(tin, tout) {	
	tin = getMilliseconds(tin);
	tout= getMilliseconds(tout);
	
	var total = tout-tin;	
	return getFormattedTime2(total);	
}

function addMilli(t) {
	t = getMilliseconds(t) + 1;
	return getFormattedTime2(t);
}

function subMilli(t) {
	t = getMilliseconds(t) - 1;
	return getFormattedTime2(t);
}

function roundNum(n) {
	return Math.round(n * 1000) / 1000;
}

//adds missing zeros before number if ness
function padDigits(n, totalDigits) { 
	n = n.toString(); 
	var pd = ''; 
	if (totalDigits > n.length) 	{ 
		for (i=0; i < (totalDigits-n.length); i++) { 
			pd += '0'; 
		} 
	} 
	return pd + n.toString(); 
} 

/*function inASpace() {
	if ( ($("#clip-name").text() == "Space after Clip "+ curClip) || ($("#clip-name").text() == "Space before Clip "+ (curClip+1))) 
		return true;
	else 
		return false;
}*/

function inAClip() {
	if ( $("#clip-name").text() == "Clip "+ curClip ) 
		return true;
	else 
		return false;
}