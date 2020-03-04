<?php
namespace Application\Command\Jira;

use Application\Config;
use Application\Service\Jira;
use Colibri\Console\Command;

class Assignees extends Command
{

    protected function definition(): Command
    {
        return $this
            ->setDescription('Shows list of assignable users in Jira (for configured project)');
    }

    protected function go(): int
    {
        $rows = [];
        foreach (Jira::issue()->assignees(Config::jira('project-key')) as $assignee) {
            $rows [] = [
                $assignee['name'],
                $assignee['displayName'],
                $assignee['active'],
            ];
        }

        $this->table(['Id', 'Name', 'Active'], $rows);

        return 0;
    }
}
