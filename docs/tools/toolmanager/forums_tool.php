<?php

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $db;



if(isset($_POST['save'])) {
    if(isset($associated_forum))
    unset($associated_forum);

    if(isset($_SESSION['associated_forum']))
        unset($_SESSION['associated_forum']);
        
    
//rimuovo dalla tabella le precedenti associazioni con il contenuto
    if(isset($_POST['check'])){
        $i=0;
        $associated_forum = '';
        foreach ($_POST['check'] as $selected_forum) {
            $associated_forum[$i]= $selected_forum;
            $i++;
        }
        if($associated_forum != '')
            $_SESSION['associated_forum']= $associated_forum; ?>
        <script type="text/javascript">
            window.opener.document.getElementById('Forums').style='border:solid; border-color: #43addb';
        </script>
    <?php } else {
        $_SESSION['associated_forum'] = 'none'; ?>
        <script type="text/javascript">
            window.opener.document.getElementById('Forums').style='';
        </script>
    <?php }?>
    <script type="text/javascript">
        javascript:window.close();
    </script>
<?php }

$sql = "SELECT f.* FROM ".TABLE_PREFIX."forums f INNER JOIN ".TABLE_PREFIX."forums_courses fc USING (forum_id) WHERE fc.course_id = $_SESSION[course_id]";
$result = mysql_query($sql, $db);

if(mysql_num_rows($result) != 0) {
    while ($row = mysql_fetch_assoc($result)) {
        $path =  "forum/index.php?fid=".$row['forum_id']; 					// memorizzo i dati necessari per comporre i link di ogni elemento
        $content_list[] = array('id'=>$row['forum_id'], 'title' => $row['title'], 'path' => $path, 'image' => AT_BASE_HREF.'images/home-forums_sm.png');
    }
    return $content_list;
} else {
    $msg->addInfo('NO_FORUMS');
    $msg->printInfos();
    return;
}

?>