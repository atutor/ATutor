<?php

   function curPageURL() {
      $pageURL = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on")
             ? 'http'
             : 'https';
      $pageURL .= "://";
      $pageURL .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      return $pageURL;
   }

  // Warning - this is n-squared for large files
  function zip_open_and_read_entry($file_name, $zip_file) {
    if ( ! function_exists('zip_open' ) ) {
       echo("<!-- zip_open is not supported in this PHP -->\n");
       return;
    }

    $zip = zip_open($file_name);
    if (! is_resource($zip)) return;

    while ($zip_entry = zip_read($zip)) {
        if ( zip_entry_name($zip_entry) != $zip_file ) continue;
        if (zip_entry_open($zip, $zip_entry, "r")) {
            $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
            zip_entry_close($zip_entry);
            zip_close($zip);
            return $buf;
        }
     }
     zip_close($zip);
   }

$default_desc = str_replace("CUR_URL", str_replace("lms.php", "tool.php", curPageURL()), 
'<?xml version="1.0" encoding="UTF-8"?>
<basic_lti_link xmlns="http://www.imsglobal.org/services/cc/imsblti_v1p0" 
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <title>A Simple Descriptor</title>
  <custom>
    <parameter key="Cool:Factor">120</parameter>
  </custom>
  <launch_url>CUR_URL</launch_url>
</basic_lti_link>');

?>

