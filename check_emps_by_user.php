<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$userIds = [319, 320, 378];
foreach($userIds as $uid) {
    $e = \Modules\Employees\Models\Employee::where('user_id', $uid)->first();
    if($e) {
        echo "User ID: {$uid} | Employee ID: {$e->id} | Name: {$e->name} | is_active: {$e->is_active} | Company: {$e->company_id}\n";
    } else {
        echo "User ID: {$uid} | Employee NOT FOUND\n";
    }
}
