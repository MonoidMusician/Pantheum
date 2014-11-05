#!/bin/bash
cd /var/www/latin
rm -f notify
ls entr_* *.sql | entr +notify &
sleep 0.2
while read F; do
  date
  echo "put $F /var/www/latin2/$F" | cmd=sftp ~/.ssh/basement.connect -b -
done < notify

