<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
$links_limit = 3;		//Numero massimo dei possibili sottocontenuti visualizzabili nella home-page
$result = queryDB("SELECT * FROM %slinks L INNER JOIN %slinks_categories C ON C.cat_id = L.cat_id WHERE owner_id=%d ORDER BY SubmitDate DESC LIMIT %d",
                    array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id'], $links_limit));
if (!empty($result)) {
    foreach ($result as $row) {
        $link_id = $row['link_id'];
        $link_name = $row['LinkName'];
        $list[] = '<a href="'.url_rewrite('mods/_standard/links/index.php?view='.$link_id, AT_PRETTY_URL_IS_HEADER).'"'.
              (strlen($link_name) > SUBLINK_TEXT_LEN ? ' title="'.$link_name.'"' : '') .'>'.
              AT_print(validate_length($link_name, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'resource_links.LinkName') .'</a>';
    }
    return $list;
} else {
    return 0;
}
?>
