<?php
define('AT_INCLUDE_PATH', '../../include/');
$page = 'file_manager';
$_header_file = 'file_manager_header.php';
$_footer_file = 'file_manager_footer.php';

require('file_manager_top.php');

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('file_manager')."\n";
}
echo '</h3>'."\n";

$msg->printAll();

require('filemanager.php');

closedir($dir);


?>
<script type="text/javascript">
function Checkall(form){ 
  for (var i = 0; i < form.elements.length; i++){    
    eval("form.elements[" + i + "].checked = form.checkall.checked");  
  } 
}
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}

</script>
<?php
	require($_footer_file);
?>