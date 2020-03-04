<?php
namespace Application\Model;

use Application\Config;
use YouTrack\Issue as YTIssue;

class Issue
{
    /**
     * @var \YouTrack\Issue
     */
    private $issue;

    /**
     * Issue constructor.
     *
     * @param \YouTrack\Issue $issue
     */
    public function __construct(YTIssue $issue)
    {
        $this->issue = $issue;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return (string)Config::issue('types-map')[$this->issue->Type];
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return (string)$this->issue->summary;
    }

    /**
     * @return array
     */
    public function getLabels(): array
    {
        return Config::issue('default.labels');
    }

    /**
     * @return string|null
     */
    public function getAssignee()
    {
        $assigneeField = Config::youTrack('assignee-field');
        $map           = Config::issue('assignees-map');
        $assignee      = $this->issue->$assigneeField ?: 'default';

        return $map[$assignee] ?? $map['default'];
    }

    /**
     * @return string|null
     */
    public function getDescription(): string
    {
        return $this->issue->getDescription();
    }

    /**
     * @return \Application\Model\Attachment[]|\Generator|iterable
     */
    public function getAttachments()
    {
        foreach ($this->issue->getAttachments() as $attachment) {
            yield new Attachment($attachment);
        }
    }

    /**
     * @return \Application\Model\Comment[]|\Generator|iterable
     */
    public function getComments()
    {
        foreach ($this->issue->getComments() as $comment) {
            yield new Comment($comment);
        }
    }
}
