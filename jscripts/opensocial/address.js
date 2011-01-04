/*
 * Copyright 2007 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @fileoverview Representation of an address.
 */


/**
 * @class
 * Base interface for all address objects.
 *
 * @name opensocial.Address
 */


/**
 * Base interface for all address objects.
 *
 * @private
 * @constructor
 */
opensocial.Address = function() {};


/**
 * @static
 * @class
 * All of the fields that an address has. These are the supported keys for the
 * <a href="opensocial.Address.html#getField">Address.getField()</a> method.
 *
 * @name opensocial.Address.Field
 */
opensocial.Address.Field = {
  /**
   * The address type or label, specified as a string.
   * Examples: work, my favorite store, my house, etc.
   * This field may be used interchangeably with the string 'type'.
   * @member opensocial.Address.Field
   */
  TYPE : 'type',

  /**
   * If the container does not have structured addresses in its data store,
   * this field contains the unstructured address that the user entered,
   * specified as a string. Use
   * opensocial.getEnvironment().supportsField to see which fields are
   * supported.
   * This field may be used interchangeably with the string 
   * 'unstructuredAddress'.
   * @member opensocial.Address.Field
   */
  UNSTRUCTURED_ADDRESS : 'unstructuredAddress',

  /**
   * The P.O. box of the address, if there is one; specified as a string.
   * This field may be used interchangeably with the string 'poBox'.
   * @member opensocial.Address.Field
   */
  PO_BOX : 'poBox',

  /**
   * The street address, specified as a string.
   * This field may be used interchangeably with the string 'streetAddress'.
   * @member opensocial.Address.Field
   */
  STREET_ADDRESS : 'streetAddress',

  /**
   * The extended street address, specified as a string.
   * This field may be used interchangeably with the string 'extendedAddress'.
   * @member opensocial.Address.Field
   */
  EXTENDED_ADDRESS : 'extendedAddress',

  /**
   * The region, specified as a string.
   * This field may be used interchangeably with the string 'region'.
   * @member opensocial.Address.Field
   */
  REGION : 'region',

  /**
   * The locality, specified as a string.
   * This field may be used interchangeably with the string 'locality'.
   * @member opensocial.Address.Field
   */
  LOCALITY : 'locality',

  /**
   * The postal code, specified as a string.
   * This field may be used interchangeably with the string 'postalCode'.
   * @member opensocial.Address.Field
   */
  POSTAL_CODE : 'postalCode',

  /**
   * The country, specified as a string.
   * This field may be used interchangeably with the string 'country'.
   * @member opensocial.Address.Field
   */
  COUNTRY : 'country',

  /**
   * The latitude, specified as a number.
   * This field may be used interchangeably with the string 'latitude'.
   * @member opensocial.Address.Field
   */
  LATITUDE : 'latitude',

  /**
   * The longitude, specified as a number.
   * This field may be used interchangeably with the string 'longitude'.
   * @member opensocial.Address.Field
   */
  LONGITUDE : 'longitude'
};


/**
 * Gets data for this body type that is associated with the specified key.
 *
 * @param {String} key The key to get data for;
 *    keys are defined in <a href="opensocial.Address.Field.html"><code>
 *    Address.Field</code></a>
 * @param {Map.&lt;opensocial.DataRequest.DataRequestFields, Object&gt;}
 *  opt_params Additional
 *    <a href="opensocial.DataRequest.DataRequestFields.html">params</a>
 *    to pass to the request
 * @return {String} The data
 */
opensocial.Address.prototype.getField = function(key, opt_params) {};
