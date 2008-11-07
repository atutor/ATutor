# sql file for Google Calendar module

CREATE TABLE `google_prefs` (
  `member_id` mediumint(8) NOT NULL,
  `private_xml` varchar(150) NOT NULL,
  `private_html` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `timezone` varchar(50) NOT NULL,
  PRIMARY KEY  (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `language_text` VALUES ('en', '_module','google_calendar','Google Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','google_howto','To use the Google Calendar module with ATutor, you need to <a href="http://www.google.com">register a google account</a>. If your google calendar is a shared calendar, you don\'t need to do anything to get your calendar to display in ATutor, provided the email address you used in ATutor is the same as the one used in your google account. If you have a Private calendar, follow these steps once you have your Google account setup:
<h4>Private Calendar Setup</h4>
<ol>
<li>Sign in to your google account, and click on My Account</li>
<li>Click on "Calendar" under My Products</li>
<li>Click on the calendar "Settings" link</li>
<li>Click on the Calendars tab under Calendar Settings</li>
<li>Click on the name in the first column for the calandar you wish to make available through ATutor</li>
<li>Scroll down to "Private Address" and click on the XML button</li>
<li>Copy the URL generated into the Private Calendar XML URL field below.</li>
<li>Click on the HTML button</li>
<li>Copy the URL generated into the Private Calendar HTML URL field below.
<li>Press Save.</li>
</ol>
<p>For additional details about Google Calendar setup and features, See <a href="http://www.google.com/support/calendar/">Google Calendar Help</a>.</p>',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','google_private_xml','Private Calendar XML URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','google_private_html','Private Calendar HTML URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','google_private_calendar_prefs','Set Private Calendar Preferences',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','google_private_calendar','Private Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','google_setup','How to setup your Google calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','google_timzone','Select Timezoner',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','google_calendar_permission','You do not have permission to view all events in this calendar. See details on setting up your Calendar below the main Google Calendar tool.',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_c_msgs','AT_FEEDBACK_GOOGLE_CAL_UPDATED','Google calendar preferences were successfully updated',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_c_msgs','AT_FEEDBACK_GOOGLE_CAL_UPDATE_FAILED','Google calendar preferences update failed for some undetermined reason.',NOW(),'');
