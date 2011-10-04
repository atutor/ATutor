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
 * @fileoverview Representation of a environment.
 */


/**
 * @class
 * Represents the current environment for a gadget.
 *
 * <p>
 * <b>See also:</b>
 * <a href="opensocial.html#getEnvironment">opensocial.getEnvironment()</a>,
 *
 * @name opensocial.Environment
 */


/**
 * Base interface for all environment objects.
 *
 * @param {String} domain The current domain
 * @param {Map.&lt;String, Map.&lt;String, Boolean&gt;&gt;} supportedFields
 *    The fields supported by this container
 *
 * @private
 * @constructor
 */
opensocial.Environment = function() {};


/**
 * Returns the current domain &mdash;
 * for example, "orkut.com" or "myspace.com".
 *
 * @return {String} The domain
 */
opensocial.Environment.prototype.getDomain = function() {};


/**
 * @static
 * @class
 *
 * The types of objects in this container.
 *
 * <p>
 * <b>See also:</b>
 * <a href="opensocial.Environment.html#supportsField">
 * <code>Environment.supportsField()</code></a>
 *
 * @name opensocial.Environment.ObjectType
 */
opensocial.Environment.ObjectType = {
  /**
   * This field may be used interchangeably with the string 'person'.
   * @member opensocial.Environment.ObjectType
   */
  PERSON : 'person',
  /**
   * This field may be used interchangeably with the string 'address'.
   * @member opensocial.Environment.ObjectType
   */
  ADDRESS : 'address',
  /**
   * This field may be used interchangeably with the string 'bodyType'.
   * @member opensocial.Environment.ObjectType
   */
  BODY_TYPE : 'bodyType',
  /**
   * This field may be used interchangeably with the string 'email'.
   * @member opensocial.Environment.ObjectType
   */
  EMAIL : 'email',
  /**
   * This field may be used interchangeably with the string 'name'.
   * @member opensocial.Environment.ObjectType
   */
  NAME : 'name',
  /**
   * This field may be used interchangeably with the string 'organization'.
   * @member opensocial.Environment.ObjectType
   */
  ORGANIZATION : 'organization',
  /**
   * This field may be used interchangeably with the string 'phone'.
   * @member opensocial.Environment.ObjectType
   */
  PHONE : 'phone',
  /**
   * This field may be used interchangeably with the string 'url'.
   * @member opensocial.Environment.ObjectType
   */
  URL : 'url',
  /**
   * This field may be used interchangeably with the string 'activity'.
   * @member opensocial.Environment.ObjectType
   */
  ACTIVITY : 'activity',
  /**
   * This field may be used interchangeably with the string 'mediaItem'.
   * @member opensocial.Environment.ObjectType
   */
  MEDIA_ITEM : 'mediaItem',
  /**
   * This field may be used interchangeably with the string 'message'.
   * @member opensocial.Environment.ObjectType
   */
  MESSAGE : 'message',
  /**
   * This field may be used interchangeably with the string 'messageType'.
   * @member opensocial.Environment.ObjectType
   */
  MESSAGE_TYPE : 'messageType',
  /**
   * This field may be used interchangeably with the string 'sortOrder'.
   * @member opensocial.Environment.ObjectType
   */
  SORT_ORDER : 'sortOrder',
  /**
   * This field may be used interchangeably with the string 'filterType'.
   * @member opensocial.Environment.ObjectType
   */
  FILTER_TYPE : 'filterType'
};


/**
 * Returns true if the specified field is supported in this container on the
 * given object type, and returns false otherwise.
 *
 * @param {opensocial.Environment.ObjectType} objectType
 *    The <a href="opensocial.Environment.ObjectType.html">object type</a>
 *    to check for the field
 * @param {String} fieldName The name of the field to check for
 * @return {Boolean} True if the field is supported on the specified object type
 */
opensocial.Environment.prototype.supportsField = function(objectType,
    fieldName) {};
