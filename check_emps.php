<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ids = [319, 320, 378];
foreach($ids as $id) {
    $e = \Modules\Employees\Models\Employee::find($id);
    if($e) {
        echo "Employee ID: {$id} | Name: {$e->name} | is_active: {$e->is_active} | Company: {$e->company_id}\n";
    } else {
        echo "Employee ID: {$id} | NOT FOUND\n";
    }
}
