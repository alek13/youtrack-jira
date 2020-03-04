<?php
namespace Application\Command\Issues;

use Application\Config;
use Colibri\Console\Command;
use YouTrack\Issue;

abstract class ListBaseCommand extends Command
{
    /**
     * @param \YouTrack\Issue $issue
     */
    protected function outputIssueInfo(Issue $issue)
    {
        $assigneeField = Config::youTrack('assignee-field');

        $this
            ->info($issue->getId())
            ->write(' ')
            ->comment($issue->getType())
            ->write(' ')
            ->write("<fg=magenta>{$issue->getStatus()}</>")
            ->write(' ')
            ->write("<fg=cyan>{$issue->$assigneeField}</>")
            ->write(' ')
            ->writeLn($issue->getSummary())
        ;
    }
}