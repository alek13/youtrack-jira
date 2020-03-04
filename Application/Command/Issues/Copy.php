<?php
namespace Application\Command\Issues;

use Application\Config;
use Application\Model\Issue;
use Application\Service\Jira;
use Application\Service\YouTrack;
use Application\Db;
use Colibri\Console\Command;
use Symfony\Component\Console\Input\InputOption;


class Copy extends Command
{

    protected function definition(): Command
    {
        return $this
            ->setDescription('Copies issues from YouTrack into Jira')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Stops after first N issues')
            ;
    }

    /**
     * @return int script exit code
     */
    protected function go(): int
    {
        $limit      = $this->option('limit');
        $projectKey = Config::youTrack('project-key');
        $filter     = Config::youTrack('issues-filter');

        YouTrack::issue()->walk($projectKey, $filter, function (\YouTrack\Issue $issue) use ($limit) {
            static $i = 0;

            $this
                ->info($issue->getId())
                ->write(' ')
                ->outByWidth($issue->summary, 60)
            ;

            $this->createInJira($issue);

            return !((bool)$limit && ++$i >= $limit);
        }, true)
        ;

        return 0;
    }

    /**
     * @param \YouTrack\Issue $issue
     */
    private function createInJira(\YouTrack\Issue $issue)
    {
        $converter = new Issue($issue);
        $key       = Jira::issue()->create(
            $converter->getType(),
            $converter->getSummary(),
            $converter->getLabels(),
            $converter->getAssignee(),
            $converter->getDescription()
        )
        ;

        foreach ($converter->getAttachments() as $attachment) {
            Jira::issue()->addAttachment(
                $key,
                $attachment->download(),
                $attachment->getName()
            )
            ;
        }

        foreach ($converter->getComments() as $comment) {
            $author  = $comment->getAuthor();
            $message = $comment->getMessage();
            $date    = $comment->getCreated();

            Jira::issue()->addComment(
                $key,
                "*[$date] $author :*\n$message"
            )
            ;
        }

        Jira::issue()->addYouTrackLink($key, $issue->getId());
    }

}
