<?php
/**
 * Created by PhpStorm.
 * User: TiagoGouvea
 * Date: 09/08/15
 * Time: 11:13
 */

namespace gameme\PHPGamification\Model;


use gameme\PHPGamification;

class UserLog extends Entity
{
    protected $idUser;
    protected $courseId;
    protected $idEvent;
    protected $eventDate;
    protected $idBadge;
    protected $idLevel;
    protected $points;

    function __construct($stdClass = null)
    {
        if ($stdClass)
            $this->fillAtributes($stdClass, $this);
    }

    function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    public function getIdEvent()
    {
        return $this->idEvent;
    }

    /**
     * @return Event
     * @throws \Exception
     */
    public function getEvent()
    {
        return PHPGamification::getInstance()->getEventById($this->idEvent);
    }

    public function getEventDate()
    {
        return $this->eventDate;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function getIdBadge()
    {
        return $this->idBadge;
    }
     public function getCourseId()
    {
        return $this->courseId;
    }
    public function getBadge()
    {
        return PHPGamification::getInstance()->getBadge($this->idBadge);
    }

    public function getIdLevel()
    {
        return $this->idLevel;
    }
}