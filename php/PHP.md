# PHP Utilities for USAGov Chatbot Pipeline

This folder contains PHP scripts and classes for interacting with the USAGov Chatbot pipeline, including data extraction, embedding, and chatbot querying.

## Contents

- `ChatbotServices.php`  
  Main class for embedding, querying, and managing ChromaDB and Ollama models.

- `cb-askchat.php`  
  Command-line tool to ask questions to the chatbot and get responses from a specified collection.

- `extract-html-text.php`  
  Extracts relevant text from HTML files for embedding.

## Requirements

- PHP 8.1 or newer
- Composer dependencies (see `composer.json`)
- Access to ChromaDB and Ollama services

## Setup

1. Requires PHP 8.2.x CLI and php-xml libraries

2. Install dependencies:
   ```sh
   composer install
   ```

3. Configure input and output directories as needed in the scripts.

## Usage

### Ask the Chatbot

```sh
php cb-askchat.php -q="What services are available to veterans?" -c=usagovsite -j
```

**Options:**
- `-q=QUESTION` — The question to ask the chatbot (required)
- `-c=COLLECTION` — The ChromaDB collection to use (optional, default: `usagovsite`)
- `-j` — Output response as JSON array (optional)
- `-h`, `--help` — Show usage/help

To extract just the chatbot's response using `jq`:
```sh
php cb-askchat.php -q="..." | jq '.response'
```

### Extract HTML Text

```sh
php cb-site-extractor.php
```
Extracts relevant text from HTML files in the input directory and writes `.dat` files to the output directory.

### Embedding Data

Embedding is handled via methods in `ChatbotServices.php`. See the class for details.

---

Note:
You must have running ChromaDB and Ollama services running (locally or externally), and configure their URLs/Ports in [ChatbotServices.php](../php/ChatbotServices.php) as needed.

The [server](../server/) folder has a [docker-compose.yml](../server/docker-compose.yml) file to get those services running locally if you do not have an external server running them.
