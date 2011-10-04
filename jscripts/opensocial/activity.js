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
 * @class
 * Representation of an activity.
 *
 * <p>Activities are rendered with a title and an optional activity body.</p>
 *
 * <p>You may set the title and body directly as strings when calling
 * opensocial.newActivity. However, it is usually beneficial to create activities using
 * Message Templates for the title and body.</p>
 *
 * <p>Users will have many activities in their activity streams, and containers
 * will not show every activity that is visible to a user. To help display
 * large numbers of activities, containers will summarize a list of activities
 * from a given source to a single entry.</p>
 *
 * <p>You can provide Activity Summaries to customize the text shown when
 * multiple activities are summarized. If no customization is provided, a
 * container may ignore your activities altogether or provide default text
 * such as "Bob changed his status message + 20 other events like this."</p>
 * <ul>
 *  <li>Activity Summaries will always summarize around a specific key in a
 *   key/value pair. This is so that the summary can say something concrete
 *   (this is clearer in the example below).</li>                               
 *  <li>Other variables will have synthetic "Count" variables created with
 *   the total number of items summarized.</li>
 *  <li>Message ID of the summary is the message ID of the main template + ":" +
 *   the data key</li>
 * </ul>
 *
 * <p>Example summaries:
 * <pre>
 * &lt;messagebundle&gt;
 *   &lt;msg name="LISTEN_TO_THIS_SONG:Artist"&gt;
 *     ${Subject.Count} of your friends have suggested listening to songs
 *     by ${Artist}!
 *   &lt;/msg&gt;
 *   &lt;msg name="LISTEN_TO_THIS_SONG:Song"&gt;
 *     ${Subject.Count} of your friends have suggested listening to ${Song}
 *   !&lt;/msg&gt;
 *   &lt;msg name="LISTEN_TO_THIS_SONG:Subject"&gt;
 *    ${Subject.DisplayName} has recommended ${Song.Count} songs to you.
 *   &lt;/msg&gt;
 * &lt;/messagebundle&gt;
 * </pre></p>
 *
 * <p>
 * <b>See also:</b>
 * <a href="opensocial.Message.html">opensocial.Message</a>,
 * <a href="opensocial.html#newActivity">opensocial.newActivity()</a>,
 * <a href="opensocial.html#requestCreateActivity">
 * opensocial.requestCreateActivity()</a>
 *
 * @name opensocial.Activity
 */


/**
 * Base interface for all activity objects.
 *
 * Private, see opensocial.createActivity() for usage.
 *
 * @param {Map.&lt;opensocial.Activity.Field, Object&gt;} params
 *    Parameters defining the activity
 * @private
 * @constructor
 */
opensocial.Activity = function() {};


/**
 * @static
 * @class
 * All of the fields that activities can have.
 *
 * <p>It is only required to set one of TITLE_ID or TITLE. In addition, if you
 * are using any variables in your title or title template,
 * you must set TEMPLATE_PARAMS.</p>
 *
 * <p>Other possible fields to set are: URL, MEDIA_ITEMS, BODY_ID, BODY,
 * EXTERNAL_ID, PRIORITY, STREAM_TITLE, STREAM_URL, STREAM_SOURCE_URL,
 * and STREAM_FAVICON_URL.</p>
 *
 * <p>Containers are only required to use TITLE_ID or TITLE, they may ignore
 * additional parameters.</p>
 *
 * <p>
 * <b>See also:</b>
 * <a
 * href="opensocial.Activity.html#getField">opensocial.Activity.getField()</a>
 * </p>
 *
 * @name opensocial.Activity.Field
 */
opensocial.Activity.Field = {
  /**
   * <p>A string specifying the title template message ID in the gadget
   *   spec.</p>
   *
   * <p>The title is the primary text of an activity.</p>
   *
   * <p>Titles may only have the following HTML tags: &lt;b&gt; &lt;i&gt;,
   * &lt;a&gt;, &lt;span&gt;.
   * The container may ignore this formatting when rendering the activity.</p>
   *
   * <p>This field may be used interchangeably with the string 'titleId'.</p>
   *
   * @member opensocial.Activity.Field
   */
  TITLE_ID : 'titleId',

  /**
   * <p>A string specifying the primary text of an activity.</p>
   *
   * <p>Titles may only have the following HTML tags: &lt;b&gt; &lt;i&gt;,
   * &lt;a&gt;, &lt;span&gt;.
   * The container may ignore this formatting when rendering the activity.</p>
   *
   * <p>This field may be used interchangeably with the string 'title'.</p>
   *
   * @member opensocial.Activity.Field
   */
  TITLE : 'title',

  /**
   * <p>A map of custom key/value pairs associated with this activity.
   * These will be used for evaluation in templates.</p>
   *
   * <p>The data has type <code>Map&lt;String, Object&gt;</code>. The
   * object may be either a String or an opensocial.Person.</p>
   *
   * <p>When passing in a person with key PersonKey, can use the following
   * replacement variables in the template:</p>
   * <ul>
   *  <li>PersonKey.DisplayName - Display name for the person</li>
   *  <li>PersonKey.ProfileUrl. URL of the person's profile</li>
   *  <li>PersonKey.Id -  The ID of the person</li>
   *  <li>PersonKey - Container may replace with DisplayName, but may also
   *     optionally link to the user.</li>
   * </ul>
   *
   * <p>This field may be used interchangeably with the string 
   * 'templateParams'.</p>
   *
   * @member opensocial.Activity.Field
   */
  TEMPLATE_PARAMS : 'templateParams',

  /**
   * <p>A string specifying the URL that represents this activity.</p>
   *
   * <p>This field may be used interchangeably with the string 'url'.</p>
   *
   * @member opensocial.Activity.Field
   */
  URL : 'url',

  /**
   * <p>Any photos, videos, or images that should be associated
   * with the activity. Higher priority ones are higher in the list.
   * The data has type <code>Array&lt;
   * <a href="opensocial.MediaItem.html">MediaItem</a>&gt;</code>.</p>
   *
   * <p>This field may be used interchangeably with the string 'mediaItems'.</p>
   *
   * @member opensocial.Activity.Field
   */
  MEDIA_ITEMS : 'mediaItems',

  /**
   * <p>A string specifying the body template message ID in the gadget spec.</p>
   *
   * <p>The body is an optional expanded version of an activity.</p>
   *
   * <p>Bodies may only have the following HTML tags: &lt;b&gt; &lt;i&gt;,
   * &lt;a&gt;, &lt;span&gt;.
   * The container may ignore this formatting when rendering the activity.</p>
   *
   * <p>This field may be used interchangeably with the string 'bodyId'.</p>
   *
   * @member opensocial.Activity.Field
   */
  BODY_ID : 'bodyId',

  /**
   * <p>A string specifying an optional expanded version of an activity.</p>
   *
   * <p>Bodies may only have the following HTML tags: &lt;b&gt; &lt;i&gt;,
   * &lt;a&gt;, &lt;span&gt;.
   * The container may ignore this formatting when rendering the activity.</p>
   *
   * <p>This field may be used interchangeably with the string 'body'.</p>
   *
   * @member opensocial.Activity.Field
   */
  BODY : 'body',

  /**
   * <p>An optional string ID generated by the posting application.</p>
   *
   * <p>This field may be used interchangeably with the string 'externalId'.</p>
   *
   * @member opensocial.Activity.Field
   */
  EXTERNAL_ID : 'externalId',

  /**
   * <p>A string specifing the title of the stream.</p>
   *
   * <p>This field may be used interchangeably with the string 
   * 'streamTitle'.</p>
   *
   * @member opensocial.Activity.Field
   */
  STREAM_TITLE : 'streamTitle',

  /**
   * <p>A string specifying the stream's URL.</p>
   *
   * <p>This field may be used interchangeably with the string 'streamUrl'.</p>
   *
   * @member opensocial.Activity.Field
   */
  STREAM_URL : 'streamUrl',

  /**
   * <p>A string specifying the stream's source URL.</p>
   *
   * <p>This field may be used interchangeably with the string 
   * 'streamSourceUrl'.</p>
   *
   * @member opensocial.Activity.Field
   */
  STREAM_SOURCE_URL : 'streamSourceUrl',

  /**
   * <p>A string specifying the URL for the stream's favicon.</p>
   *
   * <p>This field may be used interchangeably with the string 
   * 'streamFaviconUrl'.</p>
   *
   * @member opensocial.Activity.Field
   */
  STREAM_FAVICON_URL : 'streamFaviconUrl',

  /**
   * <p>A number between 0 and 1 representing the relative priority of
   * this activity in relation to other activities from the same source</p>
   *
   * <p>This field may be used interchangeably with the string 'priority'.</p>
   *
   * @member opensocial.Activity.Field
   */
  PRIORITY : 'priority',

  /**
   * <p>A string ID that is permanently associated with this activity.
   * This value can not be set.</p>
   *
   * <p>This field may be used interchangeably with the string 'id'.</p>
   *
   * @member opensocial.Activity.Field
   */
  ID : 'id',

  /**
   * <p>The string ID of the user who this activity is for.
   * This value can not be set.</p>
   *
   * <p>This field may be used interchangeably with the string 'userId'.</p>
   *
   * @member opensocial.Activity.Field
   */
  USER_ID : 'userId',

  /**
   * <p>A string specifying the application that this activity is associated 
   * with. This value can not be set.</p>
   *
   * <p>This field may be used interchangeably with the string 'appId'.</p>
   *
   * @member opensocial.Activity.Field
   */
  APP_ID : 'appId',

  /**
   * <p>A string specifying the time at which this activity took place
   * in milliseconds since the epoch.
   * This value can not be set.</p>
   *
   * <p>This field may be used interchangeably with the string 'postedTime'.</p>
   *
   * @member opensocial.Activity.Field
   */
  POSTED_TIME : 'postedTime'
};


/**
 * Gets an ID that can be permanently associated with this activity.
 *
 * @return {String} The ID
 * @member opensocial.Activity
 */
opensocial.Activity.prototype.getId = function() {};


/**
 * Gets the activity data that's associated with the specified key.
 *
 * @param {String} key The key to get data for;
 *   see the <a href="opensocial.Activity.Field.html">Field</a> class
 * for possible values
 * @param {Map.&lt;opensocial.DataRequest.DataRequestFields, Object&gt;}
 *  opt_params Additional
 *    <a href="opensocial.DataRequest.DataRequestFields.html">params</a>
 *    to pass to the request.
 * @return {String} The data
 * @member opensocial.Activity
 */
opensocial.Activity.prototype.getField = function(key, opt_params) {};

/**
 * Sets data for this activity associated with the given key.
 *
 * @param {String} key The key to set data for
 * @param {String} data The data to set
 */
opensocial.Activity.prototype.setField = function(key, data) {};
