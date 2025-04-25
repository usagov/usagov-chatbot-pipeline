#!/usr/bin/env bash

source ../proxy/.env

BASE=/srv/usagov-chatbot-pipeline/proxy

echo curl https://github.com/srvrco/getssl/releases/download/v2.49/getssl_2.49-1_all.deb
echo sudo dpkg -i getssl_2.49-1_all.deb

for server in $OLLAMA_HOSTNAME $CHROMA_HOSTNAME; do
  echo getssl $server 
done


echo "See the following:  https://github.com/srvrco/getssl?tab=readme-ov-file#quick-start-guide"
