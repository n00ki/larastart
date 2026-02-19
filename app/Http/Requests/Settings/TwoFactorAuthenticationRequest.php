<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;

final class TwoFactorAuthenticationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Features::enabled(Features::twoFactorAuthentication());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Safely validates two-factor state when strict model mode is enabled.
     */
    public function ensureStateIsValid(): void
    {
        if (! Fortify::confirmsTwoFactorAuthentication()) {
            return;
        }

        // Reload user attributes to avoid strict-model missing attribute errors.
        $this->user()->refresh();

        $currentTime = time();

        if (! $this->user()->hasEnabledTwoFactorAuthentication()) {
            $this->session()->put('two_factor_empty_at', $currentTime);
        }

        if ($this->hasJustBegunConfirmingTwoFactorAuthentication()) {
            $this->session()->put('two_factor_confirming_at', $currentTime);
        }

        if ($this->neverFinishedConfirmingTwoFactorAuthentication($currentTime)) {
            resolve(DisableTwoFactorAuthentication::class)($this->user());

            $this->session()->put('two_factor_empty_at', $currentTime);
            $this->session()->remove('two_factor_confirming_at');
        }
    }

    private function hasJustBegunConfirmingTwoFactorAuthentication(): bool
    {
        $attributes = $this->user()->getAttributes();
        $secret = $attributes['two_factor_secret'] ?? null;
        $confirmedAt = $attributes['two_factor_confirmed_at'] ?? null;

        return ! is_null($secret) &&
            is_null($confirmedAt) &&
            $this->session()->has('two_factor_empty_at') &&
            is_null($this->session()->get('two_factor_confirming_at'));
    }

    private function neverFinishedConfirmingTwoFactorAuthentication(int $currentTime): bool
    {
        $attributes = $this->user()->getAttributes();
        $confirmedAt = $attributes['two_factor_confirmed_at'] ?? null;

        return ! $this->session()->hasOldInput('code') &&
            is_null($confirmedAt) &&
            $this->session()->get('two_factor_confirming_at', 0) !== $currentTime;
    }
}
