<?php

require_once __DIR__ . '/ChatbotServices.php';
//use ChatbotServices;

$cbs = new ChatbotServices(
    forceDirChecks: TRUE,
    outPath: __DIR__ . '/../output',
    inPath: __DIR__ . '/../input',
    embeddingModel: 'nomic-embed-text:latest',
    chatModel: 'llama3.2',
    chromaHost: 'http://localhost',
    chromaPort: 8000,
    ollamaHost: 'http://localhost:11434',
    collectionName: 'usagovsite'
);

$cbs->embedSite();

echo "Embeddings collection completed successfully.\n";
