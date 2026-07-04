<?php

declare(strict_types=1);

use Laravel\Fortify\Features;

test('well-known passkey endpoints advertise the security settings page', function () {
    expect(Features::enabled(Features::passkeys()))->toBeTrue();

    $this->getJson('/.well-known/passkey-endpoints')
        ->assertSuccessful()
        ->assertExactJson([
            'enroll' => route('settings.security.edit'),
            'manage' => route('settings.security.edit'),
        ]);
});
