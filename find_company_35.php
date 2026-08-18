<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach(\App\Models\Company::all() as $c) {
    $count = \Modules\Employees\Models\Employee::where('company_id', $c->id)->count();
    $activeCount = \Modules\Employees\Models\Employee::where('company_id', $c->id)->where('is_active', 1)->count();
    if($count == 35 || $activeCount == 35) {
        echo "FOUND Company ID: {$c->id} | Total: {$count} | Active: {$activeCount}\n";
    }
}
