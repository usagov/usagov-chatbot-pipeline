<?php

require_once __DIR__ . '/ChatbotServices.php';
//use ChatbotServices;

$cbs = new ChatbotServices();

$cbs->embedSite();

echo "Embeddings collection completed successfully.\n";
