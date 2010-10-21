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
 * @fileoverview Representation of an enum.
 */


/**
 * @class
 * Base interface for all enum objects.
 * This class allows containers to use constants for fields that are usually
 * have a common set of values.
 * There are two main ways to use this class.
 *
 * <p>
 * If your gadget just wants to display how much of a smoker someone is,
 * it can simply use:
 * </p>
 *
 * <pre>html = "This person smokes: " + person.getField('smoker').getValue();</pre>
 *
 * <p>
 * This value field will be correctly set up by the container. This is a place
 * where the container can even localize the value for the gadget so that it
 * always shows the right thing.
 * </p>
 *
 * <p>
 * If your gadget wants to have some logic around the smoker
 * field it can use:
 * </p>
 *
 * <pre>if (person.getField('smoker').getKey() != "NO") { //gadget logic here }</pre>
 *
 * <p class="note">
 * <b>Note:</b>
 * The key may be null if the person's smoker field cannot be coerced
 * into one of the standard enum types.
 * The value, on the other hand, is never null.
 * </p>
 *
 * @name opensocial.Enum
 */


/**
 * Base interface for all enum objects.
 *
 * @private
 * @constructor
 */
opensocial.Enum = function() {};


/**
 * Use this for logic within your gadget. If they key is null then the value
 * does not fit in the defined enums.
 *
 * @return {String} The enum's key. This should be one of the defined enums
 *     below.
 */
opensocial.Enum.prototype.getKey = function() {};


/**
 * The value of this enum. This will be a user displayable string. If the
 * container supports localization, the string will be localized.
 *
 * @return {String} The enum's value.
 */
opensocial.Enum.prototype.getDisplayValue = function() {};


/**
 * @static
 * @class
 * The enum keys used by the smoker field.
 * <p><b>See also:</b>
 * <a href="opensocial.Person.Field.html">
 * opensocial.Person.Field.Smoker</a>
 * </p>
 *
 * @name opensocial.Enum.Smoker
 */
opensocial.Enum.Smoker = {
  /** 
   * This field may be used interchangeably with the string 'NO'.
   * @member opensocial.Enum.Smoker 
   */
  NO : 'NO',
  /** 
   * This field may be used interchangeably with the string 'YES'.
   * @member opensocial.Enum.Smoker 
   */
  YES : 'YES',
  /** 
   * This field may be used interchangeably with the string 'SOCIALLY'.
   * @member opensocial.Enum.Smoker 
   */
  SOCIALLY : 'SOCIALLY',
  /** 
   * This field may be used interchangeably with the string 'OCCASIONALLY'.
   * @member opensocial.Enum.Smoker 
   */
  OCCASIONALLY : 'OCCASIONALLY',
  /** 
   * This field may be used interchangeably with the string 'REGULARLY'.
   * @member opensocial.Enum.Smoker 
   */
  REGULARLY : 'REGULARLY',
  /** 
   * This field may be used interchangeably with the string 'HEAVILY'.
   * @member opensocial.Enum.Smoker 
   */
  HEAVILY : 'HEAVILY',
  /** 
   * This field may be used interchangeably with the string 'QUITTING'.
   * @member opensocial.Enum.Smoker 
   */
  QUITTING : 'QUITTING',
  /** 
   * This field may be used interchangeably with the string 'QUIT'.
   * @member opensocial.Enum.Smoker 
   */
  QUIT : 'QUIT'
};


/**
 * @static
 * @class
 * The enum keys used by the drinker field.
 * <p><b>See also:</b>
 * <a href="opensocial.Person.Field.html">
 * opensocial.Person.Field.Drinker</a>
 * </p>
 *
 * @name opensocial.Enum.Drinker
 */
opensocial.Enum.Drinker = {
  /** 
   * This field may be used interchangeably with the string 'NO'.
   * @member opensocial.Enum.Drinker 
   */
  NO : 'NO',
  /** 
   * This field may be used interchangeably with the string 'YES'.
   * @member opensocial.Enum.Drinker 
   */
  YES : 'YES',
  /** 
   * This field may be used interchangeably with the string 'SOCIALLY'.
   * @member opensocial.Enum.Drinker 
   */
  SOCIALLY : 'SOCIALLY',
  /** 
   * This field may be used interchangeably with the string 'OCCASIONALLY'. 
   * @member opensocial.Enum.Drinker 
   */
  OCCASIONALLY : 'OCCASIONALLY',
  /**
   * This field may be used interchangeably with the string 'REGULARLY'.  
   * @member opensocial.Enum.Drinker 
   */
  REGULARLY : 'REGULARLY',
  /** 
   * This field may be used interchangeably with the string 'HEAVILY'.
   * @member opensocial.Enum.Drinker 
   */
  HEAVILY : 'HEAVILY',
  /** 
   * This field may be used interchangeably with the string 'QUITTING'.
   * @member opensocial.Enum.Drinker 
   */
  QUITTING : 'QUITTING',
  /** 
   * This field may be used interchangeably with the string 'QUIT'.
   * @member opensocial.Enum.Drinker 
   */
  QUIT : 'QUIT'
};


/**
 * @static
 * @class
 * The enum keys used by the gender field.
 * <p><b>See also:</b>
 * <a href="opensocial.Person.Field.html">
 * opensocial.Person.Field.Gender</a>
 * </p>
 *
 * @name opensocial.Enum.Gender
 */
opensocial.Enum.Gender = {
  /** 
   * This field may be used interchangeably with the string 'MALE'.
   * @member opensocial.Enum.Gender 
   */
  MALE : 'MALE',
  /** 
   * This field may be used interchangeably with the string 'FEMALE'.
   * @member opensocial.Enum.Gender 
   */
  FEMALE : 'FEMALE'
};


/**
 * @static
 * @class
 * The enum keys used by the lookingFor field.
 * <p><b>See also:</b>
 * <a href="opensocial.Person.Field.html">
 * opensocial.Person.Field.LookingFor</a>
 * </p>
 *
 * @name opensocial.Enum.LookingFor
 */
opensocial.Enum.LookingFor = {
  /** 
   * This field may be used interchangeably with the string 'DATING'.
   * @member opensocial.Enum.LookingFor 
   */
  DATING : 'DATING',
  /** 
   * This field may be used interchangeably with the string 'FRIENDS'.
   * @member opensocial.Enum.LookingFor 
   */
  FRIENDS : 'FRIENDS',
  /** 
   * This field may be used interchangeably with the string 'RELATIONSHIP'.
   * @member opensocial.Enum.LookingFor 
   */
  RELATIONSHIP : 'RELATIONSHIP',
  /** 
   * This field may be used interchangeably with the string 'NETWORKING'.
   * @member opensocial.Enum.LookingFor 
   */
  NETWORKING : 'NETWORKING',
  /** 
   * This field may be used interchangeably with the string 'ACTIVITY_PARTNERS'.
   * @member opensocial.Enum.LookingFor 
   */
  ACTIVITY_PARTNERS : 'ACTIVITY_PARTNERS',
  /** 
   * This field may be used interchangeably with the string 'RANDOM'.
   * @member opensocial.Enum.LookingFor 
   */
  RANDOM : 'RANDOM'
};


/**
 * @static
 * @class
 * The enum keys used by the networkPresence field.
 * <p><b>See also:</b>
 * <a href="opensocial.Person.Field.html">
 * opensocial.Person.Field.NetworkPresence</a>
 * </p>
 *
 * @name opensocial.Enum.Presence
 */
opensocial.Enum.Presence = {
  /**
   * The entity or resource is off line.
   * This field may be used interchangeably with the string 'OFFLINE'.
   *
   * @member opensocial.Enum.Presence
   */
  OFFLINE : 'OFFLINE',
  /**
   * The entity or resource is on line.
   * This field may be used interchangeably with the string 'ONLINE'.
   *
   * @member opensocial.Enum.Presence
   */
  ONLINE : 'ONLINE',
  /**
   * The entity or resource is temporarily away.
   * This field may be used interchangeably with the string 'AWAY'.
   *
   * @member opensocial.Enum.Presence
   */
  AWAY : 'AWAY',
  /**
   * The entity or resource is actively interested in chatting.
   * This field may be used interchangeably with the string 'CHAT'.
   * @member opensocial.Enum.Presence
   */
  CHAT : 'CHAT',
  /**
   * The entity or resource is busy (dnd = "Do Not Disturb").
   * This field may be used interchangeably with the string 'DND'.
   * @member opensocial.Enum.Presence
   */
  DND : 'DND',
  /**
   * The entity or resource is away for an extended period
   * (xa = "eXtended Away").
   * This field may be used interchangeably with the string 'XA'.
   * @member opensocial.Enum.Presence
   */
  XA : 'XA'
};
