<?php
/**
 * Created by PhpStorm.
 * User: TiagoGouvea
 * Date: 13/06/15
 * Time: 09:45
 */

namespace gameme\PHPGamification\Model;

use Exception;

class Event extends Entity
{
    protected $id = null;
    protected $courseId = 0;
    protected $alias = null;              /* Event alias */
    protected $description = null;        /* Event description */

    protected $reachRequiredRepetitions = null;        /* Trigger counter (null = triggers every execution. $allowRepetitions must be true, otherwise triggers once) */
    protected $allowRepetitions = false;  /* Allows reachRequiredRepetitions (Default: YES) */
    protected $maxPoints = null;          /* Max points granted for this event */

    protected $idEachBadge = null;        /* Badge granted when triggers */
    protected $idReachBadge = null;       /* Badge granted when triggers */
    protected $eachPoints = null;         /* Points granted every time event called */
    protected $reachPoints = null;        /* Points granted when reachRequiredRepetitions are reached */
    protected $eachCallback = null;
    protected $reachCallback = null;
    protected $reachMessage = null;
    function __construct($stdClass = null)
    {
        if ($stdClass)
            $this->fillAtributes($stdClass, $this);
    }

    public function getBadge()
    {
        return $this->badge;
    }

    public function getDescription()
    {
        return $this->description;
    }
    public function getCourseId()
    {
        return $this->courseId;
    }
    public function getAlias()
    {
        return $this->alias;
    }

    public function getEachCallback()
    {
        return $this->eachCallback;
    }

    public function getReachCallback()
    {
        return $this->reachCallback;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMaxPoints()
    {
        return $this->maxPoints;
    }

    public function getEachPoints()
    {
        return $this->eachPoints;
    }

    public function getReachPoints()
    {
        return $this->reachPoints;
    }
    public function getReachMessage()
    {
        return $this->reachMessage;
    }
    public function getRequiredRepetitions()
    {
        return $this->reachRequiredRepetitions;
    }

    public function getIdEachBadge()
    {
        return $this->idEachBadge;
    }

    public function getIdReachBadge()
    {
        return $this->idReachBadge;
    }

    public function getAllowRepetitions()
    {
        return $this->allowRepetitions;
    }

    /**
     * @param $bool
     * @return Event
     * @throws Exception
     */
    public function setAllowRepetitions($bool)
    {
        if($bool == 0){ 
         $bool = FALSE;
        } else{
         $bool = TRUE;
        }
        if (!is_bool($bool)) throw new Exception(__METHOD__ . ': Invalid AllowRepetitions');
        $this->allowRepetitions = $bool;
        return $this;
    }

    /**
     * @param Badge $badge
     * @return Event
     */
    public function setEachBadgeGranted(Badge $badge)
    {
        $this->idEachBadge = $badge->getId();
        return $this;
    }
    public function copyEachBadgeGranted($id)
    {
        $sql = "SELECT id_each_badge FROM %sgm_events WHERE id =%d AND course_id = %d";
        $this_badge_granted = queryDB($sql, array(TABLE_PREFIX, $id, 0), TRUE);
        $this->idEachBadge = $this_badge_granted['id_each_badge'];
        return $this;
    }
    /**
     * @param Badge $badge
     * @return Event
     */
    public function setReachBadgeGranted(Badge $badge)
    {
        $this->idReachBadge = $badge->getId();
        return $this;
    }
    public function copyReachBadgeGranted($id)
    {
        $sql = "SELECT id_reach_badge FROM %sgm_events WHERE id =%d AND course_id = %d";
        $this_badge_granted = queryDB($sql, array(TABLE_PREFIX, $id, 0), TRUE);
        $this->idReachBadge = $this_badge_granted['id_reach_badge'];
        return $this;
    }
    /**
     * @param int $id_badge
     * @return Event
     */
    public function setIdBadgeGranted($id_badge)
    {
        $this->idEachBadge = $id_badge;
        return $this;
    }

    /**
     * @param $str
     * @return Event
     */
    public function setDescription($str)
    {
        $this->description = $str;
        return $this;
    }

    /**
     * @param $str
     * @return Event
     */
    public function setAlias($str)
    {
        $this->alias = $str;
        return $this;
    }

    /**
     * @param $callback
     * @return Event
     * @throws Exception
     */
    public function setEachCallback($callback)
    {
        if (!is_callable($callback))
            throw new Exception(__METHOD__ . ': Invalid EachCallback function: '.print_r($callback,true));
        $this->eachCallback = ($callback);
        return $this;
    }

    /**
     * @param $callback
     * @return Event
     * @throws Exception
     */
    public function setReachCallback($callback)
    {
        if (!is_callable($callback))
            throw new Exception(__METHOD__ . ': Invalid EventCallback function: '.print_r($callback,true));
        $this->reachCallback = ($callback);
        return $this;
    }

    /**
     * @param $f
     * @return Event
     * @throws Exception
     */
    public function setId($f)
    {
        if (!is_numeric($f)) throw new Exception(__METHOD__ . ': Invalid id');

        $this->id = $f;

        return $this;
    }
    /**
     * @param $c
     * @return Event
     * @throws Exception
     */
    public function setCourseId($c)
    {
        $this->courseId = $c;
        return $this;
    }
    /**
     * @param $n
     * @return Event
     * @throws Exception
     */
    public function setMaxPointsGranted($n)
    {
        if (!is_numeric($n)) throw new Exception(__METHOD__ . ': Invalid points');
        $this->maxPoints = $n;
        return $this;
    }

    /**
     * @param $n
     * @return Event
     * @throws Exception
     */
    public function setEachPointsGranted($n)
    {
        if (!is_numeric($n)) throw new Exception(__METHOD__ . ': Invalid points');
        $this->eachPoints = $n;
        return $this;
    }

    public function setReachPointsGranted($n)
    {
        if (!is_numeric($n)) throw new Exception(__METHOD__ . ': Invalid points');
        $this->reachPoints = $n;
        return $this;
    }
    public function copyEachPointsGranted($n)
    {
        if (!is_numeric($n)) throw new Exception(__METHOD__ . ': Invalid points');
        $this->eachPoints = $n;
        return $this;
    }

    public function copyReachMessage($id)
    {
        $sql = "SELECT reach_message FROM %sgm_events WHERE id =%d AND course_id = %d";
        $this_reach_message = queryDB($sql, array(TABLE_PREFIX, $id, 0), TRUE);
        $this->reachMessage = $this_reach_message['reach_message'];
        return $this;
    }
    public function setReachMessage($str)
    {
        $this->reachMessage = $str;
        return $this;
    }
    /**
     * @param $n
     * @return Event
     * @throws Exception
     */
    public function setReachRequiredRepetitions($n)
    {
        if (!is_numeric($n)) throw new Exception(__METHOD__ . ': Invalid reachRequiredRepetitions');
        $this->reachRequiredRepetitions = $n;
        return $this;
    }




}