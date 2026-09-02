<?php

namespace App\Services\Firebase;

use GuzzleHttp\Client;
use Google\Auth\CredentialsLoader;


class FirebaseNotificationService
{

    protected $token = null;
    protected $topic = null;

    public function getTopic()
    {
        return $this->topic;
    }

    public function setTopic($topic)
    {
        $this->topic = $topic;
        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function sendNotifBroadcast($title, $message, $topic)
    {
        $this->topic = $topic;
        $this->token = null;

        return $this->sendNotifV2($title, $message);
    }

    public function sendNotifPerson($title, $message, $token)
    {
        $this->topic = null;
        $this->token = $token;
        return $this->sendNotifV2($title, $message);
    }

    private function sendNotifV2($title, $message)
    {
        $credentialPath = storage_path('app/firebase/haromain-afindo-firebase-adminsdk-qzlal-87b11d086c.json');


        $scope = 'https://www.googleapis.com/auth/firebase.messaging';

        $credentials = CredentialsLoader::makeCredentials(
            [$scope],
            json_decode(file_get_contents($credentialPath), true)
        );


        $authToken = $credentials->fetchAuthToken();
        $accessToken = $authToken['access_token'] ?? null;

        if (!$accessToken) {
            return false;
        }

        $client = new Client([
            'base_uri' => 'https://fcm.googleapis.com',
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ],
        ]);

        $payload = [
            'message' => [
                'notification' => [
                    'title' => $title,
                    'body'  => $message,
                ],
            ],
        ];

        if ($this->getToken()) {
            $payload['message']['token'] = $this->getToken();
        } elseif ($this->getTopic()) {
            $payload['message']['topic'] = $this->getTopic();
        } else {
            return false;
        }

        $response = $client->post(
            '/v1/projects/haromain-afindo/messages:send',
            ['json' => $payload]
        );

        return $response->getBody()->getContents();
    }
}
