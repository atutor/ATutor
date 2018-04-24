<?php
/**
 * Created by PhpStorm.
 * User: TiagoGouvea
 * Date: 09/08/15
 * Time: 11:13
 */

namespace gameme\PHPGamification\Model;


use gameme\PHPGamification;

class UserBadge extends Entity
{
    protected $idUser;
    protected $courseId;
    protected $idBadge;
    protected $badgesCounter = 0;
    protected $grantDate;

    function __construct($stdClass = null)
    {
        if ($stdClass)
            $this->fillAtributes($stdClass, $this);
    }

    function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    public function getAlias()
    {
//        return $this->
    }

    public function getBadgeCounter()
    {
        return $this->badgesCounter;
    }

    public function getIdBadge()
    {
        return $this->idBadge;
    }
     public function getCourseId()
    {
        return $this->courseId;
    }
    public function getDescription()
    {
    }

    /**
     * @return Badge
     */
    public function getBadge()
    {
        return PHPGamification::getInstance()->getBadge($this->idBadge);
    }


}