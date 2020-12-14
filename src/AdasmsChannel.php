<?php

namespace NotificationChannels\Adasms;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use NotificationChannels\Adasms\Exceptions\CouldNotSendNotification;

/**
 * Class AdasmsChannel.
 */
class AdasmsChannel
{
    /**
     * @var Adasms
     */
    protected $adasms;

    /**
     * Channel constructor.
     *
     * @param Adasms $adasms
     */
    public function __construct(Adasms $adasms)
    {
        $this->adasms = $adasms;
    }

    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     *
     * @throws CouldNotSendNotification
     * @return null|array
     */
    public function send($notifiable, Notification $notification): ?array
    {
        $message = $notification->toAdasms($notifiable);

        if (is_string($message)) {
            $message = new AdasmsMessage($message);
        }
        if (!$to = $notifiable->routeNotificationFor('adasms', $notification)) {
            return null;
        }

        $params = [
            'phone_numbers' => [$to],
            'message' => trim($message->content)
        ];

        if ($message->statusCallback) {
            $params['callback_url'] = $message->statusCallback;
        }

        return $this->adasms->send($params);
    }
}
