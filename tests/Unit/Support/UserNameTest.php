<?php

declare(strict_types=1);

use App\Support\UserName;

test('normalizes surrounding and repeated whitespace', function () {
    expect(UserName::normalize('  Ada   Lovelace  '))->toBe('Ada Lovelace');
});

test('replaces control characters with spaces before compacting', function () {
    expect(UserName::normalize("ada\tBYRON\nlovelace"))->toBe('Ada Byron Lovelace');
});

test('title-cases names across common separators', function () {
    expect(UserName::normalize('  nOam   ShemESh  '))->toBe('Noam Shemesh')
        ->and(UserName::normalize("anne-marie o'connor"))->toBe("Anne-Marie O'Connor");
});
