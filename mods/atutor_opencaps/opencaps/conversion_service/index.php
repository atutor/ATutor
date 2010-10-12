<?php

// Load configuration file
include('include/config.inc.php');

// Core classes
include('include/classes/core_Caption_class.php');
include('include/classes/core_CaptionCollection_class.php');
include('include/classes/core_CaptionFormat_class.php');
include('include/classes/core_ConversionManager_class.php');
include('include/classes/core_CcService_class.php');

// Vitals 
include('include/classes/static_CcUtilVital_class.php');
include('include/classes/static_TimeUtil_class.php');
include('include/classes/static_TxtFileTools_class.php');


// get values from GET method

if ( (isset($_GET['cc_url'])) && (isset($_GET['cc_target'])) && (isset($_GET['cc_result'])) && (isset($_GET['cc_name'])))
{
	//$myCcService = new CcService(0,'http://localhost/php/_myphp/__phpOO/RosettaCaption/imported/Abbott_Costello_captions.Jsrt','JSONcc','Caption_service_test.txt')
$myCcService = new CcService($_GET['cc_result'],$_GET['cc_url'],$_GET['cc_target'],$_GET['cc_name']);	

} else { 

$htmlForm = '
<html>
<head>
<title>RosettaCCService</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<form name="form1" method="get" action="">
  <strong>cc_url</strong> - CaptionUrl: 
  <input name="cc_url" type="text" id="cc_url" value="" size="40" />
  <br>
  <strong>cc_result - Type of Result: </strong> 
  <input name="cc_result" type="text" id="cc_result" value="0" size="3" maxlength="1" />
  if 0 returns a caption string, if = 1 returns the URL where target caption is 
  found<br>
  <strong>cc_target</strong> - Type of Target Caption: 
  <select name="cc_target" id="cc_target">
    <option value="0">---</option>
    <option value="DFXP">DFXP</option>
    <option value="DvdStl">DvdStl</option>
    <option value="JSONcc" selected>JSONcc</option>
    <option value="MPlayer">MPlayer</option>
    <option value="MicroDvd">MicroDvd</option>
    <option value="QTSMIL">QTSMIL</option>
    <option value="QTtext">QTtext</option>
    <option value="RealText">RealText</option>
    <option value="Sami">Sami</option>
    <option value="Scc">Scc</option>
    <option value="SubRipSrt">SubRipSrt</option>
    <option value="SubViewer">SubViewer</option>
  </select>
  <br>
  <strong>cc_name</strong> - Name of the Caption: 
  <input name="cc_name" type="text" id="cc_name" value="Name_of_the_caption.___" />
  <br>
  <input type="submit" name="Submit" value="Submit">
</form>
</body>
</html>
';
echo $htmlForm;
}
?>