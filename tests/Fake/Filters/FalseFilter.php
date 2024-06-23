<?php

namespace Albert\Waf\Tests\Fake\Filters;

use Albert\Waf\Contracts\ValidatesRequest;
use Illuminate\Http\Request;

class FalseFilter implements ValidatesRequest
{
    public function requestIsValid(Request $request): bool
    {
        return false;
    }
}
