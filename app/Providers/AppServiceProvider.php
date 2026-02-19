<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Sleep;
use Illuminate\Validation\Rules\Password;
use Override;

final class AppServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void {}

    public function boot(): void
    {
        $this->configureDateDefaults();
        $this->configureModelDefaults();
        $this->configurePasswordDefaults();
        $this->configureUrlDefaults();
        $this->configureViteDefaults();
        $this->configureTestingDefaults();
    }

    private function configureDateDefaults(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureModelDefaults(): void
    {
        Model::shouldBeStrict();
        Model::automaticallyEagerLoadRelationships();
    }

    private function configurePasswordDefaults(): void
    {
        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    private function configureUrlDefaults(): void
    {
        if (app()->isProduction()) {
            URL::forceHttps();
        }
    }

    private function configureViteDefaults(): void
    {
        Vite::useAggressivePrefetching();
    }

    private function configureTestingDefaults(): void
    {
        if (app()->runningUnitTests()) {
            Sleep::fake();
        }
    }
}
