#!/bin/bash
#change repo to release repo
case $(uname -m) in
cat > "/etc/apt/sources.list.d/fusionpbx.list" << DELIM
deb http://repo.fusionpbx.com/fusionpbx/release/debian/ wheezy main
DELIM
esac

case $(uname -m) in armv7l)
/bin/cat > "/etc/apt/sources.list.d/freeswitch.list" <<DELIM
deb http://repo.fusionpbx/freeswitch-armhf/release/debian/ wheezy main
DELIM
esac

#update repo list
apt-get update

#read pkg out to a logfile
dpkg --get-selections 'fusionpbx*' > /tmp/fusionpbx-pkg.log

#remove the pkgs in the list
apt-get remove -y fusionpbx*

#read list and reinstall rm pkgs
aptitude install $(cat /tmp/fusionpbx-pkg.log | awk '{print $1}')

#update scripts
cp -rp /var/lib/fusionpbx/scripts /var/lib/fusionpbx/scripts.bak
rm -rf /var/lib/fusionpbx/scripts/*
cp -rp /usr/share/examples/fusionpbx/resources/install/scripts /var/lib/fusionpbx/
chown -R www-data:www-data /var/lib/fusionpbx/scripts
find "/var/lib/fusionpbx/scripts" -type f -exec chmod 664 {} +
find "/var/lib/fusionpbx/scripts" -type d -exec chmod 775 {} +

#update custom shell script menu
apt-get remove custom-scripts && apt-get install custom-scripts

#fix faxing recv dir
find "/var/lib/freeswitch/storage" -type f -exec chmod 664 {} +
find "/var/lib/freeswitch/storage" -type d -exec chmod 775 {} +

