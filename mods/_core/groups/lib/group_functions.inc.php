<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

function get_latest_additions($module, $group_id)
{
    global $db;
    $record_limit = 3;
    $module_name = $module->getName();
    
    switch($module_name)
    {
        case _AT('forums'): 
            $sql        = "SELECT forum_id FROM %sforums_groups WHERE group_id = %d";
            $sql_Params = array(TABLE_PREFIX, $group_id);
            $forum      = queryDB($sql, $sql_Params, true);
            $sql	= "SELECT *, last_comment + 0 AS stamp, DATE_FORMAT(last_comment, '%%Y-%%m-%%d %%H:%%i:%%s') AS last_comment FROM %sforums_threads WHERE parent_id=0 AND forum_id=(SELECT forum_id FROM %sforums_groups WHERE group_id = %d) AND member_id>0 ORDER BY sticky DESC, last_comment DESC LIMIT %d";
            $sqlParams = array(TABLE_PREFIX, TABLE_PREFIX, $group_id, $record_limit);
            break;
        case _AT('file_storage'): 
            $sql = "SELECT * FROM %sfiles WHERE owner_type = %d AND owner_id = %d ORDER BY date DESC LIMIT %d";
            $sqlParams = array(TABLE_PREFIX, WORKSPACE_GROUP, $group_id, $record_limit);
            break;
        case _AT('blogs'): 
            $sql = "SELECT * FROM %sblog_posts WHERE owner_type=%d AND owner_id=%d ORDER BY date DESC LIMIT %d";
            $sqlParams = array(TABLE_PREFIX, BLOGS_GROUP, $group_id, $record_limit);
            break;
        case _AT('links'): 
            $sql = 'SELECT * FROM %slinks L INNER JOIN %slinks_categories C USING (cat_id) WHERE (owner_id =%s AND owner_type=%s) AND L.Approved=1 ORDER BY SubmitDate DESC LIMIT %d';
            $sqlParams = array(TABLE_PREFIX, TABLE_PREFIX, $group_id, LINK_CAT_GROUP, $record_limit);
            break;
        default: return 0;
            break;
    }
    return get_additions($sql, $sqlParams, $module, $module_name, $group_id);
}

function get_additions($sql, $sqlParams, $module, $module_name, $group_id)
{
    global $_base_path;
    $record_limit = 3;
    $str = '';
    
    $rows = queryDB($sql, $sqlParams);
    
    if(count($rows) > 0)
    {
        echo "<h4 class = 'page-title'>".$module->getName()."</h4>";
        $str .= '<ol id = "tools"><li class = "top-tool">';
        foreach($rows as $row)
        {
            switch($module_name)
            {
                case _AT('forums'): 
                $str.= '<a href="'.$_base_path. url_rewrite('mods/_standard/forums/forum/view.php?fid='.$row[forum_id].SEP.'pid='.$row['post_id']).'" title="'.AT_print($full_subject, 'forums_threads.subject').'">'.$row['subject'].'</a></br>';    
                break;
            
                case _AT('file_storage'): 
                if($row['description'] !=""){
                    $filetext = $row['description'];
                } else {
                    $filetext = $row['file_name'];
                }
                $str.= '<a href="'.$_base_path.url_rewrite('mods/_standard/file_storage/index.php?download=1'.SEP.'files[]='. $row['file_id']).'"'.(strlen($filetext) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($filetext, 'input.text').'"' : '') .'>'.AT_print(validate_length($filetext, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'input.text') .'</a><br/>';
                break;
                
                case _AT('blogs'):
                $link_title = $row['title'];
                $str.= '<a href="'.$_base_path.url_rewrite('mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$group_id.SEP.'id='.$row['post_id']).'"'.(strlen($link_title) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($link_title, 'blog_posts.title').'"' : '') .'>'.AT_print(validate_length($link_title, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'blog_posts.title') .'</a><br/>';
                break;
            
                case _AT('links'):
                $str.= '<a href="'.$_base_path.url_rewrite('mods/_standard/links/index.php?view='.$row['link_id']).'"'.(strlen($row['LinkName']) > SUBLINK_TEXT_LEN ? ' title="'.$row['LinkName'].'"' : '') .'>'.validate_length($row['LinkName'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a><br/>';
                break;
            
                default: return 0;
                break;
            }
            
        }
        $str.= "</li></ol>";
        echo $str;
        return 1;
    }
    else
    {
        return 0;
    }
}

?>