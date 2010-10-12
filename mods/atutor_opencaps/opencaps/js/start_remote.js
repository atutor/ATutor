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

$(document).ready(function () {
	var rid = window.location.search.substring(3,4);
	var page = window.location.search.substring(10);
	
	if (page == '')
		page = 1;

	//create start tabs, local and remote	
	$.get("include/workflow.php", { task: 'get_tabs' }, function(data) {
		$("#start-tabs").html(data);	
		$("#remote-"+rid).addClass('current');
	});	
	
	$.get("include/workflow.php", { task: 'print_projs_remote', rid: rid, page: page }, function(data){		
		$("#projects").html(data);	
	});
	
});


function validateOpenForm() {
	var myform = document.forms[0];
	var errs = '';
	var chosen = null;
	
	//make sure a project was selected
	chosen = myform.proj.value;

	projlen = myform.proj.length;  //if array
	
		for (i = 0; i <projlen; i++) {
		if (myform.proj[i].checked) {
			chosen = myform.proj[i].value;
		}
	}	
	
	if (chosen == null) 
		errs += 'You must choose a project to open.\n';

	if (errs != '') {
		alert(errs);
		return false;
	} else {
		return true;
	}
}

function processOpenRemote() {	
	var myform = document.forms[0];

	//get chosen project id
	chosen = myform.proj.value;

	projlen = myform.proj.length;  //if array	
	for (i = 0; i<projlen; i++) {
		if (myform.proj[i].checked) {
			chosen = myform.proj[i].value;
		}
	}	
	
	//open project
	$.get("include/workflow.php", { task: 'open_proj_remote', pid: chosen  }, function(data){		
		if (!data) {
			window.location = "editor.php";
		} else {
			alert(data);
		}
	});
}


/*for(var i=0; i<document.forms[1].pkg_url.length; i++) {
if (document.forms[1].pkg_url[i].checked == true ) {
	var pkg_url = document.forms[1].pkg_url[i].value;
	break;
}

var pkg_url = document.forms[1].pkg_url.value;

$.get("include/workflow.php", { task:'open_proj_pkg', xml:pkg_url }, function(data){
if (!data) {
	window.location = "editor.php";
} else {
	alert(data);
}
});	}*/