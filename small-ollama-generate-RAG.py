__import__('pysqlite3')
import sys
sys.modules['sqlite3'] = sys.modules.pop('pysqlite3')
import chromadb, ollama

cli = chromadb.HttpClient(host="localhost", port=8000)
col = cli.get_or_create_collection(name="buildragwithpython")

query = " ".join(sys.argv[1:])
embed = ollama.embed(model="nomic-embed-text", input=query)['embeddings']

rels = '\n\n'.join(col.query(query_embeddings=embed)['documents'][0])
prompt = f"{query} - Answer that question using ONLY the resources provided.  Do not provide any data, or make any suggestions unless it comes from the following resources: {rels}"

# noragoutput = ollama.generate(model="llama3.2", prompt=query, stream=False)
# print(f"Answered without RAG: {noragoutput['response']}")
# print("---")

ragoutput = ollama.generate(model="llama3.2", prompt=prompt, stream=False)

print(f"Answered with RAG: {ragoutput['response']}")
