<?php

require_once('lib/lib.php');

class ACConnection {


    var $adobe_connect_host;
    var $adobe_connect_port;
    var $adobe_connect_adminuser;
    var $adobe_connect_adminpass;
    var $adobe_connect_folderid;


    public function __construct() {

        $cfg = getAdobeConnectConfig();

        foreach ($cfg as $attribute => $v) {
            $this->$attribute = $cfg->$attribute;
        }

    }


    public function getACHost() {
        return $this->adobe_connect_host;
    }


    public function getACPort() {
        return $this->adobe_connect_port;
    }


    public function getAdminSession() {

        $fp = @fsockopen($this->adobe_connect_host, $this->adobe_connect_port);
        if (!$fp) {
            return false;
        }
  
        $url = '/api/xml?action=login&external-auth=use';

        fputs($fp, "GET ".$url." HTTP/1.0\r\n".$this->adobe_connect_adminpass.":".$this->adobe_connect_adminuser."\r\nHost: ".$this->adobe_connect_host."\r\n\r\n");

        while ($line = fgets($fp)) {
            if (strstr($line, "code=\"ok\"")) {
                $response = 1;
                break;
            }

            if (preg_match('/BREEZESESSION=(.*)\;/', $line, $result)) {
                $session = explode(';', $result[1]);
                $sessionid = $session[0];
            }
        }
        fclose($fp);

        if (empty($response)) {
            return false;
        }

        return $sessionid;
    }


    public function checkResponse($socket, $source) {

        while ($line = fgets($socket)) {
            if (strstr($line, "code=\"ok\"")) {
                return true;
            }
        }

        return false;
    }


}

?>
