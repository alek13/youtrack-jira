<?php
namespace Application\Model;


use Carbon\Carbon;

class Comment
{
    /**
     * @var \YouTrack\Comment
     */
    private $comment;

    /**
     * Comment constructor.
     *
     * @param \YouTrack\Comment $comment
     */
    public function __construct(\YouTrack\Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->comment->getAuthor()->getFullName();
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->comment->getText();
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getCreated(): Carbon
    {
        return Carbon::createFromTimestampMs($this->comment->getCreated());
    }
}