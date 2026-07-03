<?php

use Laravel\Boost\BoostServiceProvider;
use Noardcode\LaravelSignhost\Providers\SignhostServiceProvider;
use Noardcode\LaravelSignhost\Workbench\App\Providers\WorkbenchServiceProvider;

return [
    WorkbenchServiceProvider::class,
    SignhostServiceProvider::class,
    BoostServiceProvider::class,
];
