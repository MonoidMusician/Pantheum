#!/bin/bash
./clean.sh 2>/dev/null 1>/dev/null
echo "Lines | Words | Characters"
wc $(find /var/www/tournament | sed '/jquery/d' | sed '/md5/d' | sed '/pack/d' | sed '/sha512/d' | sed '/whirlpool/d' | sed '/Swift/d' | sed '/Images/d' | sed '/~$/d' | sed '/\.git/d' | tr '\n' ' ') 2>&1 | tail -n 1
