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
// $Id: ATutorPersonService.php 10055 2010-06-29 20:30:24Z cindy $

class ATutorPersonService extends ATutorService implements PersonService {
  public function getPerson($userId, $groupId, $fields, SecurityToken $token) {
    if (! is_object($userId)) {
      $userId = new UserId('userId', $userId);
      $groupId = new GroupId('self', 'all');
    }
    $person = $this->getPeople($userId, $groupId, new CollectionOptions(), $fields, $token);
    if (is_array($person->getEntry())) {
      $person = $person->getEntry();
      if (is_array($person) && count($person) == 1) {
        return array_pop($person);
      }
    }
    throw new SocialSpiException("Person not found", ResponseError::$BAD_REQUEST);
  }

  public function getPeople($userId, $groupId, CollectionOptions $options, $fields, SecurityToken $token) {
    $ids = $this->getIdSet($userId, $groupId, $token);
    $allPeople = ATutorDbFetcher::get()->getPeople($ids, $fields, $options, $token);
    $totalSize = $allPeople['totalSize'];
    $people = array();
    foreach ($ids as $id) {
      $person = null;
      if (is_array($allPeople) && isset($allPeople[$id])) {
        $person = $allPeople[$id];
        if (! $token->isAnonymous() && $id == $token->getViewerId()) {
          $person->setIsViewer(true);
        }
        if (! $token->isAnonymous() && $id == $token->getOwnerId()) {
          $person->setIsOwner(true);
        }
        if (! in_array('@all', $fields)) {
          $newPerson = array();
          $newPerson['isOwner'] = $person->isOwner;
          $newPerson['isViewer'] = $person->isViewer;
          $newPerson['displayName'] = $person->displayName;
          // Force these fields to always be present
          $fields[] = 'id';
          $fields[] = 'displayName';
          $fields[] = 'thumbnailUrl';
          $fields[] = 'profileUrl';
          foreach ($fields as $field) {
            if (isset($person->$field) && ! isset($newPerson[$field])) {
              $newPerson[$field] = $person->$field;
            }
          }
          $person = $newPerson;
        }
        array_push($people, $person);
      }
    }
    $sorted = $this->sortPersonResults($people, $options);
    $collection = new RestfulCollection($people, $options->getStartIndex(), $totalSize);
    $collection->setItemsPerPage($options->getCount());
    if (! $sorted) {
      $collection->setSorted(false); // record that we couldn't sort as requested
    }
    if ($options->getUpdatedSince()) {
      $collection->setUpdatedSince(false); // we can never process an updatedSince request
    }
    return $collection;
  }
}
?>