<?php 
  global $savant;
  
  require('subscribe_button.php');
  $box_content = '<a href="'.$sub_href.'">'.$sub_button.'</a>';
  
  
  $savant->assign('dropdown_contents', $box_content);
  
  $savant->assign('title', _AT('announcement_subscription'));
  $savant->display('include/box.tmpl.php');
?>
