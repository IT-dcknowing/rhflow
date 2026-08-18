<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $chatbot = app(\Modules\Chatbot\Services\PayrollChatbot::class);
    $reflection = new \ReflectionClass($chatbot);
    $method = $reflection->getMethod('getAvailableTools');
    $method->setAccessible(true);
    $tools = $method->invoke($chatbot);
    
    $ai = app(\Modules\Chatbot\Services\AnthropicService::class);
    $history = [['role'=>'user','content'=>'Bonjour']];
    $res = $ai->chat($history, $tools);
    echo "SUCCESS:\n";
    echo json_encode($res, JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo 'Error: '.$e->getMessage();
}
