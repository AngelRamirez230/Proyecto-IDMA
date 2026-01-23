<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Inspire command (default)
|--------------------------------------------------------------------------
*/
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduler de Becas
|--------------------------------------------------------------------------
*/
Schedule::command('becas:finalizar')->dailyAt('00:00');


/*
|--------------------------------------------------------------------------
| Scheduler de plan de pagos
|--------------------------------------------------------------------------
*/
Schedule::command('plan:finalizar')->dailyAt('00:01');

