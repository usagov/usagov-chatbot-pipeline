# A pipeline for importing USA.gov static html into an LLM+VDB

USAGov has created a small team to implement a proof of concept application for a USAGov-specific chatbot

This document outlines steps stand up a development environment to support the import into and queries of the USAGov html files to/from a local-only LLM+VDB

The "development environment" can also be used to create non-local (read: AWS) implementations of the LLM+VDB for better performance.

## 1. Implementation Considerations
### [Technical Info](./doc/TechConsiderations.md)

## 2. LLM server side:
### [Backend Architecture](./doc/Architecture.md)
### [Requirements](./doc/BackendRequirements)

## 3. LLM client side:
Implementations of chunking and embedding processes are available in PHP and Python
Implementations of simple LLM query scripts which access the RAG data in the VDB are available in PHP and Python
### [PHP](./php/PHP.md)
### [Python](./python/Python.md)
