<?php

require_once __DIR__ . '/ChatbotServices.php';
//use ChatbotServices;

$toJSON = false;

// Usage/help message
function print_usage() {
    echo "Usage: php cb-askchat.php [options]\n\n";
    echo "Ask a question to the chatbot and get a response from the specified collection.\n\n";
    echo "The output will be a JSON object containing the both the response to the question (-q=...) ";
    echo "from the chatbot, as well as a list of vector DB items used, and stats about the query.\n\n";
    echo "By using the -j option, the response field will be formatted as a JSON array, otherwise it will be a string.\n\n";
    echo "Options:\n";
    echo "  -q=QUESTION      Specify the question to ask the chatbot\n";
    echo "  -c=COLLECTION    Specify the collection name\n";
    echo "  -j               Output response as JSON\n";
    echo "  -h, --help       Show this help message\n\n";
}

// Show usage if no arguments or help requested
if ($argc < 2) {
    print_usage();
    exit(1);
}

foreach ($argv as $arg) {
    if (str_starts_with($arg, '-q=')) {
        $question = substr($arg, strlen('-q='));
    } elseif (str_starts_with($arg, '-c=')) {
        $collectionName = substr($arg, strlen('-c='));
    } elseif (($arg === '-j')) {
        $toJSON = true;
    } elseif (str_starts_with($arg, '-oh=')) {
        $ollamaHost = substr($arg, strlen('-oh='));
    } elseif (str_starts_with($arg, '-op=')) {
        $ollamaPort = substr($arg, strlen('-op='));
    } elseif (str_starts_with($arg, '-ch=')) {
        $chromaHost = substr($arg, strlen('-ch='));
    } elseif (str_starts_with($arg, '-cp=')) {
        $chromaPort = substr($arg, strlen('-cp='));
    } elseif (($arg === '-h') || ($arg === '--help')) {
        $helpRequested = TRUE;
        print_usage();
        exit(0);
    }
}

if ($question === null) {
    echo "Error: Question is required. Use -q=QUESTION to specify the question.\n";
    print_usage();
    exit(1);
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

$response = $cbs->askChat(
    query:$question,
    collectionName:$collectionName,
    toJSON:$toJSON
);

print json_encode($response);
