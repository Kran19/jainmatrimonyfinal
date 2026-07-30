<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('storage:link-safe', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');

    if (!file_exists($target)) {
        @mkdir($target, 0755, true);
    }

    if (file_exists($link) || is_link($link)) {
        if (is_link($link)) {
            @unlink($link);
        } elseif (is_dir($link)) {
            $this->info("Storage link directory already exists at [$link].");
            return 0;
        }
    }

    if (@symlink($target, $link)) {
        $this->info("The [$link] link has been connected to [$target].");
    } else {
        if (!file_exists($link)) {
            @mkdir($link, 0755, true);
        }
        $this->info("symlink() disabled on host. Fallback directory created at [$link].");
    }
    return 0;
})->purpose('Create storage symlink safely without requiring exec() on shared hosting');
