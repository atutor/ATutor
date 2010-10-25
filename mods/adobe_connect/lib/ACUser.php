<?php

require_once('lib/ACConnection.php');

class ACUser extends ACConnection {


    public function getUserSession($username) {

        $fp = @fsockopen($this->adobe_connect_host, $this->adobe_connect_port);
        if (!$fp) {
            return false;
        }
  
        $url = '/api/xml?action=login&external-auth=use';

        fputs($fp, "GET ".$url." HTTP/1.0\r\n".$this->adobe_connect_adminpass.":".$username."\r\nHost: ".$this->adobe_connect_host."\r\n\r\n");

        while($line = fgets($fp)) {

            if (preg_match('/BREEZESESSION=(.*)\;/', $line, $result)) {
                $session = explode(';', $result[1]);
                $sessionid = $session[0];
                break;
            }

        }
        fclose($fp);

        if (empty($sessionid)) {
            return false;
        }

        return $sessionid;
    }


    public function createUser($sessionid, $username, $fname, $lname) {

        $fp = @fsockopen($this->adobe_connect_host, $this->adobe_connect_port);
        if (!$fp) {
            return false;
        }

        if ($this->checkUser($sessionid, $username)) {
            return false;
        }
  
        if ($fname == '' OR $fname == false OR $fname == ' ') { return false;}
        if ($lname == '' OR $lname == false OR $lname == ' ') { return false;}
        if ($username == '' OR $username == false OR $username == ' ') { return false;}
        if (strlen($fname) > 40) { return false;}
        if (strlen($lname) > 40) { return false;}
        if (strlen($username) > 40) { return false;}
        $username = trim($username);

        $url = "/api/xml?action=principal-update&login=$username&first-name=$fname&has-children=0&last-name=$lname&type=user&session=$sessionid";
        fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->adobe_connect_host."\r\n\r\n");

        while($line = fgets($fp)) {
            if (preg_match('/\ principal-id=\"([0-9]*)\"/', $line, $result)) {
		$principalid = $result[1];
            }
        }
        fclose($fp);

        if (empty($principalid)) {
            return false;
        }

        return true;
    }


    public function deleteUser($sessionid, $username) {

	if (!$principalid = $this->checkUser($sessionid, $username)) { 
            return false;
	} else {

            $fp = @fsockopen($this->adobe_connect_host, $this->adobe_connect_port);
            if (!$fp) {
		return false;
            }

            $url = "/api/xml?action=principals-delete&principal-id=$principalid&session=$sessionid";
            fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->adobe_connect_host."\r\n\r\n");

            $response = $this->checkResponse($fp, __FUNCTION__);
            fclose($fp);
            return $response;
	}
    }


    public function checkUser($sessionid, $username) {
  
        $fp = @fsockopen($this->adobe_connect_host, $this->adobe_connect_port);
        if (!$fp) {
            return false;
        }

        $url = "/api/xml?action=principal-list&filter-login=$username&session=$sessionid";
        fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->adobe_connect_host."\r\n\r\n");

        while($line = fgets($fp)) {

            if (preg_match('/\ principal-id=\"([0-9]*)\"/', $line, $result)) {
		$principalid = $result[1];
            }

        }
        fclose($fp);

        if (empty($principalid)) {
            return false;
        }

        return $principalid;
    }

}

?>
