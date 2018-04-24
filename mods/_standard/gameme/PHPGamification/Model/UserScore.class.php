<?php
/**
 * Created by PhpStorm.
 * User: TiagoGouvea
 * Date: 09/08/15
 * Time: 11:13
 */

namespace gameme\PHPGamification\Model;


class UserScore extends Entity
{
    protected $idUser;
    protected $courseId;
    protected $points = 0;
    protected $idLevel = 1;
    protected $progress;
    private $level;

    function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    public function setIdLevel($idLevel)
    {
        $this->idLevel = $idLevel;
    }

    public function setProgress($int)
    {
        $this->progress = $int;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function getIdLevel()
    {
        return $this->idLevel;
    }
     public function getCourseId()
    {
        return $this->courseId;
    }
    function __construct($stdClass = null)
    {
        if ($stdClass)
            $this->fillAtributes($stdClass, $this);
    }

    public function setLevel(Level $level)
    {
        $this->level = $level;
    }

    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * @return Level
     */
    public function getLevel()
    {
        return $this->level;
    }

    public function getPublicVars()
    {
        return get_object_vars($this);
    }

    public function getIdUser()
    {
        return $this->idUser;
    }
}