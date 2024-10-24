<?php

namespace Sangth\Auth\Sdk\Exceptions;

use Illuminate\Auth\AuthenticationException;

class PrepAuthenticationException extends AuthenticationException
{
    public function __construct($message = 'Unauthenticated.', $guards = [])
    {
        parent::__construct($message, $guards);
    }
}
