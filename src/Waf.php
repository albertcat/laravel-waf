<?php

namespace Albert\Waf;

use Albert\Waf\Models\Ban;
use Illuminate\Support\Carbon;

class Waf
{
    public function ipHasBan(string $ip): bool
    {
        if (! $ban = Ban::where('ip_address', $ip)->latest('banned_until')->first()) {
            return false;
        }

        if (! $ban->banned_until) {
            return true;
        }

        return $ban->banned_until->gt(Carbon::now());
    }
}
