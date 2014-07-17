#!/bin/bash

cd /var/www/tournament

for i in $(find); do
	if [ -f $i ]; then
		if [ "$(grep '$_POST' $i)" != "" ]; then
			echo $i
		elif [ "$(grep '$_GET' $i)" != "" ]; then
			echo $i
		fi
	fi
done
