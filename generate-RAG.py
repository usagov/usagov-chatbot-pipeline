# pylint: disable=missing-module-docstring, invalid-name, wrong-import-position, line-too-long
import os
import sys
import chromadb
import ollama

ollama_host = os.environ.get("OLLAMA_HOST")
if ollama_host != "https://ob.straypacket.com:443":
    print("\n")
    print("OLLAMA_HOST not set. Please export OLLAMA_HOST='https://ob.straypacket.com:443'")
    print("\n")
    print("---")
    sys.exit()

chroma_host = os.environ.get("CHROMA_HOST", "cd.straypacket.com")
chroma_port = os.environ.get("CHROMA_PORT", "443")
chroma_ssl  = os.environ.get("CHROMA_SSL", True )
chromaclient = chromadb.HttpClient(host=chroma_host, port=chroma_port,ssl=chroma_ssl)

collection = chromaclient.get_or_create_collection(name="usagovsite")

query = " ".join(sys.argv[1:])

oc = ollama.Client()
#oc = ollama.Client(host=ollama_host)
queryembed = oc.embed(model="nomic-embed-text", input=query)['embeddings']

relateddocs = '\n\n'.join(collection.query(query_embeddings=queryembed)['documents'][0])
prompt = (
    f"{query} - Answer that question using ONLY the resources provided. "

    # Things not to tell the AI:
    # f"Please provide as verbose an answer as is possible, using the resources provided. " <-- this lead to flowery, antiquated language

    # Things to tell the AI to avoid:
    f"Please avoid saying things similar to 'not enough data' and 'there is no further information'"
    f"Do not admit ignorance of other data, even if there is more data available, "
    f"outside of the resources provided. "

    # Things to tell the AI to do:
    f"Please keep the answer factual, and avoid superlatives or unnecessary adjectives."

    # Finally, give it our resources:
    f"Do not provide any data, or make any suggestions unless it comes from the "
    f"following resources: {relateddocs}."

    # Apparently, the answer cannot be traced back to it's input files?  If not, then we'll need to think about how to provide
    # links in the responses.
    # f"Additionally, can you please let me know how to display the document file names used for the answer you gave?"
    # f"Additionally, please include the names of the files of the documents from the resources provided used in your answer. "
)

ragoutput = oc.generate(model="llama3.2", prompt=prompt, stream=False, options={"temperature": 0})
print(f"Answered using only data from USAgov site pages:\n{ragoutput['response']}")

print("---")
