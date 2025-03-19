__import__('pysqlite3')
import sys
sys.modules['sqlite3'] = sys.modules.pop('pysqlite3')

print("Hello")

import os
import re
from ollama import embed
import chromadb
# from llama_index.readers import SimpleDirectoryReader

print("Hello2")


def readtextfiles(path):
    text_contents = {}
    directory = os.path.join(path)

    for filename in os.listdir(directory):
        if filename.endswith(".dat"):
            file_path = os.path.join(directory, filename)

            with open(file_path, "r", encoding="utf-8") as file:
                content = file.read()

            text_contents[filename] = content

    return text_contents


print("Hello3")


def chunksplitter(text, chunk_size=100):
    words = re.findall(r'\S+', text)

    chunks = []
    current_chunk = []
    word_count = 0

    for word in words:
        current_chunk.append(word)
        word_count += 1

        if word_count >= chunk_size:
            chunks.append(' '.join(current_chunk))
            current_chunk = []
            word_count = 0

    if current_chunk:
        chunks.append(' '.join(current_chunk))
    return chunks


print("Hello4")


def getembedding(chunks):
    embeds = embed(model="nomic-embed-text", input=chunks)
    return embeds.get('embeddings', [])


print("Hello5")


chromaclient = chromadb.HttpClient(host="localhost", port=8000)
textdocspath = "output"
text_data = readtextfiles(textdocspath)


print("Hello6")


collection = chromaclient.get_or_create_collection(name="buildragwithpython", metadata={"hnsw:space": "cosine"})


print("Hello7")


if chromaclient.get_collection("buildragwithpython"):
    print("Collection already exists")
    for collection in chromaclient.list_collections():
        # print(collection.name)
        chromaclient.delete_collection("buildragwithpython")


collection = chromaclient.get_or_create_collection(name="buildragwithpython", metadata={"hnsw:space": "cosine"})
# collection = chromaclient.get_collection("buildragwithpython")
print("Hello9")


for filename, text in text_data.items():
    print(filename)
    chunks = chunksplitter(text)
    embeds = getembedding(chunks)
    chunknumber = list(range(len(chunks)))
    ids = [filename + str(index) for index in chunknumber]
    metadatas = [{"source": filename} for index in chunknumber]
    collection.add(ids=ids, documents=chunks, embeddings=embeds,
                   metadatas=metadatas)


print("Hello10")


"""
"""
