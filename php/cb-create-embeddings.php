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

$cbs->embedSite( collectionName: $hostArgs['collectionName'] );

echo "Embeddings collection completed successfully.\n";
