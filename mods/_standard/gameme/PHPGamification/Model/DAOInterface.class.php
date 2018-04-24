<?php

namespace gameme\PHPGamification\Model;

/**
 * Interface PHPGamificationDAOint
 * Describe the basic methods to data access with PHPGamification
 */
interface DAOInterface
{
    public function __construct($host,$dbname,$username,$password);

    public function saveBadge($alias, $title, $description, $imageURL);

    public function saveLevel($points, $title, $description);

    public function saveEvent(Event $event);

    public function getUserAlerts($userId, $resetAlerts = false);

    public function getUserBadges($userId);

    public function getUserEvents($userId);

    public function getUserEvent($userId, $eventId);

    public function getUserScore($userId);

    public function getUsersPointsRanking($limit);

    public function grantBadgeToUser($userId, $badgeId);

    public function hasBadgeUser($userId, $badgeId);

    public function grantLevelToUser($userId, $levelId);

    public function grantPointsToUser($userId, $points);

    public function saveBadgeAlert($userId, $badgeId);

    public function saveLevelAlert($userId, $levelId);

    public function increaseEventCounter($userId, $eventId);

    public function increaseEventPoints($userId, $eventId, $points);

    public function logUserEvent($userId, $eventId, $points = null, $badgeId = null, $levelId = null);

    public function getUserLog($userId);

    public function truncateDatabase($truncateLevelBadge = false);
}