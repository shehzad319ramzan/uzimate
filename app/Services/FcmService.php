<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected ?string $serverKey;
    protected ?string $projectId;
    protected ?string $credentialsPath;
    /** @var string|null  */
    protected ?string $credentialsJson;
    public const FCM_V1_TOKEN_CACHE_KEY = 'fcm_v1_access_token';

    protected ?string $lastError = null;

    public static function clearTokenCache(): void
    {
        Cache::forget(self::FCM_V1_TOKEN_CACHE_KEY);
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key');
        $this->projectId = config('services.fcm.project_id');
        $this->credentialsPath = config('services.fcm.credentials');
        $this->credentialsJson = config('services.fcm.credentials_json');
    }


    public function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        $this->lastError = null;
        $data = array_map(fn ($v) => (string) $v, $data);

        if ($this->useV1Api()) {
            return $this->sendV1($token, $title, $body, $data);
        }

        if (empty($this->serverKey)) {
            $this->lastError = 'FCM_NOT_CONFIGURED: Set FCM_SERVER_KEY (legacy) or FIREBASE_PROJECT_ID + FIREBASE_CREDENTIALS_FILE (v1) in .env';
            Log::warning('FCM not configured: set FCM_SERVER_KEY (legacy) or FIREBASE_PROJECT_ID + FIREBASE_CREDENTIALS_FILE (v1). Skipping push.');
            return false;
        }

        return $this->sendLegacy($token, $title, $body, $data);
    }

    protected function useV1Api(): bool
    {
        if (empty($this->projectId)) {
            return false;
        }
        if (!empty($this->credentialsJson)) {
            $decoded = json_decode($this->credentialsJson, true);
            return is_array($decoded) && !empty($decoded['client_email']) && !empty($decoded['private_key']);
        }
        if (empty($this->credentialsPath)) {
            return false;
        }
        $path = $this->resolveCredentialsPath();
        return $path !== null && is_file($path) && is_readable($path);
    }

    protected function resolveCredentialsPath(): ?string
    {
        $path = trim($this->credentialsPath);
        if ($path === '') {
            return null;
        }
        if (str_starts_with($path, '/') || (strlen($path) >= 2 && $path[1] === ':')) {
            return $path;
        }
        return base_path($path);
    }

    protected function sendLegacy(string $token, string $title, string $body, array $data): bool
    {
        $payload = [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            if (!$response->successful()) {
                $msg = 'FCM_LEGACY_HTTP_' . $response->status() . ': ' . $response->body();
                $this->lastError = $msg;
                Log::warning('FCM legacy send failed', [
                    'reason' => $msg,
                    'token_preview' => substr($token, 0, 20) . '...',
                ]);
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            $this->lastError = 'FCM_LEGACY_EXCEPTION: ' . get_class($e) . ' - ' . $e->getMessage();
            Log::error('FCM legacy exception', ['reason' => $e->getMessage(), 'exception' => get_class($e)]);
            return false;
        }
    }

    protected function sendV1(string $token, string $title, string $body, array $data): bool
    {
        $accessToken = $this->getV1AccessToken();
        if (empty($accessToken)) {
            if (empty($this->lastError)) {
                $this->lastError = 'FCM_V1: Failed to obtain access token (check logs for details).';
            }
            return false;
        }

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ],
        ];

        try {
            $url = 'https://fcm.googleapis.com/v1/projects/' . $this->projectId . '/messages:send';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if (!$response->successful()) {
                $bodyStr = $response->body();
                $decoded = json_decode($bodyStr, true);
                $detail = isset($decoded['error']['message']) ? $decoded['error']['message'] : $bodyStr;
                $this->lastError = 'FCM_V1_HTTP_' . $response->status() . ': ' . $detail;
                Log::warning('FCM v1 send failed', [
                    'reason' => $this->lastError,
                    'token_preview' => substr($token, 0, 20) . '...',
                ]);
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            $this->lastError = 'FCM_V1_EXCEPTION: ' . get_class($e) . ' - ' . $e->getMessage();
            Log::error('FCM v1 exception', ['reason' => $e->getMessage(), 'exception' => get_class($e)]);
            return false;
        }
    }


    protected function getV1AccessToken(): ?string
    {
        return Cache::remember(self::FCM_V1_TOKEN_CACHE_KEY, 55 * 60, function () {
            $json = $this->credentialsJson;
            if (empty($json)) {
                $path = $this->resolveCredentialsPath();
                if ($path === null) {
                    $this->lastError = 'FCM_V1: Credentials path not set (FIREBASE_CREDENTIALS_FILE in .env).';
                    return null;
                }
                $json = @file_get_contents($path);
                if ($json === false) {
                    $this->lastError = 'FCM_V1: Could not read credentials file at: ' . $path . ' (check path and file permissions).';
                    Log::warning('FCM v1: could not read credentials file: ' . $this->credentialsPath);
                    return null;
                }
            }
            $creds = json_decode($json, true);
            if (empty($creds['client_email']) || empty($creds['private_key'])) {
                $this->lastError = 'FCM_V1: Invalid credentials JSON - client_email and private_key are required.';
                Log::warning('FCM v1: invalid credentials JSON (client_email, private_key required).');
                return null;
            }
            $privateKey = str_replace('\\n', "\n", $creds['private_key']);
            $jwt = $this->createJwt($creds['client_email'], $privateKey);
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);
            if (!$response->successful()) {
                $this->lastError = 'FCM_V1: Failed to get OAuth access token - ' . $response->body();
                Log::warning('FCM v1: failed to get access token', ['body' => $response->body()]);
                return null;
            }
            return $response->json('access_token');
        });
    }

    protected function createJwt(string $clientEmail, string $privateKey): string
    {
        $now = time();
        $payload = [
            'iss' => $clientEmail,
            'sub' => $clientEmail,
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        ];
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $segments = [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($payload)),
        ];
        $signature = '';
        openssl_sign(
            implode('.', $segments),
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );
        $segments[] = $this->base64UrlEncode($signature);
        return implode('.', $segments);
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }


    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $tokens = array_unique(array_filter($tokens));
        $results = [];
        foreach ($tokens as $token) {
            $results[$token] = $this->sendToToken($token, $title, $body, $data);
        }
        return $results;
    }
}
