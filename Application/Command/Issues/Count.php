<?php
namespace Application\Command\Issues;

use Application\Config;
use Application\Service\YouTrack;
use Colibri\Console\Command;

class Count extends Command
{

    protected function definition(): Command
    {
        return $this
            ->setDescription('Shows total count of issues in YouTrack by filter & project in config')
            ;
    }

    protected function go(): int
    {
        $projectKey = Config::youTrack('project-key');
        $filter     = Config::youTrack('issues-filter');

        $this->writeLn((string)YouTrack::issue()->count(
            $projectKey,
            $filter
        ));

        return 0;
    }
}
