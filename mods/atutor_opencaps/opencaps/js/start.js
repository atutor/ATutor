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
	$("#start-entry").hide();
	$("#open-entry").hide();	
		
	//create start tabs, local and remote	
	$.get("include/workflow.php", { task: 'get_tabs' }, function(data) {
		$("#start-tabs").html(data);	
		$("#home").addClass('current');
	});	
	
	if(document.location.search.substring(1, 5) == 'page')
		startOpen();	
	else if (document.location.search.substring(1, 7) == 'submit')
		startNew();
		
});


function startNew() {
	$("#open-entry").hide();
	$("#start-entry").show();
}
function startOpen() {
	$("#start-entry").hide();
	
	if (document.location.search != '') {
		$.get("include/workflow.php", { task: 'print_projs', page: document.location.search.substring(6) }, function(data){		
			$("#projects").html(data);	
		});
	} else {
		$.get("include/workflow.php", { task: 'print_projs' }, function(data){		
			$("#projects").html(data);	
		});
	}
	
	$("#open-entry").show();	
}	


/*
 * start project
 */
/*function processNew() {	
		
	$.get("include/workflow.php", { task:'new_proj', name:name, media_url:media_url, media_file:media_file, captions:captions }, function(data){
		if (!data) {
			window.location = "editor.php";
		} else {
			alert(data);
		}
	});	

}*/

function validateNewForm() {
	var myform = document.forms[0];
	var errs = '';
	
	if (myform.projname.value == '') {
		errs = 'Project name cannot be empty.\n';
	} else {
		var projname = myform.projname.value;
	}
	
	if ((myform.media_url.value == '' || myform.media_url.value == 'http://') && myform.media_file.value == '') {
		errs += 'Video file cannot be empty.\n';
	}
	
	if (errs != '') {
		alert(errs);
		startNew();
		if (projname) 
			myform.projname.value = projname;	
		return false;
	} else {
		document.forms[0].submit();
		//return true;
	}
}	


function processOpen() {	
	var myform = document.forms[1];

	//get chosen project id
	chosen = myform.proj.value;

	projlen = myform.proj.length;  //if array	
	for (i = 0; i<projlen; i++) {
		if (myform.proj[i].checked) {
			chosen = myform.proj[i].value;
		}
	}	
	
	//open project
	$.get("include/workflow.php", { task: 'open_proj', pid: chosen }, function(data){		
		if (!data) {
			window.location = "editor.php";
		} else {
			alert(data);
		}
	});
}

function validateOpenForm() {
	var myform = document.forms[1];
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
		startOpen();		
		return false;
	} else {
		return true;
	}
}	

function confirmDelete(proj_id, name) {
	if (confirm("Are you sure you want to delete the project '"+name+"'?")) {
		$.get("include/workflow.php", { task:'proj_delete', pid: proj_id }, function(data){
			if (data) {
				alert(data);
			} 
		});
		startOpen();
	}
}	