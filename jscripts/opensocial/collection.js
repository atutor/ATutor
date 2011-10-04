/**
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
 * @fileoverview Collection of multiple objects with useful accessors.
 *
 * May also represent subset of a larger collection (i.e. page 1 of 10), and
 * contain information about the larger collection.
 */


/**
 * @class
 * Collection of multiple objects with useful accessors.
 * May also represent subset of a larger collection
 * (for example, page 1 of 10)
 * and contain information about the larger collection.
 *
 * @name opensocial.Collection
 */


/**
 * Create a collection.
 *
 * @private
 * @constructor
 */
opensocial.Collection = function() {};


/**
 * Finds the entry with the given ID value, or returns null if none is found.
 * @param {String} id The ID to look for
 * @return {Object?} The data
 */
opensocial.Collection.prototype.getById = function(id) {};


/**
 * Gets the size of this collection,
 * which is equal to or less than the
 * total size of the result.
 * @return {Number} The size of this collection
 */
opensocial.Collection.prototype.size = function() {};


/**
 * Executes the provided function once per member of the collection,
 * with each member in turn as the
 * parameter to the function.
 * @param {Function} fn The function to call with each collection entry
 */
opensocial.Collection.prototype.each = function(fn) {};


/**
 * Returns an array of all the objects in this collection.
 * @return {Array.&lt;Object&gt;} The values in this collection
 */
opensocial.Collection.prototype.asArray = function() {};


/**
 * Gets the total size of the larger result set
 * that this collection belongs to.
 * @return {Number} The total size of the result
 */
opensocial.Collection.prototype.getTotalSize = function() {};


/**
 * Gets the offset of this collection within a larger result set.
 * @return {Number} The offset into the total collection
 */
opensocial.Collection.prototype.getOffset = function() {};
