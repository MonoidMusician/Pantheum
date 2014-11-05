#!/bin/bash
date
echo "Starting automatic upload script in directory $(pwd -P)"
rm -f notify
echo "put * /var/www/latin/PHP5/" | cmd=sftp ~/.ssh/basement.connect -b -
ls * | entr +notify &
sleep 0.2
while read F; do
  echo
  date
  echo "put $F /var/www/latin/PHP5/$F" | cmd=sftp ~/.ssh/basement.connect -b -
done < notify
