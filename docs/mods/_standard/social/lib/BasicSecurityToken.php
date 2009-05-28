<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

/**
 * Primitive token implementation that uses stings as tokens.
 */
class BasicSecurityToken extends SecurityToken {
  /** serialized form of the token */
  private $token;
  
  /** data from the token */
  private $tokenData;
  
  /** tool to use for signing and encrypting the token */
  protected $crypter;
  
  private $OWNER_KEY = "o";
  private $APP_KEY = "a";
  private $VIEWER_KEY = "v";
  private $DOMAIN_KEY = "d";
  private $APPURL_KEY = "u";
  private $MODULE_KEY = "m";

  /**
   * {@inheritDoc}
   */
  public function toSerialForm() {
    return urlencode($this->token);
  }

  /**
   * Generates a token from an input string
   * @param token String form of token
   * @param maxAge max age of the token (in seconds)
   * @throws BlobCrypterException 
   */
  static public function createFromToken($token, $maxAge) {
    return new BasicSecurityToken($token, $maxAge, null, null, null, null, null, null);
  }

  /**
   * Generates a token from an input array of values
   * @param owner owner of this gadget
   * @param viewer viewer of this gadget
   * @param app application id
   * @param domain domain of the container
   * @param appUrl url where the application lives
   * @param moduleId module id of this gadget 
   * @throws BlobCrypterException 
   */
  static public function createFromValues($owner, $viewer, $app, $domain, $appUrl, $moduleId) {
    return new BasicSecurityToken(null, null, $owner, $viewer, $app, $domain, $appUrl, $moduleId);
  }

  public function __construct($token, $maxAge, $owner, $viewer, $app, $domain, $appUrl, $moduleId) {
    $this->crypter = $this->getCrypter();
    if (! empty($token)) {
      $this->token = $token;
      $this->tokenData = $this->crypter->unwrap($token, $maxAge);
    } else {
      $this->tokenData = array();
      $this->tokenData[$this->OWNER_KEY] = $owner;
      $this->tokenData[$this->VIEWER_KEY] = $viewer;
      $this->tokenData[$this->APP_KEY] = $app;
      $this->tokenData[$this->DOMAIN_KEY] = $domain;
      $this->tokenData[$this->APPURL_KEY] = $appUrl;
      $this->tokenData[$this->MODULE_KEY] = $moduleId;
      $this->token = $this->crypter->wrap($this->tokenData);
    }
//	debug($this->tokenData);
  }

  protected function getCrypter() {
    return new BasicBlobCrypter();
  }

  public function isAnonymous() {
    return ($this->tokenData[$this->OWNER_KEY] === 0 && $this->tokenData[$this->VIEWER_KEY] === 0);
  }

  /**
   * {@inheritDoc}
   */
  public function getAppId() {
    if ($this->isAnonymous()) {
      throw new Exception("Can't get appId from an anonymous token");
    }
    return $this->tokenData[$this->APP_KEY];
  }

  /**
   * {@inheritDoc}
   */
  public function getDomain() {
    if ($this->isAnonymous()) {
      throw new Exception("Can't get domain from an anonymous token");
    }
    return $this->tokenData[$this->DOMAIN_KEY];
  }

  /**
   * {@inheritDoc}
   */
  public function getOwnerId() {
    if ($this->isAnonymous()) {
      throw new Exception("Can't get ownerId from an anonymous token");
    }
    return $this->tokenData[$this->OWNER_KEY];
  }

  /**
   * {@inheritDoc}
   */
  public function getViewerId() {
    if ($this->isAnonymous()) {
      throw new Exception("Can't get viewerId from an anonymous token");
    }
    return $this->tokenData[$this->VIEWER_KEY];
  }

  /**
   * {@inheritDoc}
   */
  public function getAppUrl() {
    if ($this->isAnonymous()) {
      throw new Exception("Can't get appUrl from an anonymous token");
    }
    return $this->tokenData[$this->APPURL_KEY];
  }

  /**
   * {@inheritDoc}
   */
  public function getModuleId() {
    if ($this->isAnonymous()) {
      throw new Exception("Can't get moduleId from an anonymous token");
    }
    if (! is_numeric($this->tokenData[$this->MODULE_KEY])) {
      throw new Exception("Module ID should be an integer");
    }
    return $this->tokenData[$this->MODULE_KEY];
  }
}
