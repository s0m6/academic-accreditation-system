<?php

test('pdf browsershot temp path configuration has fallback to storage path', function () {
    $tempPath = config('laravel-pdf.browsershot.temp_path');

    expect($tempPath)->toBe(storage_path('app/pdf-tmp'));
});
