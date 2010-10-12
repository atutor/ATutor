<?php
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

/* edit with correct values and copy to /include/config.inc.php */


/* general config */
define('MAX_FILE_SIZE',	'10485760'); //10M
define('DISABLE_LOCAL',	true);
define('ACTIVE_SYSTEM', '1');

/* mysql config */
define('DB_USER',		'');
define('DB_PASSWORD',	'');
define('DB_HOST',		'localhost');
define('DB_PORT',		'3306');
define('DB_NAME',       'opencaps');
define('ACTIVE_SYSTEM', '1');
define('MODULE_MODE_ON', true);
define('OC_DEBUG_MODE_ON', false);

/*
 * added by Anto
 */
if(!isset($_SESSION['atutor_base_url']))
{
	if(isset($_GET['athome']) && $_GET['athome']!='')
	{
		$_SESSION['atutor_base_url'] = $_GET['athome'];
	} else {
		$_SESSION['atutor_base_url'] = '';
	}
}
$systems[1]['url'] = $_SESSION['atutor_base_url'].'/mods/AtOpenCaps/service.php';
$systems[1]['name'] = 'atutor';
$systems[1]['type'] = 'atutor';
?>