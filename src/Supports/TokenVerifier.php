<?php

namespace Sangth\Auth\Sdk\Supports;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use Sangth\Auth\Sdk\Exceptions\JWTPayloadException;

trait TokenVerifier
{
    /**
     * @throws JWTPayloadException
     * @throws Exception
     */
    public function verifyByRS256(string $jwt, string $publicKeyPath): array
    {
        if (!file_exists($publicKeyPath)) {
            throw new Exception('Public key not found.');
        }
        $publicKey = file_get_contents($publicKeyPath);
        try {
            return (array)JWT::decode($jwt, new Key($publicKey, 'RS256'));
        } catch (\Throwable $exception) {
            Log::error($exception);
            throw new JWTPayloadException('Invalid token.');
        }
    }
}
