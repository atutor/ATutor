<?php
/*////////////////////////////////////////////////////////
        global vars: (these may be shared)
//////////////////////////////////////////////////////*/

define('MAX_FILE_SIZE',	'10485760'); //10M

// store the Caption Formats available
$ccFormats = Array();


/*////////////////////////////////////////////////////////
        global settings
//////////////////////////////////////////////////////*/
$rosettaCCSettings = Array();
$rosettaCCSettings['fileMaxSize'] = 5000000;
$rosettaCCSettings['uploadDir'] = 'imported';
$rosettaCCSettings['ccformats'] = './include/classes/ccformats';

// define server OS
define('SERVER_OS','linux'); 

// Absolute URL
define('ROSETTA_WEB_PATH','http://filoante.com/capscribe/service');

// Root folder
define('ROSETTA_ROOT_PATH','/capscribe/service'); // linux server

// determine if the server has get_magic_quotes_gpc on 
if ( get_magic_quotes_gpc() == 1 ) 
{
	$rosettaCCSettings['get_magic_quotes_gpc'] = 1;
	//echo '<br/>get_magic_quotes_gpc = 1';
} else {
	$rosettaCCSettings['get_magic_quotes_gpc'] = 0;
	//echo '<br/>get_magic_quotes_gpc != 1';
}



?>