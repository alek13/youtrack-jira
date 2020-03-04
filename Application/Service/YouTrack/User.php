<?php
namespace Application\Service\YouTrack;

use YouTrack\Connection;

class User
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
     * @param callable $callback callback function which takes issue as param: `function (\YouTrack\User $issue) {...}`
     * @param bool     $fetch
     */
    public function walk(callable $callback, $fetch = false): void
    {
        $cursor = $this->cursor($fetch);
        foreach ($cursor as $user) {
            if ($callback($user) === false) {
                break;
            }
        }
    }

    /**
     * @param bool $fetch
     *
     * @return \Generator|\YouTrack\User[]
     */
    public function cursor($fetch = false): \Generator
    {
        $start = 0;
        while ($users = $this->client->getUsers('', '', '', '', '', false, $start)) {
            foreach ($users as $user) {
                yield ($fetch
                    ? $this->client->getUser($user->getLogin())
                    : $user
                );
            }
            $start += 10;
        }
    }
}