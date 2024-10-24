<?php

namespace Sangth\Auth\Sdk\Providers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Auth\Authenticatable;
use Sangth\Auth\Sdk\Exceptions\JWTPayloadException;
use Sangth\Auth\Sdk\Exceptions\PrepAuthenticationException;
use Sangth\Auth\Sdk\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Sangth\Auth\Sdk\Supports\TokenVerifier;

class UserProvider implements \Illuminate\Contracts\Auth\UserProvider
{
    use TokenVerifier;
    
    const URI = "/api/jwt/claims";

    protected string $host;

    protected string $model;

    public function __construct(string $host, string $model)
    {
        $this->host = $host;

        $this->model = $model;
    }

    /**
     * @throws PrepAuthenticationException
     */
    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials): User
    {
        $claims = $this->authenticate($credentials);

        if (!empty($claims)) {
            return new User($claims);
        }

        throw new PrepAuthenticationException();
    }

    /**
     * @throws PrepAuthenticationException
     * @throws Exception
     */
    protected function authenticate(array $credentials)
    {
        if (!config('auth.enable_self_verify_token')) {
            return $this->authenticateWithApi($credentials);
        }
        try {
            return $this->verifyByRS256($credentials['access_token'], storage_path('oauth-keys/oauth-public.key'));
        } catch (JWTPayloadException $exception) {
            Log::error($exception);
            return $this->authenticateWithApi($credentials);
        }
    }

    /**
     * @throws PrepAuthenticationException
     */
    protected function authenticateWithApi(array $credentials)
    {
        $url = $this->host . self::URI;
        $client = app(Client::class);
        $origin = Arr::get($_SERVER, 'HTTP_ORIGIN', Arr::get($_SERVER, 'HTTP_REFERER'));

        $headers = [
            'AUTH_ORIGIN' => $origin,
            'Authorization' => $credentials['access_token'] ? "Bearer {$credentials['access_token']}" : '',
        ];

        $response = $client->get($url, [
            'http_errors' => false,
            'headers' => $headers,
        ]);

        $content = $response->getBody()->getContents();
        $statusCode = $response->getStatusCode();

        if ($statusCode == \Symfony\Component\HttpFoundation\Response::HTTP_OK && !empty($content)) {
            $decoded = json_decode($content, true);

            return $decoded['data'];
        }

        Log::warning("Fail to send auth request to $url (http code=$statusCode) " . PHP_EOL . $content);

        throw new PrepAuthenticationException();
    }


    /**
     * @throws Exception
     */
    public function retrieveById($identifier)
    {
        throw new Exception('Not implemented');
    }

    /**
     * @throws Exception
     */
    public function retrieveByToken($identifier, #[\SensitiveParameter] $token)
    {
        throw new Exception('Not implemented');
    }

    /**
     * @throws Exception
     */
    public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token)
    {
        throw new Exception('Not implemented');
    }

    /**
     * @throws Exception
     */
    public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials)
    {
        throw new Exception('Not implemented');
    }

    /**
     * @throws Exception
     */
    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false)
    {
        throw new Exception('Not implemented');
    }
}
