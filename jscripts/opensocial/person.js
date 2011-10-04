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
 * @fileoverview Representation of a person.
 */


/**
 * @class
 * Base interface for all person objects.
 *
 * @name opensocial.Person
 */


/**
 * Base interface for all person objects.
 *
 * @private
 * @constructor
 */
opensocial.Person = function() {};


/**
 * @static
 * @class
 * All of the fields that a person has. These are the supported keys for the
 * <a href="opensocial.Person.html#getField">Person.getField()</a> method.
 *
 * @name opensocial.Person.Field
 */
opensocial.Person.Field = {
  /**
   * A string ID that can be permanently associated with this person.
   * This field may be used interchangeably with the string 'id'.
   * @member opensocial.Person.Field
   */
  ID : 'id',

  /**
   * A opensocial.Name object containing the person's name.
   * This field may be used interchangeably with the string 'name'.
   * @member opensocial.Person.Field
   */
  NAME : 'name',

  /**
   * A String representing the person's nickname. This represents the casual 
   * way to address this person in real life. 
   * This field may be used interchangeably with the string 'nickname'.
   * @member opensocial.Person.Field
   */
  NICKNAME : 'nickname',

  /**
   * Person's photo thumbnail URL, specified as a string.
   * This URL must be fully qualified. Relative URLs will not work in gadgets.
   * This field may be used interchangeably with the string 'thumbnailUrl'.
   * @member opensocial.Person.Field
   */
  THUMBNAIL_URL : 'thumbnailUrl',

  /**
   * Person's profile URL, specified as a string.
   * This URL must be fully qualified. Relative URLs will not work in gadgets.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'profileUrl'.
   * @member opensocial.Person.Field
   */
  PROFILE_URL : 'profileUrl',

  /**
   * Person's current location, specified as an
   * <a href="opensocial.Address.html">Address</a>.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'currentLocation'.
   * @member opensocial.Person.Field
   */
  CURRENT_LOCATION : 'currentLocation',

  /**
   * Addresses associated with the person, specified as an Array of
   * <a href="opensocial.Address.html">Address</a>es.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'addresses'.
   * @member opensocial.Person.Field
   */
  ADDRESSES : 'addresses',

  /**
   * Emails associated with the person, specified as an Array of
   * <a href="opensocial.Email.html">Email</a>s.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'emails'.
   * @member opensocial.Person.Field
   */
  EMAILS : 'emails',

  /**
   * Phone numbers associated with the person, specified as an Array of
   * <a href="opensocial.Phone.html">Phone</a>s.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'phoneNumbers'.
   * @member opensocial.Person.Field
   */
  PHONE_NUMBERS : 'phoneNumbers',

  /**
   * A general statement about the person, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'aboutMe'.
   * @member opensocial.Person.Field
   */
  ABOUT_ME : 'aboutMe',

  /**
   * Person's status, headline or shoutout, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'status'.
   * @member opensocial.Person.Field
   */
  STATUS : 'status',

  /**
   * Person's profile song, specified as an opensocial.Url.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'profileSong'.
   * @member opensocial.Person.Field
   */
  PROFILE_SONG : 'profileSong',

  /**
   * Person's profile video, specified as an opensocial.Url.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'profileVideo'.
   * @member opensocial.Person.Field
   */
  PROFILE_VIDEO : 'profileVideo',

  /**
   * Person's gender, specified as an opensocial.Enum with the enum's
   * key referencing opensocial.Enum.Gender.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'gender'.
   * @member opensocial.Person.Field
   */
  GENDER : 'gender',

  /**
   * Person's sexual orientation, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'sexualOrientation'.
   * @member opensocial.Person.Field
   */
  SEXUAL_ORIENTATION : 'sexualOrientation',

  /**
   * Person's relationship status, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 
   * 'relationshipStatus'.
   * @member opensocial.Person.Field
   */
  RELATIONSHIP_STATUS : 'relationshipStatus',

  /**
   * Person's age, specified as a number.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'age'.
   * @member opensocial.Person.Field
   */
  AGE : 'age',

  /**
   * Person's date of birth, specified as a Date object.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'dateOfBirth'.
   * @member opensocial.Person.Field
   */
  DATE_OF_BIRTH : 'dateOfBirth',

  /**
   * Person's body characteristics, specified as an opensocial.BodyType.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'bodyType'.
   * @member opensocial.Person.Field
   */
  BODY_TYPE : 'bodyType',

  /**
   * Person's ethnicity, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'ethnicity'.
   * @member opensocial.Person.Field
   */
  ETHNICITY : 'ethnicity',

  /**
   * Person's smoking status, specified as an opensocial.Enum with the enum's
   * key referencing opensocial.Enum.Smoker.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'smoker'.
   * @member opensocial.Person.Field
   */
  SMOKER : 'smoker',

  /**
   * Person's drinking status, specified as an opensocial.Enum with the enum's
   * key referencing opensocial.Enum.Drinker.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'drinker'.
   * @member opensocial.Person.Field
   */
  DRINKER : 'drinker',

  /**
   * Description of the person's children, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'children'.
   * @member opensocial.Person.Field
   */
  CHILDREN : 'children',

  /**
   * Description of the person's pets, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'pets'.
   * @member opensocial.Person.Field
   */
  PETS : 'pets',

  /**
   * Description of the person's living arrangement, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'livingArrangement'.
   * @member opensocial.Person.Field
   */
  LIVING_ARRANGEMENT : 'livingArrangement',

  /**
   * Person's time zone, specified as the difference in minutes between
   * Greenwich Mean Time (GMT) and the user's local time. See
   * Date.getTimezoneOffset() in javascript for more details on this format.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'timeZone'.
   * @member opensocial.Person.Field
   */
  TIME_ZONE : 'timeZone',

  /**
   * List of the languages that the person speaks as ISO 639-1 codes,
   * specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'languagesSpoken'.
   * @member opensocial.Person.Field
   */
  LANGUAGES_SPOKEN : 'languagesSpoken',

  /**
   * Jobs the person has held, specified as an Array of
   * <a href="opensocial.Organization.html">Organization</a>s.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'jobs'.
   * @member opensocial.Person.Field
   */
  JOBS : 'jobs',

  /**
   * Person's favorite jobs, or job interests and skills, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'jobInterests'.
   * @member opensocial.Person.Field
   */
  JOB_INTERESTS : 'jobInterests',

  /**
   * Schools the person has attended, specified as an Array of
   * <a href="opensocial.Organization.html">Organization</a>s.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'schools'.
   * @member opensocial.Person.Field
   */
  SCHOOLS : 'schools',

  /**
   * Person's interests, hobbies or passions, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'interests'.
   * @member opensocial.Person.Field
   */
  INTERESTS : 'interests',

  /**
   * URLs related to the person, their webpages, or feeds. Specified as an
   * Array of opensocial.Url.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'urls'.
   * @member opensocial.Person.Field
   */
  URLS : 'urls',

  /**
   * Person's favorite music, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'music'.
   * @member opensocial.Person.Field
   */
  MUSIC : 'music',

  /**
   * Person's favorite movies, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'movies'.
   * @member opensocial.Person.Field
   */
  MOVIES : 'movies',

  /**
   * Person's favorite TV shows, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'tvShows'.
   * @member opensocial.Person.Field
   */
  TV_SHOWS : 'tvShows',

  /**
   * Person's favorite books, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'books'.
   * @member opensocial.Person.Field
   */
  BOOKS : 'books',

  /**
   * Person's favorite activities, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'activities'.
   * @member opensocial.Person.Field
   */
  ACTIVITIES : 'activities',

  /**
   * Person's favorite sports, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'sports'.
   * @member opensocial.Person.Field
   */
  SPORTS : 'sports',

  /**
   * Person's favorite heroes, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'heroes'.
   * @member opensocial.Person.Field
   */
  HEROES : 'heroes',

  /**
   * Person's favorite quotes, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'quotes'.
   * @member opensocial.Person.Field
   */
  QUOTES : 'quotes',

  /**
   * Person's favorite cars, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'cars'.
   * @member opensocial.Person.Field
   */
  CARS : 'cars',

  /**
   * Person's favorite food, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'food'.
   * @member opensocial.Person.Field
   */
  FOOD : 'food',

  /**
   * Person's turn ons, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'turnOns'.
   * @member opensocial.Person.Field
   */
  TURN_ONS : 'turnOns',

  /**
   * Person's turn offs, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'turnOffs'.
   * @member opensocial.Person.Field
   */
  TURN_OFFS : 'turnOffs',

  /**
   * Arbitrary tags about the person, specified as an Array of strings.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'tags'.
   * @member opensocial.Person.Field
   */
  TAGS : 'tags',

  /**
   * Person's comments about romance, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'romance'.
   * @member opensocial.Person.Field
   */
  ROMANCE : 'romance',

  /**
   * What the person is scared of, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'scaredOf'.
   * @member opensocial.Person.Field
   */
  SCARED_OF : 'scaredOf',

  /**
   * Describes when the person is happiest, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'happiestWhen'.
   * @member opensocial.Person.Field
   */
  HAPPIEST_WHEN : 'happiestWhen',

  /**
   * Person's thoughts on fashion, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'fashion'.
   * @member opensocial.Person.Field
   */
  FASHION : 'fashion',

  /**
   * Person's thoughts on humor, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'humor'.
   * @member opensocial.Person.Field
   */
  HUMOR : 'humor',

  /**
   * Person's statement about who or what they are looking for, or what they are
   * interested in meeting people for. Specified as an Array of opensocial.Enum
   * with the enum's key referencing opensocial.Enum.LookingFor.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'lookingFor'.
   * @member opensocial.Person.Field
   */
  LOOKING_FOR : 'lookingFor',

  /**
   * Person's relgion or religious views, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'religion'.
   * @member opensocial.Person.Field
   */
  RELIGION : 'religion',

  /**
   * Person's political views, specified as a string.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'politicalViews'.
   * @member opensocial.Person.Field
   */
  POLITICAL_VIEWS : 'politicalViews',

  /**
   * A boolean indicating whether the person has used the current app.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'hasApp'.
   * @member opensocial.Person.Field
   */
  HAS_APP : 'hasApp',

  /**
   * Person's current network status. Specified as an Enum with the enum's
   * key referencing opensocial.Enum.Presence.
   * Container support for this field is OPTIONAL.
   * This field may be used interchangeably with the string 'networkPresence'.
   * @member opensocial.Person.Field
   */
  NETWORK_PRESENCE : 'networkPresence'
};


/**
 * Gets an ID that can be permanently associated with this person.
 *
 * @return {String} The ID
 */
opensocial.Person.prototype.getId = function() {};


/**
 * Gets a text display name for this person; guaranteed to return
 * a useful string.
 *
 * @return {String} The display name
 */
opensocial.Person.prototype.getDisplayName = function() {};


/**
 * Gets data for this person that is associated with the specified key.
 *
 * @param {String} key The key to get data for;
 *    keys are defined in <a href="opensocial.Person.Field.html"><code>
 *    Person.Field</code></a>
 * @param {Map.&lt;opensocial.DataRequest.DataRequestFields, Object&gt;}
 *  opt_params Additional
 *    <a href="opensocial.DataRequest.DataRequestFields.html">params</a>
 *    to pass to the request.
 * @return {String} The data
 */
opensocial.Person.prototype.getField = function(key, opt_params) {};


/**
 * Returns true if this person object represents the currently logged in user.
 *
 * @return {Boolean} True if this is the currently logged in user;
 *   otherwise, false
 */
opensocial.Person.prototype.isViewer = function() {};


/**
 * Returns true if this person object represents the owner of the current page.
 *
 * @return {Boolean} True if this is the owner of the page;
 *   otherwise, false
 */
opensocial.Person.prototype.isOwner = function() {};
