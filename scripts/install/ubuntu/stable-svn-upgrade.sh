#!/bin/bash
#change to the fusionpbx www dir
cd /var/www/fusionpbx
svn switch http://fusionpbx.googlecode.com/svn/trunk/fusionpbx
svn update
#run upgrade commands
php /var/www/fusionpbx/core/upgrade/upgrade.php
php /var/www/fusionpbx/core/upgrade/upgrade_domains.php
php /var/www/fusionpbx/core/upgrade/app_defaults.php
php /var/www/fusionpbx/core/upgrade/upgrade_schema.php
#cd root dir
cd ~
cp -rp /usr/local/freeswitch/scripts /usr/local/freeswitch/scripts.bak
rm -rf /usr/local/freeswitch/scripts/*
cp -r /var/www/fusionpbx/resources/install/scripts/* /usr/local/freeswitch/scripts
chown -R www-data:www-data /usr/local/freeswitch/scripts
service freeswitch restart
service php5-fpm restart
