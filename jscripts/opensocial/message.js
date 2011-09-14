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
 * @fileoverview Representation of a message.
 */


/**
 * @class
 * Base interface for all message objects.
 * *
 * <p>
 * <b>See also:</b>
 * <a href="opensocial.html#newMessage">opensocial.newMessage()</a>,
 * <a href="opensocial.html#requestSendMessage">
 * opensocial.requestSendMessage()</a>
 *

 *
 * @name opensocial.Message
 */


/**
 * Base interface for all message objects.
 *
 * @param {String} body The main text of the message.
 * @param {Map.<opensocial.Message.Field, Object>} opt_params Any other
 *    fields that should be set on the message object. All of the defined
 *    Fields are supported.
 * @private
 * @constructor
 */
opensocial.Message = function() {};


/**
 * @static
 * @class
 * All of the fields that messages can have.
 *
 * <p>
 * <b>See also:</b>
 * <a
 * href="opensocial.Message.html#getField">opensocial.Message.getField()</a>
 * </p>
 *
 * @name opensocial.Message.Field
 */
opensocial.Message.Field = {
  /**
   * The title of the message, specified as an opensocial.Message.Type.
   * This field may be used interchangeably with the string 'type'.
   * @member opensocial.Message.Field
   */
  TYPE : 'type',

  /**
   * The title of the message. HTML attributes are allowed and are
   * sanitized by the container.
   * This field may be used interchangeably with the string 'title'.
   * @member opensocial.Message.Field
   */
  TITLE : 'title',

  /**
   * The main text of the message. HTML attributes are allowed and are
   * sanitized by the container.
   * This field may be used interchangeably with the string 'body'.
   * @member opensocial.Message.Field
   */
  BODY : 'body',

  /**
   * The title of the message as a message template. Specifies the
   * message ID to use in the gadget xml.
   * This field may be used interchangeably with the string 'titleId'.
   * @member opensocial.Message.Field
   */
  TITLE_ID : 'titleId',

  /**
   * The main text of the message as a message template. Specifies the
   * message ID to use in the gadget xml.
   * This field may be used interchangeably with the string 'bodyId'.
   * @member opensocial.Message.Field
   */
  BODY_ID : 'bodyId'
};


/**
 * @static
 * @class
 * The types of messages that can be sent.
 *
 * @name opensocial.Message.Type
 */
opensocial.Message.Type = {
  /**
   * An email.
   * This field may be used interchangeably with the string 'email'.
   * @member opensocial.Message.Type
   */
  EMAIL : 'email',

  /**
   * A short private message.
   * This field may be used interchangeably with the string 'notification'.
   * @member opensocial.Message.Type
   */
  NOTIFICATION : 'notification',

  /**
   * A message to a specific user that can be seen only by that user.
   * This field may be used interchangeably with the string 'privateMessage'.
   * @member opensocial.Message.Type
   */
  PRIVATE_MESSAGE : 'privateMessage',

  /**
   * A message to a specific user that can be seen by more than that user.
   * This field may be used interchangeably with the string 'publicMessage'.
   * @member opensocial.Message.Type
   */
  PUBLIC_MESSAGE : 'publicMessage'
};


/**
 * Gets the message data that's associated with the specified key.
 *
 * @param {String} key The key to get data for;
 *   see the <a href="opensocial.Message.Field.html">Field</a> class
 * for possible values
 * @param {Map.&lt;opensocial.DataRequest.DataRequestFields, Object&gt;}
 *  opt_params Additional
 *    <a href="opensocial.DataRequest.DataRequestFields.html">params</a>
 *    to pass to the request.
 * @return {String} The data
 * @member opensocial.Message
 */
opensocial.Message.prototype.getField = function(key, opt_params) {};


/**
 * Sets data for this message associated with the given key.
 *
 * @param {String} key The key to set data for
 * @param {String} data The data to set
 */
opensocial.Message.prototype.setField = function(key, data) {};
