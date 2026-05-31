<?php

declare(strict_types=1);

use Illuminate\Log\Formatters\JsonFormatter;

test('stderr logs use laravel json formatting by default', function () {
    expect(config('logging.channels.stderr.formatter'))->toBe(JsonFormatter::class);
});
