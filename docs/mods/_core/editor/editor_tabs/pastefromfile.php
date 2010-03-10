<?php
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Untitled Document</title>
        <script src="<?php echo $_base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
        <script type="text/javascript">
            function pasteFromFile(title) {
                jQuery("#ctitle",window.opener.document).val(title);
            }
        </script>
    </head>
<?php

class FileData
{
    
    private $title = "";
    private $head = "";
    private $useHead = 0;
    private $body = "";
    
    public function getTitle() {
        return $this->title;
    }
    
    public function setTitle($value) {
        $this->title = $value;
    }

    public function getHead() {
        return $this->head;
    }
    
    public function setHead($value) {
        $this->head = $value;
    }
    
    public function getUseHead() {
        return $this->useHead;
    }
    
    public function setUseHead($value) {
        $this->useHead = $value;
    }
    
    public function getBody() {
        return $this->body;
    }
    
    public function setBody($value) {
        $this->body = $value;
    }
    
}

function paste_from_file() {
    $fileData = new FileData();
    
    if ($_FILES['uploadedfile_paste']['name']
        && (($_FILES['uploadedfile_paste']['type'] == 'text/plain')
            || ($_FILES['uploadedfile_paste']['type'] == 'text/html')) ) {

        $path_parts = pathinfo($_FILES['uploadedfile_paste']['name']);
        $ext = strtolower($path_parts['extension']);

        if (in_array($ext, array('html', 'htm'))) {
            $contents = file_get_contents($_FILES['uploadedfile_paste']['tmp_name']);

            /* get the <title></title> of this page             */

            $start_pos  = strpos(strtolower($contents), '<title>');
            $end_pos    = strpos(strtolower($contents), '</title>');

            if (($start_pos !== false) && ($end_pos !== false)) {
                $start_pos += strlen('<title>');
                $fileData->setTitle(trim(substr($contents, $start_pos, $end_pos-$start_pos)));
            }
            unset($start_pos);
            unset($end_pos);

            $fileData->setHead(trim(get_html_head_by_tag($contents, array("link", "style", "script"))));
            if (strlen($fileData->getHead()) > 0)   
                $fileData->setUseHead(1);
            
            $fileData->setBody(get_html_body($contents)); 
        } else if ($ext == 'txt') {
            $fileData->setBody(file_get_contents($_FILES['uploadedfile_paste']['tmp_name']));
        }
    }
    return $fileData;
}

if (isset($_POST['submit_file']))
{
	$fileData = paste_from_file();
	echo '<script type="text/javascript">';
	echo 'pasteFromFile("'.$fileData->getTitle().'")';
	echo '</script>';
}

?>
    <body>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
	       <input type="file" name="uploadedfile_paste" id="uploadedfile" class="formfield" size="20" />
           <input type="submit" name="submit_file" id="submit_file" value="<?php echo _AT('paste'); ?>" class="button" />
        </form>
    </body>
</html>
