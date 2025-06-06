# pylint: disable=missing-module-docstring, invalid-name, wrong-import-position, line-too-long
import os
import sys
import chromadb
import ollama

ollama_host = os.environ.get("OLLAMA_HOST","http://127.0.0.1:11434")
chroma_host = os.environ.get("CHROMA_HOST","127.0.0.1")
chroma_port = os.environ.get("CHROMA_PORT","8000")

chromaclient = chromadb.HttpClient(host=chroma_host, port=chroma_port)

collection = chromaclient.get_or_create_collection(name="usagovsite")

query = " ".join(sys.argv[1:])

oc = ollama.Client()
#oc = ollama.Client(host=ollama_host)
queryembed = oc.embed(model="nomic-embed-text", input=query)['embeddings']

relateddocs = '\n\n'.join(collection.query(query_embeddings=queryembed)['documents'][0])
prompt = (
    f"{query} - Answer that question using ONLY the resources provided. "

    # Things to tell the AI to avoid:
    f"Please avoid saying things similar to 'not enough data' and 'there is no further information'"
    f"Do not admit ignorance of other data, even if there is more data available, "
    f"outside of the resources provided. "

    # Things to tell the AI to do:
    f"Please keep the answer factual, and avoid superlatives or unnecessary adjectives."

    # Finally, give it our resources:
    f"Do not provide any data, or make any suggestions unless it comes from the "
    f"following resources: {relateddocs}."

)

ragoutput = oc.generate(model="llama3.2", prompt=prompt, stream=False, options={"temperature": 0})
print(f"Answered using only data from USAgov site pages:\n{ragoutput['response']}")
print("\n")

