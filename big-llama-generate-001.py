__import__('pysqlite3')
import sys
sys.modules['sqlite3'] = sys.modules.pop('pysqlite3')

# from llama_index.readers import SQLiteReader
from llama_index.llms import Ollama
llm = Ollama(model="llama3.2")
response = llm.complete("llama3.2", "Write a cover letter")

print(response)
