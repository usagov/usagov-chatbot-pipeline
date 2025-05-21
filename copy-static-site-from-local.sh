#!/bin/sh
echo "This should download the static pages from a locally running docker container of the cms. Will execute: "
echo "      docker exec cms tar -c /var/www/html | tar -x -f - --strip-components=2 -C "
echo "This should download the static pages to a folder named html, which will contain a folder for each page"
echo "If you need to generate the static files: docker exec -t cms sh /var/www/scripts/tome-static.sh http://localhost"
echo

rm -rf ./input/*.html
rm -rf ./output/*.dat
rm -rf ./tokens/*.tok
docker exec cms tar -c /var/www/html | tar -x -f - --strip-components=3 -C ./input
rm -rf ./input/es
rm -rf ./input/espanol
rm -rf ./input/_data
rm -rf ./input/s3
rm -rf ./input/themes
rm -rf ./input/modules
rm -rf ./input/admin
rm -rf ./input/Topics
rm -rf ./input/sites
rm -rf ./input/core
rm -rf ./input/citizen/topics/*.shtml
rm -rf ./input/*.shtml
rm -rf ./input/*.xml
