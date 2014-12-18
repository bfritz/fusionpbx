#!/bin/bash

#stop running services for upgrade
for i in monit fail2ban freeswitch ;do service "${i}" stop > /dev/null 2>&1 ; done

#reset to new repos
case $(uname -m) in x86_64|i[4-6]86)
#adding in freeswitch reop to /etc/apt/sources.list.d/freeswitch.lists X86/AMD64
echo ' installing stable repo '
cat > "/etc/apt/sources.list.d/freeswitch.list" <<DELIM
deb http://files.freeswitch.org/repo/deb/debian/ wheezy main
DELIM

#adding key for freeswitch repo 
echo 'fetcing repo key'
curl http://files.freeswitch.org/repo/deb/debian/freeswitch_archive_g0.pub | apt-key add -
esac

#adding in freeswitch reop to /etc/apt/sources.list.d/freeswitch.lists
case $(uname -m) in armv7l)
/bin/cat > "/etc/apt/sources.list.d/freeswitch.list" <<DELIM
deb http://repo.fusionpbx/freeswitch-armhf/debian/ wheezy main
DELIM
esac

#adding FusionPBX repo
echo 'installing fusionpbx release repo'
cat > "/etc/apt/sources.list.d/fusionpbx.list" <<DELIM
deb http://repo.fusionpbx.com/release/debian/ wheezy main
DELIM

#postgresql 9.3 / 9.4repo for x86 x86-64 bit pkgs
#add in pgsql 9.3 / 9.4
cat > "/etc/apt/sources.list.d/pgsql-pgdg.list" << DELIM
deb http://apt.postgresql.org/pub/repos/apt/ wheezy-pgdg main
DELIM
#add pgsql repo key
wget --quiet -O - http://apt.postgresql.org/pub/repos/apt/ACCC4CF8.asc | apt-key add -

#update repo list
apt-get update
#rm the nolonger existing freeswitch sounds
apt-get remove fusionpbx-sounds
#read pkg out to a logfile
dpkg --get-selections 'fusionpbx*' > /tmp/fusionpbx-pkg.log
#remove the pkgs in the list
apt-get remove -y fusionpbx*
#read list and reinstall rm pkgs
aptitude install $(cat /tmp/fusionpbx-pkg.log | awk '{print $1}')

#update scripts
cp -rp /var/lib/fusionpbx/scripts /var/lib/fusionpbx/scripts.bak
rm -rf /var/lib/fusionpbx/scripts/*
cp -rp /usr/share/fusionpbx/resources/install/scripts /var/lib/fusionpbx/
chown -R www-data:www-data /var/lib/fusionpbx/scripts
find "/var/lib/fusionpbx/scripts" -type f -exec chmod 664 {} +
find "/var/lib/fusionpbx/scripts" -type d -exec chmod 775 {} +

#Make new dir's for the new layout
mkdir -p /etc/fusionpbx/switch/conf /var/lib/fusionpbx/sounds/music mkdir -p /var/lib/fusionpbx/storage 

#move freeswitch configs
cp -rp /etc/freeswitch/* /etc/fusionpbx/switch/conf
chown -R www-data:www-data /etc/fusionpbx/switch

#move faxes
cp -rp/var/lib/freeswitch/storage/fax /var/lib/fusionpbx/storage
chown www-data:freeswitch /var/lib/fusionpbx/storage/

#move recordings
cp -rp/var/lib/freeswitch/recordings/* /var/lib/fusionpbx/recordings
chown www-data:freeswitch /var/lib/fusionpbx/recordings

#move voicemail
cp -rp/var/lib/freeswitch/storage/voicemail/* /var/lib/fusionpbx/storage/
chown www-data:freeswitch /var/lib/fusionpbx/storage/voicemail

#Linking moh dir so freeswitch can read in the moh files
ln -s /var/lib/fusionpbx/sounds/music /usr/share/freeswitch/sounds/music/fusionpbx

#install the fusionpbx music on hold
apt-get install fusionpbx-music-default

#fix moh dir in local stream setting
sed "/etc/fusionpbx/switch/conf/autoload_configs/local_stream.conf.xml -i -e s,'<directory name="default" path="$${sounds_dir}/music/8000">','<directory name="default" path="$${sounds_dir}/music/fusionpbx/default/8000">',g

#configuring freeswitch to start with new layout.
#Freeswitch layout for FHS with fusionpbx
cat > '/etc/default/freeswitch' << DELIM
CONFDIR="/etc/fusionpbx/switch/conf"
fs_conf="/etc/fusionpbx/switch/conf"
fs_db="/var/lib/freeswitch/db"
fs_log="/var/log/freeswitch"
fs_recordings="/var/lib/fusionpbx/recordings"
fs_run="/var/run/freeswitch"
fs_scripts="/var/lib/fusionpbx/scripts"
fs_storage="/var/lib/fusionpbx/storage"
fs_usr=freeswitch
fs_grp=\$fs_usr
fs_options="-nc -rp"
DAEMON_ARGS="-u \$fs_usr -g \$fs_grp -conf \$fs_conf -db \$fs_db -log \$fs_log -scripts \$fs_scripts -run \$fs_run -storage \$fs_storage -recordings \$fs_recordings \$fs_options"
DELIM

#fixing permissions for sqlite db 
find "/var/lib/fusionpbx/db" -type d -exec chmod 777 {} +
find "/var/lib/fusionpbx/db" -type f -exec chmod 666 {} +

#restartng services with the fusionpbx freeswitch fhs dir layoout
for i in fail2ban freeswitch monit ;do service "${i}" stop > /dev/null 2>&1 ; done

#Reseting nginx to new dir path
sed  "/etc/nginx/sites-available/fusionpbx" -e i s,'/usr/share/nginx/www/fusionpbx','/var/www/fusionpbx',g

#restartng services with the fusionpbx freeswitch fhs dir layoout
for i in php5-fpm ngninx ;do service "${i}" stop > /dev/null 2>&1 ; done