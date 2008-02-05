<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_CALENDAR', $this->getPrivilege());

// if this module is to be made available to students on the Home or Main Navigation
$_student_tool = 'calendar/index.php';

// register this module as a calendar source, must implement calendar_get_entries()
// register_hook('calendar_source', this);

/*
- reading list (start and end dates)
- tests (start and end dates)
- assignments (due and late submission dates)
- announcements (post date)
- calendar (implements course, group, multiple calendars for system as well as the display of the calendar)

each above module implements in module_calendar.php
mixed calendar_get_entries(int $start_timestamp, int $end_timestamp, mixed $owner_type, mixed $owner_id);

loop through all registered modules calling their run_hook('calendar_source', $owner_type, $owner_id) method, 
which then includes and runs the calendar_get_entries() function.

the calendar display doesn't have to know which modules implement calendar_get_entries().
*/


?>