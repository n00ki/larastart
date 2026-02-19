<?php

declare(strict_types=1);

use Illuminate\Foundation\Http\FormRequest;
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

        expect($source, sprintf('Expected %s to use DB::transaction().', $actionFile->getRelativePathname()))
            ->toContain('DB::transaction(');
    }
});

test('actions avoid query builder facade operations', function () {
    $actionFiles = File::allFiles(app_path('Actions'));

    foreach ($actionFiles as $actionFile) {
        $source = $actionFile->getContents();

        expect($source, sprintf('Expected %s to avoid DB::table().', $actionFile->getRelativePathname()))
            ->not->toContain('DB::table(');
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

arch('requests are final form requests with request suffix')
    ->expect('App\Http\Requests')
    ->classes()
    ->toBeFinal()
    ->toHaveSuffix('Request')
    ->toExtend(FormRequest::class);

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
            expect($source, sprintf('Expected %s to avoid %s in controllers.', $controllerFile->getRelativePathname(), $inlineValidationMarker))
                ->not->toContain($inlineValidationMarker);
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
