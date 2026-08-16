<?php

declare(strict_types=1);

use App\Http\Requests\FormRequest as AppFormRequest;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

arch('app uses strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('app avoids debugging helpers')
    ->expect('App')
    ->not->toUse(['dd', 'dump', 'ray', 'var_dump']);

arch('actions are final readonly classes with handle')
    ->expect('App\Actions')
    ->classes()
    ->toBeFinal()
    ->toBeReadonly()
    ->toHaveMethod('handle');

test('db-mutating actions run writes in a database transaction', function () {
    $mutationMarkers = [
        '->create(',
        '->update(',
        '->delete(',
        '->save(',
        '::query()->update(',
        '::query()->delete(',
    ];

    $actionFiles = File::allFiles(app_path('Actions'));

    foreach ($actionFiles as $actionFile) {
        $source = $actionFile->getContents();

        if (! Str::contains($source, $mutationMarkers)) {
            continue;
        }

        expect($source)->toContain('DB::transaction(');
    }
});

test('actions avoid query builder facade operations', function () {
    $actionFiles = File::allFiles(app_path('Actions'));

    foreach ($actionFiles as $actionFile) {
        $source = $actionFile->getContents();

        expect($source)->not->toContain('DB::table(');
    }
});

arch('models are final classes')
    ->expect('App\Models')
    ->classes()
    ->toBeFinal();

arch('concerns are traits')
    ->expect('App\Concerns')
    ->toBeTraits();

arch('controllers have controller suffix')
    ->expect('App\Http\Controllers')
    ->classes()
    ->toHaveSuffix('Controller');

arch('controllers are final and extend nothing')
    ->expect('App\Http\Controllers')
    ->classes()
    ->toBeFinal()
    ->toExtendNothing();

arch('base request extends Laravel form request')
    ->expect(AppFormRequest::class)
    ->toBeAbstract()
    ->toExtend(BaseFormRequest::class);

test('concrete requests are final app form requests with request suffix', function () {
    $requests = collect(File::allFiles(app_path('Http/Requests')))
        ->map(fn (SplFileInfo $file): string => 'App\\Http\\Requests\\' . str_replace(
            ['/', '.php'],
            ['\\', ''],
            $file->getRelativePathname(),
        ))
        ->reject(fn (string $request): bool => $request === AppFormRequest::class);

    foreach ($requests as $request) {
        if (! class_exists($request)) {
            $this->fail("Expected [{$request}] to exist.");
        }

        $reflection = new ReflectionClass($request);

        expect($reflection->isFinal())->toBeTrue()
            ->and(Str::endsWith($request, 'Request'))->toBeTrue()
            ->and($reflection->isSubclassOf(AppFormRequest::class))->toBeTrue();
    }
});

arch('controllers avoid db facade usage')
    ->expect('App\Http\Controllers')
    ->not->toUse(DB::class);

test('controllers use form requests instead of inline validation', function () {
    $inlineValidationMarkers = [
        '->validate(',
        '->validateWithBag(',
        'Validator::make(',
        'Validator::validate(',
    ];

    $controllerFiles = File::allFiles(app_path('Http/Controllers'));

    foreach ($controllerFiles as $controllerFile) {
        $source = $controllerFile->getContents();

        foreach ($inlineValidationMarkers as $inlineValidationMarker) {
            expect($source)->not->toContain($inlineValidationMarker);
        }
    }
});

test('test files use test suffix', function () {
    $filesWithoutTestSuffix = collect(File::allFiles(base_path('tests')))
        ->reject(fn (SplFileInfo $file): bool => in_array($file->getFilename(), ['Pest.php', 'TestCase.php'], true))
        ->reject(fn (SplFileInfo $file): bool => Str::endsWith($file->getFilename(), 'Test.php'))
        ->map(fn (SplFileInfo $file): string => $file->getRelativePathname())
        ->values()
        ->all();

    expect($filesWithoutTestSuffix)->toBeEmpty();
});
