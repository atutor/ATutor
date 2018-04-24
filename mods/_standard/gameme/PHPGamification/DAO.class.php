<?php
/**
 * Class PHPGamificationDAO
 * Default DAO to use PHPGamification, recommended to be used
 * Data Access Object for gamification persistent data layer
 */

namespace gameme\PHPGamification;

use Exception;
use PDO;
use PDOException;
use PDOStatement;
use ReflectionClass;
use gameme\PHPGamification\Model\DAOInterface;
use gameme\PHPGamification\Model\Badge;
use gameme\PHPGamification\Model\Event;
use gameme\PHPGamification\Model\Level;
use gameme\PHPGamification\Model\UserAlert;
use gameme\PHPGamification\Model\UserBadge;
use gameme\PHPGamification\Model\UserEvent;
use gameme\PHPGamification\Model\UserLog;
use gameme\PHPGamification\Model\UserScore;

class DAO implements DAOInterface
{
    /* @var $conn PDO */
    public $conn = null;

    public function __construct($host, $dbname, $username, $password)
    {
        if (!$this->conn) {
            try {
                $this->conn = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $username, $password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            } catch (PDOException $e) {
                exit($e->getMessage());
            }
        }
    }

    /**
     * @return null|PDO
     */
    public function getConnection()
    {

        return $this->conn;
    }

    public function execute($sql, $params = array())
    {
//        echo "<br><b>$sql</b><br>";echo print_r($params);
        /** @var PDOStatement $stmt */
        //print_r($params);
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId()
    {
        $conn = $this->getConnection();
        return $conn->lastInsertId();
    }

    /**
     * @param $sql
     * @param array $params
     * @return array
     */
    public function query($sql, $params = array())
    {
        $conn = $this->getConnection();
        if (empty($params)) {
            /** @var $stmt PDOStatement */
            $stmt = $conn->query($sql);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
        }
        $result = $stmt->fetchAll();
        if (is_array($result) && count($result) == 0)
            $result = null;
        return $result;
    }

    /**
     * Just get a record by Id, from some table
     * @param $tableName
     * @param $id
     * @return mixed
     */
    private function getById($tableName, $id)
    {
        $sql = "SELECT * FROM $tableName WHERE id = :id AND course_id = :cid";
        $params = array(
            ':id' => $id,
            ':cid' => $_SESSION['course_id']
        );
        $results = $this->query($sql, $params);
        if ($results)
            return $results[0];
    }

    /**
     * Just get a record by it Alias, from some table
     * @param $tableName
     * @param $id
     * @return mixed
     */
    private function getByAlias($tableName, $alias)
    {
        $sql = "SELECT * FROM $tableName WHERE alias = :alias AND course_id = :cid";
        $params = array(
            ':alias' => $alias,
            ':cid' => $_SESSION['course_id']
            
        );
        $results = $this->query($sql, $params);
        if ($results)
            return $results[0];
    }

//    public function toArray($query, $params = false)
//    {
//        $a = array();
//        $q = $this->query($query, $params);
//        while ($r = $q->fetch(PDO::FETCH_ASSOC)) $a[] = $r;
//        return $a;
//    }

    private function toArrayObject(Array $dataArray, $targetClass, $keyField = null)
    {
        $return = array();
        foreach ($dataArray as $data) {
            $reflection = new ReflectionClass("gameme\\PHPGamification\\Model\\" . $targetClass);
            $objInstance = $reflection->newInstanceArgs(array('StdClass' => $data));
            if ($keyField != null)
                $return[$objInstance->get($keyField)] = $objInstance;
            else
                $return[] = $objInstance;
        }
        return $return;
    }

    /**
     * Interface methods
     */


    /**
     * Save a new badge on database table "_badges
     * @param $alias
     * @param $title
     * @param $description
     * @param $imageURL
     * @return Badge
     * @throws Exception
     */
    public function saveBadge($alias,$title, $description, $imageURL = null)
    {
        // Already exists?
        if ($this->getByAlias(TABLE_PREFIX.'gm_badges', $alias))
            throw new Exception(__METHOD__ . ': Alias ' . $alias . ' already exists');
        if($_SESSION['course_id'] > 0){
            $this_cid = $_SESSION['course_id']; 
        }else{
            $this_cid = 0;
        }
        $sql = 'INSERT INTO '.TABLE_PREFIX.'gm_badges
                (course_id, alias, title, description,image_url)
                VALUES
                (:cid, :alias, :title, :description,:image_url)';
        $params = array(
            ':cid' => $this_cid,
            ':alias' => $alias,
            ':title' => $title,
            ':description' => $description,
            ':image_url' => $imageURL
        );

        $this->execute($sql, $params);
        return $this->getBadgeById($this->lastInsertId());
    }
    /**
     * Save a new badge on database table "_badges
     * @param $id
     * @param $alias
     * @param $title
     * @param $description
     * @param $imageURL
     * @return Badge
     * @throws Exception
     */
    public function copyBadge($id,$alias,$title, $description, $imageURL = null)
    {
        // Already exists?
       // if ($this->getByAlias(TABLE_PREFIX.'gm_badges', $alias, $course_id))
       //     throw new Exception(__METHOD__ . ': Alias ' . $alias . ' already exists');

        $sql = 'REPLACE INTO '.TABLE_PREFIX.'gm_badges
                (id,course_id, alias, title, description,image_url)
                VALUES
                (:id, :cid, :alias, :title, :description,:image_url)';
        $params = array(
            ':id'=>$id,
            ':cid' => $_SESSION['course_id'],
            ':alias' => $alias,
            ':title' => $title,
            ':description' => $description,
            ':image_url' => $imageURL
        );

        $this->execute($sql, $params);
        return true;
       // return $this->getBadgeById($this->lastInsertId(), $course_id);
    }
    /**
     * Get a Badge by Id
     * @param $id
     * @return StdClass
     */
    public function getBadgeById($id)
    {
        return new Badge($this->getById(TABLE_PREFIX.'gm_badges', $id));
    }

    /**
     * Get a Badge by Alias
     * @param $alias
     * @internal param $id
     * @return Badge
     */
    public function getBadgeByAlias($alias)
    {
        $badgeStdClass = $this->getByAlias(TABLE_PREFIX.'gm_badges', $alias);
        return new Badge($badgeStdClass);
    }

    /**
     * Save a new level on database table "_levels"
     * @param $alias
     * @param $title
     * @param $description
     * @param $imageURL
     * @return array
     */
    public function saveLevel($points, $title, $description)
    {   
        $course_id = 0;
        $sql = 'REPLACE INTO '.TABLE_PREFIX.'gm_levels
                (id, course_id, points, title, description)
                VALUES
                (:id, :cid, :points, :title, :description)';
        $params = array(
            ':id'=>'',
            'cid'=> $course_id,
            ':points' => $points,
            ':title' => $title,
            ':description' => $description
        );

        $this->execute($sql, $params);
        return $this->getLevelById($this->lastInsertId());
    }
    public function copyLevel($id, $title, $description, $points, $icon)
    {
        $sql = 'REPLACE INTO '.TABLE_PREFIX.'gm_levels
                (id, course_id,  title, description, points, icon)
                VALUES
                (:id, :cid, :title, :description, :points, :icon)';
        $params = array(
            ':id'=>$id,
            ':cid'=>$_SESSION['course_id'],
            ':title' => $title,
            ':description' => $description,
            ':points' => $points,
            ':icon' => $icon
        );

        $this->execute($sql, $params);
        return $this->getLevelById($this->lastInsertId());
    }
    /**
     * Get a Event by Id
     * @param $id
     * @return PHPGamificationLevel
     */
    private function getLevelById($id)
    {
        return $this->getById(TABLE_PREFIX.'gm_levels', $id);
    }

    public function getFirstLevel()
    {
        $sql = 'SELECT * FROM '.TABLE_PREFIX.'gm_levels ORDER BY points ASC LIMIT 1';
        $results = $this->query($sql);
        return new Level($results[0]);
    }

    public function getNextLevel($levelId, $score)
    {
        $sql = 'SELECT * FROM '.TABLE_PREFIX.'gm_levels WHERE id<>' . $levelId . ' AND points>' . $score . ' ORDER BY points ASC LIMIT 1';
//        die($sql);
        $results = $this->query($sql);
        if ($results)
            return new Level($results[0]);
    }

    /**
     * Save a new event on database table "_events"
     * @param Event $event
     * @return Event
     */
    public function saveEvent(Event $event)
    {
        if($_SESSION['course_id'] > 0){
            $course_id = $_SESSION['course_id'];
        } else {
            $course_id = 0;
        }
        $sql = 'REPLACE INTO '.TABLE_PREFIX.'gm_events
                (id, course_id, alias, description, allow_repetitions, reach_required_repetitions, max_points, id_each_badge, id_reach_badge, each_points, reach_points, each_callback, reach_callback, reach_message)
                VALUES
                (:id, :cid,:alias, :description, :allow_repetitions, :reach_required_repetitions, :max_points, :id_each_badge, :id_reach_badge, :each_points, :reach_points, :each_callback, :reach_callback, :reach_message)';
        $params = array(
            ':id' => $event->getId(),
            ':cid' => $course_id,
            ':alias' => $event->getAlias(),
            ':description' => $event->getDescription(),
            ':allow_repetitions' => $event->getAllowRepetitions(),
            ':reach_required_repetitions' => $event->getRequiredRepetitions(),
            ':id_each_badge' => $event->getIdEachBadge(),
            ':id_reach_badge' => $event->getIdReachBadge(),
            ':each_points' => $event->getEachPoints(),
            ':reach_points' => $event->getReachPoints(),
            ':max_points' => $event->getMaxPoints(),
            ':each_callback' => $event->getEachCallback(),
            ':reach_callback' => $event->getReachCallback(),
            ':reach_message' => $event->getReachMessage()
        );
        
        $this->execute($sql, $params);
        return $this->getEventById($this->lastInsertId());
    }

    /**
     * Get a Event by Id
     * @param $id
     * @return Event
     */
    private function getEventById($id)
    {
        $eventStdClass = $this->getById(TABLE_PREFIX.'gm_events', $id);
        return new Event($eventStdClass);
    }


    public function getLevels()
    {
        $sql = 'SELECT * FROM '.TABLE_PREFIX.'gm_levels ';
        $result = $this->query($sql);
        if ($result)
            return $this->toArrayObject($result, 'Level', 'id');
    }

    public function getBadges()
    {
        $sql = 'SELECT * FROM '.TABLE_PREFIX.'gm_badges ';
        $result = $this->query($sql);
        if ($result)
            return $this->toArrayObject($result, 'Badge', 'id');
    }

    public function getEvents()
    {
        $sql = 'SELECT * FROM '.TABLE_PREFIX.'gm_events ';
        $result = $this->query($sql, $param);
        if ($result)
            return $this->toArrayObject($result, 'Event', 'alias');
    }

    public function getUserAlerts($userId, $resetAlerts = false)
    {
        $sql = 'SELECT id_user, id_badge, id_level FROM '.TABLE_PREFIX.'gm_user_alerts WHERE id_user = :uid AND course_id = :cid';
        $params = array(
            ':uid' => $userId,
            ':cid' => $_SESSION['course_id']
        );
        $result = $this->query($sql, $params);
        if ($result && $resetAlerts) {
            $sql = 'DELETE FROM '.TABLE_PREFIX.'gm_user_alerts WHERE id_user = :uid AND course_id = :cid';
            $params = array(
                ':uid' => $userId,
                ':cid' => $_SESSION['course_id']
            );
            $this->execute($sql, $params);
        }

        if ($result)
            return $this->toArrayObject($result, 'UserAlert');
    }

    public function getUserBadges($userId)
    {
        $sql = 'SELECT * FROM '.TABLE_PREFIX.'gm_user_badges WHERE id_user = :uid AND course_id = :cid';
        $params = array(
            ':uid' => $userId,
            ':cid' => $_SESSION['course_id']
        );
        $result = $this->query($sql, $params);

        if ($result)
            return $this->toArrayObject($result, 'UserBadge');
    }

    public function getUserEvents($userId)
    {
        $sql = 'SELECT * FROM '.TABLE_PREFIX.'gm_user_events WHERE id_user = :uid AND course_id = :cid';
        $params = array(
            ':uid' => $userId,
            ':cid' => $_SESSION['course_id']
        );
        $result = $this->query($sql, $params);
        if ($result)
            return $this->toArrayObject($result, 'UserEvent');
    }

    public function getUserLog($userId)
    {
        $sql = 'SELECT * FROM '.TABLE_PREFIX.'gm_user_logs WHERE id_user = :uid AND course_id = :cid ORDER BY event_date DESC';
        if($_SESSION['course_id'] == 0){
            $this_cid = 0;
        }else{
            $this_cid = $_SESSION['course_id'];
        }
        $params = array(
            ':uid' => $userId,
            ':cid' => $this_cid
        );
        $result = $this->query($sql, $params);
        if ($result)
            return $this->toArrayObject($result, 'UserLog');
    }

    public function getUserEvent($userId, $eventId)
    {
        $sql = 'SELECT * FROM '.TABLE_PREFIX.'gm_user_events WHERE id_user = :uid AND id_event = :eid  AND course_id = :cid LIMIT 1';
        $params = array(
            ':uid' => $userId,
            ':eid' => $eventId,
            ':cid' => $_SESSION['course_id']
        );
        $result = $this->query($sql, $params);
        if ($result)
            return new UserEvent($result[0]);
        else {
            $score = new UserEvent();
            return $score;
        }
    }

    /**
     * @param $userId
     * @return UserScore
     */
    public function getUserScore($userId)
    {
        $sql = 'SELECT *
                FROM '.TABLE_PREFIX.'gm_user_scores
                WHERE id_user = :uid and course_id = :cid';
        $params = array(
            ':uid' => $userId,
            ':cid' => $_SESSION['course_id']
        );
        $result = $this->query($sql, $params);
        if ($result)
            return new UserScore($result[0]);
        else {
            $score = new UserScore();
            $score->setIdUser($userId);
            $score->setIdLevel($this->getFirstLevel()->getId());
            return $score;
        }
    }

    /**
     * Return users ordered by level
     */
    public function getUsersPointsRanking($limit)
    {
        $sql = 'SELECT *
                FROM '.TABLE_PREFIX.'gm_user_scores
                WHERE course_id = :cid ORDER BY points DESC, id_user ASC
                LIMIT ' . $limit;         
        $params = array(
            ':cid' => $_SESSION['course_id']
        );
        $result = $this->query($sql, $params);

        if ($result)
            return $result;
            //return $this->toArrayObject($result, 'UserScore');
    }

    public function grantBadgeToUser($userId, $badgeId)
    {
        $sql = 'INSERT INTO '.TABLE_PREFIX.'gm_user_badges (id_user, id_badge, badges_counter, grant_date, course_id) VALUES (:uid, :bid, 1, UTC_TIMESTAMP(), :cid) ON DUPLICATE KEY UPDATE badges_counter = badges_counter + 1';
        $params = array(
            ':uid' => $userId,
            ':bid' => $badgeId,
            ':cid' => $_SESSION['course_id']
        );
        $this->execute($sql, $params);
        return true;
    }

    public function hasBadgeUser($userId, $badgeId)
    {
        $sql = 'SELECT coalesce(count(*),0) AS count
                FROM '.TABLE_PREFIX.'gm_user_badges
                WHERE id_user=:uid AND id_badge=:bid AND course_id=:cid' ;
        $params = array(
            ':uid' => $userId,
            ':bid' => $badgeId,
            ':cid' => $_SESSION['course_id']
        );
        $r = $this->query($sql, $params);
        return $r[0]->count > 0;
    }

    public function grantLevelToUser($userId, $levelId)
    {
        $sql = 'UPDATE '.TABLE_PREFIX.'gm_user_scores
                SET id_level = :lid
                WHERE id_user = :uid AND course_id = :cid';
        $params = array(
            ':uid' => $userId,
            ':lid' => $levelId,
            ':cid' => $_SESSION['course_id']
        );
        if ($levelId == 0) die ("00");
        return $this->execute($sql, $params);
    }

    public function grantPointsToUser($userId, $points)
    {
        $sql = 'INSERT INTO '.TABLE_PREFIX.'gm_user_scores
                (id_user, points, id_level, course_id)
                VALUES
                (:uid, :p, :firstlevel, :cid)
                ON DUPLICATE KEY UPDATE points = points + :p';
        $params = array(
            ':uid' => $userId,
            ':p' => $points,
            ':firstlevel' => 1,
            ':cid' => $_SESSION['course_id']
        );
        return $this->execute($sql, $params);
    }

    public function logUserEvent($userId, $eventId, $points = null, $badgeId = null, $levelId = null, $eventDate = null)
    {
        $sql = 'INSERT INTO '.TABLE_PREFIX.'gm_user_logs
                (id_user, id_event, event_date, points, id_badge, id_level, course_id)
                VALUES
                (:uid, :eid, :edate, :p, :bid, :lid, :cid)';
        $params = array(
            ':uid' => $userId,
            ':eid' => $eventId,
            ':p' => $points,
            ':bid' => $badgeId,
            ':lid' => $levelId,
            ':edate' => ($eventDate ? $eventDate : date("Y-m-d H:i:s",time())),
            ':cid' => $_SESSION['course_id']
        );
        return $this->execute($sql, $params);
    }

    /**
     * Insert a user event on "_user_events" database
     * @param $userId
     * @param $eventId
     * @return bool
     */
    public function increaseEventCounter($userId, $eventId)
    {
        if($_SESSION['course_id'] >0){
            $this_cid = $_SESSION['course_id'];
        }else if($_REQUEST['course']){
            $this_cid = $_REQUEST['course'];
        } 
        $sql = 'INSERT INTO '.TABLE_PREFIX.'gm_user_events
                (id_user, id_event, event_counter, course_id)
                VALUES
                (:uid, :eid, 1, :cid)
                ON DUPLICATE KEY UPDATE event_counter = event_counter + 1';
        $params = array(
            ':uid' => $userId,
            ':eid' => $eventId,
            ':cid' => $this_cid
        );
        return $this->execute($sql, $params);
    }

    public function increaseEventPoints($userId, $eventId, $points)
    {
        $sql = 'UPDATE '.TABLE_PREFIX.'gm_user_events
                SET points_counter = points_counter + :c
                WHERE id_user = :uid AND id_event = :eid AND course_id = :cid';
        $params = array(
            ':c' => $points,
            ':uid' => $userId,
            ':eid' => $eventId,
            ':cid' => $_SESSION['course_id']
        );
        return $this->execute($sql, $params);
    }


    public function saveBadgeAlert($userId, $badgeId)
    {
        // echo "User: $userId - badge: $badgeId<br>";
        if($_SESSION['course_id'] > 0){
            $this_cid = $_SESSION['course_id']; 
        }else{
            $this_cid = 0;
        }
        $sql = 'INSERT INTO '.TABLE_PREFIX.'gm_user_alerts
                (id_user, id_badge, id_level, course_id)
                VALUES
                (:uid, :bid, NULL, :cid)';
        $params = array(
            ':uid' => $userId,
            ':bid' => $badgeId,
            ':cid' => $this_cid
        );
        $this->execute($sql, $params);
        return true;
    }

    public function saveLevelAlert($userId, $levelId)
    {
        $sql = 'INSERT INTO '.TABLE_PREFIX.'gm_user_alerts
                (id_user, id_badge, id_level, course_id)
                VALUES
                (:uid, NULL, :lid, :cid)';
        $params = array(
            ':uid' => $userId,
            ':lid' => $levelId,
            ':cid' => $_SESSION['course_id']
        );
        return $this->execute($sql, $params);
    }

    /**
     * Truncate all tables
     * @param Bool $truncateLevelBadge Truncate the "levels" and "badges" tables (rules)
     * @return bool
     */
    public function truncateDatabase($truncateLevelBadge = false)
    {
        $sql = 'TRUNCATE '.TABLE_PREFIX.'gm_user_alerts;
                TRUNCATE '.TABLE_PREFIX.'gm_user_badges;
                TRUNCATE '.TABLE_PREFIX.'gm_user_events;
                TRUNCATE '.TABLE_PREFIX.'gm_user_logs;
                TRUNCATE '.TABLE_PREFIX.'gm_user_scores;';

        if ($truncateLevelBadge)
            $sql .= 'TRUNCATE '.TABLE_PREFIX.'gm_levels;
                     TRUNCATE '.TABLE_PREFIX.'gm_badges;
                     TRUNCATE '.TABLE_PREFIX.'gm_events;';

        $this->execute($sql);
        return true;
    }


}