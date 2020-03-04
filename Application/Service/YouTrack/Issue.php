<?php
namespace Application\Service\YouTrack;

use Generator;
use YouTrack\Connection;

class Issue
{
    /**
     * @var \YouTrack\Connection
     */
    private $client;


    /**
     * @param \YouTrack\Connection $client
     */
    public function __construct(Connection $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $projectKey
     * @param string $filter
     *
     * @return int
     */
    public function count(string $projectKey, string $filter): int
    {
        $query = ($projectKey ? 'project: ' . $projectKey . ' ' : '') . $filter;

        return (int)$this->client->executeCountQueries([$query])[0];
    }

    /**
     * @param string   $projectKey
     * @param string   $filter
     * @param callable $callback callback function which takes issue as param: `function (\YouTrack\Issue $issue) {...}`
     * @param bool     $all      walk through all pages
     * @param int      $page     page number
     * @param int      $rpp      records per page
     */
    public function walk(string $projectKey, string $filter, callable $callback, bool $all = false, int $page = 0, int $rpp = 30)
    {
        $list = $all
            ? $this->listCursor($projectKey, $filter)
            : $this->list($projectKey, $filter, $page, $rpp)
        ;
        foreach ($list as $issue) {
            if ($callback($issue) === false) {
                break;
            }
        }
    }

    /**
     * @param string $projectKey
     * @param string $filter
     *
     * @return \Generator|\YouTrack\Issue[]
     */
    public function listCursor(string $projectKey, string $filter): Generator
    {
        $rpp   = 100;
        $count = $this->count($projectKey, $filter);
        $pages = ceil($count / $rpp);

        for ($i = 0; $i < $pages; $i++) {
            $page = $this->list($projectKey, $filter, $i, $rpp);
            foreach ($page as $issue) {
                yield $issue;
            }
        }
    }

    /**
     * @param string $projectKey
     * @param string $filter
     * @param int    $page page number
     * @param int    $rpp  records per page
     *
     * @return iterable|\YouTrack\Issue[]
     */
    public function list(string $projectKey, string $filter, int $page, int $rpp): iterable
    {
        return $projectKey
            ? $this->client->getIssues($projectKey, $filter, ($page) * $rpp, $rpp)
            : $this->client->getIssuesByFilter($filter, ($page) * $rpp, $rpp);
    }

}