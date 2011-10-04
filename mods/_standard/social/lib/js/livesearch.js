var xmlHttp;

function showResults(str, search_id, url) {
	if (str.length==0) { 
	  document.getElementById(search_id).innerHTML="";
	  return;
	}
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null) {
	  alert ("Browser does not support HTTP Request");
	  return;
	} 

	url=url+"?q="+str;
	xmlHttp.onreadystatechange=stateChanged;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
} 

function stateChanged() { 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("livesearch").innerHTML=xmlHttp.responseText;
	} 
}

function GetXmlHttpObject() {
	var xmlHttp=null;
	try {
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	} catch (e) {
		// Internet Explorer
		try {
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}