<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: 

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../../include/');

require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_THEMES);
global $msg, $_config;
require (AT_INCLUDE_PATH.'header.inc.php');
if(isset($_POST['submit'])) {
    upload_custom_logo();
}

function upload_custom_logo()
{
    global $msg;
    global $_config;
    global $stripslashes;
    global $addslashes;
    
    if (isset($_POST['custom_logo_enabled'])) {
        $_config['custom_logo_enabled'] = $addslashes($_POST['custom_logo_enabled']);
        
        $sql = "REPLACE INTO %sconfig VALUES ('custom_logo_enabled','%d')";
        queryDB($sql, array(TABLE_PREFIX, $_config['custom_logo_enabled']));
    }
    if (isset($_POST['custom_logo_foot_enabled'])) {
        $_config['custom_logo_foot_enabled'] = $addslashes($_POST['custom_logo_foot_enabled']);
        
        $sql = "REPLACE INTO %sconfig VALUES ('custom_logo_foot_enabled','%d')";
        queryDB($sql, array(TABLE_PREFIX, $_config['custom_logo_foot_enabled']));
    }    
    if (isset($_POST['custom_logo_enabled']) && $_POST['custom_logo_enabled'] == 1) {
        $missing_fields = array();
        //custom logo alt text missing
        if (isset($_POST['custom_logo_alt_text']) && $_POST['custom_logo_alt_text'] == '') {
            $missing_fields[] = _AT('custom_logo_alt_text');
        }
        
        //custom logo url missing
        if(isset($_POST['custom_logo_url']) && $_POST['custom_logo_url'] == '') {
            $missing_fields[] = _AT('custom_logo_url');
        } 
        
        if($missing_fields) {
            $missing_fields = implode(', ', $missing_fields);
            $msg->addError(array('EMPTY_FIELDS', $missing_fields));
        }
        
        if ($_FILES['file']['name'] != ''){
            
            //error in the file
            if ($_FILES['file']['error'] == UPLOAD_ERR_FORM_SIZE){
                // Check if filesize is too large for a POST
                $msg->addError(array('FILE_MAX_SIZE', $_config['prof_pic_max_file_size'] . ' ' . _AT('bytes')));
            }
            
            //check if file size is ZERO	
            if ($_FILES['file']['size'] == 0) {
                $msg->addError('IMPORTFILE_EMPTY');
            }
    
            if ($_FILES['file']['error'] || !is_uploaded_file($_FILES['file']['tmp_name'])) {
                $msg->addError('FILE_NOT_SAVED');
            }
            
        }
    
        if (!$msg->containsErrors()) {
            //save custom logo alt text
            $_config['custom_logo_alt_text'] = $addslashes($_POST['custom_logo_alt_text']);
            
            $sql = "REPLACE INTO %sconfig VALUES ('custom_logo_alt_text','%s')";
            $result = queryDB($sql, array(TABLE_PREFIX, $_config['custom_logo_alt_text']));
            
            //save custom logo url
            if (isset($_POST['custom_logo_url']) && $_POST['custom_logo_url'] && (!strstr($_POST['custom_logo_url'],"://"))) {
                $_POST['custom_logo_url'] = "http://".$_POST['custom_logo_url'];
                $_config['custom_logo_url'] = $addslashes($_POST['custom_logo_url']);
            
                $sql = "REPLACE INTO %sconfig VALUES ('custom_logo_url','%s')";
                queryDB($sql, array(TABLE_PREFIX, $_config['custom_logo_url']));
            } else {    
                $_config['custom_logo_url'] = $addslashes($_POST['custom_logo_url']);
            
                $sql = "REPLACE INTO %sconfig VALUES ('custom_logo_url','%s')";
                queryDB($sql, array(TABLE_PREFIX, $_config['custom_logo_url']));
            }
            
            if($_FILES['file']['name'] != '') {
                if (defined('AT_FORCE_GET_FILE')) {
                    $path = AT_CONTENT_DIR.'logos/';
                } else {
                    $path = '../logos/';
                }
            
                if (!is_dir($path)) {
                    @mkdir($path);
                }
            
                //deleting previously existing custom logo
                $path_gif = $path.'custom_logo.gif';
                $path_jpg = $path.'custom_logo.jpg';
                $path_png = $path.'custom_logo.png';
    
                if(file_exists($path_gif)) {
                    unlink($path_gif);
                } else if(file_exists($path_jpg)) {
                    unlink($path_jpg);
                } else if(file_exists($path_png)) {
                    unlink($path_png);
                }
            
                $gd_info = gd_info();
                $supported_images = array();
                if ($gd_info['GIF Create Support']) {
                    $supported_images[] = 'gif';
                }
                if ($gd_info['JPG Support'] || $gd_info['JPEG Support']) {
                    $supported_images[] = 'jpg';
                }
                if ($gd_info['PNG Support']) {
                    $supported_images[] = 'png';
                }

                // check if this is a supported file type
                $filename   = $stripslashes($_FILES['file']['name']);
                $path_parts = pathinfo($filename);
                $extension  = strtolower($path_parts['extension']);
                $image_attributes = getimagesize($_FILES['file']['tmp_name']);

                if ($extension == 'jpeg') {
                    $extension = 'jpg';
                }

                // resize the original but don't backup a copy.
                $width  = $image_attributes[0];
                $height = $image_attributes[1];
                $original_img	= $_FILES['file']['tmp_name'];
                $thumbnail_img	= $path . "custom_logo.". $extension;
            
                $_FILES['file']['name'] = addslashes($_FILES['file']['name']);
            
                $thumbnail_fixed_height = 46; 
                $thumbnail_fixed_width = 153; 

                if ($width > $height && $height > $thumbnail_fixed_height) {
                    $thumbnail_height= $thumbnail_fixed_height;
                    $thumbnail_width = intval($thumbnail_fixed_width * $height / $width);
                    resize_image($original_img, $thumbnail_img, $height, $width, $thumbnail_height, $thumbnail_width, $extension);
                    //cropping
                    //resize_image($thumbnail_img, $thumbnail_img, $thumbnail_fixed_height, $thumbnail_fixed_width, $thumbnail_fixed_height, $thumbnail_fixed_width, $extension, ($thumbnail_width-$thumbnail_fixed_width)/2);
                } else if ($width <= $height && $width>$thumbnail_fixed_width) {
                    $thumbnail_height = intval($thumbnail_fixed_width * $height / $width);
                    $thumbnail_width  = $thumbnail_fixed_width;
                    resize_image($original_img, $thumbnail_img, $height, $width, $thumbnail_height, $thumbnail_width, $extension);
                    //cropping
                    //resize_image($thumbnail_img, $thumbnail_img, $thumbnail_fixed_height, $thumbnail_fixed_width, $thumbnail_fixed_height, $thumbnail_fixed_width, $extension, 0, ($thumbnail_height-$thumbnail_fixed_height)/2);
                } else {
                    // no resizing, just copy the image.
                    // it's too small to resize.
                    copy($original_img, $thumbnail_img);
                }
            }
            
            $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
        } else {
            $msg->addError('FILE_NOT_SAVED');
        }
    } else {
        $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
    }
    header('Location:custom_logo.php');
}

?>
<script>
    $(document).ready(function() {
        toggle_fields();
    });
    function toggle_fields()
    {
        if(document.getElementById("custom_logo_enabled").checked)
            state = 1;
        else
            state = 0;
        
        if(!state) {
            document.getElementById("custom_logo_alt_text").disabled = true;
            document.getElementById("custom_logo_url").disabled = true;
            document.getElementById("file").disabled = true;
        } else {
            document.getElementById("custom_logo_alt_text").disabled = false;
            document.getElementById("custom_logo_url").disabled = false;
            document.getElementById("file").disabled = false;
        }
        
    }
</script>

<form name="customlogoForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
	<div class="input-form" style="width:95%;">
		<div class="row">
			<h3><?php echo _AT('add_custom_logo'); ?></h3>
		</div>
        <div class="row">
            <p><?php echo _AT('custom_logo_instructions'); ?></p>
        </div>
        <div class="row">
            <?php
            if($_config['custom_logo_foot_enabled'] == 1) {
                $num2 = ' checked="checked"';
            } else {
                $num3 = ' checked="checked"';
            }
            ?>
            <?php echo _AT('custom_logo_foot_disable'); ?><br/>
            <input type="radio" id="custom_logo_foot_enabled" name="custom_logo_foot_enabled" value="1" <?php echo $num2; ?> onchange="toggle_fields()" /><label for="custom_logo_foot_enabled"><?php echo _AT('enable'); ?></label>
            <input type="radio" id="custom_logo_foot_disabled" name="custom_logo_foot_enabled" value="0" <?php echo $num3; ?> onchange="toggle_fields()"/><label for="custom_logo_foot_disabled"><?php echo _AT('disable'); ?></label>
		</div>
		
        <div class="row">
            <?php
            if($_config['custom_logo_enabled']) {
                $num1 = ' checked="checked"';
            } else {
                $num0 = ' checked="checked"';
            }
            ?>
            <?php echo _AT('custom_logo_enabled'); ?><br/>
            <input type="radio" id="custom_logo_enabled" name="custom_logo_enabled" value="1" <?php echo $num1; ?> onchange="toggle_fields()" /><label for="custom_logo_enabled"><?php echo _AT('enable'); ?></label>
            <input type="radio" id="custom_logo_disabled" name="custom_logo_enabled" value="0" <?php echo $num0; ?> onchange="toggle_fields()"/><label for="custom_logo_disabled"><?php echo _AT('disable'); ?></label>
		</div>
        
        <div class="row">
            <?php
            $alt_text = $_config['custom_logo_alt_text'];
            ?>
            <label for="custom_logo_alt_text"><?php echo _AT('custom_logo_alt_text'); ?></label><br/>
            <input type="text" id="custom_logo_alt_text" name="custom_logo_alt_text" value="<?php echo $alt_text; ?>" />
		</div>
        
        <div class="row">
            <?php
            $logo_url = $_config['custom_logo_url'];
            ?>
            <label for="custom_logo_url"><?php echo _AT('custom_logo_url'); ?></label><br/>
            <input type="text" id="custom_logo_url" name="custom_logo_url" value="<?php echo $logo_url; ?>" />
		</div>
        
        <div class="row">
            <?php
            if (defined('AT_FORCE_GET_FILE')) {
                $path = AT_CONTENT_DIR.'logos/';
            } else {
                $path = '../logos/';
            }
            
            $path_gif = $path.'custom_logo.gif';
            $path_jpg = $path.'custom_logo.jpg';
            $path_png = $path.'custom_logo.png';
            
            if (is_dir($path) && (file_exists($path_jpg) || file_exists($path_gif) || file_exists($path_png)) ) {
                $logo = "get_custom_logo.php";
            } else {
                if($_SESSION['prefs']['PREF_THEME']=='atspaces') {
                    $logo = AT_BASE_HREF."themes/atspaces/images/atspaces_logo49.jpg";
                } else {
                    $logo = AT_BASE_HREF."images/AT_Logo_1_sm.png";
                }
            }
            echo _AT('use_existing_logo');
            ?>
            <br/>
            <img src="<?php echo $logo; ?>" alt="custom_logo" border="1" style="float: left; margin: 2px;">
			&nbsp;&nbsp;&nbsp; <?php echo _AT('or'); ?>	
            <div class="row" style="float:right;width:40%;">
                <label for="file"><?php echo _AT('upload_custom_logo'); ?></label><br />
                <input type="file" name="file" size="40" id="file" />
            </div>
            <br style="clear: left;">
        </div>
        
        <div class="row buttons">
			<input type= "submit" name="submit" value="<?php echo _AT('save'); ?>" />
		</div>
	</div>
</form>
<br />
<?php
require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>