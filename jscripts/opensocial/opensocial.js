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
 * @fileoverview Browser environment for interacting with people.
 */


/**
 * @static
 * @class
 * Namespace for top-level people functions.
 *
 * @name opensocial
 */

/**
 * Namespace for top level people functions.
 *
 * @private
 * @constructor (note: a constructor for JsDoc purposes)
 */
var opensocial = function() {};


/**
 * Requests the container to send a specific message to the specified users.
 *
 * <p>
 * The callback function is passed one parameter, an
 *    opensocial.ResponseItem. The error code will be set to reflect whether
 *    there were any problems with the request. If there was no error, the
 *    message was sent. If there was an error, you can use the response item's
 *    getErrorCode method to determine how to proceed. The data on the response
 *    item will not be set.
 * </p>
 * 
 * <p>
 * If the container does not support this method
 * the callback will be called with an
 * opensocial.ResponseItem that has an error code of
 * NOT_IMPLEMENTED.
 * </p>
 *
 * @param {Array.&lt;String&gt; | String} recipients An ID, array of IDs, or a
 *     group reference; the supported keys are VIEWER, OWNER, VIEWER_FRIENDS,
 *    OWNER_FRIENDS, or a single ID within one of those groups
 * @param {opensocial.Message} message The message to send to the specified
 *     users
 * @param {Function} opt_callback The function to call once the request has been
 *    processed; either this callback will be called or the gadget will be
 *    reloaded from scratch
 * @param {opensocial.NavigationParameters} opt_params The optional parameters
 *     indicating where to send a user when a request is made, or when a request is
 *     accepted; options are of type
 *     <a href="opensocial.NavigationParameters.DestinationType.html">
 *     NavigationParameters.DestinationType</a>
 *
 * @member opensocial
 */
opensocial.requestSendMessage = function(recipients, message, opt_callback,
    opt_params) {};


/**
 * Requests the container to share this gadget with the specified users.
 *
 * <p>
 * The callback function is passed one parameter, an
 *    opensocial.ResponseItem. The error code will be set to reflect whether
 *    there were any problems with the request. If there was no error, the
 *    sharing request was sent. If there was an error, you can use the response
 *    item's getErrorCode method to determine how to proceed. The data on the
 *    response item will not be set.
 * </p>
 *
 * <p>
 * If the
 * container does not support this method the callback will be called with a
 * opensocial.ResponseItem. The response item will have its error code set to
 * NOT_IMPLEMENTED.
 * </p>
 *
 * @param {Array.&lt;String&gt; | String} recipients An ID, array of IDs, or a
 *     group reference; the supported keys are VIEWER, OWNER, VIEWER_FRIENDS,
 *    OWNER_FRIENDS, or a single ID within one of those groups
 * @param {opensocial.Message} reason The reason the user wants the gadget to
 *     share itself. This reason can be used by the container when prompting the
 *     user for permission to share the app. It may also be ignored.
 * @param {Function} opt_callback The function to call once the request has been
 *    processed; either this callback will be called or the gadget will be
 *    reloaded from scratch
 * @param {opensocial.NavigationParameters} opt_params The optional parameters
 *     indicating where to send a user when a request is made, or when a request is
 *     accepted; options are of type
 *     <a href="opensocial.NavigationParameters.DestinationType.html">
 *     NavigationParameters.DestinationType</a>
 *
 * @member opensocial
 */
opensocial.requestShareApp = function(recipients, reason, opt_callback,
    opt_params) {};


/**
 * Takes an activity and tries to create it,
 * without waiting for the operation to complete.
 * Optionally calls a function when the operation completes.
 * <p>
 * <b>See also:</b>
 * <a href="#newActivity">newActivity()</a>
 * </p>
 *
 * <p class="note">
 * <b>Note:</b>
 * If this is the first activity that has been created for the user and
 * the request is marked as HIGH priority then this call may open a user flow
 * and navigate away from your gadget.
 *
 * <p>
 * This callback will either be called or the gadget will be
 *    reloaded from scratch. This function will be passed one parameter, an
 *    opensocial.ResponseItem. The error code will be set to reflect whether
 *    there were any problems with the request. If there was no error, the
 *    activity was created. If there was an error, you can use the response
 *    item's getErrorCode method to determine how to proceed. The data on the
 *    response item will not be set.
 * </p>
 *
 * <p>
 * If the container does not support this method the callback will be called
 * with a opensocial.ResponseItem. The response item will have its error code
 * set to NOT_IMPLEMENTED.
 * </p>
 *
 * @param {opensocial.Activity} activity The <a href="opensocial.Activity.html">
 *    activity</a> to create
 * @param {opensocial.CreateActivityPriority} priority The
 *    <a href="opensocial.CreateActivityPriority.html">priority</a> for this
 *    request
 * @param {Function} opt_callback The function to call once the request has been
 *    processed.
 *
 * @member opensocial
 */
opensocial.requestCreateActivity = function(activity, priority,
    opt_callback) {};


/**
 * @static
 * @class
 * The priorities a create activity request can have.
 * <p><b>See also:</b>
 * <a href="opensocial.html#requestCreateActivity">
 * opensocial.requestCreateActivity()</a>
 * </p>
 *
 * @name opensocial.CreateActivityPriority
 */
opensocial.CreateActivityPriority = {
  /**
   * If the activity is of high importance, it will be created even if this
   * requires asking the user for permission. This may cause the container to
   * open a user flow which may navigate away from your gagdet.
   * This field may be used interchangeably with the string 'HIGH'.
   * @member opensocial.CreateActivityPriority
   */
  HIGH : 'HIGH',

  /**
   * If the activity is of low importance, it will not be created if the
   * user has not given permission for the current app to create activities.
   * With this priority, the requestCreateActivity call will never open a user
   * flow.
   * This field may be used interchangeably with the string 'LOW'.
   * @member opensocial.CreateActivityPriority
   */
  LOW : 'LOW'
};


/**
 * Returns true if the current gadget has access to the specified
 * permission. If the gadget calls opensocial.requestPermission and permissions
 * are granted then this function must return true on all subsequent calls.
 *
 * @param {opensocial.Permission} permission
 *    The <a href="opensocial.Permission.html">permission</a>
 * @return {Boolean}
 *    True if the gadget has access for the permission; false if it doesn't
 *
 * @member opensocial
 */
opensocial.hasPermission = function(permission) {};


/**
 * Requests the user to grant access to the specified permissions. If the
 * container does not support this method the callback will be called with a
 * opensocial.ResponseItem. The response item will have its error code set to
 * NOT_IMPLEMENTED.
 *
 * @param {Array.&lt;opensocial.Permission&gt;} permissions
 *    The <a href="opensocial.Permission.html">permissions</a> to request
 *    from the viewer
 * @param {String} reason Displayed to the user as the reason why these
 *    permissions are needed
 * @param {Function} opt_callback The function to call once the request has been
 *    processed; either this callback will be called or the gadget will be
 *    reloaded from scratch. This function will be passed one parameter, an
 *    opensocial.ResponseItem. The error code will be set to reflect whether
 *    there were any problems with the request. If there was no error, all
 *    permissions were granted. If there was an error, you can use
 *    opensocial.hasPermission to check which permissions are still denied. The
 *    data on the response item will be set. It will be an array of the
 *    opensocial.Permissions that were granted.
 *
 * @member opensocial
 */
opensocial.requestPermission = function(permissions, reason, opt_callback) {};


/**
 * @static
 * @class
 *
 * The permissions an app can ask for.
 *
 * <p>
 * <b>See also:</b>
 * <a href="opensocial.html#hasPermission">
 * <code>opensocial.hasPermission()</code></a>,
 * <a href="opensocial.html#requestPermission">
 * <code>opensocial.requestPermission()</code></a>
 *
 * @name opensocial.Permission
 */
opensocial.Permission = {
  /**
   * Access to the viewer person object
   * This field may be used interchangeably with the string 'viewer'.
   * @member opensocial.Permission
   */
  VIEWER : 'viewer'
};


/**
 * Gets the current environment for this gadget. You can use the environment to
 * make queries such as what profile fields and surfaces are supported by this
 * container, what parameters were passed to the current gadget, and so on.
 *
 * @return {opensocial.Environment}
 *    The current <a href="opensocial.Environment.html">environment</a>
 *
 * @member opensocial
 */
opensocial.getEnvironment = function() {};


/**
 * Creates a data request object to use for sending and fetching data from the
 * server.
 *
 * @return {opensocial.DataRequest} The
 *    <a href="opensocial.DataRequest.html">request</a> object
 * @member opensocial
 */
opensocial.newDataRequest = function() {};


/**
 * Creates an activity object,
 * which represents an activity on the server.
 * <p>
 * <b>See also:</b>
 * <a href="#requestCreateActivity">requestCreateActivity()</a>,
 * </p>
 *
 * <p>It is only required to set one of TITLE_ID or TITLE. In addition, if you
 * are using any variables in your title or title template,
 * you must set TEMPLATE_PARAMS.</p>
 *
 * <p>Other possible fields to set are: URL, MEDIA_ITEMS, BODY_ID, BODY,
 * EXTERNAL_ID, PRIORITY, STREAM_TITLE, STREAM_URL, STREAM_SOURCE_URL,
 * and STREAM_FAVICON_URL.</p>
 *
 * <p>Containers are only required to use TITLE_ID or TITLE, and may choose to
 * ignore additional parameters.</p>
 *
 * <p>See <a href="opensocial.Activity.Field.html">Field</a> for
 * more details.</p>
 *
 * @param {Map.&lt;opensocial.Activity.Field, Object&gt;} params
 *    Parameters defining the activity
 * @return {opensocial.Activity} The new
 *    <a href="opensocial.Activity.html">activity</a> object
 * @member opensocial
 */
opensocial.newActivity = function(params) {};


/**
 * Creates a media item.
 * Represents images, movies, and audio.
 * Used when creating activities on the server.
 *
 * @param {String} mimeType
 *    <a href="opensocial.MediaItem.Type.html">MIME type</a> of the
 *    media
 * @param {String} url Where the media can be found
 * @param {Map.&lt;opensocial.MediaItem.Field, Object&gt;} opt_params
 *    Any other fields that should be set on the media item object;
 *    all of the defined
 *    <a href="opensocial.MediaItem.Field.html">Field</a>s
 *    are supported
 *
 * @return {opensocial.MediaItem} The new
 *    <a href="opensocial.MediaItem.html">media item</a> object
 * @member opensocial
 */
opensocial.newMediaItem = function(mimeType, url, opt_params) {};


/**
 * Creates a media item associated with an activity.
 * Represents images, movies, and audio.
 * Used when creating activities on the server.
 *
 * @param {String} body The main text of the message
 * @param {Map.&lt;opensocial.Message.Field, Object&gt;} opt_params
 *    Any other fields that should be set on the message object;
 *    all of the defined
 *    <a href="opensocial.Message.Field.html">Field</a>s
 *    are supported
 *
 * @return {opensocial.Message} The new
 *    <a href="opensocial.Message.html">message</a> object
 * @member opensocial
 */
opensocial.newMessage = function(body, opt_params) {};


/**
 * @static
 * @class
 * The types of escaping that can be applied to person data or fields.
 *
 * @name opensocial.EscapeType
 */
opensocial.EscapeType = {
  /**
   * When used will HTML-escape the data.
   * This field may be used interchangeably with the string 'htmlEscape'.
   * @member opensocial.EscapeType
   */
  HTML_ESCAPE : 'htmlEscape',
  /**
   * When used will not escape the data.
   * This field may be used interchangeably with the string 'none'.
   * @member opensocial.EscapeType
   */
  NONE : 'none'
};


/**
 * Creates an IdSpec object.
 *
 * @param {Map.&lt;opensocial.IdSpec.Field, Object&gt;} parameters
 *     Parameters defining the id spec.
 * @return {opensocial.IdSpec} The new
 *     <a href="opensocial.IdSpec.html">IdSpec</a> object
 * @member opensocial
 */
opensocial.newIdSpec = function(params) {};


/**
 * Creates a NavigationParameters object.
 * <p>
 * <b>See also:</b>
 * <a href="#requestShareApp">requestShareApp()</a>
 * </p>
 *
 *
 * @param {Map.&lt;opensocial.NavigationParameters.Field, Object&gt;} parameters
 *     Parameters defining the navigation
 * @return {opensocial.NavigationParameters} The new
 *     <a href="opensocial.NavigationParameters.html">NavigationParameters</a> 
 *     object
 * @member opensocial
 */
opensocial.newNavigationParameters = function(params) {};
