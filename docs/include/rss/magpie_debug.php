<?php

#echo "Warnings and Notices<p>";
#ini_set('display_errors', 1);

// define path to Magpie files
// and load library
define('MAGPIE_DIR', '/home/kellan/projs/magpierss/');
require_once(MAGPIE_DIR.'rss_fetch.inc');

define('MAGPIE_DEBUG', 2);
// flush cache quickly for debugging purposes, don't do this on a live site
define('MAGPIE_CACHE_AGE', 2);


$url = "http://localhost/rss/laughingmeme.rdf";

$rss = fetch_rss( $url );

echo "</p><p>RSS Information</p>";
echo "<p><b>Displaying RSS:</b> $url";

if ( !$rss ) {
	echo ("Error: " . magpie_error() );
}
else {
	$rss->show_channel();
	$rss->show_list();
}

?>
