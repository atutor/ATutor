<?php
/**
* 
* Tests the default and example plugins for Savant.
* 
* @version $Id: plugins.tpl.php,v 1.1 2004/04/06 17:56:26 joel Exp $
*
*/
?>


<h1>ahref</h1>
<pre>
<?php
	$result = $this->splugin('ahref', 'http://example.com/index.html?this="this"&that="that"', 'Example Link', 'target="_blank"');
	$this->plugin('modify', $result, 'htmlentities nl2br');
?>
</pre>


<h1>checkbox</h1>


<pre>
<?php
	$result = $this->splugin('checkboxes', 'xboxen', $set, 'key1', '0', "<br /><br />\n", 'dumb="dumber"');
	$this->plugin('modify', $result, 'htmlentities nl2br');
 ?>
</pre>

<form><?php echo $result ?></form>


<h1>cycle</h1>


<h2>repeat 1</h2>
<pre>
<?php for ($i = 0; $i < 9; $i++): ?>
	<?php $this->plugin('cycle', $i, array('a', 'b', 'c'), 1) ?><br />
<?php endfor; ?>
</pre>





<h2>repeat 3</h2>


<pre>
<?php for ($i = 0; $i < 9; $i++): ?>
	<?php $this->plugin('cycle', $i, array('a', 'b', 'c'), 3) ?><br />
<?php endfor; ?>
</pre>






<h1>dateformat</h1>
<?php $this->plugin('dateformat', "Aug 8, 1970") ?>




<h1>javascript</h1>
<pre><?php echo htmlentities($this->splugin('javascript', 'path/to/file.js')) ?></pre>

<h1>options</h1>
<pre>
<?php
	$result = $this->splugin('options', $set, 'key1', 'dumb="dumber"');
	$this->plugin('modify', $result, 'htmlentities nl2br');
?>
</pre>
<form><select name="test"><?php echo $result ?></select></form>
 
 
<h1>radios</h1>
<pre>
<?php
	$result = $this->splugin('radios', 'das_radio', $set, 'key1', 'nil', "<br /><br />\n", 'dumb="dumber"');
	$this->plugin('modify', $result, 'htmlentities nl2br');
?>
</pre>
<form><?php echo $result ?></form>
 


<h1>stylesheet</h1>
<pre><?php echo htmlentities($this->splugin('stylesheet', 'path/to/styles.css')) ?></pre>



<?php
// tests the plugin path and a call-by-instance plugin
?>

<h1>Fester</h1>

<?php $this->plugin('fester', 'Gomez') ?><br />
<?php $this->plugin('fester', 'Morticia') ?><br />
<?php $this->plugin('fester', 'Cara Mia!') ?><br />


<?php
// finally, check the map, we should only have one instance of a plugin
?>

_plugin_obj: <pre><?php print_r($this->_plugin_obj) ?></pre>




<!-- end -->