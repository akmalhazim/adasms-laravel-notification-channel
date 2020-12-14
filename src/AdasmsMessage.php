<?php

namespace NotificationChannels\Adasms;

class AdasmsMessage
{
    /**
     * The message content.
     *
     * @var string
     */
    public $content;

    /**
     * The webhook to be called with status updates.
     *
     * @var string
     */
    public $statusCallback = '';

    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * Set the message content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function content(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the webhook callback URL to update the message status.
     *
     * @param string $callback
     *
     * @return $this
     */
    public function statusCallback(string $callback)
    {
        $this->statusCallback = $callback;

        return $this;
    }
}
