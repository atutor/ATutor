<?php
define('AT_INCLUDE_PATH', '../../include/');
$_header_file = 'file_manager_header.php';
$_footer_file = 'file_manager_footer.inc.php';
require('file_manager_top.php');
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