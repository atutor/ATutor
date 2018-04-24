<?php

namespace gameme\PHPGamification\Model;

/**
 * Created by PhpStorm.
 * User: TiagoGouvea
 * Date: 08/08/15
 * Time: 11:31
 */
class Badge extends Entity
{
    protected $id;
    protected $courseId;
    protected $alias;
    protected $title;
    protected $description;
    protected $imageUrl;

    public function __construct($stdClass=null){
        if ($stdClass)
            $this->fillAtributes($stdClass, $this);
    }

    public function getId()
    {
        return $this->id;
        $this->courseId = $_SESSION['course_id'];
        return $this;
    }
    public function getCourseId()
    {
        return $this->courseId;
    }
    public function getAlias()
    {
        return $this->alias;
    }


    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }
}