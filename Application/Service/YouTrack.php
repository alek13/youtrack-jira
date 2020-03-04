<?php
namespace Application\Service;

use Application\Config;
use YouTrack\Connection;

class YouTrack
{
    /** @var \YouTrack\Connection */
    private static $api;
    /** @var \Application\Service\YouTrack\Issue */
    private static $issueService;
    /** @var \Application\Service\YouTrack\User */
    private static $userService;

    /**
     * @return YouTrack\Issue
     */
    public static function issue(): YouTrack\Issue
    {
        return self::$issueService
            ? self::$issueService
            : self::$issueService = new YouTrack\Issue(self::getApi());
    }

    public static function user(): YouTrack\User
    {
        return self::$userService
            ? self::$userService
            : self::$userService = new YouTrack\User(self::getApi());
    }

    /**
     * @return \YouTrack\Connection
     */
    private static function getApi(): Connection
    {
        return self::$api
            ? self::$api
            : self::$api = new Connection(
                Config::youTrack('url'),
                'perm:' . Config::youTrack('token'),
                null
            );
    }
}
