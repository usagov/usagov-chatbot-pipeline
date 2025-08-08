# pylint: disable=missing-module-docstring, missing-function-docstring, invalid-name, wrong-import-position, line-too-long

# these three lines swap the stdlib sqlite3 lib with the pysqlite3 package
__import__('pysqlite3')
import sys
sys.modules['sqlite3'] = sys.modules.pop('pysqlite3')

import os
import re
from ollama import embed
import chromadb
from chromadb.utils.embedding_functions import OllamaEmbeddingFunction 

def readtextfiles(path):
    text_contents = {}
    directory = os.path.join(path)

    for local_filename in os.listdir(directory):
        if local_filename.endswith(".dat"):
            file_path = os.path.join(directory, local_filename)

            with open(file_path, "r", encoding="utf-8") as file:
                content = file.read()

            text_contents[local_filename] = content

    return text_contents


def chunksplitter(local_text, chunk_size=100):
    words = re.findall(r'\S+', local_text)

    local_chunks = []
    current_chunk = []
    word_count = 0

    for word in words:
        current_chunk.append(word)
        word_count += 1

        if word_count >= chunk_size:
            local_chunks.append(' '.join(current_chunk))
            current_chunk = []
            word_count = 0

    if current_chunk:
        local_chunks.append(' '.join(current_chunk))
    return local_chunks


def getembedding(local_chunks):
    ollama_ef = OllamaEmbeddingFunction("https://ob.straypacket.com", "nomic-embed-text")
    texts = local_chunks
    embeddings = ollama_ef(texts)
    return embeddings

ollama_host = os.environ.get("OLLAMA_HOST")
if ollama_host != "https://ob.straypacket.com":
    print("\n")
    print("OLLAMA_HOST not set. Please export OLLAMA_HOST='https://ob.straypacket.com'")
    print("\n")
    print("---")
    sys.exit()

chroma_host = os.environ.get("CHROMA_HOST", "cd.straypacket.com")
chroma_port = os.environ.get("CHROMA_PORT", "443")
chroma_ssl  = os.environ.get("CHROMA_SSL", True)
chromaclient = chromadb.HttpClient(host=chroma_host, port=chroma_port, ssl=chroma_ssl)

textdocspath = "../output"
text_data = readtextfiles(textdocspath)

colname = "usagovsite"

try:
    chromaclient.get_collection(colname)
except:
    print(f" Collection {colname} does not exist - creating")
else:
    print(f" Collection {colname} already exists - deleting")
    chromaclient.delete_collection(colname)

collection = chromaclient.get_or_create_collection(name=colname, metadata={"hnsw:space": "cosine"})

if chromaclient.get_collection(colname):
    for filename, text in text_data.items():
        print(F"Embedding: {filename}")
        chunks = chunksplitter(text)
        embeds = getembedding(chunks)
        chunknumber = list(range(len(chunks)))
        ids = [filename + str(index) for index in chunknumber]
        metadatas = [{"source": filename} for index in chunknumber]
        collection.add(ids=ids, documents=chunks, embeddings=embeds,
                    metadatas=metadatas)
else:
    print(f"Collection {colname} could not be created")

print("---")
