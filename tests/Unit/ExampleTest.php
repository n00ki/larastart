<?php

declare(strict_types=1);

test('application uses the testing environment', function () {
    expect(app()->environment())->toBe('testing');
});
