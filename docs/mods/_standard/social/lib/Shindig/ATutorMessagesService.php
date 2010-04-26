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

class ATutorMessagesService extends ATutorService implements MessagesService {
  public function createMessage($userId, $appId, $message, $optionalMessageId, SecurityToken $token) {
	try {
		$messages = ATutorDbFetcher::get()->createMessage($userId, $token->getAppId(), $message);
	} catch (SocialSpiException $e) {
		throw $e;
    } catch (Exception $e) {
		throw new SocialSpiException("Invalid create message request: " . $e->getMessage(), ResponseError::$INTERNAL_ERROR);
    }
  }
}
?>