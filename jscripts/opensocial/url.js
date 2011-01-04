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
 * @fileoverview Representation of a URL.
 */


/**
 * @class
 * Base interface for all URL objects.
 *
 * @name opensocial.Url
 */


/**
 * Base interface for all URL objects.
 *
 * @private
 * @constructor
 */
opensocial.Url = function() {};


/**
 * @static
 * @class
 * All of the fields that a URL has. These are the supported keys for the
 * <a href="opensocial.Url.html#getField">Url.getField()</a> method.
 *
 * @name opensocial.Url.Field
 */
opensocial.Url.Field = {
  /**
   * The URL number type or label, specified as a string.
   * Examples: work, blog feed, website, etc.
   * This field may be used interchangeably with the string 'type'.
   * @member opensocial.Url.Field
   */
  TYPE : 'type',

  /**
   * The text of the link, specified as a string.
   * This field may be used interchangeably with the string 'linkText'.
   * @member opensocial.Url.Field
   */
  LINK_TEXT : 'linkText',

  /**
   * The address the URL points to, specified as a string.
   * This field may be used interchangeably with the string 'address'.
   * @member opensocial.Url.Field
   */
  ADDRESS : 'address'
};


/**
 * Gets data for this URL that is associated with the specified key.
 *
 * @param {String} key The key to get data for;
 *    keys are defined in <a href="opensocial.Url.Field.html"><code>
 *    Url.Field</code></a>
 * @param {Map.&lt;opensocial.DataRequest.DataRequestFields, Object&gt;}
 *  opt_params Additional
 *    <a href="opensocial.DataRequest.DataRequestFields.html">params</a>
 *    to pass to the request.
 * @return {String} The data
 */
opensocial.Url.prototype.getField = function(key, opt_params) {};
