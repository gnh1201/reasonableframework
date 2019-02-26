#!/bin/bash
find . -type d -exec chmod 0755 {} \;
find . -type f -exec chmod 0644 {} \;
cp ./storage/example/_.htaccess ./.htaccess
