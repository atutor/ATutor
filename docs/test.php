<?php

	define('AT_INCLUDE_PATH', 'include/');

	require(AT_INCLUDE_PATH.'vitals.inc.php');

$onload = 'onload="initEditor();"';
require(AT_INCLUDE_PATH.'header.inc.php');

?>
<script type="text/javascript">
  _editor_url = "<?php echo $_base_path; ?>jscripts/htmlarea/";
  _editor_lang = "en";
</script>
<script type="text/javascript" src="<?php echo $_base_path; ?>jscripts/htmlarea/htmlarea.js"></script>

<script type="text/javascript">
var editor = null;
function initEditor() {
  editor = new HTMLArea("ta");

  var config = editor.config; // this is the default configuration

config.toolbar = [
  ['fontsize', 'space',
   'formatblock', 'space',
"bold", "italic", "underline", "separator",
  "strikethrough", "subscript", "superscript", "separator",
  "copy", "cut", "paste", "space", "undo", "redo" ]
];


  // comment the following two lines to see how customization works
  editor.generate();
  return false;
}
function highlight() {
  editor.surroundHTML('[?]', '[/?]');
}
</script>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="edit" name="edit">

<textarea id="ta" name="ta" rows="20" cols="80"></textarea>

<p />

<input type="submit" name="ok" value="  submit  " />
<input type="button" name="hil" value="  highlight text  " onclick="return highlight();" />


</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>