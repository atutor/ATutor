<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $moduleFactory, $_pages, $_top_level_pages;
$record_limit = 3;	// Number of sublinks to display for this module on course home page -> detail view

$group_list = implode(',', $_SESSION['groups']);
if($group_list != ''){
	$sql = "SELECT group_id, title, description, modules FROM %sgroups WHERE group_id IN (%s) ORDER BY title limit %d";
	$sqlParams = array(TABLE_PREFIX, $group_list, $record_limit);
	$rows = queryDB($sql, $sqlParams);
}
if (!$_SESSION['groups']) {
    return 0;
}
?>

<script src="<?php echo $_base_path;?>mods/_core/groups/js/groups.js"></script>
<?php
include ('lib/group_functions.inc.php');
if (count($rows) > 0) {
    foreach($rows as $row) {
        $add = 0;
        $modules = explode('|', $row['modules']);
        $str = '<a href="#" onclick="popup_open(\''.$row[group_id].'\'); return false;">'.validate_length($row['title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>';
        asort($modules);
        if ($modules) {
            echo "<div title = '".AT_print($row['title'], 'groups.title')." -> Latest Additions' id = 'group_".$row['group_id']."' class='group_dialog'>";
            $str.= '<ul class="child-top-tool">';
            foreach ($modules as $module_name) {
                $fn = basename($module_name) . '_get_group_url';
                $module = $moduleFactory->getModule($module_name);
                if ($module->isEnabled() && function_exists($fn)) {
                    $add += get_latest_additions($module, $row['group_id']);
                    $str.= '<li class="child-tool"><a href="'.$_base_path. url_rewrite($fn($row['group_id'])) .'" >'._AT($_pages[$module->getGroupTool()]['title_var']).'</a></li>';    
                }    
            }
            $str.= '</ul>';
            if(!$add) {
                echo "No new additions";
            }
        }
        $list[] = $str;
        echo "</div>";
    }
    return $list;	
} else {
    return 0;
}

?>
