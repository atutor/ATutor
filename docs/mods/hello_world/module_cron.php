<?php
/*******
 * this function named [module_name]_cron is run by the global cron script at the module's specified
 * interval.
 */

function hello_world_cron() {
	global $db;

	debug('yay i am running!');
}

?>