<?php

declare(strict_types=1);

test('public disk url trims trailing slashes from the app url', function () {
    $hadEnvAppUrl = array_key_exists('APP_URL', $_ENV);
    $hadServerAppUrl = array_key_exists('APP_URL', $_SERVER);
    $previousEnvAppUrl = $_ENV['APP_URL'] ?? null;
    $previousServerAppUrl = $_SERVER['APP_URL'] ?? null;
    $previousPutenvAppUrl = getenv('APP_URL');

    $_ENV['APP_URL'] = 'https://example.test/';
    $_SERVER['APP_URL'] = 'https://example.test/';
    putenv('APP_URL=https://example.test/');

    try {
        /** @var array{disks: array{public: array{url: string}}} $filesystems */
        $filesystems = require config_path('filesystems.php');

        expect($filesystems['disks']['public']['url'])->toBe('https://example.test/storage');
    } finally {
        if ($hadEnvAppUrl) {
            $_ENV['APP_URL'] = $previousEnvAppUrl;
        } else {
            unset($_ENV['APP_URL']);
        }

        if ($hadServerAppUrl) {
            $_SERVER['APP_URL'] = $previousServerAppUrl;
        } else {
            unset($_SERVER['APP_URL']);
        }

        if ($previousPutenvAppUrl === false) {
            putenv('APP_URL');
        } else {
            putenv('APP_URL=' . $previousPutenvAppUrl);
        }
    }
});
