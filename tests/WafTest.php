<?php

use Albert\Waf\Facades\Waf;
use Albert\Waf\Models\Ban;
use Illuminate\Support\Carbon;

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
