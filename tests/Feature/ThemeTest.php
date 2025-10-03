<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

test('html has dark class when theme cookie is dark', function () {
    Config::set('app.theme_key', 'theme');

    $response = $this->withCookie('theme', 'dark')->get('/');

    $response->assertOk();
    $response->assertSee('<html', escape: false);
    $response->assertSee('class="dark"', escape: false);
});

test('html does not have dark class when theme cookie is light', function () {
    Config::set('app.theme_key', 'theme');

    $response = $this->withCookie('theme', 'light')->get('/');

    $response->assertOk();
    $response->assertSee('<html', escape: false);
    $response->assertDontSee('class="dark"', escape: false);
});

test('server shares theme view variable with default system', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('data-theme="system"', escape: false);
});
