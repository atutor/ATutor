<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

/**
 * ATutor's implementation of OAuthLookupService, which does all the heavy lifting for the oauth protocol handling
 * and authentication matching
 * Refer to shindig/php/src/common/OAuthLookUpService.php
 */
class ATutorOAuthLookupService extends OAuthLookupService {

  /**
   * ATutor's implementation of the OAuth Lookup service. ATutor supports all currently existing forms of
   * OAuth signatures: 3 legged, 2 legged and body_hash's
   *
   * @param RequestItem $oauthRequest
   * @param string $appUrl
   * @param string $userId
   * @return SecurityToken or null
   */
  public function getSecurityToken($oauthRequest, $appUrl, $userId) {
    try {
      // Incomming requests with a POST body can either have an oauth_body_hash, or include the post body in the main oauth_signature; Also for either of these to be valid
      // we need to make sure it has a proper the content-type; So the below checks if it's a post, if so if the content-type is supported, and if so deals with the 2
      // post body signature styles
      $includeRawPost = false;
      if (isset($GLOBALS['HTTP_RAW_POST_DATA']) && ! empty($GLOBALS['HTTP_RAW_POST_DATA'])) {
		  if (isset($_GET['oauth_body_hash'])) {
			// this request uses the oauth_body_hash spec extension. Check the body hash and if it fails return 'null' (oauth signature failure)
			// otherwise continue on to the regular oauth signature verification, without including the post body in the main oauth_signature calculation
			if (! $this->verifyBodyHash($GLOBALS['HTTP_RAW_POST_DATA'], $_GET['oauth_body_hash'])) {
			  return null;
			}
		  } else {
			// use the (somewhat oauth spec invalid) raw post body in the main oauth hash calculation
			$includeRawPost = $GLOBALS['HTTP_RAW_POST_DATA'];
		  }
      }
      $dataStore = new ATutorOAuthDataStore();
      if ($includeRawPost) {
        // if $includeRawPost has been set above, we need to include the post body in the main oauth_signature
        $oauthRequest->set_parameter($includeRawPost, '');
      }
      if (! isset($oauthRequest->parameters['oauth_token'])) {
        // No oauth_token means this is a 2 legged OAuth request
        $ret = $this->verify2LeggedOAuth($oauthRequest, $userId, $appUrl, $dataStore);
      } else {
        // Otherwise it's a clasic 3 legged oauth request
        $ret = $this->verify3LeggedOAuth($oauthRequest, $userId, $appUrl, $dataStore);
      }
      if ($includeRawPost) {
        unset($oauthRequest->parameters[$includeRawPost]);
      }
      return $ret;
    } catch (OAuthException $e) {
      return null;
    }
  }

  /**
   * Verfies the oauth_body_hash signature, for more information on this oauth spec extension see:
   * http://oauth.googlecode.com/svn/spec/ext/body_hash/1.0/drafts/4/spec.html
   */
  private function verifyBodyHash($postBody, $oauthBodyHash) {
    return base64_encode(sha1($postBody, true)) == $oauthBodyHash;
  }

  /**
   * Verfies a 2 legged OAuth signature. 2 legged OAuth means the security context is of the application,
   * and no specific user is associated with it. Most of the logic is done manually and not through the OAuth
   * library, since it has no knowledge of- / support for 2 legged OAuth.
   */
  private function verify2LeggedOAuth($oauthRequest, $userId, $appUrl, $dataStore) {
    $consumerToken = $dataStore->lookup_consumer($oauthRequest->parameters['oauth_consumer_key']);
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
    $signature_valid = $signature_method->check_signature($oauthRequest, $consumerToken, null, $_GET["oauth_signature"]);
    if (! $signature_valid) {
      // signature did not check out, abort
      return null;
    }
    return new OAuthSecurityToken($userId, $appUrl, $dataStore->get_app_id($consumerToken), "atutor");
  }

  /**
   * The 'clasic' 3 legged OAuth, where the user went through the OAuth dance and granted the remote app
   * access to his/her data.
   */
  private function verify3LeggedOAuth($oauthRequest, $userId, $appUrl, $dataStore) {
    $server = new OAuthServer($dataStore);
    $server->add_signature_method(new OAuthSignatureMethod_HMAC_SHA1());
    $server->add_signature_method(new OAuthSignatureMethod_PLAINTEXT());
    list($consumer, $token) = $server->verify_request($oauthRequest);
    $oauthUserId = $dataStore->get_user_id($token);
    if ($userId && $oauthUserId && $oauthUserId != $userId) {
      return null; // xoauth_requestor_id was provided, but does not match oauth token -> fail
    } else {
      $userId = $oauthUserId; // use userId from oauth token
      return new OAuthSecurityToken($userId, $appUrl, $dataStore->get_app_id($consumer), "atutor");
    }
  }
}
