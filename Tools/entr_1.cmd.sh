#!/bin/bash
cd /var/www/latin
date
c=$(echo 'SELECT COUNT(*) FROM languages' | mysql -N PHPLang)
if [[ $c == 0 ]]; then
echo "Recreating database (currently empty)..."
mysql < create_db.sql
echo "Done!"
else
echo "Do you wish to re-create (aka delete existing data) in database PHPLang?"
select yn in "Yes" "No"; do
    case $yn in
        Yes ) mysql < create_db.sql; echo "Done!" break;;
        No ) exit;;
    esac
done
fi

