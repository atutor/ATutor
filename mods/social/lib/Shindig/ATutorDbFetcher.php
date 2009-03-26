<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

class ATutorDbFetcher {
  private $url_prefix;
  private $cache;
  public $db;

  function debug($var, $title='') {
	echo '<pre style="border: 1px black solid; padding: 0px; margin: 10px;" title="debugging box">';
	if ($title) {
		echo '<h4>'.$title.'</h4>';
	}
	
	ob_start();
	print_r($var);
	$str = ob_get_contents();
	ob_end_clean();

	$str = str_replace('<', '&lt;', $str);

	$str = str_replace('[', '<span style="color: red; font-weight: bold;">[', $str);
	$str = str_replace(']', ']</span>', $str);
	$str = str_replace('=>', '<span style="color: blue; font-weight: bold;">=></span>', $str);
	$str = str_replace('Array', '<span style="color: purple; font-weight: bold;">Array</span>', $str);
	echo $str;
	echo '</pre>';
}

  // Singleton
  private static $fetcher;

  private function connectDb() {
    // one of the class paths should point to ATutor's document root, abuse that fact to find our config
    $extension_class_paths = Config::get('extension_class_paths');
    foreach (explode(',', $extension_class_paths) as $path) {
      if (file_exists($path . "/ATutorDbFetcher.php")) {
        $configFile = $path . '/../../../../include/lib/mysql_connect.inc.php';
        if (file_exists($configFile)) {
		  define('AT_INCLUDE_PATH', $path . '/../../../../include/');
		  include(AT_INCLUDE_PATH.'config.inc.php');
		  include(AT_INCLUDE_PATH . 'lib/constants.inc.php');
		  include(AT_INCLUDE_PATH . 'lib/mysql_connect.inc.php');
		  $this->db = $db;
          break;
        }
      }
    }
    if (! isset($configFile)) {
      throw new Exception("Could not locate ATutor's configuration file while scanning extension_class_paths ({$extension_class_paths})");
    }
//    $this->db = mysqli_connect($config['db_host'], $config['db_user'], $config['db_passwd'], $config['db_database']);
//    mysqli_select_db($this->db, $config['db_database']);
//    $this->url_prefix = $config['partuza_url'];
  }

  private function __construct() {
    $cache = Config::get('data_cache');
    $this->cache = new $cache();
  }

  private function checkDb() {
    if (! is_resource($this->db)) {
      $this->connectDb();
    }
  }

  private function __clone() {  // private, don't allow cloning of a singleton
  }

  static function get() {
    // This object is a singleton
    if (! isset(ATutorDbFetcher::$fetcher)) {
      ATutorDbFetcher::$fetcher = new ATutorDbFetcher();
    }
    return ATutorDbFetcher::$fetcher;
  }

  public function createActivity($member_id, $activity, $app_id = '0') {
    $this->checkDb();
    $app_id = intval($app_id);
    $person_id = intval($member_id);
    $title = trim(isset($activity['title']) ? $activity['title'] : '');
    if (empty($title)) {
      throw new Exception("Invalid activity: empty title");
    }
//    $body = isset($activity['body']) ? $activity['body'] : '';
    $title = mysql_real_escape_string($title);
//    $body = mysql_real_escape_string($body);
	$sql = "insert into ".TABLE_PREFIX."social_activities (id, member_id, application_id, title, created_date) values (0, $member_id, $app_id, '$title', NOW())";
    mysql_query($sql, $this->db);
    if (! ($activityId = mysql_insert_id($this->db))) {
      return false;
    }

/**
 * I don't have this on my system yet. -Harris

    $mediaItems = isset($activity['mediaItems']) ? $activity['mediaItems'] : array();
    if (count($mediaItems)) {
      foreach ($mediaItems as $mediaItem) {
        $type = isset($mediaItem['type']) ? $mediaItem['type'] : '';
        $mimeType = isset($mediaItem['mimeType']) ? $mediaItem['mimeType'] : '';
        $url = isset($mediaItem['url']) ? $mediaItem['url'] : '';
        $type = mysqli_real_escape_string($this->db, trim($type));
        $mimeType = mysqli_real_escape_string($this->db, trim($mimeType));
        $url = mysqli_real_escape_string($this->db, trim($url));
        if (! empty($mimeType) && ! empty($type) && ! empty($url)) {
          mysqli_query($this->db, "insert into activity_media_items (id, activity_id, mime_type, media_type, url) values (0, $activityId, '$mimeType', '$type', '$url')");
          if (! mysqli_insert_id($this->db)) {
            return false;
          }
        } else {
          return false;
        }
      }
    }
*/
    return true;
  }

//  public function getActivities($ids, $appId, $sortBy, $filterBy, $filterOp, $filterValue, $startIndex, $count, $fields, $activityIds) {
  public function getActivities($ids, $appId, $sortBy, $filterBy, $filterOp, $filterValue, $startIndex, $count, $fields) {
	global $db;
    //TODO add support for filterBy, filterOp and filterValue
    $this->checkDb();
    $activities = array();
    foreach ($ids as $key => $val) {
      $ids[$key] = mysql_real_escape_string($val);
    }
    $ids = implode(',', $ids);
    if (isset($activityIds) && is_array($activityIds)) {
      foreach ($activityIds as $key => $val) {
        $activityIds[$key] = mysql_real_escape_string($val);
      }
      $activityIdQuery = " and activities.id in (".implode(',', $activityIds);
    } else {
      $activityIdQuery = '';
    }
    // return a proper totalResults count
	$sql = "select count(id) from ".TABLE_PREFIX."social_activities where ".TABLE_PREFIX."activities.person_id in ($ids) $activityIdQuery";
    $res = mysql_query($sql, $this->db);

    if ($res !== false) {
      list($totalResults) = mysql_fetch_row($res);
    } else {
      $totalResults = '0';
    }
    $startIndex = (! is_null($startIndex) && $startIndex !== false && is_numeric($startIndex)) ? intval($startIndex) : '0';
    $count = (! is_null($count) && $count !== false && is_numeric($count)) ? intval($count) : '20';
    $activities['totalResults'] = $totalResults;
    $activities['startIndex'] = $startIndex;
    $activities['count'] = $count;
    $query = "
			select 
				".TABLE_PREFIX."social_activities.member_id as member_id,
				".TABLE_PREFIX."social_activities.id as activity_id,
				".TABLE_PREFIX."social_activities.title as title,
				".TABLE_PREFIX."social_activities.created as created
			from 
				".TABLE_PREFIX."social_activities
			where
				".TABLE_PREFIX."social_activities.member_id in ($ids)
				$activityIdQuery
			order by 
				created desc
			limit 
				$startIndex, $count
			";
    $res = mysql_query($query, $this->db);
    if ($res) {
      if (@mysql_num_rows($res)) {
        while ($row = @mysql_fetch_assoc($res)) {
          $activity = new Activity($row['activity_id'], $row['member_id']);
          $activity->setStreamTitle('activities');
          $activity->setTitle($row['activity_title']);
//          $activity->setBody($row['activity_body']);
          $activity->setPostedTime($row['created']);
          $activity->setMediaItems($this->getMediaItems($row['activity_id']));
          $activities[] = $activity;
        }
      } elseif (isset($activityIds) && is_array($activityIds)) {
        // specific activity id was specified, return a not found flag
        return false;
      }
      return $activities;
    } else {
      return false;
    }
  }

  public function deleteActivities($userId, $appId, $activityIds) {
    $this->checkDb();
    foreach ($activityIds as $key => $val) {
      $activityIds[$key] = mysql_real_escape_string($val);
    }
    $activityIds = implode(',', $activityIds);
    $userId = intval($userId);
    $appId = intval($appId);
	//can use this instead: 	$sql = "delete from ".TABLE_PREFIX."social_activities where id in ($activityIds)";
	$sql = "delete from ".TABLE_PREFIX."social_activities where member_id = $userId and application_id = $appId and id in ($activityIds)";
	
    mysql_query($sql, $this->db);
    return (mysql_affected_rows($this->db) != 0);
  }

/**
  * I didn't implement this yet
  */
  private function getMediaItems($activity_id) {
    $media = array();
//    $activity_id = mysqli_real_escape_string($db, $activity_id);
//    $res = mysqli_query($this->db, "select mime_type, media_type, url from ".TABLE_PREFIX."activity_media_items where activity_id = $activity_id");
//    while (list($mime_type, $type, $url) = @mysqli_fetch_row($res)) {
//      $media[] = new MediaItem($mime_type, $type, $url);
//    }
    return $media;
  }  

  public function getFriendIds($member_id) {
	global $db;
    $this->checkDb();
    $ret = array();
    $person_id = intval($person_id);
	$sql = "select member_id, friend_id from ".TABLE_PREFIX."friends where member_id = $member_id or friend_id = $member_id";
    $res = mysql_query($sql, $this->db);
    while (list($mid, $fid) = mysql_fetch_row($res)) {
      $id = ($mid == $member_id) ? $fid : $mid;
      $ret[] = $id;
    }
    return $ret;
  }

  public function setAppData($member_id, $key, $value, $app_id) {
	$this->checkDb();
    $member_id = intval($member_id);
    $key = mysql_real_escape_string($key);
    $value = mysql_real_escape_string($value);
    $app_id = intval($app_id);
    if (empty($value)) {
      // empty key kind of became to mean "delete data" (was an old orkut hack that became part of the spec spec)
	  $sql = "delete from ".TABLE_PREFIX."social_application_settings where application_id = $app_id and member_id = $member_id and name = '$key'";
      if (! @mysql_query($sql, $this->db)) {
        return false;
      }
    } else {
		$sql ="insert into ".TABLE_PREFIX."social_application_settings (application_id, member_id, name, value) values ($app_id, $member_id, '$key', '$value') on duplicate key update value = '$value'";
      if (! @mysql_query($sql, $this->db)) {
        return false;
      }
    }
    return true;
  }

  public function deleteAppData($member_id, $key, $app_id) {
    global $db;
	$this->checkDb();
    $person_id = intval($member_id);
    $app_id = intval($app_id);
    if ($key == '*') {
		$sql = "delete from ".TABLE_PREFIX."social_application_settings where application_id = $app_id and member_id = $member_id";
      if (! @mysql_query($sql, $this->db)) {
        return false;
      }
    } else {
      $key = mysql_real_escape_string($this->db, $key);
	  $sql = "delete from ".TABLE_PREFIX."social_application_settings where application_id = $app_id and member_id = $member_id and name = '$key'";
      if (! @mysql_query($sql, $this->db)) {
        return false;
      }
    }
    return true;
  }

  public function getAppData($ids, $keys, $app_id) {
    $this->checkDb();
    $data = array();
    foreach ($ids as $key => $val) {
      if (! empty($val)) {
        $ids[$key] = intval($val);
      }
    }
    if (! isset($keys[0])) {
      $keys[0] = '*';
    }
    if ($keys[0] == '*') {
      $keys = '';
    } elseif (is_array($keys)) {
      foreach ($keys as $key => $val) {
        $keys[$key] = "'" . addslashes($val) . "'";
      }
      $keys = "and name in (" . implode(',', $keys) . ")";
    } else {
      $keys = '';
    }
	$sql = "select member_id, name, value from ".TABLE_PREFIX."social_application_settings where application_id = $app_id and member_id in (" . implode(',', $ids) . ") $keys";
    $res = mysql_query($sql, $this->db);
    while (list($member_id, $key, $value) = mysql_fetch_row($res)) {
      if (! isset($data[$member_id])) {
        $data[$member_id] = array();
      }
      $data[$member_id][$key] = $value;
    }
    return $data;
  }

  public function getPeople($ids, $fields, $options, $token) {
	$first = $options->getStartIndex();
    $max = $options->getCount();
    $this->checkDb();
    $ret = array();
    $filterQuery = '';
    if ($options->getFilterBy() == 'hasApp') {
      // remove the filterBy field, it's taken care of in the query already, otherwise filterResults will disqualify all results
      $options->setFilterBy(null);
      $appId = $token->getAppId();
      $filterQuery = " and id in (select member_id from ".TABLE_PREFIX."social_applications where application_id = $appId)";
    } elseif ($options->getFilterBy() == 'all') {
      $options->setFilterBy(null);
    }
    $query = "SELECT member.*, info.interests, info.associations, info.awards FROM ".TABLE_PREFIX."members member LEFT JOIN ".TABLE_PREFIX."social_member_additional_information info ON member.member_id=info.member_id WHERE  member.member_id IN (" . implode(',', $ids) . ") $filterQuery ORDER BY member.member_id ";

    $res = mysql_query($query, $this->db);
    if ($res) {
      while ($row = mysql_fetch_assoc($res)) {
        $member_id = intval($row['member_id']);
        $name = new Name($row['first_name'] . ' ' . $row['last_name']);

        $name->setGivenName($row['first_name']);
        $name->setFamilyName($row['last_name']);
        $person = new Person($row['member_id'], $name);
        $person->setDisplayName($name->getFormatted());
        $person->setAboutMe($row['about_me']);
        $person->setAge($row['age']);
        $person->setChildren($row['children']);
        $person->setBirthday(date('Y-m-d', $row['date_of_birth']));
        $person->setEthnicity($row['ethnicity']);
        $person->setFashion($row['fashion']);
        $person->setHappiestWhen($row['happiest_when']);
        $person->setHumor($row['humor']);
        $person->setJobInterests($row['job_interests']);
        $person->setLivingArrangement($row['living_arrangement']);
        $person->setLookingFor($row['looking_for']);
        $person->setNickname($row['nickname']);
        $person->setPets($row['pets']);
        $person->setPoliticalViews($row['political_views']);
        $person->setProfileSong($row['profile_song']);
        $person->setProfileUrl($this->url_prefix . '/profile/' . $row['member_id']);
        $person->setProfileVideo($row['profile_video']);
        $person->setRelationshipStatus($row['relationship_status']);
        $person->setReligion($row['religion']);
        $person->setRomance($row['romance']);
        $person->setScaredOf($row['scared_of']);
        $person->setSexualOrientation($row['sexual_orientation']);
        $person->setStatus($row['status']);
        $person->setThumbnailUrl(! empty($row['thumbnail_url']) ? $this->url_prefix . $row['thumbnail_url'] : '');

        if (! empty($row['thumbnail_url'])) {
          // also report thumbnail_url in standard photos field (this is the only photo supported by ATutor)
          $person->setPhotos(array(
              new Photo($this->url_prefix . 'get_profile_img.php?id='.$row['member_id'], 'thumbnail', true)));
        }
        $person->setUtcOffset(sprintf('%+03d:00', $row['time_zone'])); // force "-00:00" utc-offset format
        if (! empty($row['drinker'])) {
          $person->setDrinker($row['drinker']);
        }
        if (! empty($row['gender'])) {
          $person->setGender(strtolower($row['gender']));
        }
		if (! empty($row['email'])){
		  //TODO: Assumed <static> object TYPE to be "home".  Change it if ATutor starts accepting more than one email
		  $email = new Email(strtolower($row['email']), 'home');
          $person->setEmails($email);
		}
		if (! empty($row['interests'])){
          $strings = explode(',', $row['interests']);
          $person->setInterests($strings);
		}

		//TODO: Not in ATutor yet, skeleton field
        if (! empty($row['smoker'])) {
          $person->setSmoker($row['smoker']);
        }
        /* the following fields require additional queries so are only executed if requested */
        if (isset($fields['activities']) || isset($fields['@all'])) {
          $activities = array();
		  $sql = "select title from ".TABLE_PREFIX."social_activities where member_id = " . $member_id;
          $res2 = mysql_query($sql, $this->db);

          while (list($activity) = mysql_fetch_row($res2)) {
            $activities[] = $activity;
          }
          $person->setActivities($activities);
        }

        if (isset($fields['addresses']) || isset($fields['@all'])) {
          $addresses = array();
		  $sql = "select address, postal, city, province, country from ".TABLE_PREFIX."members m where m.member_id = " . $member_id;
          $res2 = mysql_query($sql, $this->db);
          while ($row = mysql_fetch_assoc($res2)) {
            if (empty($row['unstructured_address'])) {
              $row['unstructured_address'] = trim($row['street_address'] . " " . $row['province'] . " " . $row['country']);
            }
            $addres = new Address($row['unstructured_address']);
            $addres->setCountry($row['country']);
            $addres->setLatitude($row['latitude']);
            $addres->setLongitude($row['longitude']);
            $addres->setLocality($row['locality']);
            $addres->setPostalCode($row['postal_code']);
            $addres->setRegion($row['province']);
            $addres->setStreetAddress($row['street_address']);
            $addres->setType($row['address_type']);
            //FIXME quick and dirty hack to demo PC
            $addres->setPrimary(true);
            $addresses[] = $addres;
          }
          $person->setAddresses($addresses);
        }
		//TODO: Not in ATutor yet, skeleton field
        if (isset($fields['bodyType']) || isset($fields['@all'])) {
          $res2 = mysqli_query($db, "select * from ".TABLE_PREFIX."person_body_type where person_id = " . $person_id);
          if (@mysqli_num_rows($res2)) {
            $row = @mysql_fetch_array($res2, MYSQLI_ASSOC);
            $bodyType = new BodyType();
            $bodyType->setBuild($row['build']);
            $bodyType->setEyeColor($row['eye_color']);
            $bodyType->setHairColor($row['hair_color']);
            $bodyType->setHeight($row['height']);
            $bodyType->setWeight($row['weight']);
            $person->setBodyType($bodyType);
          }
        }
		//TODO: Not in ATutor yet, skeleton field
        if (isset($fields['books']) || isset($fields['@all'])) {
          $books = array();
          $res2 = mysqli_query($db, "select book from ".TABLE_PREFIX."person_books where person_id = " . $person_id);
          while (list($book) = @mysqli_fetch_row($res2)) {
            $books[] = $book;
          }
          $person->setBooks($books);
        }
		//TODO: Not in ATutor yet, skeleton field
        if (isset($fields['cars']) || isset($fields['@all'])) {
          $cars = array();
          $res2 = mysqli_query($db, "select car from ".TABLE_PREFIX."person_cars where person_id = " . $person_id);
          while (list($car) = @mysqli_fetch_row($res2)) {
            $cars[] = $car;
          }
          $person->setCars($cars);
        }
		//TODO: Not in ATutor yet, skeleton field
        if (isset($fields['currentLocation']) || isset($fields['@all'])) {
          $addresses = array();
          $res2 = mysqli_query($db, "select a.* from ".TABLE_PREFIX."person_current_location pcl, ".TABLE_PREFIX."person_addresses pa, ".TABLE_PREFIX."addresses a where a.id = pcl.address_id and pa.person_id = " . $person_id);
          if (@mysqli_num_rows($res2)) {
            $row = mysqli_fetch_array($res2, MYSQLI_ASSOC);
            if (empty($row['unstructured_address'])) {
              $row['unstructured_address'] = trim($row['street_address'] . " " . $row['region'] . " " . $row['country']);
            }
            $addres = new Address($row['unstructured_address']);
            $addres->setCountry($row['country']);
            $addres->setLatitude($row['latitude']);
            $addres->setLongitude($row['longitude']);
            $addres->setLocality($row['locality']);
            $addres->setPostalCode($row['postal_code']);
            $addres->setRegion($row['region']);
            $addres->setStreetAddress($row['street_address']);
            $addres->setType($row['address_type']);
            $person->setCurrentLocation($addres);
          }
        }
		//TODO: Email is a singleton in ATutor, expand it.  A person may have 1+ emails nowadays.
		//added to the above with all the other member's properties
/*
        if (isset($fields['emails']) || isset($fields['@all'])) {
          $emails = array();
		  $sql = "select address, email_type from ".TABLE_PREFIX."person_emails where person_id = " . $person_id;
          $res2 = mysql_query();
          while (list($address, $type) = @mysqli_fetch_row($res2)) {
            $emails[] = new Email(strtolower($address), $type); // TODO: better email canonicalization; remove dups
          }
          $person->setEmails($emails);
        }
*/
		//TODO: Not in ATutor yet, skeleton field
        if (isset($fields['food']) || isset($fields['@all'])) {
          $foods = array();
          $res2 = mysqli_query($db, "select food from ".TABLE_PREFIX."person_foods where person_id = " . $person_id);
          while (list($food) = @mysqli_fetch_row($res2)) {
            $foods[] = $food;
          }
          $person->setFood($foods);
        }
		//TODO: Not in ATutor yet, skeleton field
        if (isset($fields['heroes']) || isset($fields['@all'])) {
          $strings = array();
          $res2 = mysqli_query($db, "select hero from ".TABLE_PREFIX."person_heroes where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setHeroes($strings);
        }
		//Added with the above profile, interests is in CSV
/*
        if (isset($fields['interests']) || isset($fields['@all'])) {
          $strings = array();
          $res2 = mysqli_query($db, "select interest from ".TABLE_PREFIX."person_interests where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setInterests($strings);
        }
*/
        $organizations = array();
        $fetchedOrg = false;
        if (isset($fields['jobs']) || isset($fields['@all'])) {
		  $sql = "SELECT * FROM ". TABLE_PREFIX . "member_position WHERE member_id = ".$member_id;
          $res2 = mysql_query($sql, $this->db);
          while ($row = mysql_fetch_assoc($res2)) {
            $organization = new Organization();
            $organization->setDescription($row['description']);
            $organization->setEndDate($row['to']);
            $organization->setField($row['field']);
            $organization->setName($row['company']);
            $organization->setSalary($row['salary']);
            $organization->setStartDate($row['from']);
            $organization->setSubField($row['sub_field']); 
            $organization->setTitle($row['title']);
            $organization->setWebpage($row['webpage']);
            $organization->setType('job');

			//TODO: Address: To be implemented
			/*
            if ($row['address_id']) {
              $res3 = mysqli_query($db, "select * from ".TABLE_PREFIX."addresses where id = " . mysqli_real_escape_string($db, $row['address_id']));
              if (mysqli_num_rows($res3)) {
                $row = mysqli_fetch_array($res3, MYSQLI_ASSOC);
                if (empty($row['unstructured_address'])) {
                  $row['unstructured_address'] = trim($row['street_address'] . " " . $row['region'] . " " . $row['country']);
                }
                $addres = new Address($row['unstructured_address']);
                $addres->setCountry($row['country']);
                $addres->setLatitude($row['latitude']);
                $addres->setLongitude($row['longitude']);
                $addres->setLocality($row['locality']);
                $addres->setPostalCode($row['postal_code']);
                $addres->setRegion($row['region']);
                $addres->setStreetAddress($row['street_address']);
                $addres->setType($row['address_type']);
                $organization->setAddress($address);
              }
            }
			*/
            $organizations[] = $organization;
          }
          $fetchedOrg = true;
        }
        if (isset($fields['schools']) || isset($fields['@all'])) {
          $res2 = mysqli_query($db, "select o.* from ".TABLE_PREFIX."person_schools ps, ".TABLE_PREFIX."organizations o where o.id = ps.organization_id and ps.person_id = " . $person_id);
          while ($row = mysqli_fetch_array($res2, MYSQLI_ASSOC)) {
            $organization = new Organization();
            $organization->setDescription($row['description']);
            $organization->setEndDate($row['to']);
            $organization->setField($row['field']);
            $organization->setName($row['university']);
            $organization->setSalary($row['salary']);
            $organization->setStartDate($row['from']);
            $organization->setSubField($row['sub_field']);
            $organization->setTitle($row['degree']);
            $organization->setWebpage($row['webpage']);
            $organization->setType($row['school']);
			//TODO: Address: To be implemented
			/*
            if ($row['address_id']) {
              $res3 = mysqli_query($db, "select * from ".TABLE_PREFIX."addresses where id = " . mysqli_real_escape_string($db, $row['address_id']));
              if (mysqli_num_rows($res3)) {
                $row = mysqli_fetch_array($res3, MYSQLI_ASSOC);
                if (empty($row['unstructured_address'])) {
                  $row['unstructured_address'] = trim($row['street_address'] . " " . $row['region'] . " " . $row['country']);
                }
                $addres = new Address($row['unstructured_address']);
                $addres->setCountry($row['country']);
                $addres->setLatitude($row['latitude']);
                $addres->setLongitude($row['longitude']);
                $addres->setLocality($row['locality']);
                $addres->setPostalCode($row['postal_code']);
                $addres->setRegion($row['region']);
                $addres->setStreetAddress($row['street_address']);
                $addres->setType($row['address_type']);
                $organization->setAddress($address);
              }
            }
			*/
            $organizations[] = $organization;
          }
          $fetchedOrg = true;
        }
        if ($fetchedOrg) {
          $person->setOrganizations($organizations);
        }
        //TODO languagesSpoken, currently missing the languages / countries tables so can't do this yet
		//TODO: Not in ATutor yet, skeleton field
        if (isset($fields['movies']) || isset($fields['@all'])) {
          $strings = array();
          $res2 = mysqli_query($db, "select movie from ".TABLE_PREFIX."person_movies where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setMovies($strings);
        }
        if (isset($fields['music']) || isset($fields['@all'])) {
          $strings = array();
          $res2 = mysqli_query($db, "select music from ".TABLE_PREFIX."person_music where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setMusic($strings);
        }
        if (isset($fields['phoneNumbers']) || isset($fields['@all'])) {
          $numbers = array();
          $res2 = mysqli_query($db, "select number, number_type from ".TABLE_PREFIX."person_phone_numbers where person_id = " . $person_id);
          while (list($number, $type) = @mysqli_fetch_row($res2)) {
            $numbers[] = new Phone($number, $type);
          }
          $person->setPhoneNumbers($numbers);
        }
        if (isset($fields['ims']) || isset($fields['@all'])) {
          $ims = array();
          $res2 = mysqli_query($db, "select value, value_type from ".TABLE_PREFIX."person_ims where person_id = " . $person_id);
          while (list($value, $type) = @mysqli_fetch_row($res2)) {
            $ims[] = new Im($value, $type);
          }
          $person->setIms($ims);
        }
        if (isset($fields['accounts']) || isset($fields['@all'])) {
          $accounts = array();
          $res2 = mysqli_query($db, "select domain, userid, username from ".TABLE_PREFIX."person_accounts where person_id = " . $person_id);
          while (list($domain, $userid, $username) = @mysqli_fetch_row($res2)) {
            $accounts[] = new Account($domain, $userid, $username);
          }
          $person->setAccounts($accounts);
        }
        if (isset($fields['quotes']) || isset($fields['@all'])) {
          $strings = array();
          $res2 = mysqli_query($db, "select quote from ".TABLE_PREFIX."person_quotes where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setQuotes($strings);
        }
        if (isset($fields['sports']) || isset($fields['@all'])) {
          $strings = array();
          $res2 = mysqli_query($db, "select sport from ".TABLE_PREFIX."person_sports where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setSports($strings);
        }
        if (isset($fields['tags']) || isset($fields['@all'])) {
          $strings = array();
          $res2 = mysqli_query($db, "select tag from ".TABLE_PREFIX."person_tags where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setTags($strings);
        }
        
        if (isset($fields['turnOns']) || isset($fields['@all'])) {
          $strings = array();
          $res2 = mysqli_query($db, "select turn_on from ".TABLE_PREFIX."person_turn_ons where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setTurnOns($strings);
        }
        if (isset($fields['turnOffs']) || isset($fields['@all'])) {
          $strings = array();
          $res2 = mysqli_query($db, "select turn_off from ".TABLE_PREFIX."person_turn_offs where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = $data;
          }
          $person->setTurnOffs($strings);
        }
        if (isset($fields['urls']) || isset($fields['@all'])) {
          $strings = array();
          $res2 = mysqli_query($db, "select url from ".TABLE_PREFIX."person_urls where person_id = " . $person_id);
          while (list($data) = @mysqli_fetch_row($res2)) {
            $strings[] = new Url($data, null, null);
          }
          $strings[] = new Url($this->url_prefix . '/profile/' . $member_id, null, 'profile'); // always include profile URL
          $person->setUrls($strings);
        }
        $ret[$member_id] = $person;
      }
    }

    try {
      $ret = $this->filterResults($ret, $options);
      $ret['totalSize'] = count($ret);
    } catch(Exception $e) {
      $ret['totalSize'] = count($ret) - 1;
      $ret['filtered'] = 'false';
    }
    if ($first !== false && $max !== false && is_numeric($first) && is_numeric($max) && $first >= 0 && $max > 0) {
      $count = 0;
      $result = array();
      foreach ($ret as $id => $person) {
        if ($id == 'totalSize' || $id == 'filtered') {
          $result[$id] = $person;
          continue;
        }
        if ($count >= $first && $count < $first + $max) {
          $result[$id] = $person;
        }
        ++$count;
      }
      return $result;
    } else {
      return $ret;
    }
  }

  private function filterResults($peopleById, $options) {
    if (! $options->getFilterBy()) {
      return $peopleById; // no filtering specified
    }
    $filterBy = $options->getFilterBy();
    $op = $options->getFilterOperation();
    if (! $op) {
      $op = CollectionOptions::FILTER_OP_EQUALS; // use this container-specific default
    }
    $value = $options->getFilterValue();
    $filteredResults = array();
    $numFilteredResults = 0;
    foreach ($peopleById as $id => $person) {
      if ($person instanceof Person) {
        if ($this->passesFilter($person, $filterBy, $op, $value)) {
          $filteredResults[$id] = $person;
          $numFilteredResults ++;
        }
      } else {
        $filteredResults[$id] = $person; // copy extra metadata verbatim
      }
    }
    if (! isset($filteredResults['totalSize'])) {
      $filteredResults['totalSize'] = $numFilteredResults;
    }
    return $filteredResults;
  }

  private function passesFilter($person, $filterBy, $op, $value) {
    $fieldValue = $person->getFieldByName($filterBy);
    if ($fieldValue instanceof ComplexField) {
      $fieldValue = $fieldValue->getPrimarySubValue();
    }
    if (! $fieldValue || (is_array($fieldValue) && ! count($fieldValue))) {
      return false; // person is missing the field being filtered for
    }
    if ($op == CollectionOptions::FILTER_OP_PRESENT) {
      return true; // person has a non-empty value for the requested field
    }
    if (! $value) {
      return false; // can't do an equals/startswith/contains filter on an empty filter value
    }
    // grab string value for comparison
    if (is_array($fieldValue)) {
      // plural fields match if any instance of that field matches
      foreach ($fieldValue as $field) {
        if ($field instanceof ComplexField) {
          $field = $field->getPrimarySubValue();
        }
        if ($this->passesStringFilter($field, $op, $value)) {
          return true;
        }
      }
    } else {
      return $this->passesStringFilter($fieldValue, $op, $value);
    }    
    return false;
  }

  private function passesStringFilter($fieldValue, $op, $filterValue) {
    switch ($op) {
      case CollectionOptions::FILTER_OP_EQUALS:
        return $fieldValue == $filterValue;
      case CollectionOptions::FILTER_OP_CONTAINS:
        return stripos($fieldValue, $filterValue) !== false;
      case CollectionOptions::FILTER_OP_STARTSWITH:
        return stripos($fieldValue, $filterValue) === 0;
      default:
        throw new Exception('unrecognized filterOp');
    }
  }
}
