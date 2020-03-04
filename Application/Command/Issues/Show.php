<?php
namespace Application\Command\Issues;

use Application\Config;
use Application\Service\YouTrack;
use Colibri\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use YouTrack\Issue;

/**
 * `issues:show` command.
 * Displays list of issues page by page or `--all` pages.
 * Shows issue key and summary.
 */
class Show extends ListBaseCommand
{
    /**
     * @return \Colibri\Console\Command
     */
    protected function definition(): Command
    {
        return $this
            ->setDescription('Shows list of issues in YouTrack by filter & project in config (paged)')
            ->addOption('page', 'p', InputOption::VALUE_REQUIRED, 'page of list', 0)
            ->addOption('records-per-page', 'r', InputOption::VALUE_REQUIRED, 'records count per shown page of list', 30)
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Display all pages')
            ->addOption('collect', 'c', InputOption::VALUE_REQUIRED, 'Collects and shows issue field that can be converted to string')
            ;
    }

    /**
     * @return int script exit code
     */
    protected function go(): int
    {
        $collect = (string)$this->option('collect');

        $collection = [];
        $this->issuesWalk(function (Issue $issue) use ($collect, &$collection) {
            if ($collect && $issue->$collect) {
                $value              = (string)$issue->$collect;
                $collection[$value] = isset($collection[$value]) ? $collection[$value] + 1 : 1;
            }
            $this->outputIssueInfo($issue);
        });

        if ($collect) {
            $this->table(
                [$collect, 'Count'],
                $this->collectionToTableRows($collection)
            );
        }

        return 0;
    }

    private function issuesWalk(callable $callback)
    {
        $page = (int)$this->option('page');
        $rpp  = (int)$this->option('records-per-page');
        $all  = (bool)$this->option('all');

        $projectKey = Config::youTrack('project-key');
        $filter     = Config::youTrack('issues-filter');

        YouTrack::issue()->walk($projectKey, $filter, $callback, $all, $page, $rpp);
    }

    /**
     * @param array $collection
     *
     * @return array
     */
    private function collectionToTableRows(array $collection): array
    {
        return array_map(function ($key, $value) {
            return [$key, $value];
        }, array_keys($collection), array_values($collection));
    }
}
