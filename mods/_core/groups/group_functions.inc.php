<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

function get_latest_additions($module, $group_id)
{
    global $db;
    $record_limit = 3;
    $module_name = $module->getName();
    
    switch($module_name)
    {
        case _AT('forums'): return get_forum_additions($module, $group_id);
            break;
        case _AT('file_storage'): return get_files_additions($module, $group_id);
            break;
        case _AT('blogs'): return get_blog_additions($module, $group_id);
            break;
        case _AT('links'): return get_link_additions($module, $group_id);
           break;
        default: return 0;
            break;
    }
    
}

function get_forum_additions($module, $group_id)
{
    global $db, $_base_path;
    $record_limit = 3;
    $sql        = "SELECT forum_id FROM %sforums_groups WHERE group_id = %d";
    $sql_Params = array(TABLE_PREFIX, $group_id);
    $forum      = queryDB($sql, $sql_Params, true);
    $str = '';
    
    if(!empty($forum))
    {
        $sql	= "SELECT *, last_comment + 0 AS stamp, DATE_FORMAT(last_comment, '%Y-%m-%d %H:%i:%s') AS last_comment FROM ".TABLE_PREFIX
                    ."forums_threads WHERE parent_id=0 AND forum_id=$forum[forum_id] AND member_id>0 ORDER BY sticky DESC, last_comment DESC LIMIT ".$record_limit;
        $result	= mysql_query($sql, $db);
        if(mysql_num_rows($result) > 0)
        {
            echo "<script>construct_module_ol('group_".$group_id."','".$module->getName()."');</script>";
            $str .= '<ol id = "tools"><li class = "top-tool">';
            while($row = mysql_fetch_assoc($result))
            {
                $str.= '<a href="'.$_base_path. url_rewrite('mods/_standard/forums/forum/view.php?fid='.$forum[forum_id].SEP.'pid='.$row['post_id'])
                        .'" title="'.AT_print($full_subject, 'forums_threads.subject').'">'
                        .$row['subject'].'</a></br>';
            }
            $str.= "</li></ol>";
            echo "<script>$('#group_".$group_id."').append('".$str."');</script>";
            return 1;
        }
        else {
            return 0;
        }
    }
    else {
        return 0;
    }
    
}

function get_files_additions($module, $group_id)
{
    global $_base_path;
    $record_limit = 3;
    $str = '';
    
    $sql = "SELECT * FROM %sfiles WHERE owner_type = %d AND owner_id = %d ORDER BY date DESC LIMIT %d";
    $sqlParams = array(TABLE_PREFIX, WORKSPACE_GROUP, $group_id, $record_limit);
    $result = queryDB($sql, $sqlParams);
    
    if(count($result) > 0)
    {
        echo "<script>construct_module_ol('group_".$group_id."','".$module->getName()."');</script>";
        $str .= '<ol id = "tools"><li class = "top-tool">';
        foreach($result as $row)
        {
            if($row['description'] !=""){
		$filetext = $row['description'];
            } else {
		$filetext = $row['file_name'];
            }
            $str.= '<a href="'.$_base_path.url_rewrite('mods/_standard/file_storage/index.php?download=1'.SEP.'files[]='. $row['file_id']).'"'.
		          (strlen($filetext) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($filetext, 'input.text').'"' : '') .'>'. 
		          AT_print(validate_length($filetext, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'input.text') .'</a><br/>';
        }
        $str.= "</li></ol>";
        echo "<script>$('#group_".$group_id."').append('".$str."');</script>";
        return 1;
    }
    else
    {
        return 0;
    }
    
}

function get_blog_additions($module, $group_id)
{
    global $_base_path;
    $record_limit = 3;
    $str = '';
    
    $sql = "SELECT * FROM %sblog_posts WHERE owner_type=%d AND owner_id=%d ORDER BY date DESC LIMIT %d";
    $sqlParams = array(TABLE_PREFIX, BLOGS_GROUP, $group_id, $record_limit);
    $result = queryDB($sql, $sqlParams);
    
    if(count($result) > 0)
    {
        echo "<script>construct_module_ol('group_".$group_id."','".$module->getName()."');</script>";
        $str = '<ol id = "tools"><li class = "top-tool">';
        foreach($result as $row)
        {
            $link_title = $row['title'];
            $str.= '<a href="'.$_base_path.url_rewrite('mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$group_id.SEP.'id='.$row['post_id']).'"'.
                      (strlen($link_title) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($link_title, 'blog_posts.title').'"' : '') .'>'. 
                      AT_print(validate_length($link_title, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'blog_posts.title') .'</a><br/>';
        }
        $str.= "</li></ol>";
        echo "<script>$('#group_".$group_id."').append('".$str."');</script>";
        return 1;
    }
    else
    {
        return 0;
    }
    
}

function get_link_additions($module, $group_id)
{
    global $_base_path;
    $record_limit = 3;
    $str = '';
    
    $sql = 'SELECT * FROM %slinks L INNER JOIN %slinks_categories C USING (cat_id) WHERE (owner_id =%s AND owner_type=%s) AND L.Approved=1 ORDER BY SubmitDate DESC LIMIT %d';
    $sqlParams = array(TABLE_PREFIX, TABLE_PREFIX, $group_id, LINK_CAT_GROUP, $record_limit);
    $result = queryDB($sql, $sqlParams);
    
    if(count($result) > 0)
    {
        echo "<script>construct_module_ol('group_".$group_id."','".$module->getName()."');</script>";
        $str = '<ol id = "tools"><li class = "top-tool">';
        foreach($result as $row)
        {
            $str.= '<a href="'.$_base_path.url_rewrite('mods/_standard/links/index.php?view='.$row['link_id']).'"'.
								(strlen($row['LinkName']) > SUBLINK_TEXT_LEN ? ' title="'.$row['LinkName'].'"' : '') .'>'. 
								validate_length($row['LinkName'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a><br/>';
        }
        $str.= "</li></ol>";
        echo "<script>$('#group_".$group_id."').append('".$str."');</script>";
        return 1;
    }
    else
    {
        return 0;
    }
}

?>