#!/bin/bash
#change repo to release repo
cat > /etc/apt/sources.list.d/fusionpbx.list << DELIM
deb http://repo.fusionpbx.com/deb/debian/ wheezy main
DELIM
#update repo list
apt-get update
#read pkg out to a logfile
dpkg --get-selections 'fusionpbx*' > /tmp/fusionpbx-pkg.log
#remove the pkgs in the list
apt-get remove -y fusionpbx*
#read list and reinstall rm pkgs
aptitude install $(cat /tmp/fusionpbx-pkg.log | awk '{print $1}')
#change to the fusionpbx www dir
cd /usr/share/nginx/www/fusionpbx
#run upgrade commands
php /usr/share/nginx/www/fusionpbx/core/upgrade/upgrade.php
php /usr/share/nginx/www/fusionpbx/core/upgrade/upgrade_domains.php
php /usr/share/nginx/www/fusionpbx/core/upgrade/app_defaults.php
php /usr/share/nginx/www/fusionpbx/core/upgrade/upgrade_schema.php
#cd root dir
cd ~