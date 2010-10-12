<?php
/****************************************************************/
/* Atutor-OpenCaps Module						
/****************************************************************/
/* Copyright (c) 2010                           
/* Written by Antonio Gamba						
/* Adaptive Technology Resource Centre / University of Toronto
/*
/* This program is free software. You can redistribute it and/or
/* modify it under the terms of the GNU General Public License
/* as published by the Free Software Foundation.
/****************************************************************/

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

//
// load ATutor-OpenCaps Module Vitals 
include_once('include/vitals.inc.php');


$mediaFile ='';
$captionFile = '';
$width = '320';
$height = '240';

if (isset($_GET['mediaFile']) && $_GET['mediaFile']!='')
{
	$mediaFile = $_GET['mediaFile'];
	
	if (isset($_GET['captionFile']) && $_GET['captionFile']!='')
	{
		$captionFile = $_GET['captionFile'];
	}
	
	if (isset($_GET['width']) && $_GET['width']!='')
	{
		$width = $_GET['width'];
	}
	
	if (isset($_GET['height']) && $_GET['height']!='')
	{
		$height = $_GET['height'];
	}
}

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<script type="text/javascript" src="<?php echo AT_BASE_HREF ?>mods/AtOpenCaps/flowplayer/flowplayer-3.2.2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo AT_BASE_HREF ?>mods/AtOpenCaps/module.css">
	<title>AT Media Player</title>
</head>
<body>

<div id="ATOC_playerPreview" align="center" style="width: <?php echo $width ?>;height: <?php echo $height ?>;">
<?php
$playerEmbed = '
<a id="ATmediaPlayer">
	<img src="'.AT_BASE_HREF.'mods/AtOpenCaps/images/poster.jpg"
	style="opacity: 1.0; " />
</a>

<script language="JavaScript">

$f("ATmediaPlayer", "'.AT_BASE_HREF.'mods/AtOpenCaps/flowplayer/flowplayer-3.2.2.swf", 
{

		';
	
$playerEmbed .= "

	clip: 
	{
		url: '".$mediaFile."',
		

		// this is the Timed Text file with captions info
		captionUrl: '".$captionFile."'
	},
	plugins:  
	{

		captions: {
			url: '".AT_BASE_HREF."mods/AtOpenCaps/flowplayer/flowplayer.captions-3.2.1.swf',

			// pointer to a content plugin (see below)
			captionTarget: 'content'
		},

		// configure a content plugin to look good for our purpose
		content: {
			url:'".AT_BASE_HREF."mods/AtOpenCaps/flowplayer/flowplayer.content-3.2.0.swf',
			bottom: 25,
			width: '80%',
			height: 60,
			backgroundColor: 'transparent',
			backgroundGradient: 'low',
			//borderRadius: 1,
			border: 0,
			textDecoration: 'outline',

			style: {
			    'body': {
				fontSize: '22',
				fontFamily: 'Arial',
				textAlign: 'center',
				color: '#FFFFFF'
			    }
		    }
		}
	}
});

</script>
"; // end stript

echo $playerEmbed;
?>

<?php

?>
</div>
<div id="ATOC_preview_code">
<h3><?php echo _AT('atoc_htmlCode'); ?>:</h3>
	<form name="playerCode" id="playerCode" method="post" action="">
	  <textarea name="flowPlayerCode" cols="40" rows="5" id="flowPlayerCode">

<script type="text/javascript" src="<?php echo AT_BASE_HREF ?>mods/AtOpenCaps/flowplayer/flowplayer-3.2.2.min.js"></script>
<div align="center" style="width: <?php echo $width ?>;height: <?php echo $height ?>;">
<?php
echo $playerEmbed;
?>
</div>
	  </textarea>
	</form>
</div>

</body>
</html>

	