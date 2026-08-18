<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach(\App\Models\User::all() as $u) {
    if($u->company_id) {
        $count = \Modules\Employees\Models\Employee::where('company_id', $u->company_id)->count();
        if($count >= 30 && $count <= 40) {
            echo "User: {$u->name} (ID: {$u->id}) | Company ID: {$u->company_id} | Employees: {$count}\n";
        }
    }
}
