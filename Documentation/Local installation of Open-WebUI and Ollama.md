# Installing openweb-ui and ollama locally

## Tested on Win10/WSL2 (32gb ram) and MacOS (16gb ram)

### Rationale

There is a need to have a local-only LLM environment to experiment in a manner which prevents possibly private or proprietary data from migrating into publicly-hosted models.  Openweb-ui and Ollama should accomplish this is a manner which allows safe, local use of models small enough to use on a typical developer's workstation.

### Installation

#### Updated method:

```
docker compose up ### :)

docker exec -it ollama-backend bash
ollama pull llama3.2
```

See [docker-compose.yml](docker-compose.yml)

#### Original Method Used
Docker was used to host these components as containers.  Docker was installed natively on WSL2, and Docker Desktop was used on MacOS.

```
git clone git@github.com:open-webui/open-webui.git
cd open-webui
./run-compose.sh
docker exec -it ollama bash
ollama pull llama3.2
```

1. This will set up a web server on localhost:3000
1. It takes maybe 4 minutes, after the script completes, for the web UI to become active.
1. First login should prompt you to set up an account - this is local-only

### Setting up Retrieval-augmented Generation (WIP)

In order to add specific domain data to the model, it is possible to add data (e.g. documents) to the model.   This is called RAG or Retrieval-augmented Generation.  

The first test of the above installed AI was to ask it how to implement Retrieval-augmented Generation on my local llama3.2 model.   It was very helpful, and provided some information and coding examples.
