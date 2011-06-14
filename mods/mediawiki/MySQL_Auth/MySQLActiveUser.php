<?php
 
class MySQLActiveUser
{
    var $active_user_id ;
    var $active_user_name ;
    var $active_user_password;
    var $active_user_email ;
    var $active_user_login_time ;
 
    function MySQLActiveUser()  {
    }
 
    function set_cookie($username, $user_id, $password_hash, $user_email, $login_time) {
        setcookie("mysql_active_user",
                  serialize(array($username, $user_id, $password_hash, $user_email, $login_time)), time()+60*60*24*100, "/") ;
 
        $this->active_user_name = $username ;
        $this->active_user_id = $user_id ;
        $this->active_user_password = $password_hash ;
        $this->active_user_email = $user_email ;
        $this->active_user_login_time = $login_time ;
    }
 
    function clear_cookie() {
        $this->active_user_name = "" ;
        $this->active_user_id = 0 ;
        $this->active_user_password = 0 ;
        $this->active_user_email = "" ;
        $this->active_user_login_time = 0 ;
        setcookie("mysql_active_user",
                serialize(array("", "", "", "")), time()-60*60*24*100, "/") ;
    }
 
    function distribute_cookie_data() {
        $mysql_cookie_name = "mysql_active_user" ;
 
        if (isset($_COOKIE[$mysql_cookie_name]))
                list($this->active_user_name,
                     $this->active_user_id,
                     $this->active_user_password,
                     $this->active_user_email,
                     $this->active_user_login_time) = @unserialize($_COOKIE[$mysql_cookie_name]);
    }
}
 
$MySQLActiveUserData = new MySQLActiveUser();
?>