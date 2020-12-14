<?php

namespace NotificationChannels\Adasms\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;

/**
 * Class CouldNotSendNotification.
 */
class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when the given phone number is not valid.
     *
     * @param string $phone
     *
     * @return static
     */
    public static function invalidPhoneNumber(string $phone = null): self
    {
        if (!$phone) {
            return new static('Phone number provided is not a valid phone number');
        }

        return new static("$phone is not a valid phone number");
    }

    /**
     * Thrown when there's a bad request and an error is responded.
     *
     * @param ClientException $exception
     *
     * @return static
     */
    public static function adasmsRespondedWithAnError($exception): self
    {
        if ($exception instanceof ClientException) {
            if (!$exception->hasResponse()) {
                return new static('Adasms responded with an error but no response body found');
            }

            $statusCode = $exception->getResponse()->getStatusCode();

            $result = json_decode($exception->getResponse()->getBody(), false);
            $description = $result->description ?? 'no description given';

            return new static("Adasms responded with an error `{$statusCode} - {$description}`", 0, $exception);
        }
        if (is_string($exception)) {
            return new static("AdaSMS server error `$exception`");
        }

        return new static('AdaSMS unknown server error');
    }

    /**
     * Thrown when there's no API token provided.
     *
     * @param string $message
     *
     * @return static
     */
    public static function adasmsTokenNotProvided($message): self
    {
        return new static($message);
    }

    /**
     * Thrown when we're unable to communicate with Adasms.
     *
     * @param $message
     *
     * @return static
     */
    public static function couldNotCommunicateWithAdasms($message): self
    {
        return new static("The communication with AdaSMS failed. `{$message}`");
    }
}
