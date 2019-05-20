#!/bin/bash
find ./ -type f -name "*.php" -exec sed -i 's/\t/    /g' {} \;
echo "done"
