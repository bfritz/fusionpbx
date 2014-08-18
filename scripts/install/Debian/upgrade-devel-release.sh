#!/bin/bash
dpkg --get-selections 'fusionpbx*' > /tmp/fusionpbx-pkg.log
apt-get remove -y fusionpbx*
aptitude install $(cat /tmp/fusionpbx-pkg.log | awk '{print $1}')
cd /usr/share/nginx/www/fusionpbx
php /usr/share/nginx/www/fusionpbx/core/upgrade/upgrade.php
php /usr/share/nginx/www/fusionpbx/core/upgrade/upgrade_domains.php
php /usr/share/nginx/www/fusionpbx/core/upgrade/app_defaults.php
php /usr/share/nginx/www/fusionpbx/core/upgrade/upgrade_schema.php
cd ~