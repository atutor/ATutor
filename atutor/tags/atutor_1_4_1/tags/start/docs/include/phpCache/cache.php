<?php
	require('phpCache.inc.php');

if ( !($et = cache_all()) ) {

	echo time();

	endcache(true, false);
}

echo '<hr>';
echo $et;

?>