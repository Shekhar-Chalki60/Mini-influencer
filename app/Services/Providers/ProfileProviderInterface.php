<?php

namespace App\Services\Providers;

interface ProfileProviderInterface
{
    public function fetch(string $username): array;
}
