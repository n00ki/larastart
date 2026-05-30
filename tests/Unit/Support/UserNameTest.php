<?php

declare(strict_types=1);

use App\Support\UserName;

test('normalizes surrounding and repeated whitespace', function () {
    expect(UserName::normalize('  Ada   Lovelace  '))->toBe('Ada Lovelace');
});

test('replaces control characters with spaces before compacting', function () {
    expect(UserName::normalize("Ada\tByron\nLovelace"))->toBe('Ada Byron Lovelace');
});

test('preserves user supplied casing and punctuation', function () {
    expect(UserName::normalize("  van   der   Waals O'Connor McDonald  "))
        ->toBe("van der Waals O'Connor McDonald");
});
