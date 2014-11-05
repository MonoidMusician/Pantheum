cd /var/www/latin
php-cgi PHP5/examples.php 2>&1 > output.html | tee -a output.html
