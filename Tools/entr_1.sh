#!/bin/bash
cd /var/www/latin
ls create_db.sql | entr -c ./entr_1.cmd.sh

