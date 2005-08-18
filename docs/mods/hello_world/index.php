<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');


require (AT_INCLUDE_PATH.'header.inc.php');
?>

<p>Hello Mr. World!</p>

<ol>
	<li>How do we deal with custom language in a module? in particular the page titles.</li>
</ol>


<h3>What the <code>module.php</code> file looks like</h3>
<?php highlight_file('./module.php'); ?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>