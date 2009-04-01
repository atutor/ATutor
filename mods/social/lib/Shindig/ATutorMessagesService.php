<?php
// $Id$
class ATutorMessagesService extends ATutorService implements MessagesService {
  public function createMessage($userId, $appId, $message, $optionalMessageId, SecurityToken $token) {
    throw new SocialSpiException("Not implemented", ResponseError::$NOT_IMPLEMENTED);
  }
}
?>