<?php
namespace Application\Command\Users;

use Application\Service\YouTrack;
use Colibri\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use YouTrack\User;

/**
 * `users:show` command.
 * Displays list of users.
 * Shows issue key and summary.
 */
class Show extends Command
{
    /**
     * @return \Colibri\Console\Command
     */
    protected function definition(): Command
    {
        return $this
            ->setDescription('Shows list of users in YouTrack')
            ->addOption('fetch', 'f', InputOption::VALUE_NONE, 'Fetches user data for each user (email, full name)')
            ;
    }

    /**
     * @return int script exit code
     */
    protected function go(): int
    {
        YouTrack::user()->walk(function (User $user) {
            $this
                ->info($user->getLogin())
                ->write(' ')
                ->write("<fg=cyan>{$user->getEmail()}</>")
                ->write(' ')
                ->writeLn((string)$user->getFullName())
            ;
        }, (bool)$this->option('fetch'))
        ;

        return 0;
    }
}
