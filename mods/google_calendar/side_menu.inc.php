<?php 
/* start output buffering: */
ob_start();

require(AT_INCLUDE_PATH.'../mods/google_calendar/get_google_user.php');
echo $private_message;

?>

<script src="http://gmodules.com/ig/ifr?url=http://www.google.com/ig/modules/calendar-for-your-site.xml&amp;up_showCalendar2=1&amp;up_showAgenda=1&amp;up_calendarFeeds=(%7B%7D)&amp;up_firstDay=Sunday&amp;up_syndicatable=true&amp;up_stylesheet=&amp;up_sub=1&amp;up_c0u=<?php
 echo  $calendar_xml;
?>&amp;up_c0c=&amp;up_c1u=&amp;up_c1c=&amp;up_c2u=&amp;up_c2c=&amp;up_c3u=&amp;up_c3c=&amp;up_min=&amp;up_start=&amp;up_timeFormat=1%3A00pm&amp;up_calendarFeedsImported=0&amp;synd=open&amp;w=200&amp;h=450&amp;title=Google+Calendar&amp;border=http%3A%2F%2Fgmodules.com%2Fig%2Fimages%2F&amp;output=js"></script>

<?php

$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('google_calendar')); // the box title
$savant->display('include/box.tmpl.php');

?>