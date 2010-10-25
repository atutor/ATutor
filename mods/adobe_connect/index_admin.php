<?php

define('AT_INCLUDE_PATH', '../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_ADOBE_CONNECT);


require_once('lib/lib.php');


// save data
if (!empty($_POST['send'])) {

    $tmpconfig->adobe_connect_adminpass = $addslashes($_POST['adobe_connect_adminpass']);
    $tmpconfig->adobe_connect_adminuser = $addslashes($_POST['adobe_connect_adminuser']);
    $tmpconfig->adobe_connect_folderid = (int)$_POST['adobe_connect_folderid'];
    $tmpconfig->adobe_connect_host = $addslashes($_POST['adobe_connect_host']);
    $tmpconfig->adobe_connect_port = $addslashes($_POST['adobe_connect_port']);

    foreach ($tmpconfig as $value) {
        if (empty($value)) {
            $msg->addError('adobe_connect_fillall');
        }
    }
    
    if (!$msg->containsErrors()) {

        $config = getAdobeConnectConfig();

        $inserts = array();
        $updates = array();
        foreach ($tmpconfig as $name => $value) {

            if (!empty($config->$name)) {
                $updates[] = " value = '$value' WHERE name = '$name'";
            } else {

                $inserts[] = " VALUES ('$name', '$value')";

            }
        }

        if (!empty($inserts)) {

            $insertsql = "INSERT INTO ".TABLE_PREFIX."config (name, value) ";
            foreach ($inserts as $insert) {
                $result = mysql_query($insertsql.$insert, $db);
                if (!$result) {
                    die('db insert problem');
                }
            }
        }

        if (!empty($updates)) {
            foreach ($updates as $update) {
                $updatesql = "UPDATE ".TABLE_PREFIX."config SET ";
                $result = mysql_query($updatesql.$update, $db);
                if (!$result) {
                    die('db update problem');
                }
            }
        }

        if (!$msg->containsErrors()) {
            $msg->addFeedback('adobe_connect_saved');
        }

        header('location: '.$_SERVER["php_self"]);
    }
}


// print header
require (AT_INCLUDE_PATH.'header.inc.php');


// get config
if (!empty($_POST['send'])) {
    foreach ($tmpconfig as $name => $tmp) {
        $acc->$name = $tmp;
    }
} else if (!$acc = getAdobeConnectConfig()) {
    $acc->adobe_connect_adminpass = '';
    $acc->adobe_connect_adminuser = '';
    $acc->adobe_connect_folderid = '';
    $acc->adobe_connect_host = '';
    $acc->adobe_connect_port = '';
}

// print form
echo '<form method="post" action="'.$_SERVER["php_self"].'">';
echo '<div class="input-form">';
foreach ($acc as $name => $value) {
    echo '<div class="row">';
    echo '<div class="required" title="Required Field">*</div>';
    echo '<label for="'.$name.'">'._AT($name).'</label><br/>';
    echo '<input type="text" name="'.$name.'" value="'.stripslashes($value).'" size="35" maxlength="70" />';
    echo '</div>';
}

echo '<div class="row buttons">';
echo '<input type="submit" name="send" value="'._AT("send").'" />';
echo '</div>';
echo '</form>';


// print footer
require (AT_INCLUDE_PATH.'footer.inc.php');


?>
