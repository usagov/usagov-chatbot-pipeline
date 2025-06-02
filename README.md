# A pipeline for importing USA.gov static html into an LLM+VDB

USAGov has created a small team to implement a proof of concept application for a USAGov-specific chatbot

This document outlines steps stand up a development environment to support the import into and queries of the USAGov html files to/from a local-only LLM+VDB

The "development environment" can also be used to create non-local (read: AWS) implementations of the LLM+VDB for better performance

The pipeline includes an nginx proxy server, useful for serving Ollama and ChromaDB over ports 80/443.  This simplifies configurations, and eliminates justifying "non-standard" ports when submitting to security reviews/audits. The proxy will also handle TLS certs, with the same configurtation and security benefits

## 1. [Proof of Concept Implementation Considerations](./doc/TechConsiderations.md)

## 2. LLM server side:
### [Backend Architecture](./doc/Architecture.md)
### [Requirements](./doc/BackendRequirements)

## 3. LLM client side:
Implementations of chunking and embedding processes are available in PHP and Python
Implementations of simple LLM query scripts which access the RAG data in the VDB are available in PHP and Python
### [PHP](./php/PHP.md)
### [Python](./python/Python.md)
