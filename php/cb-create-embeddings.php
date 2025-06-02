<?php

require_once __DIR__ . '/ChatbotServices.php';
//use ChatbotServices;

foreach ($argv as $arg) {
    if (str_starts_with($arg, '-oh=')) {
        $ollamaHost = substr($arg, strlen('-oh='));
    } elseif (str_starts_with($arg, '-op=')) {
        $ollamaPort = substr($arg, strlen('-op='));
    } elseif (str_starts_with($arg, '-ch=')) {
        $chromaHost = substr($arg, strlen('-ch='));
    } elseif (str_starts_with($arg, '-cp=')) {
        $chromaPort = substr($arg, strlen('-cp='));
    } elseif (str_starts_with($arg, '-c=')) {
        $collectionName = substr($arg, strlen('-c='));
    }
}

$chromaHost ??= 'localhost';
$chromaPort ??= '8000';
$ollamaHost ??= 'localhost';
$ollamaPort ??= '11434';
$collectionName ??= 'usagovsite';

$cbs = new ChatbotServices(
    collectionName: $collectionName,
    chromaHost: $chromaHost,
    chromaPort: $chromaPort,
    ollamaHost: "$ollamaHost:$ollamaPort"
);

$cbs->embedSite( collectionName: $collectionName );

echo "Embeddings collection completed successfully.\n";
