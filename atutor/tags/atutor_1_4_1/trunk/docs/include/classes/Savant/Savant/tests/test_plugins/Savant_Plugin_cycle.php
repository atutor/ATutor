<?php

/**
* 
* Example plugin for unit testing.
*
* @version $Id: Savant_Plugin_cycle.php,v 1.1 2004/04/06 17:56:27 joel Exp $
*
*/

require_once 'Savant/Plugin.php';

class Savant_Plugin_cycle extends Savant_Plugin {
	function cycle(&$savant)
	{
		return "REPLACES DEFAULT CYCLE";
	}
}
?>