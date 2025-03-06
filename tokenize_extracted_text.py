
import os
from nltk.tokenize import sent_tokenize
from nltk.tokenize import word_tokenize

import tiktoken
enc = tiktoken.get_encoding("cl100k_base")

from transformers import BertTokenizer, GPT2Tokenizer

# Load BERT and GPT2 tokenizers
bert_tokenizer = BertTokenizer.from_pretrained("bert-base-uncased")
gpt2_tokenizer = GPT2Tokenizer.from_pretrained("gpt2")


root_path = '.'
in_path = root_path + '/output'
out_path = root_path + '/tokens'

extension_sentence = '.sentence.tok'
extension_word = '.word.tok'
extension_bytepair = '.bp.tok'
extension_bert = '.bert.tok'
extension_gpt2 = '.gpt2.tok'

# recursively get the path of each html file we want to process for input
dat_files = []
for root, _, files in os.walk(in_path):
    for file in files:
        if file.endswith(".dat"):
            dat_files.append(os.path.join(root, file))

# iterate over each of the input text files, tokenizing into
# files as we go
for dat_file in dat_files:
    with open(dat_file, 'r', encoding='utf-8') as file:

        # pull in the extracted page content
        dat_cont = file.read()

        # create the basename for our token output files
        fin_path, output_file = os.path.split(dat_file)
        output_file, _ = os.path.splitext(output_file)

        # create the filename for sentence token output
        sentence_file = out_path + '/' + output_file + extension_sentence
        print("Processing sentence tokens to '" + sentence_file + "'")

        # open the receiving file, before we start processing the input file
        with open(sentence_file, 'w', encoding='utf-8') as ofile:
            # write to output file
            sentences = sent_tokenize(dat_cont)
            print(sentences, file=ofile)
        # close output file before proceeding to the next input file
        ofile.close()

        # create the filename for word token output
        word_file = out_path + '/' + output_file + extension_word
        print("Processing word tokens to '" + word_file + "'")

        # open the receiving file, before we start processing the input file
        with open(word_file, 'w', encoding='utf-8') as ofile:
            # write to output file
            words = word_tokenize(dat_cont)
            print(words, file=ofile)
        # close output file before proceeding to the next input file
        ofile.close()

        # create the filename for byte pair token output
        bp_file = out_path + '/' + output_file + extension_bytepair
        print("Processing byte pair tokens to '" + bp_file + "'")

        # open the receiving file, before we start processing the input file
        with open(bp_file, 'w', encoding='utf-8') as ofile:
            # write to output file
            encoded = enc.encode(dat_cont)
            decoded = enc.decode(encoded)
            assert decoded == dat_cont
            print(encoded, file=ofile)
        # close output file before proceeding to the next input file
        ofile.close()

        # create the filename for bert token output
        bert_file = out_path + '/' + output_file + extension_bert
        print("Processing bert tokens to '" + bert_file + "'")

        # open the receiving file, before we start processing the input file
        with open(bert_file, 'w', encoding='utf-8') as ofile:
            # write to output file
            bert_tokens = bert_tokenizer.tokenize(dat_cont)
            print(bert_tokens, file=ofile)
        # close output file before proceeding to the next input file
        ofile.close()

        # create the filename for gpt2 token output
        gpt2_file = out_path + '/' + output_file + extension_gpt2
        print("Processing gpt2 tokens to '" + gpt2_file + "'")

        # open the receiving file, before we start processing the input file
        with open(gpt2_file, 'w', encoding='utf-8') as ofile:
            # write to output file
            gpt2_tokens = gpt2_tokenizer.tokenize(dat_cont)
            print(gpt2_tokens, file=ofile)
        # close output file before proceeding to the next input file
        ofile.close()
