<?php


    function getAdobeConnectConfig() {

        global $db;

        $str = 'adobe_connect_adminuser,adobe_connect_adminpass,adobe_connect_folderid,adobe_connect_host,adobe_connect_port';

        $configsql = "SELECT name, value 
                      FROM ".TABLE_PREFIX."config 
                      WHERE name LIKE 'adobe_connect_%' 
                      ORDER BY name ASC";
        $config = mysql_query($configsql, $db);

        while ($cfg = @mysql_fetch_assoc($config)) {

            if (strstr($str, $cfg['name']) != false) {
                $name = $cfg["name"];
                $value = $cfg["value"];
                $data->$name = $value;
            }
        }

        if (empty($data)) {
            return false;
        }

        return $data;
    }


    function getMemberData() {

        global $db;

        $memberid = $_SESSION['member_id'];

        $membersql = "SELECT member_id, login, first_name, second_name, last_name 
                      FROM ".TABLE_PREFIX."members 
                      WHERE member_id = '$memberid'";
        $members = mysql_query($membersql, $db);

        while ($member = @mysql_fetch_assoc($members)) {
            $memberdata->member_id = $member["member_id"];
            $memberdata->login = $member["login"];
            $memberdata->first_name = $member["first_name"];
            $memberdata->second_name = $member["second_name"];
            $memberdata->last_name = $member["last_name"];
        }

        if (empty($memberdata)) {
            return false;
        }

        return $memberdata;
    }


    function getMemberCourseAssign() {

        global $db;

        $memberid = $_SESSION['member_id'];
        $courseid = $_SESSION['course_id'];

        $assignsql = "SELECT privileges, role
                      FROM ".TABLE_PREFIX."course_enrollment 
                      WHERE member_id = '$memberid' AND course_id = '$courseid' AND approved = 'y' 
                      ORDER BY role ASC";
        $assigns = mysql_query($assignsql, $db);

        while ($assign = @mysql_fetch_assoc($assigns)) {
            if ($assign['role'] == 'Instructor') {
                $assigndata->role = strtolower($assign['role']);
            } else {
                $assigndata->role = 'student';
            }
            $assigndata->privileges = $assign['privileges'];
        }

        if (empty($assigndata)) {
            return false;
        }

        return $assigndata;
    }


?>
