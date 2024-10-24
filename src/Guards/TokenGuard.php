<?php

namespace Sangth\Auth\Sdk\Guards;

use Illuminate\Http\Request;
use Sangth\Auth\Sdk\Exceptions\PrepAuthenticationException;
use Sangth\Auth\Sdk\Models\User;
use Sangth\Auth\Sdk\Providers\UserProvider;
use Sangth\Auth\Sdk\Credentials\Authenticable;

class TokenGuard
{
    protected UserProvider $userProvider;
    
    protected Authenticable $authenticable;
    
    public function __construct(UserProvider $userProvider, Authenticable $authenticable)
    {
        $this->userProvider = $userProvider;
        
        $this->authenticable = $authenticable;
    }

    /**
     * @throws PrepAuthenticationException
     */
    public function user(Request $request): User
    {
        $credentials = $this->authenticable->getCredentials($request);
 
        return $this->userProvider->retrieveByCredentials($credentials);
    }
}
