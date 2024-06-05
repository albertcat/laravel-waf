<?php

use Albert\Waf\Http\Middleware\WafMiddleware;
use Albert\Waf\Models\Ban;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('allows request when there is no ban in place', function () {
    $request = Request::create('/');
    $next = fn () => response('You shall pass.');

    $response = (new WafMiddleware)->handle($request, $next);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    expect($response->getContent())->toBe('You shall pass.');
});

it('blocks a request when the ip has a ban', function () {
    $ban = Ban::factory()->create();
    $request = Request::create(uri: '/', server: [
        'REMOTE_ADDR' => $ban->ip_address,
    ]);
    $next = fn () => response('You shall pass.');

    // Throws exception
    (new WafMiddleware)->handle($request, $next);
})->throws(HttpException::class);

it('allows a request when the ip has a ban but it is expired', function () {
    $ban = Ban::factory()->expired()->create();
    $request = Request::create(uri: '/', server: [
        'REMOTE_ADDR' => $ban->ip_address,
    ]);
    $next = fn () => response('You shall pass.');

    $response = (new WafMiddleware)->handle($request, $next);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
    expect($response->getContent())->toBe('You shall pass.');
});
