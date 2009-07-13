<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

class ATutorMessagesService extends ATutorService implements MessagesService {
  public function createMessage($userId, $appId, $message, $optionalMessageId, SecurityToken $token) {
    throw new SocialSpiException("Not implemented", ResponseError::$NOT_IMPLEMENTED);
  }
}
?>