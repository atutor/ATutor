var xmlHttp
var elementToUpdate = "";

function openCcProject(projectId)
{ 
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	 {
	 alert ("Browser does not support HTTP Request")
	 return
	 }
	
	// set html element to update
	elementToUpdate = "ATOC_editor";
	
	// set script URL
	var url="mods/AtOpenCaps/include/open_caps_iframe.php"
	
	// set url par name
	url=url+"?id="+projectId
	
	//url=url+"&sid="+Math.random()
	xmlHttp.onreadystatechange=stateChanged 
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}


function openFileManager()
{ 
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	 {
	 alert ("Browser does not support HTTP Request")
	 return
	 }
	
	// set html element to update
	elementToUpdate = "ATOC_fileManager";
	
	// set script URL
	//var url="mods/_core/file_manager/index.php?framed=1&popup=0"
	var url="mods/AtOpenCaps/include/iframe_fileManager.php"
	//var url="http://localhost/php/_myphp/__phpOO/ATutor_Devel/ATutor-2.0-beta1/mods/AtOpenCaps/include/iframe_fileManager.php"
	//alert(url)

	// set url par name
	//url=url+"?action=fileManager"
	
	xmlHttp.onreadystatechange=stateChanged 
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

/*
	update display
*/
function stateChanged() 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		// set name of <div> id where to show the result
		document.getElementById(elementToUpdate).innerHTML=xmlHttp.responseText;
	} 
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 xmlHttp=new XMLHttpRequest();
 }
catch (e)
 {
 //Internet Explorer
 try
  {
  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return xmlHttp;
}