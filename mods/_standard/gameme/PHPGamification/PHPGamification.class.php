<?php

/**
 * PHPGamification - Simple Generic PHP Gamification Framework
 * @link https://github.com/gamify/PHPGamification
 * @author Tiago Gouvea <tiago@tiagogouvea.com.br>
 *
 * Forked from gengamification (https://github.com/jfuentesa/gengamification) created by Javier Fuentes <javier.fuentes@redalumnos.com>
 *
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace gameme;

use Exception;
use gameme\PHPGamification\DAO;
use gameme\PHPGamification\Model;
use gameme\PHPGamification\Model\Badge;
use gameme\PHPGamification\Model\Event;
use gameme\PHPGamification\Model\Level;
use gameme\PHPGamification\Model\UserAlert;
use gameme\PHPGamification\Model\UserBadge;
use gameme\PHPGamification\Model\UserEvent;
use gameme\PHPGamification\Model\UserLog;
use gameme\PHPGamification\Model\UserScore;

require_once 'Autoload.php';

class PHPGamification
{
    /** @var  @var $instance PHPGamification */
    private static $instance;

    /** @var bool $letsGoParty false = stops gamification execution */
    private $letsGoParty = true;

    /** @var int $testUserId */
    private $testUserId = null;

    /** @var $events Event[] */
    private $events = array();

    /** @var array $events Events triggers definitions */
    private $eventsTriggers = array();

    /** @var array $badges Badges definitions */
    private $badges = array();

    /** @var array $levels Levels definitions */
    private $levels = array();

    // User id
    private $userId = null;

    // Events queue
    private $eventsQueue = array();

    // Data Access Object (DAO)
    /** @var DAO $dao */
    private $dao = null;
    private $engineStarted = false;


    public function __construct($enabled = true)
    {
        self::$instance = $this;
    }

    public static function getInstance()
    {
        if (self::$instance==null)
            self::$instance = new PHPGamification();
        return self::$instance;
    }

    /**
     * Add badge to gamification database
     * @param $alias
     * @param $title
     * @param $description
     * @param null $imageURL
     * @throws Exception
     * @return Badge
     */
    public function addBadge($alias, $title, $description, $imageURL = null)
    {
        if (empty($alias) || empty($title)) throw new Exception(__METHOD__ . ': Invalid parameters');

        // Save badge
        $badge = $this->dao->saveBadge($alias, $title, $description, $imageURL);
        // Add badge to gamification engine
        $this->badges[$badge->getId()] = $badge;

        return $badge;
    }
    /**
     * Add badge to gamification database
     * @param $alias
     * @param $title
     * @param $description
     * @param null $imageURL
     * @throws Exception
     * @return Badge
     */
    public function copyBadge($id, $alias, $title, $description, $imageURL = null)
    {
        if (empty($alias) || empty($title)) throw new Exception(__METHOD__ . ': Invalid parameters');

        // Save badge
        $badge = $this->dao->copyBadge($id, $alias, $title, $description, $imageURL);

        return $badge;
    }
    /**
     * Add badge to gamification database
     * @param      $points
     * @param      $title
     * @param null $description
     * @return $this
     * @throws Exception
     *
     */
    public function addLevel($points, $title, $description = null)
    {
        if (!is_numeric($points) || empty($title)) throw new Exception(__METHOD__ . ': Invalid parameters');
        $level = $this->dao->saveLevel($points, $title, $description);
        $this->levels[$level->id] = $level;
        return $level;
    }
    
    /**
     * Add level to gamification database
     * @param $id
     * @param $title
     * @param $description
     * @param $points
     * @param null $icon
     * @throws Exception
     * @return level
     */
    public function copyLevel($id, $title, $description, $points, $icon = null)
    {
        if (empty($id) || empty($title)) throw new Exception(__METHOD__ . ': Invalid parameters');
        // SaveLevel
        $level = $this->dao->copyLevel($id, $title, $description, $points, $icon);
        return $level;
    }
    /**
     * Add event to gamification database
     * @param $event Event
     * @return bool
     * @throws Exception
     */
    public function addEvent(Event $event)
    {
        $event = $this->dao->saveEvent($event);
        $alias = $event->getAlias();

        // Add new event/trigger to array events
        if (isset($this->events[$alias])) {
            // If event exists just add new trigger
            // Checking allowRepetitions matching with previously created event
            if ($this->events[$alias]->getAllowRepetitions() != $event->getAllowRepetitions())
                throw new Exception(__METHOD__ . ': Allow reachRequiredRepetitions does not match for the event ' . $alias);
        } else {
            $this->events[$alias] = $event;
        }

        // Add trigger to triggers for this event
        $this->eventsTriggers[$alias][] = $event;

        return $event;
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->dao->getEvents();
    }

    public function addUserEvent(UserEvent $userEvent)
    {
        $this->dao->increaseEventCounter($userEvent->getIdUser(), $userEvent->getIdEvent());
    }

    /**
     * Save alert for received badges
     * @param $id
     * @return bool
     */
    private function alertBadge($id)
    {
        return $this->dao->saveBadgeAlert($this->getUserId(), $id);
    }

    /**
     * Save alert for level upgrade
     * @param $id
     * @return bool
     */
    private function alertLevel($id)
    {
        // Save alert
        return $this->dao->saveLevelAlert($this->getUserId(), $id);
    }

    public function getUserAllData(){
        return array(
            'userScores'=>$this->getUserScores(),
            'userBadges'=> $this->getUserBadges(),
            'userLog'=> $this->getUserLog(),
            'userEvents'=> $this->getUserEvents());
    }

    /**
     * @param bool|false $resetAlerts
     * @return UserAlert[]
     * @throws Exception
     */
    public function getUserAlerts($resetAlerts = false)
    {
        if (is_null($this->userId)) throw new Exception(__METHOD__ . ': User id must be set before start game engine');
        return $this->dao->getUserAlerts($this->getUserId(), $resetAlerts);
    }

    /**
     * Get user events progress
     * @return array
     * @throws Exception
     */
    public function getUserEvents()
    {
        if (is_null($this->userId)) throw new Exception(__METHOD__ . ': User id must be set before start game engine');
        $return = array();
        $userEvents = $this->dao->getUserEvents($this->getUserId());
        if ($userEvents)
            foreach ($userEvents as $userEvent) {
                /* @var $userEvent UserEvent */
                $event = $this->getEventById($userEvent->getIdEvent());

                $eventTriggers = $this->eventsTriggers[$event->getAlias()];
                if ($eventTriggers == null)
                    $eventTriggers = array($this->events[$event->getAlias()]);
                $triggers = array();
                /** @var $trigger Event */
                foreach ($eventTriggers as $trigger) {
                    $triggers[] = array(
                        'reached' => ($userEvent->getEventCounter() >= $trigger->getRequiredRepetitions() ? true : false),
                        'description' => $trigger->getDescription(),
                    );
                }
                $return[] = array(
                    'id' => $userEvent->getIdEvent(),
                    'userEvent' => $userEvent,
                    'event' => $event,
                    'counter' => $userEvent->getEventCounter(),
                    'triggers' => $triggers
                );
            }
        return $return;
    }

    /**
     * Get badge info from id
     * @param $id
     * @return Badge
     * @throws Exception
     */
    public function getBadge($id)
    {
        if (!isset($this->badges[$id])) throw new Exception(__METHOD__ . ': Invalid badge id: '.$id);
        return $this->badges[$id];
    }

    /**
     * @param $id
     * @return Event
     * @throws Exception
     */
    public function getEventById($id)
    {
        foreach ($this->events as $event) {
            if ($id == $event->getId()) return $event;
        }
        throw new Exception(__METHOD__ . ': Invalid event id');
    }

    /**
     * Get level
     * @param $id
     * @return Level
     */
    public function getLevel($id)
    {
        if ($this->levels[$id])
            return new Level($this->levels[$id]);
    }

    /**
     * Get last level id
     * @return array
     */
    public function getLastLevelId()
    {
        return (string)count($this->levels);
    }

    /**
     * Get user log
     * @return UserLog[]
     * @throws Exception
     */
    public function getUserLog()
    {
        if (is_null($this->userId)) throw new Exception(__METHOD__ . ': User id must be set before start game engine');
        return $this->dao->getUserLog($this->getUserId());
    }

    /**
     * Get a list of user badges
     * @return UserBadge[]
     * @throws Exception
     */
    public function getUserBadges()
    {
        if (is_null($this->userId)) throw new Exception(__METHOD__ . ': User id must be set before start game engine');
        return $this->dao->getUserBadges($this->getUserId());
    }

    /**
     * Get user scores
     * @return UserScore
     * @throws Exception
     */
    public function getUserScores()
    {
        if (is_null($this->userId)) throw new Exception(__METHOD__ . ': User id must be set before start game engine');

        $userScore = $this->dao->getUserScore($this->getUserId());
        $userScore->setProgress(100);

        // Get user current level
        $level = $this->getLevel($userScore->getIdLevel());
        if ($level) {
            $userScore->setLevel($level);
//        var_dump($level);
//        var_dump($userScore);
            $nextLevel = $this->dao->getNextLevel($level->getId(), $userScore->getPoints());
//            die("aa");
//        $userScore->setlevelname = $level->alias;
//        var_dump($nextLevel);
            // It isn't the last level
            if (!empty($nextLevel) && $nextLevel->getPoints() > 0) {
                // Progress percentage to reach next level
                $progress = round((($userScore->getPoints() - $level->getPoints()) * 100) / ($nextLevel->getPoints() - $level->getPoints()));
                $userScore->setProgress($progress);
            }
        }

        return $userScore;
    }

    /**
     * Get user id
     * @return null
     * @throws Exception
     */
    public function getUserId()
    {
        if (is_null($this->userId)) throw new Exception(__METHOD__ . ': User id must be set before start game engine');
        return $this->userId;
    }

    /**
     * Execute gamification event
     * @param      $alias
     * @param null $additional Additional parameters to use when calling the callbacks
     * @param null $eventDate
     * @throws Exception
     * @return bool
     */
    public function executeEvent($alias, $additional = null, $eventDate = null)
    {
        // Is the service enabled?
        if (!$this->letsGoParty)
            return false;

        // Filter to user test
        if (!is_null($this->testUserId))
            if ($this->testUserId != $this->userId)
                return false;

        // Load game engine

        // Check invalid event
        if (!isset($this->events[$alias]))
            throw new Exception(__METHOD__ . ': Invalid event alias: ' . $alias);

        // Get id of event in $this->events array
        /* @var $currentEvent Event */
        $currentEvent = $this->events[$alias];
        $currentEventTriggers = $this->eventsTriggers[$alias];
        if ($currentEventTriggers == null)
            $currentEventTriggers = array($currentEvent);

        // Get event counter and current points for this event
        $userEvent = $this->dao->getUserEvent($this->getUserId(), $currentEvent->getId());

        // Counters initialization (max reachRequiredRepetitions and current points for this event)
        if (empty($userEvent)) {
            $eventCounter = 0;
            $eventPoints = 0;
        } else {
            $eventCounter = $userEvent->getEventCounter();
            $eventPoints = $userEvent->getPointsCounter();
        }

        // Disallow reachRequiredRepetitions to occurs? Event will be fired just one time
        $executeEvent = true;
        if ($eventCounter > 0 && $currentEvent->getAllowRepetitions()===false) {
            $executeEvent = false;

        }

        // Is the event allow to execute?
        if ($executeEvent) {
            // Increase internal counter for this user/event
            $eventCounter++;

            // Check if any trigger in the event is higher than current event counter for updating database
            $updateCounter = false;
            if ($eventCounter == 1) {
                // First execution time counter for event is always updated
                $updateCounter = true;
            } else {
                // COUNTERS WON'T BE UPDATED IF NOT REQUIRED FOR CONTROL EVENT REPETITIONS
                // If any trigger for this event require more reachRequiredRepetitions than eventcounter, eventcounter is updated (increased +1)
                /** @var $event Event */
                foreach ($currentEventTriggers as $event) {
                    if ((!is_null($event->getRequiredRepetitions()) && $event->getRequiredRepetitions() >= $eventCounter) || (is_null($event->getRequiredRepetitions()) && $event->getAllowRepetitions()))
                        $updateCounter = true;
                }
            }
            // Update counter for this event
            if ($updateCounter)
                $this->dao->increaseEventCounter($this->getUserId(), $currentEvent->getId());

            // Search triggers counter
            foreach ($currentEventTriggers as $event) {
                $eachOk = true;

                // Execute each function callback
                $callback = $event->getEachCallback();
                if ($callback) {
                    if (is_callable($callback))
                        $eachOk = call_user_func($callback, $additional);
                    else
                        throw new Exception(__METHOD__ . ': Each Callback not callable: ' . print_r($callback,true));
                }

                // if each iterative function returns false, event cancels execution.
                if ($eachOk) {
                    // Grant points for each
                    if ($event->getEachPoints()) {
                        $grantPoints = true;

                        // Check max points for this event - If event points counter greater than maxPoints, don't save anything
                        if (!is_null($event->getMaxpoints()) && $eventPoints >= $event->getMaxpoints())
                            $grantPoints = false;

                        // If points not reaches max event points, it saves them
                        if ($grantPoints) {
                            $this->grantPoints($event->getEachPoints(), $currentEvent->getId(), $eventDate);
                            $this->dao->increaseEventPoints($this->getUserId(), $currentEvent->getId(), $event->getEachPoints());
                        }
                    }

                    // Grant badges for each
                    if (!is_null($event->getIdEachBadge()) && !$this->dao->hasBadgeUser($this->getUserId(), $event->getIdEachBadge()))
                        $this->grantBadge($this->badges[$event->getIdEachBadge()], $currentEvent->getId(), $eventDate);


                    // Check if counter match reachRequiredRepetitions
//                    var_dump($eventCounter);
//                    var_dump($event->getRequiredRepetitions());
                    if (($event->getRequiredRepetitions() == $eventCounter)) {
                        $reachOk = true;

                        // Execute trigger function
                        $callback = $event->getReachCallback();
                        if ($callback) {
                            if (is_callable($callback))
                                $reachOk = call_user_func($callback, $additional);
                            else
                                throw new Exception(__METHOD__ . ': Reach Callback not callable: ' . print_r($callback,true));
                        }


                        // if reach callback returns false, event cancels execution
                        if ($reachOk) {
                            // Grant points
                            if (!is_null($event->getReachPoints())) {
                                $grantPoints = true;

                                // Check max points for this event - If event points OLD counter greater than maxPoints, don't save anything
                                if (!is_null($event->getMaxpoints()) && $eventPoints >= $event->getMaxpoints())
                                    $grantPoints = false;
                                if ($grantPoints) {
                                    $this->grantPoints($event->getReachPoints(), $currentEvent->getId(), $eventDate);
                                    $this->dao->increaseEventPoints($this->getUserId(), $currentEvent->getId(), $event->getReachPoints());
                                }
                            }

                            // Grant badges
                            if (!is_null($event->getIdReachBadge()) && !$this->dao->hasBadgeUser($this->getUserId(), $event->getIdReachBadge()))
                                $this->grantBadge($this->badges[$event->getIdReachBadge()], $currentEvent->getId(), $eventDate);
                        }
                    }
                }
            }
        }

        return true;
    }


    /**
     * Grant badge to user
     * @param Badge $badge
     * @param null $eventId
     * @param null $eventDate
     * @throws Exception
     * @return bool
     * @internal param $alias
     */
    public function grantBadge(Badge $badge, $eventId = null, $eventDate = null)
    {
        if (is_null($this->userId)) throw new Exception(__METHOD__ . ': User id must be set before start game engine');

        // Grant badge to user
        $this->dao->grantBadgeToUser($this->getUserId(), $badge->getId());

        // Log event
        $this->dao->logUserEvent($this->getUserId(), $eventId,  null, $badge->getId(), null, $eventDate);

        // Gamification alert
        $this->alertBadge($badge->getId());

        // Add event to queue when the user reach this level
//        if (!is_null($this->badges[$badgeId]['event']))
//            $this->addEventToQueue($this->badges[$badgeId]['event']);

        return true;
    }

    /**
     * Grant level to user
     * @param      $levelId
     * @param null $eventId
     * @param null $eventDate
     * @throws Exception
     * @return bool
     */
    private function grantLevel($levelId, $eventId = null, $eventDate = null)
    {
        if (is_null($this->userId))
            throw new Exception(__METHOD__ . ': User id must be set before execute game engine');

//        echo "<br>aaa $levelId bbb $eventId<br>";
        // Grant level
        $this->dao->grantLevelToUser($this->getUserId(), $levelId);

        // Log event
        $this->dao->logUserEvent($this->getUserId(), $eventId, null, null, $levelId, $eventDate);

        // Gamification alert
        $this->alertLevel($levelId);

        return true;
    }

    /**
     * Grant points to user
     * If user reach some level, it grants the level to user
     * @param int $points
     * @param null $eventId
     * @param null $eventDate
     * @throws Exception
     */
    public function grantPoints($points, $eventId = null, $eventDate = null)
    {
        // Get user level/points
        $score = $this->dao->getUserScore($this->getUserId());

        $userPoints = $score->getPoints();
        // Add points to user counter
        $this->dao->grantPointsToUser($this->getUserId(), $points);
        // Log event
        $this->dao->logUserEvent($this->getUserId(), $eventId, $points, null, null, $eventDate);
        // Updated points for levels comparison
        $userPoints += $points;
        // Check levels higher than user level
        $nextLevel = $this->dao->getNextLevel($score->getIdLevel(), $score->getPoints());
        if ($nextLevel) {
            // Check if user reaches next level
            if ($userPoints >= $nextLevel->getPoints()) {
                $this->grantLevel($nextLevel->getId(), $eventId, $eventDate);
            }
        }
    }

    public function pointsToNextLevel()
    {
        $scores = $this->getUserScores();

        $r = null;

        // Check last level
        if ((int)$scores['idLevel'] < (int)$this->getLastLevelId()) {
            $nextLevel = $this->getLevel($scores['idLevel'] + 1);
            $r = $nextLevel['threshold'] - $scores['points'];
        }

        return $r;
    }

    /**
     * Set Data Access Object
     * @param $dao
     */
    public function setDAO($dao)
    {
        $this->dao = $dao;
    }

    /**
     * Enable/disable executeEvent() globally
     * @param bool $enabled
     */
    public function setEnabled($enabled = true)
    {
        $this->letsGoParty = $enabled;
    }

    /**
     * Set user id
     * @param $userId
     * @return bool
     * @throws Exception
     */
    public function setUserId($userId)
    {
        if ($this->engineStarted == false)
            $this->startEngine();
        if (!is_numeric($userId))
            throw new Exception(__METHOD__ . ': Invalid parameters');
        $this->userId = $userId;
        return true;
    }


    /**
     * Start gamification engine, loading all data from database
     */
    private function startEngine()
    {
        if ($this->engineStarted == true) return;

        if (count($this->events) == 0) {
            $this->events = $this->dao->getEvents();
        }
        if (count($this->badges) == 0)
            $this->badges = $this->dao->getBadges();
        if (count($this->levels) == 0)
            $this->levels = $this->dao->getLevels();
    }

    /**
     * Set user id
     * @param $userId
     * @return bool
     * @throws Exception
     */
    public function setTestUserId($userId)
    {
        if (!is_numeric($userId)) throw new Exception(__METHOD__ . ': Invalid parameters');

        $this->testUserId = $userId;

        return true;
    }

    /**
     * @param $limit
     * @return UserScore[]
     */
    public function getUsersPointsRanking($limit)
    {
        $this->startEngine();
        return $this->dao->getUsersPointsRanking($limit);
    }

    public function truncateDatabase($truncateLevelBadge = false)
    {
        return $this->dao->truncateDatabase($truncateLevelBadge);
    }

    /**
     * @param $alias
     * @return Badge
     */
    public function getBadgeByAlias($alias)
    {
        return $this->dao->getBadgeByAlias($alias);
    }
}