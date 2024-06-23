<?php

use Albert\Waf\Facades\Waf;
use Albert\Waf\Models\Ban;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

test('an unbanned ip has no ban', function () {
    expect(Waf::ipHasBan('1.1.1.1'))->toBeFalse();
});

test('a banned ip without expiration is banned', function () {
    $ban = Ban::factory()->create([
        'banned_until' => null,
    ]);

    expect(Waf::ipHasBan($ban->ip_address))->toBeTrue();
});

test('a banned ip in the future, is banned', function () {
    $ban = Ban::factory()->create([
        'banned_until' => Carbon::tomorrow(),
    ]);

    expect(Waf::ipHasBan($ban->ip_address))->toBeTrue();
});

test('a banned ip in the past, is not banned', function () {
    $ban = Ban::factory()->create([
        'banned_until' => Carbon::yesterday(),
    ]);

    expect(Waf::ipHasBan($ban->ip_address))->toBeFalse();
});

test('same ip with multiple bans, last ban takes precedence', function () {
    $first = Ban::factory()->expired()->create([
        'ip_address' => '1.1.1.1',
    ]);
    $last = Ban::factory()->create([
        'ip_address' => '1.1.1.1',
    ]);

    expect(Waf::ipHasBan($last->ip_address))->toBeTrue();
});

it('bans an ip for a period of time', function () {
    Waf::banIpUntil('1.1.1.1', Carbon::tomorrow());

    expect(Waf::ipHasBan('1.1.1.1'))->toBeTrue();
});

it('allows request when there are no filters to apply', function () {
    config('waf.filters', []);
    $request = Request::create('/');

    expect(Waf::requestIsAllowed($request))->toBeTrue();
});

it('forbids request when there is a filter that denies it`', function () {
    Config::set('waf.filters', [
        \Albert\Waf\Tests\Fake\Filters\FalseFilter::class,
    ]);
    $request = Request::create('/');

    expect(Waf::requestIsAllowed($request))->toBeFalse();
});

it('allows request when all filtes allows it`', function () {
    Config::set('waf.filters', [
        \Albert\Waf\Tests\Fake\Filters\TrueFilter::class,
    ]);
    $request = Request::create('/');

    expect(Waf::requestIsAllowed($request))->toBeTrue();
});
