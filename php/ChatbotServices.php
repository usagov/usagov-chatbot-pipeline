<?php

require_once __DIR__ . '/vendor/autoload.php';

use Codewithkyrian\ChromaDB\ChromaDB;
use Codewithkyrian\ChromaDB\Embeddings\OllamaEmbeddingFunction;
use ArdaGnsrn\Ollama\Ollama;

class ChatbotServices {
  protected $chroma;
  protected $ollama;
  protected $outPath = __DIR__ . '/../output';
  protected $inPath = __DIR__ . '/../input';
  protected $embeddingModel = 'nomic-embed-text:latest';
  protected $chatModel = 'llama3.2';
  protected $chromaHost = 'http://localhost';
  protected $chromaPort = 8000;

  protected $ollamaHost = 'http://localhost';
  protected $ollamaPort = 11434;
  protected $collectionName = 'usagovsite';
  /**
   * Constructor for ChatbotServices.
   *
   * @param bool $forceDirChecks Whether to force directory checks.
   * @param string|null $outPath Output path for text files.
   * @param string|null $inPath Input path for text files.
   * @param string|null $embeddingModel Embedding model name.
   * @param string|null $chatModel Chat model name.
   * @param string|null $chromaHost ChromaDB host URL.
   * @param int|null $chromaPort ChromaDB port number.
   * @param string|null $ollamaHost Ollama host URL.
   * @param int|null $ollamaPort Ollama port number.
   * @param string|null $collectionName Name of the ChromaDB collection.
   * @throws \Exception If the output path is not writable or the input path is not a directory.
   * @throws \Exception If the input path does not exist or is not readable.
   * @throws \Exception If the ChromaDB or Ollama client cannot be initialized.
   */
  public function __construct(
    $forceDirChecks = FALSE,
    $outPath = null,
    $inPath = null,
    $embeddingModel = null,
    $chatModel = null,
    $chromaHost = null,
    $chromaPort = null,
    $ollamaHost = null,
    $ollamaPort = null,
    $collectionName = null,
    $initConnections = TRUE
  ) {
    $this->outPath = $outPath ?? $this->outPath;
    $this->inPath = $inPath ?? $this->inPath;
    $this->embeddingModel = $embeddingModel ?? $this->embeddingModel;
    $this->chatModel = $chatModel ?? $this->chatModel;
    $this->chromaHost = $chromaHost ?? $this->chromaHost;
    $this->chromaPort = $chromaPort ?? $this->chromaPort;
    $this->ollamaHost = $ollamaHost ?? $this->ollamaHost;
    $this->ollamaPort = $ollamaPort ?? $this->ollamaPort;
    $this->collectionName = $collectionName ?? $this->collectionName;

    if ( $forceDirChecks ) {
      // Force directory checks
      if (!is_dir($this->outPath) || !is_writable($this->outPath)) {
        throw new \Exception("Output path does not exist or is not writable: " . $this->outPath);
      }
      if (!is_dir($this->inPath) || !is_readable($this->inPath)) {
        throw new \Exception("Input path does not exist or is not a directory: " . $this->inPath);
      }
    }

    // Initialize ChromaDB and Ollama clients
    if ( $initConnections)  {
      try {
        $this->chroma = ChromaDB::factory()
          ->withHost($this->chromaHost)
          ->withPort($this->chromaPort)
          ->connect();
      } catch (\Exception $e) {
        throw new \Exception("Failed to initialize ChromaDB client: " . $e->getMessage());
      }

      try {
        $this->ollama = Ollama::client($this->ollamaHost . ':' . $this->ollamaPort);
      } catch (\Exception $e) {
        throw new \Exception("Failed to initialize Ollama client: " . $e->getMessage());
      }
    }
  }

  // --- Utility methods ---
  protected function readTextFiles($path) {
    $textContents = [];
    foreach (scandir($path) as $filename) {
      if (str_ends_with($filename, '.dat')) {
        $content = file_get_contents($path . DIRECTORY_SEPARATOR . $filename);
        $textContents[$filename] = $content;
      }
    }
    return $textContents;
  }

  protected function chunkSplitter($text, $chunkSize = 100) {
    $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    $chunks = [];
    $current = [];
    foreach ($words as $word) {
      $current[] = $word;
      if (count($current) >= $chunkSize) {
        $chunks[] = implode(' ', $current);
        $current = [];
      }
    }
    if ($current) {
      $chunks[] = implode(' ', $current);
    }
    return $chunks;
  }

  /**
   * List all Ollama models.
   */
  public function getModelsInfo() {
    $models = [];
    $ollamaModels = $this->ollama->models()->list()->toArray();
    foreach ($ollamaModels as $resp) {
      foreach ($resp as $model) {
        $models[] = [
          'name' => $model['name'],
          'size' => $model['size'],
          'updated' => $model['modified_at'],
        ];
      }
    }
    return $models;
  }

  /**
   * List all ChromaDB collections.
   */
  public function getCollectionsInfo() {
    $collections = [];
    $all = $this->chroma->listCollections();
    foreach ($all as $collection) {
      $collections[] = [
        'name' => $collection->name,
        'id' => $collection->id ?? '',
      ];
    }
    return $collections;
  }

  /**
   * Ask a question using the chat pipeline.
   */
  public function askChat($query, $collectionName = null, $toJSON = FALSE) {
    $collectionName = $collectionName ?? $this->collectionName;
    $collection = $this->chroma->getCollection($collectionName);

    // Get embedding for the query
    $queryEmbed = $this->ollama->embed();
    $embedResponse = $queryEmbed->create([
      'model' => $this->embeddingModel,
      'input' => [$query],
    ])->toArray();
    $embeddings = $embedResponse['embeddings'];

    // Search for similar embeddings
    $queryResponse = $collection->query(
      queryEmbeddings: $embeddings
    );
    $relateddocs = $queryResponse->ids[0] ?? [];

    $jsonInstructions = '';
    if ($toJSON === TRUE) {
      $jsonInstructions =
        "You must format the answer as a JSON array, with the the information from each resource as an element in the array. " .
        "Do not include any explanatory text outside of the JSON array - the output should only contain the JSON array. ";
    }

    $prompt =
      "{$query}. - Answer that question using ONLY the resources provided. " .
      "If the query is not in the form of a question, prefix the query with \"Tell me about \". " .
      $jsonInstructions .
      "You must include the following information, about each resource: " .
      "agency, bureau or department name, full description, telephone number, email and URL. It is important to include all relevant details." .

      "Please avoid saying things similar to 'not enough data' and 'there is no further information'. " .
      "Do not admit ignorance of other data, even if there is more data available, outside of the resources provided. " .

      "You must keep the answer factual, and avoid superlatives or unnecessary adjectives. " .
      "Do not provide any data, or make any suggestions unless it comes from the resources provided. " .

      "The resources to use in your answer are these: " .
      implode(', ', $relateddocs) . ".";

    $completions = $this->ollama->completions()->create([
      'model' => $this->chatModel,
      'prompt' => $prompt,
    ]);

    return [
      'completions' => $completions,
      'related_docs' => $relateddocs,
    ];
  }

  /**
   * Embed all .dat files in the output directory into ChromaDB.
   */
  public function embedSite($collectionName = null, $chunkSize = 100) {
  
    $collectionName = $collectionName ?? $this->collectionName;
    $embeddingFunction = new OllamaEmbeddingFunction(
      baseUrl: $this-> ollamaHost . ':' . $this->ollamaPort,
      model: $this->embeddingModel
    );

    // Optionally delete and recreate collection
    try {
      $this->chroma->deleteCollection($collectionName);
    } catch (\Exception $e) {
      // Ignore if not exists
    }

    $collection = $this->chroma->createCollection(
      $collectionName,
      ['hnsw:space' => 'cosine'],
      $embeddingFunction
    );

    $textDocsPath = $this->outPath;
    $textData = $this->readTextFiles($textDocsPath);

    foreach ($textData as $filename => $text) {
      $chunks = $this->chunkSplitter($text, $chunkSize);
      $ids = [];
      $metadatas = [];
      foreach (array_keys($chunks) as $i) {
        $ids[] = $filename . $i;
        $metadatas[] = ['source' => $filename];
      }
      $collection->add(
        ids: $ids,
        documents: $chunks,
        metadatas: $metadatas
      );
      print "Added '$filename' to collection '$this->collectionName' with " . count($chunks) . " chunks.\n";
    }
    return TRUE;
  }

  public static function getArgs($argv,$trace=null) {
    // Load from .env file (lowest priority)
    $envFile = __DIR__ . '/.env';
    $envVars = [];
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            [$key, $value] = array_map('trim', explode('=', $line, 2) + [null, null]);
            if ($key && $value !== null) {
                $envVars[$key] = $value;
            }
        }
    }

    // Helper to get value by priority: argv > getenv > .env > default, with sanitization
    $getValue = function($shortOpt, $envName, $default, $type = null ) use ($argv, $envVars, $trace) {
        // 1. Check argv
        foreach ($argv as $arg) {
            if (str_starts_with($arg, $shortOpt . '=')) {
                $val = substr($arg, strlen($shortOpt . '='));
                if ($trace) {
                    print(json_encode(["$envName","Using value from command line: $val\n"]));
                }
                break;
            }
        }
        // 2. Check process environment
        if (!isset($val)) {
            $envVal = getenv($envName);
            if ($envVal !== false) {
                $val = $envVal;
                if ($trace) {
                    print(json_encode(["$envName","Using value from environment: $val\n"]));
                }
            }
        }
        // 3. Check .env file
        if (!isset($val) && isset($envVars[$envName])) {
            $val = $envVars[$envName];
            if ($trace) {
                print(json_encode(["$envName","Using value from .env file: $val\n"]));
            }
        }
        // 4. Default
        if (!isset($val)) {
            $val = $default;
            if ($trace) {
                print(json_encode(["$envName","Using default value: $val\n"]));
            }
        }

        // --- Sanitization ---
        if ($type === 'port') {
            // Valid TCP port: integer 1-65535
            if (!is_numeric($val) || (int)$val < 1 || (int)$val > 65535) {
                throw new \InvalidArgumentException("Invalid port for $envName: $val");
            }
            $val = (int)$val;
        } elseif ($type === 'host') {
            // Valid URL (http/https)
            if (!filter_var($val, FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException("Invalid host URL for $envName: $val");
            }
        } elseif ($type === 'collection') {
            // a-z, A-Z, 0-9, -, _, must start with letter or _
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9\-_]*$/', $val)) {
                throw new \InvalidArgumentException("Invalid collection name for $envName: $val");
            }
        }
        return $val;
    };

    $sthis = new ChatbotServices(initConnections: FALSE);
    $ollamaHost = $getValue('-oh', 'OLLAMA_HOST', $sthis->ollamaHost, 'host', $trace);
    $ollamaPort = $getValue('-op', 'OLLAMA_PORT', $sthis->ollamaPort, 'port', $trace);
    $chromaHost = $getValue('-ch', 'CHROMA_HOST', $sthis->chromaHost, 'host', $trace);
    $chromaPort = $getValue('-cp', 'CHROMA_PORT', $sthis->chromaPort, 'port', $trace);
    $collectionName = $getValue('-c', 'CHROMA_COLLECTION', $sthis->collectionName, 'collection', $trace);

    return [
        'ollamaHost' => $ollamaHost,
        'ollamaPort' => $ollamaPort,
        'chromaHost' => $chromaHost,
        'chromaPort' => $chromaPort,
        'collectionName' => $collectionName,
    ];
  }
}
