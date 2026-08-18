<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $user = \App\Models\User::whereNotNull('company_id')->first();
    echo "Testing with user: ".$user->name." (ID: ".$user->id.", Company: ".$user->company_id.")\n";
    
    $chatbot = app(\Modules\Chatbot\Services\PayrollChatbot::class);
    $history = [];
    $res = $chatbot->processStatelessChat($history, 'Bonjour, fais un audit svp', $user);
    
    echo "SUCCESS:\n";
    echo json_encode($res, JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo 'Error: '.$e->getMessage();
}
