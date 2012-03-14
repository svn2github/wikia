#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd /tmp
php /usr/wikia/source/wiki/maintenance/wikia/getDatabase.php -c A -f plpoznan
php /usr/wikia/source/wiki/maintenance/wikia/getDatabase.php -i plpoznan.sql.gz
rm plpoznan.sql.gz

cd $DIR
sudo -u www-data SERVER_ID=5915 php videoReset.php --conf /usr/wikia/docroot/wiki.factory/LocalSettings.php | tee ST_poznan.log || exit
echo ""
sudo -u www-data SERVER_ID=5915 php videoSanitize.php --conf /usr/wikia/docroot/wiki.factory/LocalSettings.php | tee ST_poznan.log || exit
echo ""