<?php

require_once('lib/ACConnection.php');
require_once('lib/ACUser.php');

class ACRoom extends ACConnection {


    public function createRoom($sessionid, $roomname) {

        $fp = @fsockopen($this->adobe_connect_host, $this->adobe_connect_port);
        if (!$fp) {
            return false;
        }
        if ($this->checkRoom($sessionid, $roomname)) {
            return false;
        }
  
        $roomname = trim($roomname);
        $url = "/api/xml?action=sco-update&name=$roomname&folder-id=".$this->adobe_connect_folderid."&type=meeting&session=$sessionid";
        fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->adobe_connect_host."\r\n\r\n");

        while($line = fgets($fp)) {

            if (preg_match('/\ sco-id=\"([0-9]*)\"/', $line, $result)) {
                $scoid = $result[1];
            }
        }
        fclose($fp);

        if (empty($scoid)) {
            return false;
        }

        return true;
    }


    public function deleteRoom($sessionid, $roomname) {

        if (!$scoid = $this->checkRoom($sessionid, $roomname)) {
            return false;
        } else {

            $fp = @fsockopen($this->adobe_connect_host, $this->adobe_connect_port);
            if (!$fp) {
                return false;
            }

            $url = "/api/xml?action=sco-delete&sco-id=$scoid&session=$sessionid";
            fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->adobe_connect_host."\r\n\r\n");

            $response = $this->checkResponse($fp, __FUNCTION__);
            fclose($fp);

            return $response;
        }
    }


    public function assignUser($sessionid, $username, $roomname, $role) {

        if (!$scoid = $this->checkRoom($sessionid, $roomname)) {
            return false;
        }

        $user = new ACUser();
        $principalid = $user->checkUser($sessionid, $username);
        if (!$principalid) {
            return false;
        }
  
        switch ($role) {
          case 'instructor':
            $acrole = 'host';
            break;
          case 'student':
            $acrole = 'view';
            break;
          default:
            return false;
        }

        return $this->assignAction($sessionid, $scoid, $principalid, $acrole);
    }


    public function assignAction($sessionid, $scoid, $principalid, $role) {
  
        $fp = @fsockopen($this->adobe_connect_host, $this->adobe_connect_port);
        if (!$fp) {
            return false;
        }

        $url = "/api/xml?action=permissions-update&permission-id=$role&acl-id=$scoid&principal-id=$principalid&session=$sessionid";
        fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->adobe_connect_host."\r\n\r\n");
  
        $response = $this->checkResponse($fp, __FUNCTION__);
        fclose($fp);

        return $response;
    }


    public function getRoomUrl($sessionid, $scoid) {

  
        $fp = @fsockopen($this->adobe_connect_host, $this->adobe_connect_port);
        if (!$fp) {
            return false;
        }

        $url = "/api/xml?action=sco-info&sco-id=$scoid&session=$sessionid";
        fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->adobe_connect_host."\r\n\r\n");

        while ($line = fgets($fp)) {
            if (preg_match('@\<url-path\>\/([^/]+)@', $line, $result)) {
                $roomurl = $result[1];
            }
        }
        fclose($fp);

        if (empty($roomurl)) {
            return false;
        }

        return $roomurl;
    }


    public function checkRoom($sessionid, $roomname) {
  
        $fp = @fsockopen($this->adobe_connect_host, $this->adobe_connect_port);
        if (!$fp) {
            return false;
        }

        $url = "/api/xml?action=sco-expanded-contents&sco-id=".$this->adobe_connect_folderid."&filter-type=meeting&filter-name=$roomname&session=$sessionid";
        fputs($fp, "GET ".$url." HTTP/1.0\r\nHost: ".$this->adobe_connect_host."\r\n\r\n");

        while($line = fgets($fp)) {

            if (preg_match('/\ sco-id=\"([0-9]*)\"/', $line, $result)) {
                $scoid = $result[1];
            }

        }
       fclose($fp);

       if (empty($scoid)) {
           return false;
       }

       return $scoid;
    }

}

?>
