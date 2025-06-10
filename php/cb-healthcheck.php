<?php

require_once __DIR__ . '/ChatbotServices.php';
//use ChatbotServices;

$hostArgs = ChatbotServices::getArgs($argv);

$cbs = new ChatbotServices(
    collectionName: $hostArgs['collectionName'],
    chromaHost: $hostArgs['chromaHost'],
    chromaPort: $hostArgs['chromaPort'],
    ollamaHost: $hostArgs['ollamaHost'],
    ollamaPort: $hostArgs['ollamaPort']
);

$ci = [ "collections" => [] ];
$mi = [ "models" => [] ];
$status = ["status" => "Health check completed successfully"];

try {
    $ci = [ "collections" => $cbs->getCollectionsInfo() ];
    //throw new Exception("This is a test exception to demonstrate error handling in health check.");
    $mi = [ "models" => $cbs->getModelsInfo() ];
} catch (Exception $e) {
    $status = ["status" => "Health check failed", "message" => $e->getMessage()];
}

print json_encode(array_merge($status, $ci, $mi));
