<?php
require_once('OAuth.php');
require_once('../Shindig/ATutorOAuthDataStore');

$oauthDataStore = new ATutorOAuthDataStore();

try {
  $server = new OAuthServer($oauthDataStore);
  $server->add_signature_method(new OAuthSignatureMethod_HMAC_SHA1());
  $server->add_signature_method(new OAuthSignatureMethod_PLAINTEXT());
  $request = OAuthRequest::from_request();
  $token = $server->fetch_request_token($request);
  if ($token) {
	echo $token->to_string();
  }
} catch (OAuthException $e) {
  echo $e->getMessage();
} catch (Exception $e) {
  echo $e->getMessage();
}
?>