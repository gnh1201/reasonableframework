#!/bin/bash
#find . -type d -exec chmod 0755 {} \;
#find . -type f -exec chmod 0644 {} \;
chmod -R u+rwX,go+rX,go-w .
cp ./storage/example/_.htaccess ./.htaccess
