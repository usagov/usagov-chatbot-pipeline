# Python Setup for LLM stuff

## Python version setup, if less than 3.9.8

https://github.com/pyenv/pyenv/wiki#suggested-build-environment

### Begin Python Version Setup

```bash
sudo apt update

sudo apt install build-essential libssl-dev zlib1g-dev \
libbz2-dev libreadline-dev libsqlite3-dev curl git \
libncursesw5-dev xz-utils tk-dev libxml2-dev libxmlsec1-dev libffi-dev liblzma-dev

curl -fsSL https://pyenv.run | bash

echo 'export PYENV_ROOT="$HOME/.pyenv"' >> ~/.bashrc
echo '[[ -d $PYENV_ROOT/bin ]] && export PATH="$PYENV_ROOT/bin:$PATH"' >> ~/.bashrc
echo 'eval "$(pyenv init - bash)"' >> ~/.bashrc

and/or

echo 'export PYENV_ROOT="$HOME/.pyenv"' >> ~/.profile
echo '[[ -d $PYENV_ROOT/bin ]] && export PATH="$PYENV_ROOT/bin:$PATH"' >> ~/.profile
echo 'eval "$(pyenv init - bash)"' >> ~/.profile

and/or

echo 'export PYENV_ROOT="$HOME/.pyenv"' >> ~/.bash_profile
echo '[[ -d $PYENV_ROOT/bin ]] && export PATH="$PYENV_ROOT/bin:$PATH"' >> ~/.bash_profile
echo 'eval "$(pyenv init - bash)"' >> ~/.bash_profile

###
### Restart your shell, and make sure you can run 'pyenv'
###

pyenv global 3.9.8

```
### End Python Version Setup

## System and Python Packages needed for dealing with LLM

```bash
sudo apt install cargo

python3 -m pip install --upgrade pip
python3 -m pip install torch
python3 -m pip install pandas
python3 -m pip install bs4
python3 -m pip install tiktoken
python3 -m pip install nltk
python3 -m nltk.downloader all
python3 -m pip install transformers
python3 -m pip install git+https://github.com/huggingface/transformers
python3 -m pip install git+https://github.com/huggingface/accelerate
python3 -m pip install huggingface_hub
python3 -m pip install sentencepiece
python3 -m pip install bitsandbytes
python3 -m pip install haystack-ai
python3 -m pip install sentence-transformers
python3 -m pip install datasets
```
