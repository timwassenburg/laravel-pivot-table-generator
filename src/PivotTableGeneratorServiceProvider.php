<?php

namespace TimWassenburg\PivotTableGenerator;

use Illuminate\Support\ServiceProvider;
use TimWassenburg\PivotTableGenerator\Console\MakePivotCommand;

class PivotTableGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([MakePivotCommand::class]);
        }
    }
}
