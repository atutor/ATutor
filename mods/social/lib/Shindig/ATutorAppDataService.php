<?php
// $Id$
class ATutorAppDataService extends ATutorService implements AppDataService {
  public function deletePersonData($userId, GroupId $groupId, $appId, $fields, SecurityToken $token) {
    $ids = $this->getIdSet($userId, $groupId, $token);
    if (count($ids) < 1) {
      throw new InvalidArgumentException("No userId specified");
    } elseif (count($ids) > 1) {
      throw new InvalidArgumentException("Multiple userIds not supported");
    }
    $userId = $ids[0];
    $appId = $token->getAppId();
    if ($fields == null) {
      if (! ATutorDbFetcher::get()->deleteAppData($userId, '*', $appId)) {
        throw new SocialSpiException("Internal server error", ResponseError::$INTERNAL_ERROR);
      }
    } else {
      foreach ($fields as $key) {
        if (! self::isValidKey($key) && $key != '*') {
          throw new SocialSpiException("The person app data key had invalid characters", ResponseError::$BAD_REQUEST);
        }
      }
      foreach ($fields as $key) {
        if (! ATutorDbFetcher::get()->deleteAppData($userId, $key, $appId)) {
          throw new SocialSpiException("Internal server error", ResponseError::$INTERNAL_ERROR);
        }
      }
    }
  }

  public function getPersonData($userId, GroupId $groupId, $appId, $fields, SecurityToken $token) {
    $ids = $this->getIdSet($userId, $groupId, $token);
    $data = ATutorDbFetcher::get()->getAppData($ids, $fields, $appId);
    // If the data array is empty, return empty DataCollection.
    return new DataCollection($data);
  }

  public function updatePersonData(UserId $userId, GroupId $groupId, $appId, $fields, $values, SecurityToken $token) {
    if ($userId->getUserId($token) == null) {
      throw new SocialSpiException("Unknown person id.", ResponseError::$NOT_FOUND);
    }
    foreach ($fields as $key) {
      if (! self::isValidKey($key)) {
        throw new SocialSpiException("The person app data key had invalid characters", ResponseError::$BAD_REQUEST);
      }
    }
    switch ($groupId->getType()) {
      case 'self':
        foreach ($fields as $key) {
          $value = isset($values[$key]) ? $values[$key] : null;
          if (! ATutorDbFetcher::get()->setAppData($userId->getUserId($token), $key, $value, $token->getAppId())) {
            throw new SocialSpiException("Internal server error", ResponseError::$INTERNAL_ERROR);
          }
        }
        break;
      default:
        throw new SocialSpiException("We don't support updating data in batches yet", ResponseError::$NOT_IMPLEMENTED);
        break;
    }
  }
}
?>