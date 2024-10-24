<?php

namespace Sangth\Auth\Sdk\Credentials;

class TokenAuthenticator implements Authenticable
{
    public function getIdentifier(): string
    {
        return 'access_token';
    }

    public function getCredentials(\Illuminate\Http\Request $request): array
    {
        $bearerToken = $request->bearerToken();
        
        return [$this->getIdentifier() => $bearerToken];
    }
}
