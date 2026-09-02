<?php

namespace App\Http\Controllers\MobileApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Google\Auth\CredentialsLoader;
use GuzzleHttp\Client;

abstract class BaseApiController extends Controller
{
    protected ?string $errorMessage = null;
    protected ?string $token = null;
    protected ?string $topic = null;

    /**
     * Success response
     */
    protected function success(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        $response = ['status' => true];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Error response
     */
    protected function error(string $message, int $code = 400, mixed $data = null): JsonResponse
    {
        $response = [
            'status' => false,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Not found response
     */
    protected function notFound(string $message = 'Data tidak ditemukan'): JsonResponse
    {
        return $this->error($message, 404);
    }

    /**
     * Validation error response
     */
    protected function validationError(string $message): JsonResponse
    {
        return $this->error($message, 422);
    }

    /**
     * Upload file handler
     */
    protected function upload(Request $request, string $param, string $targetDir, array $allowedExtensions = ['docx', 'doc', 'pdf', 'xlsx', 'png', 'jpeg', 'jpg', 'bmp']): string|false
    {
        if (!$request->hasFile($param)) {
            $this->errorMessage = "File tidak ditemukan";
            return false;
        }

        $file = $request->file($param);

        if (!$file->isValid()) {
            $this->errorMessage = "File tidak valid";
            return false;
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) {
            $this->errorMessage = "Tipe file tidak diizinkan. Tipe yang diizinkan: " . implode(', ', $allowedExtensions);
            return false;
        }

        $fileName = md5(uniqid() . time()) . '.' . $extension;

        try {
            $file->move(public_path($targetDir), $fileName);
            return $fileName;
        } catch (\Exception $e) {
            $this->errorMessage = "Proses upload dokumen gagal: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get error message
     */
    protected function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Set error message
     */
    protected function setErrorMessage(string $message): self
    {
        $this->errorMessage = $message;
        return $this;
    }

    /**
     * Convert numeric strings to actual numbers in array
     */
    protected function toNumber(array|object $data): array
    {
        $isArray = is_array($data) && (empty($data) || array_keys($data) === range(0, count($data) - 1));

        if ($isArray) {
            return array_map(function ($item) {
                return $this->convertItemToNumber($item);
            }, $data);
        }

        return $this->convertItemToNumber($data);
    }

    /**
     * Convert single item numeric values
     */
    private function convertItemToNumber(array|object $item): array
    {
        $result = [];
        foreach ($item as $key => $value) {
            $result[$key] = is_numeric($value) ? (float) $value : $value;
        }
        return $result;
    }

    /**
     * Send notification to specific person
     */
    protected function sendNotifPerson(string $title, string $message, string $token): mixed
    {
        $this->topic = null;
        $this->token = $token;
        return $this->sendNotification($title, $message);
    }

    /**
     * Send broadcast notification
     */
    protected function sendNotifBroadcast(string $title, string $message, string $topic): mixed
    {
        $this->topic = $topic;
        $this->token = null;
        return $this->sendNotification($title, $message);
    }

    /**
     * Send FCM notification using Firebase Admin SDK
     */
    private function sendNotification(string $title, string $message): mixed
    {
        try {
            $keyFilePath = config('services.firebase.credentials_path');

            if (!file_exists($keyFilePath)) {
                return false;
            }

            $scope = 'https://www.googleapis.com/auth/firebase.messaging';
            $credentials = CredentialsLoader::makeCredentials(
                [$scope],
                json_decode(file_get_contents($keyFilePath), true)
            );

            $authToken = $credentials->fetchAuthToken();
            $accessToken = $authToken['access_token'];

            $client = new Client([
                'base_uri' => 'https://fcm.googleapis.com',
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $payload = [
                'message' => [
                    'notification' => [
                        'title' => $title,
                        'body' => $message,
                    ],
                ],
            ];

            if ($this->token) {
                $payload['message']['token'] = $this->token;
            } elseif ($this->topic) {
                $payload['message']['topic'] = $this->topic;
            } else {
                return false;
            }

            $projectId = config('services.firebase.project_id', 'haromain-afindo');
            $response = $client->post("/v1/projects/{$projectId}/messages:send", [
                'json' => $payload,
            ]);

            return $response->getBody();
        } catch (\Exception $e) {
            \Log::error('FCM Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get token
     */
    protected function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Get topic
     */
    protected function getTopic(): ?string
    {
        return $this->topic;
    }

    /**
     * Sanitize input
     */
    protected function sanitize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        return htmlspecialchars(strip_tags($value));
    }
}
