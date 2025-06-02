# A pipeline for importing USA.gov static html into an LLM+VDB

## Updated summary of current state (LLM server side):
1. A LLM/VDB server configuration is included in repo
1. There is a terraform configuration for an AWS implementation of the virtual hardware
1. The server is container based, and hosts Ollama and ChromaDB services via an ngix proxy server
1. The proxy server implements several value-add features, most importantly DNS proxy to the internal Ollama and ChromaDB containers and TLS certificate management

### Example Backend Architecture
![image](../doc/images/chatbot-backend-architecture.png)

### Proxy admin UI, implementing the container routing
![image](../doc/images/proxy-admin-ui.png)
