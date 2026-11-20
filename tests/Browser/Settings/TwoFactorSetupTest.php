<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController;
use PragmaRX\Google2FA\Google2FA;

function holdResponses(string ...$requests): string
{
    return sprintf(<<<'JS'
        () => {
            const requests = %s;
            const loads = [];
            const total = requests.length;
            const open = XMLHttpRequest.prototype.open;
            window.heldResponses = new Promise((resolve) => {
                XMLHttpRequest.prototype.open = function (method, url, ...args) {
                    open.call(this, method, url, ...args);
                    const index = requests.indexOf(`${method} ${new URL(url, location.href).pathname}`);
                    if (index === -1) return;
                    requests.splice(index, 1);
                    if (!requests.length) XMLHttpRequest.prototype.open = open;

                    const send = this.send;
                    this.send = (body) => {
                        const load = this.onload;
                        this.onload = () => {
                            loads.push(load);
                            if (loads.length === total) resolve();
                        };
                        // A finished XHR ignores abort(). Call the abort handler for the held response.
                        this.abort = () => this.onabort();
                        send.call(this, body);
                    };
                };
            });
            window.releaseResponses = () => loads.forEach((load) => load());
        }
        JS, json_encode($requests));
}

test('leaving security settings discards unfinished two-factor setup', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/settings/security')
        ->fill('password', 'password')
        ->click('@confirm-password-button')
        ->click('Enable 2FA')
        ->assertVisible('img[alt="Two-factor authentication QR code"]')
        ->assertVisible('input[readonly]')
        ->click('[data-slot="dialog-close"]')
        ->assertSee('Continue Setup')
        ->click('@settings-nav-profile')
        ->assertPathIs('/settings/profile')
        ->click('@settings-nav-security')
        ->assertSee('Enable 2FA')
        ->assertDontSee('Continue Setup')
        ->assertNoJavaScriptErrors();
});

test('reopening two-factor setup retries a setup key that failed to load', function (): void {
    $failed = false;
    Route::middleware('web')->get('/user/two-factor-secret-key', function (Request $request) use (&$failed) {
        if (! $failed) {
            $failed = true;
            abort(500);
        }

        return app(TwoFactorSecretKeyController::class)->show($request);
    });

    $this->actingAs(User::factory()->create());

    visit('/settings/security')
        ->fill('password', 'password')
        ->click('@confirm-password-button')
        ->click('Enable 2FA')
        ->assertSee('Failed to fetch a setup key')
        ->click('[data-slot="dialog-close"]')
        ->click('Enable 2FA')
        ->assertVisible('img[alt="Two-factor authentication QR code"]')
        ->assertVisible('input[readonly]')
        ->assertNoJavaScriptErrors();
});

test('re-enabling two-factor authentication shows the new recovery codes', function (): void {
    $user = User::factory()->create();
    app(EnableTwoFactorAuthentication::class)($user);
    $user->forceFill(['two_factor_confirmed_at' => now()])->save();
    $oldCode = $user->recoveryCodes()[0];

    $this->actingAs($user);

    $page = visit('/settings/security')
        ->fill('password', 'password')
        ->click('@confirm-password-button')
        ->click('View Recovery Codes')
        ->assertSee($oldCode)
        ->click('Disable 2FA')
        ->click('Enable 2FA')
        ->assertVisible('input[readonly]');

    $setupKey = $page->value('input[readonly]');

    $page->click('Continue')
        ->type('#otp', app(Google2FA::class)->getCurrentOtp($setupKey))
        ->click('Confirm')
        ->click('View Recovery Codes')
        ->assertSee($user->refresh()->recoveryCodes()[0])
        ->assertDontSee($oldCode)
        ->assertNoJavaScriptErrors();
});

test('reopening recovery codes retries regenerated codes that failed to load', function (): void {
    $fetches = 0;
    Route::middleware('web')->get('/user/two-factor-recovery-codes', function (Request $request) use (&$fetches) {
        if (++$fetches === 2) {
            abort(500);
        }

        return app(RecoveryCodeController::class)->index($request);
    });

    $user = User::factory()->create();
    app(EnableTwoFactorAuthentication::class)($user);
    $user->forceFill(['two_factor_confirmed_at' => now()])->save();
    $oldCode = $user->recoveryCodes()[0];

    $this->actingAs($user);

    $page = visit('/settings/security')
        ->fill('password', 'password')
        ->click('@confirm-password-button')
        ->click('View Recovery Codes')
        ->assertSee($oldCode)
        ->click('Regenerate Codes')
        ->assertSee('Failed to fetch recovery codes')
        ->assertDontSee($oldCode);

    $newCodes = $user->refresh()->recoveryCodes();

    $page->click('Hide Recovery Codes')
        ->click('View Recovery Codes')
        ->assertSee($newCodes[0])
        ->assertDontSee('Failed to fetch recovery codes')
        ->assertNoJavaScriptErrors();

    expect($user->refresh()->recoveryCodes())->toBe($newCodes);
});

test('confirming two-factor setup discards setup data that loads late', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $page = visit('/settings/security')
        ->fill('password', 'password')
        ->click('@confirm-password-button');

    $page->script(holdResponses('GET /user/two-factor-qr-code', 'GET /user/two-factor-secret-key'));
    $page->click('Enable 2FA')->script('() => window.heldResponses');

    $page->click('Continue')
        ->type('#otp', app(Google2FA::class)->getCurrentOtp(decrypt($user->refresh()->two_factor_secret)))
        ->click('Confirm')
        ->assertSee('View Recovery Codes')
        ->script('() => window.releaseResponses()');

    $page->click('Disable 2FA')
        ->click('Enable 2FA')
        ->assertVisible('img[alt="Two-factor authentication QR code"]')
        ->assertVisible('input[readonly]')
        ->assertNoJavaScriptErrors();

    $user->refresh();
    $qrCodeSvg = $page->script('() => {
        const src = document.querySelector("img[alt^=Two-factor]").getAttribute("src");
        return decodeURIComponent(src.slice(src.indexOf(",") + 1));
    }');

    expect($qrCodeSvg)->toBe($user->twoFactorQrCodeSvg())
        ->and($page->value('input[readonly]'))->toBe(decrypt($user->two_factor_secret));
});

test('two-factor confirmation finishes after the user leaves the confirmation step', function (string $leave): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/settings/security')
        ->fill('password', 'password')
        ->click('@confirm-password-button')
        ->click('Enable 2FA')
        ->assertVisible('input[readonly]');

    $setupKey = $page->value('input[readonly]');

    $page->script(holdResponses('POST /user/confirmed-two-factor-authentication'));
    $page->click('Continue')
        ->type('#otp', app(Google2FA::class)->getCurrentOtp($setupKey))
        ->click('Confirm')
        ->script('() => window.heldResponses');

    $page->click($leave)->script('() => window.releaseResponses()');

    $page->assertSee('View Recovery Codes')
        ->click('Disable 2FA')
        ->assertSee('Enable 2FA')
        ->assertDontSee('Continue Setup')
        ->assertNoJavaScriptErrors();
})->with([
    'back' => 'Back',
    'close' => '[data-slot="dialog-close"]',
]);

test('re-entering two-factor confirmation waits for the pending code check', function (): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/settings/security')
        ->fill('password', 'password')
        ->click('@confirm-password-button')
        ->click('Enable 2FA')
        ->assertVisible('input[readonly]');

    $setupKey = $page->value('input[readonly]');

    $page->script(holdResponses('POST /user/confirmed-two-factor-authentication'));
    $page->click('Continue')
        ->type('#otp', 'ABCDEF')
        ->click('Confirm')
        ->script('() => window.heldResponses');

    $page->click('Back')
        ->click('Continue')
        ->assertDisabled('#otp')
        ->assertButtonDisabled('Confirm')
        ->script('() => window.releaseResponses()');

    $page->assertSee('The provided two factor authentication code was invalid.')
        ->type('#otp', app(Google2FA::class)->getCurrentOtp($setupKey))
        ->click('Confirm')
        ->assertSee('View Recovery Codes')
        ->assertNoJavaScriptErrors();
});

test('closing two-factor setup during a pending confirmation keeps the setup resumable', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $page = visit('/settings/security')
        ->fill('password', 'password')
        ->click('@confirm-password-button');

    $page->script(holdResponses('GET /user/two-factor-secret-key', 'POST /user/confirmed-two-factor-authentication'));
    $page->click('Enable 2FA')
        ->assertVisible('img[alt="Two-factor authentication QR code"]')
        ->click('Continue')
        ->type('#otp', app(Google2FA::class)->getCurrentOtp(decrypt($user->refresh()->two_factor_secret)))
        ->click('Confirm')
        ->script('() => window.heldResponses');

    $page->click('[data-slot="dialog-close"]')
        ->assertDontSee('Enable 2FA')
        ->click('Continue Setup')
        ->assertVisible('img[alt="Two-factor authentication QR code"]')
        ->script('() => window.releaseResponses()');

    $page->assertSee('View Recovery Codes')
        ->assertNoJavaScriptErrors();
});

test('an invalid two-factor code stays visible after closing and reopening setup', function (bool $releaseBeforeClose): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/settings/security')
        ->fill('password', 'password')
        ->click('@confirm-password-button')
        ->click('Enable 2FA')
        ->assertVisible('input[readonly]');

    $page->script(holdResponses('POST /user/confirmed-two-factor-authentication'));
    $page->click('Continue')
        ->type('#otp', 'ABCDEF')
        ->click('Confirm')
        ->script('() => window.heldResponses');

    if ($releaseBeforeClose) {
        $page->script('() => window.releaseResponses()');
        $page->assertSee('The provided two factor authentication code was invalid.')
            ->click('[data-slot="dialog-close"]');
    } else {
        $page->click('[data-slot="dialog-close"]')
            ->script('() => window.releaseResponses()');
    }

    $page->click('Continue Setup')
        ->click('Continue')
        ->assertSee('The provided two factor authentication code was invalid.')
        ->assertNoJavaScriptErrors();

    expect($page->value('#otp'))->toBe('');
})->with([
    'response before close' => true,
    'response after close' => false,
]);

test('setup stays resumable after an invalid code while the setup key is loading', function (): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/settings/security')
        ->fill('password', 'password')
        ->click('@confirm-password-button');

    $page->script(holdResponses('GET /user/two-factor-secret-key'));
    $page->click('Enable 2FA')
        ->assertVisible('img[alt="Two-factor authentication QR code"]')
        ->script('() => window.heldResponses');

    $page->script(holdResponses('POST /user/confirmed-two-factor-authentication'));
    $page->click('Continue')
        ->type('#otp', 'ABCDEF')
        ->click('Confirm')
        ->script('() => window.heldResponses');

    $page->click('[data-slot="dialog-close"]')
        ->script('() => new Promise((resolve) => {
            document.addEventListener("inertia:finish", () => resolve(), { once: true });
            window.releaseResponses();
        })');

    $page->assertDontSee('Enable 2FA')
        ->click('Continue Setup')
        ->click('Continue')
        ->assertSee('The provided two factor authentication code was invalid.')
        ->assertNoJavaScriptErrors();

    expect($page->value('#otp'))->toBe('');
});

test('hiding recovery codes during regeneration still shows the new codes', function (): void {
    $user = User::factory()->create();
    app(EnableTwoFactorAuthentication::class)($user);
    $user->forceFill(['two_factor_confirmed_at' => now()])->save();
    $oldCode = $user->recoveryCodes()[0];

    $this->actingAs($user);

    $page = visit('/settings/security')
        ->fill('password', 'password')
        ->click('@confirm-password-button')
        ->click('View Recovery Codes')
        ->assertSee($oldCode);

    $page->script(holdResponses('POST /user/two-factor-recovery-codes'));
    $page->click('Regenerate Codes')->script('() => window.heldResponses');

    $page->click('Hide Recovery Codes')
        ->click('View Recovery Codes')
        ->assertButtonDisabled('Regenerate Codes')
        ->script('() => window.releaseResponses()');

    $page->assertSee($user->refresh()->recoveryCodes()[0])
        ->assertDontSee($oldCode)
        ->assertNoJavaScriptErrors();
});
