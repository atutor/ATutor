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

/**
 * Implementation of supported services backed using ATutor's DB Fetcher
 * Check shindig/php/src/social/spi/ for the list of services within the interfaces.
 */
class ATutorService {
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

  /**
   * Get the set of user id's from a user or collection of users, and group
   */
  protected function getIdSet($user, GroupId $group, SecurityToken $token) {
    $ids = array();
    if ($user instanceof UserId) {
      $userId = $user->getUserId($token);
      if ($group == null) {
        return array($userId);
      }
      switch ($group->getType()) {
        case 'all':
        case 'friends':
        case 'groupId':
          $friendIds = ATutorDbFetcher::get()->getFriendIds($userId);
          if (is_array($friendIds) && count($friendIds)) {
            $ids = $friendIds;
          }
          break;
        case 'self':
          $ids[] = $userId;
          break;
      }
    } elseif (is_array($user)) {
      $ids = array();
      foreach ($user as $id) {
        $ids = array_merge($ids, $this->getIdSet($id, $group, $token));
      }
    }
    return $ids;
  }

  /**
   * Determines whether the input is a valid key. Valid keys match the regular
   * expression [\w\-\.]+.
   * 
   * @param key the key to validate.
   * @return true if the key is a valid appdata key, false otherwise.
   */
  public static function isValidKey($key) {
    if (empty($key)) {
      return false;
    }
    for ($i = 0; $i < strlen($key); ++ $i) {
      $c = substr($key, $i, 1);
      if (($c >= 'a' && $c <= 'z') || ($c >= 'A' && $c <= 'Z') || ($c >= '0' && $c <= '9') || ($c == '-') || ($c == '_') || ($c == '.')) {
        continue;
      }
      return false;
    }
    return true;
  }

  protected function sortPersonResults(&$people, $options) {
    if (! $options->getSortBy()) {
      return true; // trivially sorted
    }
    // for now, ATutor can only sort by displayName, which also demonstrates returning sorted: false
    if ($options->getSortBy() != 'displayName') {
      return false;
    }
    usort($people, array($this, 'comparator'));
    if ($options->getSortOrder() != CollectionOptions::SORT_ORDER_ASCENDING) {
      $people = array_reverse($people);
    }
    return true;
  }

  protected function comparator($person, $person1) {
    $name = ($person instanceof Person ? $person->getDisplayName() : $person['displayName']);
    $name1 = ($person1 instanceof Person ? $person1->getDisplayName() : $person1['displayName']);
    return strnatcasecmp($name, $name1);
  }
}
