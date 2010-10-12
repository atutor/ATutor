<?php

// include AT vitals
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_OPEN_CAPS);

// load ATutor-OpenCaps Module Vitals 
include_once('include/vitals.inc.php');


echo '<br/>AT_CONTENT_DIR: '.AT_CONTENT_DIR;
echo '<br/>AT_BASE_HREF: '.AT_BASE_HREF;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>

<body>
<p>Test Service</p>
<h1>getMedia: $_GET</h1>
<p><a href="<?php echo AT_BASE_HREF; ?>/mods/AtOpenCaps/service.php?id=1&action=getMedia">/mods/AtOpenCaps/service.php?id=1&amp;action=getMedia</a></p>
<p>&nbsp;</p>
<h1>putCaps: $_POST</h1>
<form name="form1" id="form1" method="post" action="<?php echo AT_BASE_HREF; ?>/mods/AtOpenCaps/service.php">
  <p>Id: 
    <input name="id" type="text" id="id" value="1" />
  </p>
  <p> action: 
    <input name="action" type="text" id="action" value="putCaps" />
  </p>
      <p>
      <strong>Width:</strong> <input name="width" id="width" value="320" type="text" size="4"/>
       <strong>Height:</strong> <input name="height" id="height" value="240" type="text" size="4"/>
      </p>

  <p> cc: <br />
    <textarea name="cc" cols="60" rows="10" id="cc">Some data so save in the caption file
uno more line</textarea>
  </p>
  <p>
    <input type="submit" name="Submit" value="Save Data" />
  </p>
</form>
<p>&nbsp;</p>
</body>
</html>

