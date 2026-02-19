<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

test('html has dark class when theme cookie is dark', function () {
    Config::set('app.theme_key', 'theme');

    $response = $this->withUnencryptedCookie('theme', 'dark')->get('/');

    $response->assertOk();
    $response->assertSee('<html', escape: false);
    $response->assertSee('class="dark"', escape: false);
});

test('html does not have dark class when theme cookie is light', function () {
    Config::set('app.theme_key', 'theme');

    $response = $this->withUnencryptedCookie('theme', 'light')->get('/');

    $response->assertOk();
    $response->assertSee('<html', escape: false);
    $response->assertDontSee('class="dark"', escape: false);
});

test('server shares theme view variable with default system', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('data-theme="system"', escape: false);
});

test('html has dark class when theme cookie is system and prefers dark', function () {
    Config::set('app.theme_key', 'theme');

    $response = $this->withUnencryptedCookie('theme', 'system')->get('/');

    $response->assertOk();
    $response->assertSee('data-theme="system"', escape: false);
});

test('server shares dark theme value from cookie', function () {
    Config::set('app.theme_key', 'theme');

    $response = $this->withUnencryptedCookie('theme', 'dark')->get('/');

    $response->assertOk();
    $response->assertSee('data-theme="dark"', escape: false);
});

test('theme cookie is readable as plaintext for pre-paint script', function () {
    Config::set('app.theme_key', 'theme');

    $response = $this->withUnencryptedCookie('theme', 'dark')->get('/');

    $response->assertOk();
    $response->assertSee('class="dark"', escape: false);
});
