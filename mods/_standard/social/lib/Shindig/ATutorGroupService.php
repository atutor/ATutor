<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

class ATutorGroupService extends ATutorService implements GroupService {
  /**
   * Fetch groups for a list of ids.
   * @param UserId The user id to perform the action for
   * @param GroupId optional grouping ID
   * @param token The SecurityToken for this request
   * @return ResponseItem a response item with the error code set if
   *     there was a problem
   */
  function getPersonGroups($userId, GroupId $groupId, SecurityToken $token){
    $ids = $this->getIdSet($userId, $groupId, $token);
    $data = ATutorDbFetcher::get()->getPersonGroups($ids);
    // If the data array is empty, return empty DataCollection.
    return new DataCollection($data);
  }
}
?>