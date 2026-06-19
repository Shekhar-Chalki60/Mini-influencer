<?php

namespace App\Services\Locking;

use Illuminate\Support\Facades\DB;

class AdvisoryLockService
{
    public function acquire(int $profileId): bool
    {
        $result = DB::selectOne('SELECT pg_try_advisory_lock(?) AS locked', [$profileId]);
        return (bool) $result->locked;
    }

    public function release(int $profileId): void
    {
        DB::selectOne('SELECT pg_advisory_unlock(?)', [$profileId]);
    }
}
