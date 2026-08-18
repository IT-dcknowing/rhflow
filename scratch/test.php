<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$employees = \Modules\Employees\Models\Employee::where('employee_id', 'LIKE', '%478%')
    ->orWhere('id', 478)
    ->get();

echo "Found " . $employees->count() . " employees.\n";
foreach ($employees as $emp) {
    echo "Employee ID: " . $emp->id . " | Name: " . $emp->name . " | Matricule: " . $emp->employee_id . " | Company ID: " . $emp->company_id . "\n";
}
