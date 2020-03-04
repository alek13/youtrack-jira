<?php
namespace Application\Command\Jira;

use Application\Service\Jira;
use Colibri\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class IssueTypes extends Command
{

    protected function definition(): Command
    {
        return $this
            ->setDescription('Shows list of issue types in Jira')
            ->addOption('only-project', 'p', InputOption::VALUE_NONE, 'Get types only from configured project')
            ;
    }

    protected function go(): int
    {
        $onlyFromProject = (bool)$this->option('only-project');

        if (!$onlyFromProject) {
            $this
                ->comment('WARNING: ')
                ->write('This is the list of ')->bold('all')->write(' in Jira. ')
                ->bold('Not only')->writeLn(' of your project!')
            ;
        }

        $types = [];
        foreach (Jira::issue()->types($onlyFromProject) as $type) {
            $types [] = [
                $type->getId(),
                $type->getName(),
            ];
        }

        $this->table(['Id', 'Name'], $types);

        return 0;
    }
}
