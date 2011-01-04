/*
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
 * @fileoverview Representation of an name.
 */


/**
 * @class
 * Base interface for all name objects.
 *
 * @name opensocial.Name
 */


/**
 * Base interface for all name objects.
 *
 * @private
 * @constructor
 */
opensocial.Name = function() {};


/**
 * @static
 * @class
 * All of the fields that a name has. These are the supported keys for the
 * <a href="opensocial.Name.html#getField">Name.getField()</a> method.
 *
 * @name opensocial.Name.Field
 */
opensocial.Name.Field = {
  /**
   * The family name, specified as a string.
   * This field may be used interchangeably with the string 'familyName'.
   * @member opensocial.Name.Field
   */
  FAMILY_NAME : 'familyName',

  /**
   * The given name, specified as a string.
   * This field may be used interchangeably with the string 'givenName'.
   * @member opensocial.Name.Field
   */
  GIVEN_NAME : 'givenName',

  /**
   * The additional name, specified as a string.
   * This field may be used interchangeably with the string 'additionalName'.
   * @member opensocial.Name.Field
   */
  ADDITIONAL_NAME : 'additionalName',

  /**
   * The honorific prefix, specified as a string.
   * This field may be used interchangeably with the string 'honorificPrefix'.
   * @member opensocial.Name.Field
   */
  HONORIFIC_PREFIX : 'honorificPrefix',

  /**
   * The honorific suffix, specified as a string.
   * This field may be used interchangeably with the string 'honorificSuffix'.
   * @member opensocial.Name.Field
   */
  HONORIFIC_SUFFIX : 'honorificSuffix',

  /**
   * The unstructured name, specified as a string.
   * This field may be used interchangeably with the string 'unstructured'.
   * @member opensocial.Name.Field
   */
  UNSTRUCTURED : 'unstructured'
};


/**
 * Gets data for this name that is associated with the specified key.
 *
 * @param {String} key The key to get data for;
 *    keys are defined in <a href="opensocial.Name.Field.html"><code>
 *    Name.Field</code></a>
 * @param {Map.&lt;opensocial.DataRequest.DataRequestFields, Object&gt;}
 *  opt_params Additional
 *    <a href="opensocial.DataRequest.DataRequestFields.html">params</a>
 *    to pass to the request.
 * @return {String} The data
 */
opensocial.Name.prototype.getField = function(key, opt_params) {};
