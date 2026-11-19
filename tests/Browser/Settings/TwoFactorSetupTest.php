<?php

declare(strict_types=1);

use App\Models\User;

test('leaving security settings cancels pending two-factor setup', function (): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/settings/security')
        ->fill('password', 'password')
        ->click('@confirm-password-button')
        ->assertSee('Enable 2FA');

    $page->script(<<<'JS'
        () => {
            const paths = ['/user/two-factor-qr-code', '/user/two-factor-secret-key'];
            window.pendingSetup = new Set();
            const open = XMLHttpRequest.prototype.open;
            XMLHttpRequest.prototype.open = function (method, url, ...args) {
                open.call(this, method, url, ...args);
                const path = new URL(url, location.href).pathname;
                if (!paths.includes(path)) return;

                this.send = () => {
                    window.pendingSetup.add(path);
                    if (window.pendingSetup.size === paths.length) XMLHttpRequest.prototype.open = open;
                };
                this.abort = () => {
                    window.pendingSetup.delete(path);
                    this.dispatchEvent(new Event('abort'));
                };
            };
        }
        JS);

    $page->click('Enable 2FA')
        ->assertScript('window.pendingSetup.size', 2)
        ->click('[data-slot="dialog-close"]')
        ->click('@settings-nav-profile')
        ->assertPathIs('/settings/profile')
        ->assertScript('window.pendingSetup.size', 0)
        ->click('@settings-nav-security')
        ->assertSee('Enable 2FA')
        ->assertDontSee('Continue Setup')
        ->click('Enable 2FA')
        ->assertVisible('input[readonly]')
        ->assertVisible('img[alt="Two-factor authentication QR code"]')
        ->assertNoJavaScriptErrors();
});
