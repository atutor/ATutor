<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institute	   */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

class ATutorActivityService extends ATutorService implements ActivityService {
  public function getActivity($userId, $groupId, $appdId, $fields, $activityId, SecurityToken $token) {
    $activities = $this->getActivities($userId, $groupId, $appdId, null, null, null, null, 0, 20, $fields, array($activityId), $token);
    if ($activities instanceof RestFulCollection) {
      $activities = $activities->getEntry();
      foreach ($activities as $activity) {
        if ($activity->getId() == $activityId) {
          return $activity;
        }
      }
    }
    throw new SocialSpiException("Activity not found", ResponseError::$NOT_FOUND);
  }

  public function getActivities($userIds, $groupId, $appId, $sortBy, $filterBy, $filterOp, $filterValue, $startIndex, $count, $fields, $activityIds, $token) {
//  public function getActivities($userIds, $groupId, $appId, $sortBy, $filterBy, $filterOp, $filterValue, $startIndex, $count, $fields, $token) {
    $ids = $this->getIdSet($userIds, $groupId, $token);
    $activities = ATutorDbFetcher::get()->getActivities($ids, $appId, $sortBy, $filterBy, $filterOp, $filterValue, $startIndex, $count, $fields, $activityIds);
//    $activities = ATutorDbFetcher::get()->getActivities($ids, $appId, $sortBy, $filterBy, $filterOp, $filterValue, $startIndex, $count, $fields);
    if ($activities) {
      $totalResults = $activities['totalResults'];
      $startIndex = $activities['startIndex'];
      $count = $activities['count'];
      unset($activities['totalResults']);
      unset($activities['startIndex']);
      unset($activities['count']);
      $ret = new RestfulCollection($activities, $startIndex, $totalResults);
      $ret->setItemsPerPage($count);
      return $ret;
    } else {
      throw new SocialSpiException("Invalid activity specified", ResponseError::$NOT_FOUND);
    }
  }

  public function createActivity($userId, $groupId, $appId, $fields, $activity, SecurityToken $token) {
    try {
      if ($token->getOwnerId() != $token->getViewerId()) {
        throw new SocialSpiException("unauthorized: Create activity permission denied.", ResponseError::$UNAUTHORIZED);
      }
      ATutorDbFetcher::get()->createActivity($userId->getUserId($token), $activity, $token->getAppId());
    } catch (SocialSpiException $e) {
      throw $e;
    } catch (Exception $e) {
      throw new SocialSpiException("Invalid create activity request: " . $e->getMessage(), ResponseError::$INTERNAL_ERROR);
    }
  }


  /**
   * Delete activity
   * @param		String userId = "@me"
   * @param		String groupId = "@self"
   * @param		String appId = auth.AppId
   * @param		String activityId 	
   * @param		AuthToken auth = HttpRequest.Authorization
   * @return	Void
   *
   * Check http://www.atutor.ca/atutor/mantis/view.php?id=4230 for details regarding to $activityId
   */
  public function deleteActivities($userId, $groupId, $appId, $activityIds, SecurityToken $token) {
    $ids = $this->getIdSet($userId, $groupId, $token);
    if (count($ids) < 1 || count($ids) > 1) {
      throw new SocialSpiException("Invalid user id or count", ResponseError::$BAD_REQUEST);
    }
    if (! ATutorDbFetcher::get()->deleteActivities($ids[0], $appId, $activityIds)) {
      throw new SocialSpiException("Invalid activity id(s)", ResponseError::$NOT_FOUND);
    }
  }
}
?>