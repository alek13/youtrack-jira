<?php


namespace Application\Model;


class Attachment
{
    /**
     * @var \YouTrack\Attachment
     */
    private $attachment;

    /**
     * Attachment constructor.
     *
     * @param \YouTrack\Attachment $attachment
     */
    public function __construct(\YouTrack\Attachment $attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * @return string filename of temp downloaded file
     */
    public function download(): string
    {

        $tmpFilename = './tmp/' . $this->attachment->getId();
        $content     = $this->attachment->fetchContent();
        file_put_contents($tmpFilename, $content);

        return $tmpFilename;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->attachment->getName();
    }
}