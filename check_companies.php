<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach(\App\Models\Company::all() as $c) {
    $count = \Modules\Employees\Models\Employee::where('company_id', $c->id)->count();
    $activeContracts = \Modules\Contracts\Models\Contract::whereHas('employee', function($q) use ($c) {
        $q->where('company_id', $c->id);
    })->where('status', 'accept')->count();
    $allContracts = \Modules\Contracts\Models\Contract::whereHas('employee', function($q) use ($c) {
        $q->where('company_id', $c->id);
    })->get()->groupBy('status')->map->count();
    
    echo "Company ID: {$c->id} | Employees: {$count} | Active Contracts (accept): {$activeContracts} | All Statuses: ".json_encode($allContracts)."\n";
}
