<?php
namespace Application\Service\Jira;

use Application\Config;
use chobie\Jira\Api;
use chobie\Jira\IssueType;

class Issue
{
    /**
     * @var Api
     */
    private $client;

    /**
     * @param \chobie\Jira\Api $client
     */
    public function __construct(Api $client)
    {
        $this->client = $client;
    }

    /**
     * @param string      $type
     * @param string      $summary
     * @param array       $labels
     * @param string|null $assignee
     * @param string|null $description
     *
     * @return string issue key
     */
    public function create(string $type, string $summary, array $labels = [], string $assignee = null, string $description = null): string
    {
        $additionalFields['labels'] = $labels;
        if ($assignee !== null) {
            $additionalFields['assignee'] = ['name' => $assignee];
        }
        if ($description !== null) {
            $additionalFields['description'] = $description;
        }

        $result = $this->client->createIssue(
            Config::jira('project-key'),
            $summary,
            $type,
            $additionalFields
        )->getResult();


        if ($result['errors']) {
            throw new \RuntimeException(print_r($result['errors'], true));
        }

        return $result['key'];
    }

    /**
     * @param bool $onlyProject
     *
     * @return array|\chobie\Jira\IssueType[]
     * @throws \Exception
     */
    public function types($onlyProject = false): array
    {
        return $onlyProject
            ? $this->getProjectIssueTypes(Config::jira('project-key'))
            : $this->client->getIssueTypes();
    }

    /**
     * @param string $projectKeys
     *
     * @return array|\chobie\Jira\Api\Result|false
     */
    public function assignees(string $projectKeys)
    {
        return $this->client->api(Api::REQUEST_GET, '/rest/api/2/user/assignable/multiProjectSearch', [
            'projectKeys' => $projectKeys
        ], true);
    }

    /**
     * @param string $jiraIssueKey
     * @param string $youTrackIssueKey
     *
     * @return \Application\Service\Jira\Issue
     */
    public function addYouTrackLink(string $jiraIssueKey, string $youTrackIssueKey)
    {
        $this->client->createRemoteLink(
            $jiraIssueKey,
            [
                'url'   => Config::youTrack('url') . '/issue/' . $youTrackIssueKey,
                'title' => "YouTrack Issue",
                'icon'  => ['url16x16' => Config::youTrack('url') . '/favicon.ico']
            ]
        );

        return $this;
    }

    /**
     * @param string $issueKey
     * @param string $filename
     * @param string $name
     *
     * @return \Application\Service\Jira\Issue
     */
    public function addAttachment(string $issueKey, string $filename, string $name)
    {
        $this->client->createAttachment($issueKey, $filename, $name);

        return $this;
    }

    /**
     * @param string $projectKey
     *
     * @return array|\chobie\Jira\IssueType[]
     * @throws \Exception
     */
    private function getProjectIssueTypes(string $projectKey): array
    {
        $types = [];
        foreach ($this->client->getProjectIssueTypes($projectKey) as $issue_type) {
            $types[] = new IssueType($issue_type);
        }

        return $types;
    }

    /**
     * @param string $issueKey
     * @param string $message
     *
     * @return \Application\Service\Jira\Issue
     */
    public function addComment(string $issueKey, string $message)
    {
        $this->client->addComment($issueKey, [
            'body' => $message,
        ]);

        return $this;
    }
}
