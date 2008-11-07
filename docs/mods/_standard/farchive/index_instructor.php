<?php
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');



$sql = "SELECT forum_id, title FROM ".TABLE_PREFIX."forums";
$result = mysql_query($sql) or die(mysql_error());

if (mysql_num_rows($result) == 0) {
    $msg->addInfo('NO_FORUMS');
    require (AT_INCLUDE_PATH.'header.inc.php');
    exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form name='forumExp' action='send_zip_archive.php' method='post'>
    <div class='input-form'>
        <div class='row'>
            <h3><?php echo _AT('farchive_select_forum'); ?></h3>
        </div>
        <div class='row'>
            <select name='selForum' id='selForum'> 
        

                <?php

                $course_id = $_SESSION['course_id'];

                // ---Get course forums for the specific course id

                $courseOptions = "";
                $sql = "SELECT forum_id FROM ".TABLE_PREFIX."forums_courses WHERE course_id='".$course_id."'";
                $result = mysql_query($sql) or die(mysql_error());
                
                while ($fid = mysql_fetch_row($result)) {

                    $sql = "SELECT title FROM ".TABLE_PREFIX."forums WHERE forum_id='".$fid[0]."'";
                    $result2 = mysql_query($sql) or die(mysql_error());

                    while ($row = mysql_fetch_row($result2)) {
                        $courseOptions .= "<option value='".$fid[0]."'>".$row[0]."</option>";
                    }
                }

                if ($courseOptions != "") {
                    print "<optgroup label='Course Forums'>";
                    print $courseOptions;
                    print "</optgroup>";
                }


                // ---Get group forums for the specific course id

                $groupOptions = "";

                // get type_id(s) for this course
                $sql = "SELECT type_id FROM ".TABLE_PREFIX."groups_types WHERE course_id='".$course_id."'";
                $result = mysql_query($sql) or die(msyql_error());

                while ($type_id = mysql_fetch_row($result)) {

                    // get group_id(s) for each type
                    $sql = "SELECT group_id, title FROM ".TABLE_PREFIX."groups WHERE type_id='".$type_id[0]."'";
                    $result2 = mysql_query($sql) or die(mysql_error());

                    while ($group_id = mysql_fetch_row($result2)) {
                        
                        // get forum_id for each group
                        $sql = "SELECT forum_id FROM ".TABLE_PREFIX."forums_groups WHERE group_id='".$group_id[0]."'";
                        $result3 = mysql_query($sql) or die(mysql_error());

                        while ($fid = mysql_fetch_row($result3)) {

                            // get forum title for this fid
                            $sql = "SELECT title FROM ".TABLE_PREFIX."forums WHERE forum_id='".$fid[0]."'";
                            $result4 = mysql_query($sql) or die(mysql_error());
                            $row = mysql_fetch_row($result4);

                            $groupOptions .= "<option value='".$fid[0]."'>".$group_id[1]."</option>";
                        }
                    }
                }

                if ($groupOptions != "") {
                    print "<optgroup label='Group Forums'>";
                    print $groupOptions;
                    print "</optgroup>";
                }

                ?>

            </select>
        </div>
        <div class='row buttons'>
            <input type='hidden' name='forumID' id='forumID' />
            <input type='button' class='button input' id='btnExportFrm' name='btnExportFrm' value='<?php echo _AT('farchive_export'); ?>' onclick='submForm()' />
        </div>
    </div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>

<script language='javascript' type='text/javascript'>

var selSize = document.getElementById('selForum'); 
if (selSize.length+2 < 10) {
    selSize.size = selSize.length+2;
} else {
    selSize.size = 10;
}

function submForm() {
    var frm = document.getElementById('selForum'); 
    var id = frm.options[frm.selectedIndex].value;
    var title = frm.options[frm.selectedIndex].text;
    document.forms[0].action = 'mods/farchive/send_zip_archive.php?fid='+id+'&title='+title;
    document.forms[0].submit();
}
</script>


