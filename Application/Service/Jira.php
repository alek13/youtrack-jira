<?php
namespace Application\Service;

use Application\Config;
use chobie\Jira\Api;

class Jira
{
    /**
     * @var Jira\Issue
     */
    static private $issueService;
    /**
     * @var Api
     */
    private static $api;

    /**
     * @return Jira\Issue
     */
    public static function issue()
    {
        return self::$issueService
            ? self::$issueService
            : self::$issueService = new Jira\Issue(self::getApi());
    }

    /**
     * @return \chobie\Jira\Api
     */
    private static function getApi(): Api
    {
        return self::$api
            ? self::$api
            : self::$api = new Api(
                Config::jira('url'),
                new Api\Authentication\Basic(
                    Config::jira('user'),
                    Config::jira('password')
                )
            );
    }
}
