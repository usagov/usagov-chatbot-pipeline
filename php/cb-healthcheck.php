<?php

require_once __DIR__ . '/ChatbotServices.php';
//use ChatbotServices;

$cbs = new ChatbotServices();

print json_encode($cbs->getCollectionsInfo());
print json_encode($cbs->getModelsInfo());

echo "Health check completed successfully.\n";
