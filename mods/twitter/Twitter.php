<?php

class Twitter
{

	function search($term){
		$curl_handle=curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,'http://search.twitter.com/search.json?q='.$term);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);

		if (empty($buffer)){
		    print "Sorry, Connection not available right now.";
		}
		else{
		    return json_decode($buffer,true);
		}
	}
}



?>
