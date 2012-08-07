<?php

	class Twitauth
	    {
	      var $key = '';
	      var $secret = '';
	
	      var $request_token = "";
	
	    function Twitauth($config)
	    {
	    	// consumer key
	        $this->key				= $config['key'];
			// consumer secret
	        $this->secret			= $config['secret'];
			// request token url
	        $this->request_token	= $config['request_token'];
	    }
	
	    function getRequestToken()
	    {

	        // Default params
	        $params = array(
	            "oauth_version" => "1.0",
	            "oauth_nonce" => time(),
	            "oauth_timestamp" => time(),
	            "oauth_consumer_key" => $this->key,
	            "oauth_signature_method" => "HMAC-SHA1"
	         );
	
	         // BUILD SIGNATURE
	            // encode params keys, values, join and then sort.
	            $keys	= $this->_urlencode_rfc3986(array_keys($params));
	            $values	= $this->_urlencode_rfc3986(array_values($params));
	            $params	= array_combine($keys, $values);
	            uksort($params, 'strcmp');

	            // convert params to string 
	            foreach ($params as $k => $v) {$pairs[] = $this->_urlencode_rfc3986($k).'='.$this->_urlencode_rfc3986($v);}
	            $concatenatedParams = implode('&', $pairs);
	
	            // form base string (first key)
	            $baseString= "GET&".$this->_urlencode_rfc3986($this->request_token)."&".$this->_urlencode_rfc3986($concatenatedParams);
	            // form secret (second key)
	            $secret = $this->_urlencode_rfc3986($this->secret)."&";
	            // make signature and append to params
	            $params['oauth_signature'] = $this->_urlencode_rfc3986(base64_encode(hash_hmac('sha1', $baseString, $secret, TRUE)));

	         // BUILD URL
	            // Resort
	            uksort($params, 'strcmp');
	            // convert params to string 
	            foreach ($params as $k => $v) {$urlPairs[] = $k."=".$v;}
	            $concatenatedUrlParams = implode('&', $urlPairs);
	            // form url
	            $url = $this->request_token."?".$concatenatedUrlParams;

	         // Send to cURL
	         //return $this->_http($url);

	        $cURL		= $this->_http($url);

			$r					= explode('&', $cURL);

			$oauth_token		= explode('=', $r[0]);
			$oauth_token_secret	= explode('=', $r[1]);

	         $ret		= array('oauth_signature'		=> $params['oauth_signature'],
			 					'oauth_token'			=> $oauth_token[1],
								'oauth_token_secret'	=> $oauth_token_secret[1]);

	         return $ret;
	    }
	
	    function _http($url, $post_data = null)
	    {
	    	
	        $ch = curl_init();
	
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	
	        if(isset($post_data))
	        {
	            curl_setopt($ch, CURLOPT_POST, 1);
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	        }

	        $response = curl_exec($ch);

	        $this->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        $this->last_api_call = $url;
	        curl_close($ch);

	        return $response;
	    }
	
	    function _urlencode_rfc3986($input)
	    {
	        if (is_array($input)) {
	            return array_map(array('Twitauth', '_urlencode_rfc3986'), $input);
	        }
	        else if (is_scalar($input)) {
	            return str_replace('+',' ',str_replace('%7E', '~', rawurlencode($input)));
	        }
	        else{
	            return '';
	        }
	    }
	}

?>