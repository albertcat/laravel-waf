<?php

namespace Albert\Waf\Tests\Fake\Filters;

use Albert\Waf\Contracts\ValidatesRequest;
use Illuminate\Http\Request;

class TrueFilter implements ValidatesRequest
{
    public function requestIsValid(Request $request): bool
    {
        return true;
    }
}
