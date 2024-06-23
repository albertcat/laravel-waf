<?php

use Albert\Waf\Filters\ResourceEnumerationFilter;
use Illuminate\Http\Request;

it('return false when a request has a forbidden url', function () {
    $request = Request::create('/.env');

    expect((new ResourceEnumerationFilter)->requestIsValid($request))
        ->toBeFalse();
});

it('return true when a request is valid', function () {
    $request = Request::create('/');

    expect((new ResourceEnumerationFilter)->requestIsValid($request))
        ->toBeTrue();
});
