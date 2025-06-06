# PHP Utilities for USAGov Chatbot Pipeline

This folder contains PHP scripts and classes for interacting with the USAGov Chatbot pipeline, including data extraction, embedding, and chatbot querying.

## Contents

- `ChatbotServices.php`  
  Main class for embedding, querying, and managing ChromaDB and Ollama models

- `cb-site-extractor.php`  
  Extract relevant text from HTML files for embedding

- `cb-healthcheck.php`  
  Display models present in LLM, and collections present in VDB

- `cb-create-embeddings.php`  
  Import extracted text into Vector DB (Chroma)

- `cb-askchat.php`  
  Ask questions to the chatbot and get responses from a specified collection

## Improvement Ideas
1. Ability to refactor system prompt
1. Ability to refactor embedding components

## Requirements

### 1. PHP 8.2 or newer

Packages: `php-cli php-xml php-zip`

### 2. Composer and dependencies (see [composer.json](./composer.json))

### 3. Access to ChromaDB and Ollama services

You must have running ChromaDB and Ollama services running (locally or externally), and configure their URLs/Ports in [ChatbotServices.php](../php/ChatbotServices.php) as needed

The [server](../server/) folder has a [docker-compose.yml](../server/docker-compose.yml) file to get those services running locally if you do not have an external server running them

## Dev Environment (or server) Setup

1. PHP 8.2.x/8.3.x cli, xml and zip libraries
   
   There were issues noted early on, with this repo and PHP 8.4 - Development and testing were done on PHP 8.3.6

   ```sh
   cd php
   sudo apt install php-cli php-xml php-zip
   ```

2. Install composer:

   See [Composer website](https://getcomposer.org/download/)
   
3. Install php dependencies:
   ```sh
   cd php
   composer install
   ```

4. Pull USAGov static site files from github
   ```sh
   cd input
   clone https://github.com/usagov/usagov-archive-2025.git
   ```

5. Start the containers

   Note that you can shorten the startup and loading time, and save some disk space by commenting out the openweb-ui container from [docker-compose.yml](../server/docker-compose.yml), if you don't need to use it

   ```
   cd server
   
   # run in foreground:
   docker compose up
   
   # run in background:
   docker compose up -d
   ```

6. Pull the models needed:
   ```
   docker exec -t ollama ollama pull nomic-embed-text
   docker exec -t ollama ollama pull llama3.2
   ```

## Pipeline Script Usage

1. Extract data from USGov static site files

   ```
   cd php
   rm ../output/*.dat
   php cb-site-extractor.php
   
   ### let us see what is there now
   ls ../output/l*.dat
   ```
2. Chunk/embed the extracted text into the vector database.  This will take some time, depending on the hardware you are using to host the LLM+VDB (7 minutes is not unusual for my local [i7+32GB+nvmeSSD on WSL2])

   ```
   php cb-create-embeddings.php
   ```
3. Ask the Chatbot
   ```sh
   ### get the answer (as embedded plaintext), as well as some metadata about the query
   php cb-askchat.php -q="What services are available to veterans?" -c=usagovsite | jq -r .
   
   ### get the answer, and only the answer, as json
   php cb-askchat.php -q="What services are available to veterans?" -c=usagovsite -j | jq -r '.completions.response'
   
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

   ### Running scripts locally, with LLM+VDB running externally
   #### The `ChatbotServices` class constructor provides the following host/port arguments
   1. `$ollamaHost` - this defaults to `localhost:11434`
   2. `$chromaHost` - this defaults to `localhost`
   3. `$chromaPort` - this defaults to `8000`

   Note that the ollama host/port are joined into one variable.  This is due to the way the ollama classes I've used tend to want the OLLAMA_HOST env var and constructor arguments - I have not seen them separated into separate host/port variables until deep into the library code

   The scripts which access the LLM+VDB provide command line arguments to set the host/port for both ollama and chromadb:

   ```
   cb-askchat.php | cb-healthcheck.php | cb-create-embeddings.php:

   -oh=<ollama hostname or ipv4 address>   (defaults to localhost)
   -op=<ollama port number>                (defaults to 11434)
   -ch=<chromadb hostname or ipv4 address> (defaults to localhost)
   -cp=<chromadb port number>              (defaults to 8000)
   ```

   The scripts which access the VDB provide a command line argument to set the collection name.  It defaults to usagovsite.  If you happen to be sharing a remote server, please change the default collection name in the `ChatbotService` class, or use the cli argument, as shown below

   ```
   cb-askchat.php | cb-healthcheck.php | cb-create-embeddings.php:
   -c=<vector db collection name>   (defaults to `usagovsite`)
   ```
## Pipeline script example output
```
### Make a query to a remote server running our LLM+VDB+proxy containers:

❯ php cb-askchat.php -c=usagovsite2 -oh=ob.straypacket.com -op=80 -ch=cd.straypacket.com -cp=80 -q="Please tell me about all federal government auctions" -j | jq -r .completions.response
[
  {
    "name": "General Services Administration (GSA) Auctions",
    "description": "Auction for the sale of government surplus property and goods",
    "telephone number": "(202) 273-0110",
    "email": "gsc@gsa.gov",
    "URL": "https://www.gsa.gov/service/government-surplus-procedure/gsa-auctions"
  },
  {
    "name": "GovernmentAuction.com",
    "description": "Online auction site for government auctions",
    "telephone number": "(888) 382-5519",
    "email": "",
    "URL": "https://www.govauctions.net/"
  },
  {
    "name": "Ibid Auction House",
    "description": "Auction house specializing in government property auctions",
    "telephone number": "(877) 222-3542",
    "email": "",
    "URL": "https://www.ibidauctions.com/"
  },
  {
    "name": "GovernmentAuctioneers",
    "description": "Online auction site for government auctions",
    "telephone number": "(888) 382-5519",
    "email": "",
    "URL": "https://www.govauctions.net/"
  },
  {
    "name": "Real Estate Auctions Inc.",
    "description": "Auction company specializing in real estate auctions",
    "telephone number": "(800) 466-4448",
    "email": "",
    "URL": "https://www.realestateauctions.com/"
  },
  {
    "name": "US Marshals Service Auctions",
    "description": "Auction site for seized property and government auctions",
    "telephone number": "(877) 276-4828",
    "email": "",
    "URL": "https://www.usmarshals.gov/auctions"
  },
  {
    "name": "Federal Property Auctions",
    "description": "Online auction site for federal property auctions",
    "telephone number": "(888) 382-5519",
    "email": "",
    "URL": "https://www.govauctions.net/"
  }
]
```
