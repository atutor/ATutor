<?php
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Paste from file tool</title>
        <script src="<?php echo $_base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
        <script type="text/javascript">
        var ATutor = ATutor || {};
        ATutor.mods = ATutor.mods || {};
        ATutor.mods.editor = ATutor.mods.editor || {};

        var errorStringPrefix = '<div id="error"><h4><?php echo _AT('the_follow_errors_occurred'); ?></h4><ul><li>';      
        var errorStringSuffix = '</li></ul></div>';      

        (function () {
            ATutor.mods.editor.insertErrorMsg = function (errorString) {
                jQuery("#subnavlistcontainer", window.opener.document).before(errorStringPrefix + errorString + errorStringSuffix);    
            };

            ATutor.mods.editor.removeErrorMsg = function () {
                jQuery("#error", window.opener.document).remove();
            };
            
            ATutor.mods.editor.pasteFromFile = function (body, title, head) {                
                if (jQuery("#html", window.opener.document).attr("checked") && 
                   (<?php echo $_SESSION['prefs']['PREF_CONTENT_EDITOR']; ?> !== 1)) {
                	window.opener.tinyMCE.activeEditor.setContent(body);
                } else {  
                    jQuery("#body_text", window.opener.document).val(body);
                }
                
                if (title != "") {
                    jQuery("#ctitle",window.opener.document).val(title);
                }
                if (head != "") {
                    jQuery("#head", window.opener.document).html(head);
                    jQuery("#use_customized_head", window.opener.document).attr("checked", true);
                }
            };
        })();
        </script>
    </head>
<?php

class FileData
{
    
    private $title = "";
    private $head = "";
    private $body = "";
    private $errorMsg = "";
    
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
      
    public function getBody() {
        return $this->body;
    }
    
    public function setBody($value) {
        $this->body = $value;
    }

    public function getErrorMsg() {
        return $this->errorMsg;
    }
    
    public function setErrorMsg($value) {
        $this->errorMsg = $value;
    }
    
}

/**
 * Paste_from_file
 * Parses a named uploaded file of html or txt type
 * The function identifies title, head and body for html files,
 * or body for text files.
 * 
 * @return FileData object
 */
function paste_from_file() {
    $fileData = new FileData();
    if ($_FILES['uploadedfile_paste']['name'] == '') {
        $fileData->setErrorMsg(_AT('AT_ERROR_FILE_NOT_SELECTED'));
    } elseif (($_FILES['uploadedfile_paste']['type'] == 'text/plain')
            || ($_FILES['uploadedfile_paste']['type'] == 'text/html') ) {

        $path_parts = pathinfo($_FILES['uploadedfile_paste']['name']);
        $ext = strtolower($path_parts['extension']);

        if (in_array($ext, array('html', 'htm'))) {
            $contents = file_get_contents($_FILES['uploadedfile_paste']['tmp_name']);

            /* get the <title></title> of this page             */
            $start_pos  = strpos(strtolower($contents), '<title>');
            $end_pos    = strpos(strtolower($contents), '</title>');

            if (($start_pos !== false) && ($end_pos !== false)) {
                $start_pos += strlen('<title>');
                $fileData->setTitle(htmlentities_utf8(trim(substr($contents, $start_pos, $end_pos-$start_pos))), true);
            }
            unset($start_pos);
            unset($end_pos);

            $fileData->setHead(htmlentities_utf8(trim(get_html_head_by_tag($contents, array("link", "style", "script")))), true);
            
            $fileData->setBody(htmlentities_utf8(get_html_body($contents)), true); 
        } else if ($ext == 'txt') {
            $fileData->setBody(file_get_contents($_FILES['uploadedfile_paste']['tmp_name']));
        } 
     } else {
        $fileData->setErrorMsg(_AT('AT_ERROR_BAD_FILE_TYPE'));
     }
     return $fileData;
}

if (isset($_POST['submit_file']))
{
	echo '<script type="text/javascript">';
    echo 'ATutor.mods.editor.removeErrorMsg();';
	$fileData = paste_from_file();
	$errorMessage = $fileData->getErrorMsg();
	if ($errorMessage == "") {
       echo 'ATutor.mods.editor.pasteFromFile("'.$fileData->getBody().'","'.$fileData->getTitle().'","'.$fileData->getHead().'");';
    } else {
       echo 'ATutor.mods.editor.insertErrorMsg("'.$errorMessage.'");';
    }
    echo "window.close();";
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
