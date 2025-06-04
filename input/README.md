# Import operations will look for the USAGov Static Site files in this directory. Subdirectories will also be recursively scanned for HTML.

### Reasonably recent site content can be retrieved with the following command:

```
 # Get static site contents from github:
 cd ./input
 git clone https://github.com/usagov/usagov-archive-2025.git

 # chunkify static site contents:
 cd ../php
 php cb-site-extractor.php
 
```
