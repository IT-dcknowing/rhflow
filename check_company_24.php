<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$companyId = 24;
$total = \Modules\Employees\Models\Employee::where('company_id', $companyId)->count();
$active = \Modules\Employees\Models\Employee::where('company_id', $companyId)->where('is_active', 1)->count();
$contracts = \Modules\Contracts\Models\Contract::whereHas('employee', function($q) use ($companyId) {
    $q->where('company_id', $companyId);
})->where('status', 'accept')->count();

echo "Company 24 | Total: {$total} | Active Employees: {$active} | Active Contracts: {$contracts}\n";

$without = \Modules\Employees\Models\Employee::where('company_id', $companyId)
    ->where('is_active', 1)
    ->whereDoesntHave('contracts', function($q) {
        $q->where('status', 'accept');
    })->count();
echo "Without Contract: {$without}\n";
