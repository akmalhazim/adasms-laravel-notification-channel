<?php

namespace NotificationChannels\Adasms;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\RequestOptions;
use NotificationChannels\Adasms\Exceptions\CouldNotSendNotification;
use Illuminate\Support\Arr;

class Adasms
{
    /** @var HttpClient HTTP Client */
    protected $http;

    /** @var string|null Adasms API Token. */
    protected $token;

    /** @var string Adasms API Base URI */
    protected $apiBaseUri;

    /**
     * @param string|null     $token
     * @param HttpClient|null $httpClient
     * @param string|null     $apiBaseUri
     */
    public function __construct($token = null, HttpClient $httpClient = null, $apiBaseUri = null)
    {
        $this->token = $token;
        $this->http = $httpClient ?? new HttpClient();
        $this->apiBaseUri($apiBaseUri ?? 'https://terminal.adasms.com/api/v1');
    }

    /**
     * Token setter.
     *
     * @param string $token
     *
     * @return $this
     */
    public function token(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * API Base URI setter.
     *
     * @param string $apiBaseUri
     *
     * @return $this
     */
    public function apiBaseUri(string $apiBaseUri): self
    {
        $this->apiBaseUri = rtrim($apiBaseUri, '/');

        return $this;
    }

    /**
     * Set HTTP Client.
     *
     * @param HttpClient $http
     *
     * @return $this
     */
    public function http(HttpClient $http): self
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Send text message.
     *
     * <code>
     * $params = [
     *   'phone_numbers' => [''],
     *   'message' => '',
     *   'callback_url' => ''
     * ];
     * </code>
     *
     * @link https://terminal.adasms.com/api
     *
     * @param array $params
     *
     * @throws CouldNotSendNotification
     *
     * @return ResponseInterface|null
     */
    public function send(array $message)
    {
        if (blank($this->token)) {
            throw CouldNotSendNotification::adasmsTokenNotProvided('You must provide your AdaSMS API token to make any API requests.');
        }

        $phone = implode(',', $message['phone_numbers']);
        $msg = $message['message'];
        $callbackUrl = $message['callback_url'] ?? null;

        $apiUrl = sprintf("%s/send", $this->apiBaseUri);

        try {
            $response = $this->http->post($apiUrl, [
                RequestOptions::JSON => [
                    '_token' => $this->token,
                    'phone' => $phone,
                    'message' => $msg,
                    'callback_url' => $callbackUrl // todo implement delivery report with $msgId
                ]
            ]);

            $payload = json_decode($response->getBody()->getContents(), true);

            if ($error = Arr::get($payload, 'error')) {
                $explain = Arr::get($payload, 'explain');
                throw CouldNotSendNotification::adasmsRespondedWithAnError("'$error': $explain");
            }

            return $payload;
        } catch (CouldNotSendNotification $exception) {
            throw $exception;
        } catch (ClientException $exception) {
            throw CouldNotSendNotification::adasmsRespondedWithAnError($exception);
        } catch (Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithAdasms($exception);
        }
    }
}
