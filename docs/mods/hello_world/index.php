<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');


require (AT_INCLUDE_PATH.'header.inc.php');
?>

<p>Hello Mr. World!</p>

<ol>
	<li>How do we deal with custom language in a module? in particular the page titles.</li>

	<li>Have to make it so that when a module is removed from <code>$_modules</code> it doesn't show up on Home and Main Navigation.</li>

	<li>How to include the vitals file automatically?</li>
</ol>

<h3>possible table structure:</h3>
<pre>module_name                     (key, dir name)

realname                        (module's English name)

version                         (module's version number)

enabled/disabled/not_installed	(whether or not this module is available)

privilege                       (the privilege bit associated with this priv)
</pre>

<h3>what this module's vitals file looks like</h3>
<?php highlight_file('./vitals.inc.php'); ?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>