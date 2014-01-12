<?php
    /****************************************************************/
    /* ATutor Calendar Module                                       */
    /* https://atutorcalendar.wordpress.com/                        */
    /*                                                              */
    /* This module provides standard calendar features in ATutor.   */
    /*                                                              */
    /* Author: Anurup Raveendran, Herat Gandhi                      */
    /* This program is free software. You can redistribute it and/or*/
    /* modify it under the terms of the GNU General Public License  */
    /* as published by the Free Software Foundation.                */
    /****************************************************************/
    
    /**
     * This php file is used for side menu. When instructor allows students to
     * access this module as a course tool then in side menu of course's home page
     * one entry will be created.
     */
    /* start output buffering: */
    ob_start(); 
    global $savant;
?>
<div id='mini-calendar'></div>

<script type='text/javascript' src="<?php echo AT_BASE_HREF; ?>mods/_standard/calendar/lib/fullcalendar/fullcalendar-original.js">
</script>

<link href="<?php echo AT_BASE_HREF; ?>mods/_standard/calendar/lib/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
<link href="<?php echo AT_BASE_HREF; ?>mods/_standard/calendar/lib/fullcalendar/miniCal.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
    var path = "<?php echo AT_BASE_HREF; ?>";
</script>
<script type="text/javascript" src="<?php echo AT_BASE_HREF; ?>mods/_standard/calendar/js/side_menu.js">
</script>
<?php
    $savant->assign('dropdown_contents', ob_get_contents());
    ob_end_clean();
    
    $savant->assign('title', _AT('calendar_header')); //The box title
    $savant->display('include/box.tmpl.php');
?>