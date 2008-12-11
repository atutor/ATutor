<?php
define('AT_INCLUDE_PATH', '../../../include/');


// Prints out an single formatted thread message
function print_entry2($row) {
	global $page,$system_courses, $forum_info;
	static $counter;
	$counter++;

	$reply_link = '<a href="forum/view.php?fid='.$row['forum_id'].SEP.'pid=';
	if ($row['parent_id'] == 0) {
		$reply_link .= $row['post_id'];
	} else {
		$reply_link .= $row['parent_id'];
	}
	$reply_link .= SEP.'reply='.$row['post_id'].SEP.'page='.$page.'#post" >'._AT('reply').'</a>';

?>

	<li class="<?php if ($counter %2) { echo 'odd'; } else { echo 'even'; } ?>">
		<a name="<?php echo $row['post_id']; ?>"></a>
		<div class="forum-post-author">
			<label class="title"><?php echo htmlspecialchars(get_display_name($row['member_id'])); ?></label><br />
		</div>

		<div class="forum-post-content">
			
			<div class="date">
                <p><?php echo AT_date(_AT('forum_date_format'), $row['date'], AT_DATE_MYSQL_DATETIME); ?></p>
            </div>
            <div class="postheader"><h3><?php echo AT_Print($row['subject'], 'forums_threads.subject'); ?></h3></div>
				
			<div class="body">
				<p><?php echo AT_print($row['body'], 'forums_threads.body'); ?></p>
			</div>
		</div>
	</li>
<?php
}

copy(getcwd()."/styles.css", AT_CONTENT_DIR."/styles.css");

$head = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN'
        'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
      <html xmlns='http://www.w3.org/1999/xhtml' lang='en' xml:lang='en'>";

$head .= "<head><link rel=StyleSheet href='styles.css' type='text/css' media='screen'><title>Exported Forum</title></head><body>";

$tmpfile = AT_CONTENT_DIR."/exported_forum.html";
$main = fopen($tmpfile, "w");

//---- Header----
fwrite($main, $head);
fwrite($main, "<h1>".$forum_title."</h1><br />");
fwrite($main, "<div class='threadlist'>");

$filearr = array();  // will hold all the files that were created

$sql = "SELECT *, DATE_FORMAT(date, '%Y-%m-%d %H-%i:%s') AS date, UNIX_TIMESTAMP(date) AS udate FROM ".TABLE_PREFIX."forums_threads WHERE parent_id=0 AND forum_id=".$forum_id." ORDER BY date ASC LIMIT 0, 70";
$result = mysql_query($sql) or die(mysql_error());

// Print out each post for each thread
while ($row = mysql_fetch_array($result)) {


    $handle = fopen(AT_CONTENT_DIR."/t-".$row['post_id'].".html", "w");
    array_push($filearr, "t-".$row['post_id'].".html");

    fwrite($main, "<a href='t-".$row['post_id'].".html'>".$row['subject']."</a><br />");
    fwrite($handle, $head);
    fwrite($handle, "<br /><div><a href='exported_forum.html' class='midtext'>Back to: Thread list</a></div><br />");
    fwrite($handle, "<div><p><br /><h1>".$row['subject']."</h1></div><br />");
    fwrite($handle, "<div><ul class='forum-thread'>");

    $sql	= "SELECT *, DATE_FORMAT(date, '%Y-%m-%d %H-%i:%s') AS date, UNIX_TIMESTAMP(date) AS udate FROM ".TABLE_PREFIX."forums_threads WHERE parent_id=".$row['post_id']." AND forum_id=".$forum_id." ORDER BY date ASC LIMIT 0, 70";

    $result_post = mysql_query($sql, $db);
    ob_start();

    print_entry2($row);
    
    while ($post_row = mysql_fetch_assoc($result_post)) {
        print_entry2($post_row);
    }
    fwrite($handle, ob_get_contents());
    ob_end_clean();
    fwrite($handle, "</ul></div>");
    fwrite($handle, "</body></html>");
    fclose($handle);
}
fwrite($main, "<div></body></html>");

?>