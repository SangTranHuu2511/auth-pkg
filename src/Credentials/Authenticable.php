<?php

namespace Sangth\Auth\Sdk\Credentials;

interface Authenticable
{
    public function getIdentifier(): string;

    public function getCredentials(\Illuminate\Http\Request $request): array;
}
