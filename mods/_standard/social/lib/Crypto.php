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

class GeneralSecurityException extends Exception {
}

final class Crypto {
  
  /**
   * HMAC algorithm to use
   */
  private static $HMAC_TYPE = "HMACSHA1";
  
  /** 
   * minimum safe length for hmac keys (this is good practice, but not 
   * actually a requirement of the algorithm
   */
  private static $MIN_HMAC_KEY_LEN = 8;
  
  /**
   * Encryption algorithm to use
   */
  private static $CIPHER_TYPE = "AES/CBC/PKCS5Padding";
  
  private static $CIPHER_KEY_TYPE = "AES";
  
  /**
   * Use keys of this length for encryption operations
   */
  public static $CIPHER_KEY_LEN = 16;
  
  private static $CIPHER_BLOCK_SIZE = 16;
  
  /**
   * Length of HMAC SHA1 output
   */
  public static $HMAC_SHA1_LEN = 20;

  private function __construct() {}

  public static function hmacSha1Verify($key, $in, $expected) {
    $hmac = Crypto::hmacSha1($key, $in);
    if ($hmac != $expected) {
      throw new GeneralSecurityException("HMAC verification failure");
    }
  }

  public static function aes128cbcEncrypt($key, $text) {
    /* Open the cipher */
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    if (! $td) {
      throw new GeneralSecurityException('Invalid mcrypt cipher, check your libmcrypt library and php-mcrypt extention');
    }
    // replaced MCRYPT_DEV_RANDOM with MCRYPT_RAND since windows doesn't have /dev/rand :)
    srand((double)microtime() * 1000000);
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    /* Intialize encryption */
    mcrypt_generic_init($td, $key, $iv);
    /* Encrypt data */
    $encrypted = mcrypt_generic($td, $text);
    /* Terminate encryption handler */
    mcrypt_generic_deinit($td);
    /*
		 *  AES-128-CBC encryption.  The IV is returned as the first 16 bytes
		 * of the cipher text.
		 */
    return $iv . $encrypted;
  }

  public static function aes128cbcDecrypt($key, $encrypted_text) {
    /* Open the cipher */
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    if (is_callable('mb_substr')) {
      $iv = mb_substr($encrypted_text, 0, Crypto::$CIPHER_BLOCK_SIZE, 'latin1');
    } else {
      $iv = substr($encrypted_text, 0, Crypto::$CIPHER_BLOCK_SIZE);
    }
    /* Initialize encryption module for decryption */
    mcrypt_generic_init($td, $key, $iv);
    /* Decrypt encrypted string */
    if (is_callable('mb_substr')) {
      $encrypted = mb_substr($encrypted_text, Crypto::$CIPHER_BLOCK_SIZE, mb_strlen($encrypted_text, 'latin1'), 'latin1');
    } else {
      $encrypted = substr($encrypted_text, Crypto::$CIPHER_BLOCK_SIZE);
    }
    $decrypted = mdecrypt_generic($td, $encrypted);
    /* Terminate decryption handle and close module */
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    /* Show string */
    return trim($decrypted);
  }

  public static function hmacSha1($key, $data) {
    $blocksize = 64;
    $hashfunc = 'sha1';
    if (strlen($key) > $blocksize) {
      $key = pack('H*', $hashfunc($key));
    }
    $key = str_pad($key, $blocksize, chr(0x00));
    $ipad = str_repeat(chr(0x36), $blocksize);
    $opad = str_repeat(chr(0x5c), $blocksize);
    $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
    return $hmac;
  }
}
