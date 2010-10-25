<?php

$sql = "SELECT subscribe FROM ".TABLE_PREFIX."courses_members_subscription WHERE member_id=".$_SESSION['member_id']." AND course_id=".$_SESSION['course_id']." LIMIT 1";
$subscribed = mysql_fetch_assoc(mysql_query($sql));

if ($subscribed['subscribe']=="1"){
  $sub_href = "../mods/announcement_subscription/subscribe.php?a=unsubscribe";
  $sub_button = _AT('announcement_subscription_unsubscribe');
} else {
  $sub_href = "../mods/announcement_subscription/subscribe.php?a=subscribe";
  $sub_button = _AT('announcement_subscription_subscribe');
}


?>
