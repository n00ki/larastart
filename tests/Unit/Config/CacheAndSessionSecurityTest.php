<?php

declare(strict_types=1);

test('cache blocks unserializing php objects by default', function () {
    expect(config('cache.serializable_classes'))->toBeFalse();
});

test('session data uses json serialization', function () {
    expect(config('session.serialization'))->toBe('json');
});
