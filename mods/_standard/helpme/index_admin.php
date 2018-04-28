<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* ATutorSpaces                                                         */
/* https://atutorspaces.com                                             */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
define('AT_INCLUDE_PATH', '../../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_HELPME);
if(isset($_POST['submit_disable'])){
    // update enable/disable setting
    if($_POST['helpme_disable'] =='1'){
        queryDB("DELETE FROM %sconfig WHERE name='%s'", array(TABLE_PREFIX, 'disable_helpme'));
     } else {
        queryDB("INSERT INTO %sconfig (`name`,`value`) VALUES ('%s','%d')", array(TABLE_PREFIX, 'disable_helpme', $_POST['helpme_disable']));
    }
    if(!$error){
        $msg->addFeedback("ACTION_COMPLETED_SUCCESSFULLY");
        header("Location:".$_SERVER['PHP_SELF']);
    }
} else if(isset($_POST['submit_lang'])){
    // Update Help messages
    //  debug($_POST);
    foreach($_POST as $term => $text){
        if($term != "submit_lang"){
        queryDB("UPDATE %slanguage_text SET text='%s' WHERE term='%s'", array(TABLE_PREFIX, $text, $term));
        }
    }
    if(!$error){
        $msg->addFeedback("ACTION_COMPLETED_SUCCESSFULLY");
    }
     
}

require (AT_INCLUDE_PATH.'header.inc.php');
$help_messages = queryDB('SELECT * FROM %slanguage_text WHERE term LIKE "AT_HELP%%"', array(TABLE_PREFIX));

?>
<div class="input-form">
    <fieldset class="group_form">
    <p><?php echo _AT('helpme_text');  ?></p>
    <br/><br />
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="input-form">
            <label for=""><?php echo _AT('helpme_disable'); ?></label><input type="checkbox" name="helpme_disable" value="1" <?php if(!isset($_config['disable_helpme'])){ echo 'checked="checked"'; } ?> />
            <input type="submit" name="submit_disable" value="<?php echo _AT('save'); ?>">
            (<strong><?php if(isset($_config['disable_helpme'])){ 
                echo _AT('disabled');
            }else{ 
                echo _AT('enabled'); 
            }
            ?></strong>)
        </form>
    </fieldset>
</div>
<br />
<div class="input-form">
    <fieldset class="group_form">
    <legend class="group_form"><?php echo _AT('helpme_language'); ?></legend>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="input-form">
            <?php
            foreach($help_messages as $item =>$text){

            echo '<strong>'._AT('helpme_message').': <label for="">'.$text['term'].'</label></strong><br/>'."\n";
            echo '<textarea id="'.$text['term'].'" name="'.$text['term'].'" rows="5" cols="80">'.$text['text'].'</textarea><br /><br />'."\n";
            }
            unset($item);
            unset($text);
            ?>
        <input type="submit" name="submit_lang" value="<?php echo _AT('save'); ?>">
        </form>
    </fieldset>
</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>