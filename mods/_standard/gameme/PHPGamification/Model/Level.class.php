<?php

namespace gameme\PHPGamification\Model;

/**
 * Created by PhpStorm.
 * User: TiagoGouvea
 * Date: 08/08/15
 * Time: 11:32
 */
class Level extends Entity
{
    protected $id;
    protected $courseId;
    protected $points;
    protected $title;
    protected $description;
    protected $icon;

    public function getPoints()
    {
        return $this->points;
    }

    public function getId()
    {
        return $this->id;
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

    public function getTitle()
    {
        return $this->title;
    }
    public function getIcon()
    {
        return $this->icon;
    }
}