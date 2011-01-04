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
 * @fileoverview DataResponse containing information about
 * friends, contacts, profile, app data, and activities.
 *
 * Whenever a dataRequest is sent to the server it will return a dataResponse
 * object. Values from the server will be mapped to the requested keys specified
 * in the dataRequest.
 */


/**
 * @class
 * This object contains the requested server data mapped to the requested keys.
 *
 * <p>
 * <b>See also:</b>
 * <a href="opensocial.DataRequest.html">DataRequest</a>
 * </p>
 *
 * @name opensocial.DataResponse
 */

/**
 * Construct the data response.
 * This object contains the requested server data mapped to the requested keys.
 *
 * @param {Map.<String, ResponseItem>} responseItems Key/value map of data
 *    response information
 * @param {Boolean} opt_globalError Optional field indicating whether there were
 *    any errors generating this data response
 *
 * @private
 * @constructor
 */
opensocial.DataResponse = function() {};


/**
 * Returns true if there was an error in fetching this data from the server.
 *
 * @return {Boolean} True if there was an error; otherwise, false
 * @member opensocial.DataResponse
 */
opensocial.DataResponse.prototype.hadError = function() {};


/**
 * If the entire request had a batch level error, returns the error message.
 *
 * @return {String} A human-readable description of the error that occurred.
 */
opensocial.DataResponse.prototype.getErrorMessage = function() {};


/**
 * Gets the ResponseItem for the requested field.
 *
 * @return {opensocial.ResponseItem} The requested
 *    <a href="opensocial.ResponseItem.html">response</a> calculated by the
 *    server
 * @member opensocial.DataResponse
 */
opensocial.DataResponse.prototype.get = function(key) {};
