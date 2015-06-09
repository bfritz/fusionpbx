#!/bin/bash
#Date July 09 2015 11:45:00 CST
################################################################################
# The MIT License (MIT)
################################################################################
# Copyright (c) <2015> <r.neese@gmail.com>
################################################################################
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
################################################################################
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
################################################################################
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.
################################################################################

##################################
#----OS ENVIRONMENT CHECKS-------
##################################
##################################################################
# check to confirm running as root. # First, we need to be root...
##################################################################
if [ "$(id -u)" -ne "0" ]; then
  sudo -p "$(basename "$0") must be run as root, please enter your sudo password : " "$0" "$@"
  exit 0
fi
echo
echo "You're root.... continuing!"
echo
###########################################
# Run a OS and Platform compatabilty Check
###########################################
########
# ARMEL
########
case $(uname -m) in armv[4-6]l)
echo
echo " ArmEL Not supported in this script. "
echo
exit
esac
########
# ARMHF
########
case $(uname -m) in armv7l)
echo
echo " ArmHF Not supported in this script"
echo
exit
esac
#############
# Intel/AMD
#############
case $(uname -m) in x86_64|i[4-6]86)
echo
echo " Intel / Amd  boards Spported"
echo
echo " Install for freeswitch Intel/AMD Debian pkgs. "
echo
esac
echo
########################################################
# removes the cd img from the /etc/apt/sources.list file
# (not needed after base install)
########################################################
sed -i '/cdrom:/d' /etc/apt/sources.list
#sed -i '2,4d' /etc/apt/sources.list

###############################################
#if lsb_release is not installed it installs it
###############################################
if [ ! -s /usr/bin/lsb_release ]; then
	apt-get update && apt-get -y install lsb-release
fi

#################
# Os/Distro Check
#################
lsb_release -c |grep -i wheezy &> /dev/null 2>&1
if [ $? -eq 0 ]; then
	echo "Good, you are running Debian 7 : wheezy"
	echo
else
	lsb_release -c |grep -i jessie > /dev/null
	if [ $? -eq 0 ]; then
		echo " OK you are running Debian 8 : Jessie. This script is known to work "
		echo " with building from source. No Jessie pkgs yet. "
	else
		echo " This script was written for Debian 7 wheezy & Debian 8 Jessie (Testing) "
		echo
		echo " Your OS appears to be: " lsb_release -a
		echo
		echo " Your OS is not currently supported... Exiting the install. "
		exit
	fi
fi
clear
###################
# Notes / Warnings
###################
echo
cat << DELIM
                  ( "Not Ment For L.a.m.p Installs" )

                ( "L.A.M.P = Linux Apache Mysql PHP" )

                 "THIS IS A ONE TIME INSTALL SCRIPT."

             "IT IS NOT INTENDED TO BE RUN MULTIPLE TIMES"

   This Script Is Ment To Be Run On A Fresh Install Of Debian 7 (Wheezy)

   or Fresh Install Of Debian 8 (Jessie).

   If It Fails For Any Reason Please Report To r.neese@gmail.com.

   Please Include Any Screen Output You Can To Show Where It Fails.

DELIM

###############################################
# Checks to see if installing on openvz server
###############################################
if [[ -f /proc/vz ]]; then
echo
cat << DELIM
    Note:

    Those of you running this script on openvz. You must run it as root and

    bash Fusionpbx-Debian-Package-Install.sh or it fails the networking check.

    Please take the time to refer to this document if you have install issues

    on openvz http://openvz.org/Virtual_Ethernet_device and make sure to setup

    a eth0 for better performance with the script.
DELIM
exit
fi

###########################
# Pre-Install Information
###########################
echo
cat << DELIM
  Note:

  Pre-Install Information:

    This script uses Sqlite by default for the fusionpbx database.

    If you wish to use postgresql localy or on a remote server.

    You need to edit the script and enable the pgsql-client or pgsql

    option and fill in the required information.
    
    Please Referance :
    
    http://wiki.fusionpbx.com/index.php?title=Debian_Fusionpbx_Pkg_or_Source_Install
    
DELIM
echo
########################################
# FreeSWITCH Debian FHS Configuration
########################################
cat << DELIM
  " FreeSWITCH Debian FHS Configuration "

     Locations:
      prefix:          /usr
      exec_prefix:     ${prefix}
      bindir:          ${exec_prefix}/bin
      sysconfdir:      /etc/freeswitch
      libdir:          ${exec_prefix}/lib

      certsdir:        /etc/freeswitch/tls
      dbdir:           /var/lib/freeswitch/db
      grammardir:      /usr/share/freeswitch/grammar
      htdocsdir:       /usr/share/freeswitch/htdocs
      logfiledir:      /var/log/freeswitch
      modulesdir:      /usr/lib/freeswitch/mod
      pkgconfigdir:    ${exec_prefix}/lib/pkgconfig
      recordingsdir:   /var/lib/freeswitch/recordings
      runtimedir:      /var/run/freeswitch
      scriptdir:       /var/lib/freeswitch/scripts
      soundsdir:       /usr/share/freeswitch/sounds
      storagedir:      /var/lib/freeswitch/storage
      cachedir:        /var/cache/freeswitch
DELIM
echo
#######################################################

########################################
#<------Start/Begin Edit HERE--------->
########################################
########################
# Freeswitch Options
########################
#####################################################################################################
# Set what language lang/say pkgs and language sound files to use. ( Only if pkgs install is selected )
# en-ca=English/CA en-us=English/US (default) fr-ca=French/Canadian pt-br=Portuguese/Brazill
# ru-ru=Russian/Russia sv-se=Swedish/Sweden zh-cn=chinese/Mandarin zh-hk=chinese/HongKong
#####################################################################################################
freeswitch_sounds_language="en-us"

################################################################
# Option to disable some loging execpt for  warnings and errors
################################################################
logging_level="n"

####################
# FUSIONPBX OPTIONS
####################
#############################################################################
#Set how long to keep freeswitch/fusionpbx log files 1 to 30 days (Default:5)
#############################################################################
keep_logs="5"

#######################################################################
#Set mp3/wav file upload/post size limit ( Must Have the M on the end )
#######################################################################
upload_size="25M"

##########################################
#----Optional Fusionpbx Apps/Modules----
##########################################
#######################################
# DO NOT SELECT FROM BOTH !!!!!!!!!!
#######################################
###################################################################
# If you wish to install all options use THE ALL OPTION ONLY!!!!!!!
###################################################################
all="n" #: Install all extra modules for fusionpbx and related freeswitch deps

###############################################################
# Else select options fusionpbx module/appsfrom here........
###############################################################
adminer="n" # : integrated for an administrator in the superadmin group to enable easy database access
backup="n" # : pbx backup module. backup sqlite db / configs/ logs
call_broadcast="n" # : Create a recording and select one or more groups to have the system call and play the recording
call_center="n" # : display queue status, agent status, tier status for call centers using mod_callcenter call queues
call_flows="n" # : Typically used with day night mode. To direct calls between two destinations.
conference_centers="n" # : tools for multi room confrences and room contol
conference="n" # : tools for single room confrences and room contol
edit="n" # : multi tools for editing (templates/xmlfiles/configfiles/scripts) files
exec="n" # : comman shells pages for executing (php/shells) commands
fax="n" # : fusionpbx send/recieve faxes service
fifo="n" # : first in first out call queues system
hot_desk="n" # : allows users to login and recieve calls on any office phone
services="n" # : allows interaction with the processes running on your server
sql_query="n" # : allows you to interactively submit SQL queries to the database used in FusionPBX
traffic_graph="n" # : php graph for monitoing the network interface traffic
aastra="n" # : phone provisioning tool &  templates for aastra phones
atcom="n" # : phone provisioning tool &  templates for atcom phones
cisco="n" # : phone provisioning tool & templates for cisco phones
grandstream="n" # : phone provisioning tool & templates for grandstream phones
linksys="n" # : phone provisioning tool & templates for linksys phones
panasonic="n" # : phone provisioning tool & templates for panasonic phones
polycom="n" # : phone provisioning tool & templates for polycom phones
snom="n" # : provisioning tool & templates for snom phones
yealink="n" # : phone provisioning tool & templates for yealink phones
verto="n" # (x86/amd64 Only) (future option on arm)
accessible_theme="n" # : accessible theme for fusionpbx
classic_theme="n" # : classic theme for fusionpbx
default_theme="n" # : default theme for fusionpbx
minimized_theme="n" # : minimal theme for fusionpbx

######################################
# POSTGRESQL ( Optional Not Required)
######################################
################################################
# Please Select Server or Client not both !!!!!!
################################################

#################################################################################
# Install postgresql Client 9.4 for connection to remote postgresql servers (y/n)
#################################################################################
postgresql_client="n"

#################################################################################
# Install postgresql server 9.4 (y/n) (client included)(Local Machine)
# Notice:
# You should not use postgresql server on a nand/emmc/sd. It cuts the performance
# life in half due to all the needed reads and writes. This cuts the life of
# your pbx emmc/sd in half.
#################################################################################
postgresql_server="n"

##########################################################
# Set Postgresql Server Admin username ( Lower case only )
##########################################################
pgsql_admin=pgsqladmin

######################################
# Set Postgresql Server Admin password
######################################
pgsql_admin_passwd=pgsqladmin2015

####################################################################################
# Set Database Name used for fusionpbx in the postgresql server (Default: fusionpbx)
####################################################################################
db_name=fusionpbx

####################################################################################
# Set FusionPBX database admin name.(used by fusionpbx to access the database table
# in the postgresql server (Default: fusionpbx)
####################################################################################
db_user_name=fusionpbxadmin

###################################################################################
# Set FusionPBX database admin password .(used by fusionpbx to access the database
# table in the postgresql server). Please set a very secure password !!!!!!
###################################################################################
db_user_passwd=fusionpbx2015

######################################
# ( Optional Not Required)
######################################
###############################################################################
# Disable xml_cdr files in /var/log/freeswitch/xml_cdr and only log cdr to the
# sqlite or pgsql database only.
###############################################################################
xml_cdr_files="n"

################################################################
#Install Ajenti Optional Admin Portal  Optional (Not Required)
################################################################
install_ajenti="n"

####################################
#<------Stop/End Edit Here-------->
####################################

######################################################
# Hard Set Varitables (Do Not EDIT) Freeswitch default
######################################################
################################################################
#Used for pkg based installs for cp the base configs into place
################################################################
fs_conf_dir="/etc/freeswitch"
fs_dflt_conf_dir="/usr/share/freeswitch/conf"

######################################################
#Nginx default www dir
######################
WWW_PATH="/var/www" #debian nginx default dir
#################################
#set Web User Interface Dir Name
#################################
wui_name="fusionpbx"
#####################
#Php ini config file
#####################
php_ini="/etc/php5/fpm/php.ini"

###################################
#-----Start PBX installation------
###################################
###############################################################################################
#Testing for internet connection. Pulled from and modified
#http://www.linuxscrew.com/2009/04/02/tiny-bash-scripts-check-internet-connection-availability/
###############################################################################################
#######################################
#-----test internet connection-------
#######################################
echo
echo "This Script Currently Requires a internet connection "
wget -q --tries=10 --timeout=5 http://www.google.com -O /tmp/index.google &> /dev/null

if [ ! -s /tmp/index.google ];then
	echo "No Internet connection. Please check ethernet cable"
	/bin/rm /tmp/index.google
	exit 1
else
	echo "Found the Internet ... continuing!"
	/bin/rm /tmp/index.google
fi
echo

#######################################
#Setup Main debian repo for right pkgs
#######################################
lsb_release -c |grep -i wheezy &> /dev/null 2>&1
if [ $? -eq 0 ]; then
 	echo "installing wheezy release repo"
	cat > "/etc/apt/sources.list" << DELIM
	deb http://httpredir.debian.org/debian/ wheezy main contrib non-free
	deb-src http://httpredir.debian.org/debian/ wheezy main contrib non-free

	deb http://httpredir.debian.org/debian/ wheezy-updates main contrib non-free
	deb-src http://httpredir.debian.org/debian/ wheeexy-updates main contrib non-free

	deb http://httpredir.debian.org/debian/ wheezy-backports main contrib non-free
	deb-src http://httpredir.debian.org/debian/ wheezy-backports main contrib non-free

DELIM
else
	lsb_release -c |grep -i jessie &> /dev/null 2>&1
	if [ $? -eq 0 ]; then
		echo "installing jessie release repo"
		cat > "/etc/apt/sources.list" << DELIM
		deb http://httpredir.debian.org/debian/ jessie main contrib non-free
		deb-src http://httpredir.debian.org/debian/ jessie main contrib non-free

		deb http://httpredir.debian.org/debian/ jessie-updates main contrib non-free
		deb-src http://httpredir.debian.org/debian/ jessie-updates main contrib non-free

		deb http://httpredir.debian.org/debian/ jessie-backports main contrib non-free
		deb-src http://httpredir.debian.org/debian/ jessie-backports main contrib non-free

DELIM
	fi
fi

####################################
#----- upgrading base install-----
####################################
apt-get update && apt-get -y upgrade
echo
#######################################
# Freeswitch pkg based install
#######################################
##############################
# Detect and Set Intel/AMD Repos
# Set Release or devel repos
##############################
case $(uname -m) in x86_64|i[4-6]86)
	#adding in freeswitch reop to /etc/apt/sources.list.d/freeswitch.lists
	echo " installing Intel/AMD64 Release/Stable repo "
	lsb_release -c |grep -i wheezy &> /dev/null 2>&1
	if [ $? -eq 0 ]; then
		echo "installing wheezy release repo"
		cat > "/etc/apt/sources.list.d/freeswitch.list" <<DELIM
		deb http://files.freeswitch.org/repo/deb/debian/ wheezy main
DELIM
	else
		lsb_release -c |grep -i jessie &> /dev/null 2>&1
		if [ $? -eq 0 ]; then
		echo "installing jessie release repo"
		cat > "/etc/apt/sources.list.d/freeswitch.list" <<DELIM
		deb http://files.freeswitch.org/repo/deb/debian/ jessie main
DELIM
		fi
	fi
esac

################################
#adding key for freeswitch repo
################################
echo " fetcing repo key "
curl http://files.freeswitch.org/repo/deb/debian/freeswitch_archive_g0.pub | apt-key add -

##################################################
#adding extra language sounds repo for freeswitch
##################################################
 #adding in freeswitch reop to /etc/apt/sources.list.d/freeswitch_lang_sounds.lists
echo " adding extra language sounds repo for freeswitch "
lsb_release -c |grep -i wheezy &> /dev/null 2>&1
if [ $? -eq 0 ]; then
		echo "adding extra language sounds repo for freeswitch for debian wheezy"
		cat > "/etc/apt/sources.list.d/freeswitch_lang_sounds.list" <<DELIM
		deb http://repo.fusionpbx.com/freeswitch_lang_sounds/release/debian/ wheezy main
DELIM
else
	lsb_release -c |grep -i jessie &> /dev/null 2>&1
	if [ $? -eq 0 ]; then
		echo "adding extra language sounds repo for freeswitch for debian jessie"
		cat > "/etc/apt/sources.list.d/freeswitch_lang_sounds.list" <<DELIM
		deb http://repo.fusionpbx.com/freeswitch_lang_sounds/release/debian/ jessie main
DELIM
	fi
fi

###################################
#----install ntpd time daemon-----
####################################
for i in update upgrade ;do apt-get -y "${i}" ; done
apt-get -y install ntp
service ntp restart

########################################
#------install Freeswitch Deps----------
########################################
apt-get -y install unixodbc uuid memcached libtiff5 libtiff-tools time bison htop screen libpq5 lame

#############################################
#-----Start Install of freeswitch-----------
#############################################
#############################################
# Install Freeswitch Pkgs used by fusion gui
#############################################
apt-get -y install --force-yes freeswitch freeswitch-init freeswitch-meta-codecs freeswitch-mod-commands freeswitch-mod-curl \
		freeswitch-mod-db freeswitch-mod-distributor freeswitch-mod-dptools freeswitch-mod-enum freeswitch-mod-esf freeswitch-mod-esl \
		freeswitch-mod-expr freeswitch-mod-fsv freeswitch-mod-hash freeswitch-mod-memcache freeswitch-mod-portaudio freeswitch-mod-portaudio-stream \
		freeswitch-mod-random freeswitch-mod-spandsp freeswitch-mod-spy freeswitch-mod-translate freeswitch-mod-valet-parking freeswitch-mod-flite \
		freeswitch-mod-pocketsphinx freeswitch-mod-tts-commandline freeswitch-mod-dialplan-xml freeswitch-mod-loopback freeswitch-mod-sofia \
		freeswitch-mod-event-multicast freeswitch-mod-event-socket freeswitch-mod-event-test freeswitch-mod-local-stream freeswitch-mod-native-file \
		freeswitch-mod-sndfile freeswitch-mod-tone-stream freeswitch-mod-lua freeswitch-mod-console freeswitch-mod-logfile freeswitch-mod-syslog \
		freeswitch-mod-say-en freeswitch-mod-posix-timer freeswitch-mod-timerfd freeswitch-mod-v8 freeswitch-mod-xml-cdr freeswitch-mod-xml-curl \
		freeswitch-mod-xml-rpc freeswitch-conf-vanilla 

############################
# Intel/AMD gets mod_shout
############################
case $(uname -m) in x86_64|i[4-6]86)
	apt-get -y install --force-yes freeswitch-mod-shout
esac

######################################
#setup language / sound files for use
######################################
if [[ $freeswitch_sounds_language == "en-ca" ]]; then
	apt-get -y install --force-yes freeswitch-lang-fr freeswitch-mod-say-fr freeswitch-sounds-en-ca-june
fi

if [[ $freeswitch_sounds_language == "en-us" ]]; then
	apt-get -y install --force-yes freeswitch-lang-en freeswitch-mod-say-en freeswitch-sounds-en-us-callie
fi

if [[ $freeswitch_sounds_language == "fr-ca" ]]; then
	apt-get -y install --force-yes freeswitch-lang-fr freeswitch-mod-say-fr freeswitch-sounds-fr-ca-june
fi

if [[ $freeswitch_sounds_language == "pt-br" ]]; then
	apt-get -y install --force-yes freeswitch-lang-pt freeswitch-mod-say-pt freeswitch-sounds-pt-br-karina
fi

if [[ $freeswitch_sounds_language == "ru-ru" ]]; then
	apt-get -y install --force-yes freeswitch-lang-ru freeswitch-mod-say-ru freeswitch-sounds-ru-ru-elena
fi

if [[ $freeswitch_sounds_language == "sv-se" ]]; then
	apt-get -y install --force-yes freeswitch-lang-sv freeswitch-mod-say-sv freeswitch-sounds-sv-se-jakob
fi

if [[ $freeswitch_sounds_language == "zh-cn" ]]; then
	apt-get -y install --force-yes freeswitch-mod-say-zh freeswitch-sounds-zh-cn-sinmei
fi

if [[ $freeswitch_sounds_language == "zh-hk" ]]; then
	apt-get -y install --force-yes freeswitch-mod-say-zh freeswitch-sounds-zh-hk-sinmei
fi

###############################
# make the freeswitch conf dir
###############################
mkdir -p "$fs_conf_dir"

#####################################
#cp the default configs into place.
#####################################
cp -rp "$fs_dflt_conf_dir"/vanilla/* "$fs_conf_dir"

########################################
#fix ownership of files for freeswitch
########################################
chown -R freeswitch:freeswitch "$fs_conf_dir"

#######################
#Restarting freeswitch
#######################
service freeswitch restart

############################
#set package install marker
############################
touch /root/.fs-pkgs

##############################################################################
#Install and configure  PHP + Nginx + sqlite3 for use with the fusionpbx gui.
##############################################################################
apt-get -y install sqlite3 ssl-cert nginx php5-cli php5-common php-apc php5-gd \
		php-db php5-fpm php5-memcache php5-sqlite php5-imap php5-mcrypt php5-curl

##################################################
# Changing file upload size from 2M to upload_size
##################################################
sed -i "$php_ini" -e "s#upload_max_filesize = 2M#upload_max_filesize = $upload_size#"

######################################################
# Changing post_max_size limit from 8M to upload_size
######################################################
sed -i "$php_ini" -e "s#post_max_size = 8M#post_max_size = $upload_size#"

#####################################################################################################
#Nginx config Copied from Debian nginx pkg (nginx on debian wheezy uses sockets by default not ports)
#####################################################################################################
cat > "/etc/nginx/sites-available/fusionpbx"  << DELIM
server{
        listen 127.0.0.1:80;
        server_name 127.0.0.1;
        
        access_log /var/log/nginx/access.log;
        error_log /var/log/nginx/error.log;

        client_max_body_size $upload_size;
        client_body_buffer_size 128k;

		root $WWW_PATH/$wui_name;
		index index.php;

        location ~ \.php$ {
			include snippets/fastcgi-php.conf;
			include fastcgi_params;
			fastcgi_pass unix:/var/run/php5-fpm.sock;
			fastcgi_param   SCRIPT_FILENAME $WWW_PATH/$wui_name/$fastcgi_script_name;
        }

        # Disable viewing .htaccess & .htpassword & .db
        location ~ .htaccess {
                        deny all;
        }
        location ~ .htpassword {
                        deny all;
        }
        location ~^.+.(db)$ {
                        deny all;
        }
}

server{
        listen 80;
        listen [::]:80 default_server ipv6only=on;
        
        server_name $wui_name;
        
        if (\$uri !~* ^.*provision.*$) {
                rewrite ^(.*) https://\$host\$1 permanent;
                break;
        }

		#grandstream
        rewrite "^.*/provision/cfg([A-Fa-f0-9]{12})(\.(xml|cfg))?$" /app/provision/?mac=\$1;

		#aastra
		#rewrite "^.*/provision/([A-Fa-f0-9]{12})(\.(cfg))?$" /app/provision/?mac=$1 last;

		#yealink common
		rewrite "^.*/provision/(y[0-9]{12})(\.cfg)?$" /app/provision/index.php?file=\$1\$2;

		#yealink mac
		rewrite "^.*/([A-Fa-f0-9]{12})(\.(xml|cfg))?$" /app/provision/index.php?mac=\$1 last;

		if (\$uri !~* ^.*provision.*$) {
			rewrite ^(.*) https://\$host\$1 permanent;
			break;
		}

        access_log /var/log/nginx/access.log;
        error_log /var/log/nginx/error.log;

        client_max_body_size $upload_size;
        client_body_buffer_size 128k;

		root $WWW_PATH/$wui_name;
		index index.php;

        location ~ \.php$ {
			include snippets/fastcgi-php.conf;
			include fastcgi_params;
			fastcgi_pass unix:/var/run/php5-fpm.sock;
			fastcgi_param   SCRIPT_FILENAME $WWW_PATH/$wui_name/$fastcgi_script_name;
        }

        # Disable viewing .htaccess & .htpassword & .db
        location ~ .htaccess {
                deny all;
        }
        location ~ .htpassword {
                deny all;
        }
        location ~^.+.(db)$ {
                deny all;
        }
}

server{
        listen 443;
        listen [::]:443 default_server ipv6only=on;
        
        server_name $wui_name;
        
 		include snippets/snakeoil.conf;
		ssl  on;

		#grandstream
        rewrite "^.*/provision/cfg([A-Fa-f0-9]{12})(\.(xml|cfg))?$" /app/provision/?mac=\$1;

		#aastra
		#rewrite "^.*/provision/([A-Fa-f0-9]{12})(\.(cfg))?$" /app/provision/?mac=$1 last;

		#yealink common
		rewrite "^.*/provision/(y[0-9]{12})(\.cfg)?$" /app/provision/index.php?file=\$1\$2;

		#yealink mac
		rewrite "^.*/provision/([A-Fa-f0-9]{12})(\.(xml|cfg))?$" /app/provision/index.php?mac=\$1 last;

        access_log /var/log/nginx/access.log;
        error_log /var/log/nginx/error.log;

        client_max_body_size $upload_size;
        client_body_buffer_size 128k;

		root $WWW_PATH/$wui_name;
		index index.php;

        location ~ \.php$ {
			include snippets/fastcgi-php.conf;
			include fastcgi_params;
			fastcgi_pass unix:/var/run/php5-fpm.sock;
			fastcgi_param   SCRIPT_FILENAME $WWW_PATH/$wui_name/$fastcgi_script_name;
        }

        # Disable viewing .htaccess & .htpassword & .db
        location ~ .htaccess {
                deny all;
        }
        location ~ .htpassword {
                deny all;
        }
        location ~^.+.(db)$ {
                deny all;
        }
}
DELIM

###############################################
# set nginx worker level limit for performance
###############################################
cat > "/etc/nginx/nginx.conf"  << DELIM
user www-data;
worker_processes 2;
pid /var/run/nginx.pid;

events {
	worker_connections 768;
	multi_accept on;
}

http {

	##
	# Basic Settings
	##

	sendfile on;
	tcp_nopush on;
	tcp_nodelay on;
	keepalive_timeout 75;
	keepalive_requests 10000;
	types_hash_max_size 2048;
	# server_tokens off;

	# server_names_hash_bucket_size 64;
	# server_name_in_redirect off;

	include /etc/nginx/mime.types;
	default_type application/octet-stream;

    ##
    # SSL Settings
    ##

	open_file_cache max=1000 inactive=20s;
	open_file_cache_valid 30s;
	open_file_cache_min_uses 2;
	open_file_cache_errors off;

	fastcgi_cache_path /var/cache/nginx levels=1:2 keys_zone=microcache:15M max_size=1000m inactive=60m;

	##
	# Logging Settings
	##

	#access_log /var/log/nginx/access.log;
	error_log /var/log/nginx/error.log;

	##
	# Gzip Settings
	##

	gzip on;
	gzip_static on;
	gzip_disable "msie6";

	# gzip_vary on;
	# gzip_proxied any;
	# gzip_comp_level 6;
	# gzip_buffers 16 8k;
	# gzip_http_version 1.1;
	# gzip_types text/plain text/css application/json application/x-javascript text/xml application/xml application/xml+rss text/javascript;

	##
	# nginx-naxsi config
	##
	# Uncomment it if you installed nginx-naxsi
	##

	#include /etc/nginx/naxsi_core.rules;

	##
	# nginx-passenger config
	##
	# Uncomment it if you installed nginx-passenger
	##

	#passenger_root /usr;
	#passenger_ruby /usrruby;

	##
	# Virtual Host Configs
	##

	include /etc/nginx/conf.d/*.conf;
	include /etc/nginx/sites-enabled/*;
}

DELIM

##############################################################
# linking fusionpbx nginx config from avaible to enabled sites
##############################################################
ln -s /etc/nginx/sites-available/"$wui_name" /etc/nginx/sites-enabled/"$wui_name"

######################
#disable default site
######################
rm -rf /etc/nginx/sites-enabled/default

##############################
#Restarting Nginx and PHP FPM
##############################
for i in nginx php5-fpm ;do service "${i}" restart > /dev/null 2>&1 ; done

########################################
#---- end f nginx / php5 install------
########################################

##############################
#Adding users to needed groups
##############################
adduser freeswitch www-data
adduser freeswitch dialout
adduser www-data freeswitch
adduser www-data audio
adduser www-data dialout

############################################################################
# ---Start--Install FusionPBX Web User Interface ( very basic install )-----
############################################################################
######################
#adding FusionPBX repo
#######################
lsb_release -c |grep -i wheezy &> /dev/null 2>&1
if [ $? -eq 0 ]; then
	echo ' installing fusionpbx wheezy Stabe/Release repo '
	cat > "/etc/apt/sources.list.d/fusionpbx.list" <<DELIM
	deb http://repo.fusionpbx.com/fusionpbx/release/debian/ wheezy main
DELIM
else
	lsb_release -c |grep -i jessie &> /dev/null 2>&1
	if [ $? -eq 0 ]; then
		echo ' installing fusionpbx jessie Stabe/Release repo '
		cat > "/etc/apt/sources.list.d/fusionpbx.list" <<DELIM
		deb http://repo.fusionpbx.com/fusionpbx/release/debian/ jessie main
DELIM
	fi
fi
echo
#################################################
#run repo update after adding in a new repo....
#################################################
apt-get update

###########################
#Installing fusionpbx pkgs
###########################
##########################################
# Install default minimal fusionpbx pkgs
##########################################
apt-get -y --force-yes install fusionpbx-core fusionpbx-app-calls fusionpbx-app-calls-active fusionpbx-app-call-block \
		fusionpbx-app-contacts fusionpbx-app-destinations fusionpbx-app-dialplan fusionpbx-app-dialplan-inbound \
		fusionpbx-app-dialplan-outbound fusionpbx-app-emails fusionpbx-app-extensions fusionpbx-app-follow-me fusionpbx-app-gateways \
		fusionpbx-app-ivr-menu fusionpbx-app-login fusionpbx-app-log-viewer fusionpbx-app-modules fusionpbx-app-music-on-hold \
		fusionpbx-app-recordings fusionpbx-app-registrations fusionpbx-app-ring-groups fusionpbx-app-settings \
		fusionpbx-app-sip-profiles fusionpbx-app-sip-status fusionpbx-app-system fusionpbx-app-time-conditions \
		fusionpbx-app-xml-cdr fusionpbx-app-vars fusionpbx-app-voicemails fusionpbx-app-voicemail-greetings \
		fusionpbx-conf fusionpbx-scripts fusionpbx-sqldb fusionpbx-theme-enhanced fusionpbx-music-default \
		fusionpbx-app-operator-panel

########################
#set permissions on dir
########################
find "/var/lib/fusionpbx" -type d -exec chmod 775 {} +
find "/var/lib/fusionpbx" -type f -exec chmod 664 {} +

###########################
#Optional APP PKGS installs
###########################
if [[ $adminer == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-adminer
fi

if [[ $backup == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-backup
fi

if [[ $call_broadcast == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-call-broadcast
fi

if [[ $call_center == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-call-center fusionpbx-app-call-center-active
		if [[ -f /root/.fs_src ]] ; then
			if [[ ! -f /usr/lib/freeswitch/mod/mod_callcenter ]] ; then
				echo " Requires freeswitch mod_callcenter "
			fi
		else
			apt-get -y --force-yes install freeswitch-mod-callcenter
		fi
fi

if [[ $call_flows == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-call-flows
fi

if [[ $conference_centers == "y" ]]; then
	apt-get -y --force-yes install freeswitch-mod-conference fusionpbx-app-conference-centers fusionpbx-app-conferences-active fusionpbx-app-meetings
		if [[ -f /root/.fs_src ]] ; then
			if [[ ! -f /usr/lib/freeswitch/mod/mod_conference ]] ; then
				echo " Requires freeswitch mod_conference "
			fi
		else
			apt-get -y --force-yes install freeswitch-mod-conference
		fi
fi

if [[ $conference == "y" ]]; then
	apt-get -y --force-yes install freeswitch-mod-conference fusionpbx-app-conferences fusionpbx-app-conferences-active fusionpbx-app-meetings
		if [[ -f /root/.fs_src ]] ; then
			if [[ ! -f /usr/lib/freeswitch/mod/mod_conference ]] ; then
				echo " Requires freeswitch mod_conference "
			fi
		else
			apt-get -y --force-yes install freeswitch-mod-conference
		fi
fi

if [[ $edit == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-edit
fi

if [[ $exec == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-exec
fi

if [[ $fax == "y" ]]; then
	apt-get -y --force-yes install ghostscript libreoffice-common fusionpbx-app-fax
fi

if [[ $fifo == "y" ]]; then
	apt-get -y --force-yes install freeswitch-mod-fifo fusionpbx-app-fifo fusionpbx-app-fifo-list
		if [[ -f /root/.fs_src ]] ; then
			if [[ ! -f /usr/lib/freeswitch/mod/mod_fifo ]] ; then
				echo " Requires freeswitch mod_fifo "
			fi
		else
			apt-get -y --force-yes install freeswitch-mod-fifo
		fi
fi

if [[ $hot_desk == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-hot-desking
fi

if [[ $services == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-services
fi

if [[ $sql_query == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-sql-query
fi

if [[ $traffic_graph == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-traffic-graph
fi

if [[ $aastra == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-devices fusionpbx-app-provision fusionpbx-provisioning-template-aastra  && mkdir -p /etc/fusionpbx/resources/templates/provision && cp -rp /usr/share/examples/fusionpbx/resources/templates/provision/aastra /etc/fusionpbx/resources/templates/provision/
fi

if [[ $atcom == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-devices fusionpbx-app-provision fusionpbx-provisioning-template-atcom  && mkdir -p /etc/fusionpbx/resources/templates/provision && cp -rp /usr/share/examples/fusionpbx/resources/templates/provision/atcom /etc/fusionpbx/resources/templates/provision/
fi

if [[ $cisco == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-devices fusionpbx-app-provision fusionpbx-provisioning-template-cisco && mkdir -p /etc/fusionpbx/resources/templates/provision && cp -rp /usr/share/examples/fusionpbx/resources/templates/provision/cisco /etc/fusionpbx/resources/templates/provision/
fi

if [[ $grandstream == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-devices fusionpbx-app-provision fusionpbx-provisioning-template-grandstream && mkdir -p /etc/fusionpbx/resources/templates/provision && cp -rp /usr/share/examples/fusionpbx/resources/templates/provision/grandstream /etc/fusionpbx/resources/templates/provision/
fi

if [[ $linksys == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-devices fusionpbx-app-provision fusionpbx-provisioning-template-linksys  && mkdir -p /etc/fusionpbx/resources/templates/provision && cp -rp /usr/share/examples/fusionpbx/resources/templates/provision/linksys /etc/fusionpbx/resources/templates/provision/
fi

if [[ $panasonic == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-devices fusionpbx-app-provision fusionpbx-provisioning-template-panasonic  && mkdir -p /etc/fusionpbx/resources/templates/provision && cp -rp /usr/share/examples/fusionpbx/resources/templates/provision/panasonic /etc/fusionpbx/resources/templates/provision/
fi

if [[ $polycom == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-devices fusionpbx-app-provision fusionpbx-provisioning-template-polycom && mkdir -p /etc/fusionpbx/resources/templates/provision && cp -rp /usr/share/examples/fusionpbx/resources/templates/provision/polycom /etc/fusionpbx/resources/templates/provision/
fi

if [[ $snom == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-devices fusionpbx-app-provision fusionpbx-provisioning-template-snom && mkdir -p /etc/fusionpbx/resources/templates/provision && cp -rp /usr/share/examples/fusionpbx/resources/templates/provision/snom /etc/fusionpbx/resources/templates/provision/
fi

if [[ $yealink == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-devices fusionpbx-app-provision fusionpbx-provisioning-template-yealink && mkdir -p /etc/fusionpbx/resources/templates/provision && cp -rp /usr/share/examples/fusionpbx/resources/templates/provision/yealink /etc/fusionpbx/resources/templates/provision/
fi

if [[ $verto == "y" ]]; then
	apt-get -y --force-yes install freeswitch-mod-verto
fi

if [[ $accessible_theme == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-theme-accessible
fi

if [[ $classic_theme == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-theme-classic
fi

if [[ $default_theme == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-theme-default
fi

if [[ $minimized_theme == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-theme-minimized
fi

if [[ $all == "y" ]]; then
	apt-get -y --force-yes install fusionpbx-app-adminer fusionpbx-app-backup fusionpbx-app-call-broadcast  \
		fusionpbx-app-call-center fusionpbx-app-call-center-active fusionpbx-app-call-flows fusionpbx-app-conference-centers \
		fusionpbx-app-conferences-active fusionpbx-app-emails fusionpbx-app-meetings fusionpbx-app-conferences \
		fusionpbx-app-edit fusionpbx-app-exec fusionpbx-app-fifo fusionpbx-app-fifo-list ghostscript libreoffice-common \
		fusionpbx-app-fax fusionpbx-app-hot-desking fusionpbx-app-phrases fusionpbx-app-services \
		fusionpbx-app-sql-query fusionpbx-app-traffic-graph fusionpbx-app-devices fusionpbx-app-provision \
		fusionpbx-provisioning-template-aastra fusionpbx-provisioning-template-atcom fusionpbx-provisioning-template-cisco \
		fusionpbx-provisioning-template-grandstream fusionpbx-provisioning-template-linksys fusionpbx-provisioning-template-panasonic \
		fusionpbx-provisioning-template-polycom fusionpbx-provisioning-template-snom fusionpbx-provisioning-template-yealink \
		fusionpbx-theme-accessible fusionpbx-theme-classic fusionpbx-theme-default fusionpbx-theme-minimized \
		&& mkdir -p /etc/fusionpbx/resources/templates/provision && cp -rp /usr/share/examples/fusionpbx/resources/templates/provision/* /etc/fusionpbx/resources/templates/provision/

	if [[ -f /root/.fs_src ]] ; then
		echo " Requires freeswitch mod_callcenter mod_conference mod_fifo mod_rtmp mod_dingaling "
	else
		apt-get -y --force-yes install freeswitch-mod-callcenter freeswitch-mod-conference freeswitch-mod-fifo freeswitch-mod-rtmp
	fi
fi
########################################
#----end of fusion pbx pkgs install----
########################################

###############################################################
#restart of freeswitch/nginx/php for fusionpbx first time setup
###############################################################
for i in freeswitch nginx php5-fpm ;do service "${i}" restart >/dev/null 2>&1 ; done

###################################
#Install postgresql-client option
###################################
if [[ $postgresql_client == "y" ]]; then
#####################################################
# add in postgresql 9.4 repo for x86 x86-64 bit pkgs
#####################################################
	case $(uname -m) in x86_64|i[4-6]86)
	lsb_release -c |grep -i wheezy > /dev/null
	if [ $? -eq 0 ]; then
		cat > "/etc/apt/sources.list.d/pgsql-pgdg.list" << DELIM
		deb http://apt.postgresql.org/pub/repos/apt/ wheezy-pgdg main
DELIM
	else
		cat > "/etc/apt/sources.list.d/pgsql-pgdg.list" << DELIM
		deb http://apt.postgresql.org/pub/repos/apt/ jessie-pgdg main
DELIM
	fi
	####################
	#add pgsql repo key
	####################
	wget --quiet -O - http://apt.postgresql.org/pub/repos/apt/ACCC4CF8.asc | apt-key add -

	###############################################
	#run repo update after adding in a new repo....
	###############################################
	apt-get update

	############################
	#Install and configure PGSQL
	############################
	for i in postgresql-client-9.4 php5-pgsql ;do apt-get -y install "${i}"; done
	service php5-fpm restart
	esac
	clear

	##########################################
	#Install and configure PGSQL 9.1 for armhf
	##########################################
	case $(uname -m) in armv7l)
	echo "no arm deb pkgs for pgsql postgresql-client-9.3"
	echo "postgresql-client-9.1 is being installed"
	for i in postgresql-client-9.1 php5-pgsql ;do apt-get -y install "${i}"; done
	esac

	##########################################################
	# Goto gui configure statement
	##########################################################
	echo
	echo " The $wui_name install has finished...  "
	echo
	echo " Now Waiting on you to finish the installation via web browser "
	echo
	printf '	Please open a web-browser to http://'; ip -f inet addr show dev eth0 | sed -n 's/^ *inet *\([.0-9]*\).*/\1/p'
	cat << DELIM
	Or the Doamin name assigned to the machine like http://"$(hostname)".yourdomin.com
	On the First configuration page of the web user interface.
	Please Select the PostgreSQL option in the pull-down menu as your Database
	Also Please fill in the SuperUser Name and Password fields.
	On the Second Configuration Page of the web user intercae please fill in the following fields:
	Server: Use the IP or Doamin name assigned to the remote postgresql database server machine
	Port: use the port for the remote postgresql server
	Database Name: "$db_name"
	Database Username: "$db_user_name"
	Database Password: "$db_user_passwd"
	Create Database Username: Database_Superuser_Name of the remote postgresql server
	Create Database Password: Database_Superuser_password of the remote postgresql server
DELIM
fi

#################################################
#-----install & configure basic postgresql-server
#################################################
if [[ $postgresql_server == "y" ]]; then
#####################################################
# add in postgresql 9.4 repo for x86 x86-64 bit pkgs
#####################################################
	case $(uname -m) in x86_64|i[4-6]86)
	lsb_release -c |grep -i wheezy > /dev/null
	if [ $? -eq 0 ]; then
		cat > "/etc/apt/sources.list.d/pgsql-pgdg.list" << DELIM
		deb http://apt.postgresql.org/pub/repos/apt/ wheezy-pgdg main
DELIM
	else
		cat > "/etc/apt/sources.list.d/pgsql-pgdg.list" << DELIM
		deb http://apt.postgresql.org/pub/repos/apt/ jessie-pgdg main
DELIM
	fi

	####################
	#add pgsql repo key
	####################
	wget --quiet -O - http://apt.postgresql.org/pub/repos/apt/ACCC4CF8.asc | apt-key add -

	###############################################
	#run repo update after adding in a new repo....
	################################################
	apt-get update

	#################################
	#Install and configure PGSQL 9.4
	#################################
	for i in postgresql-9.4 php5-pgsql ;do apt-get -y install "${i}"; done
	service php5-fpm restart
	esac

	##########################################
	#Install and configure PGSQL 9.1 for armhf
	##########################################
	case $(uname -m) in armv7l)
	echo "no arm deb pkgs for pgsql postgresql-client-9.4"
	echo "postgresql-client-9.1 is being installed"
	for i in postgresql-client-9.1 php5-pgsql ;do apt-get -y install "${i}"; done
	esac

	#########################################################
	#Adding a SuperUser and Password for Postgresql database.
	#########################################################
	su -l postgres -c "psql -c \"create role $pgsql_admin with superuser login password '$pgsql_admin_passwd'\""
	clear

	##########################################################
	# Goto gui configure statement
	##########################################################
	echo
	echo " The $wui_name install has finished...  "
	echo
	echo " Now Waiting on you to finish the installation via web browser "
	echo
	printf 'Please open a web browser to http://'; ip -f inet addr show dev eth0 | sed -n 's/^ *inet *\([.0-9]*\).*/\1/p'
	cat << DELIM
	Or the Doamin name asigned to the machine like http://"$(hostname)".yourdomin.com.
	On the First configuration page of the web user interface
	Please Select the PostgreSQL option in the pull-down menu as your Database
	Also Please fill in the SuperUser Name and Password fields.
	On the Second Configuration Page of the web user interface please fill in the following fields:
	Database Name: "$db_name"
	Database Username: "$db_user_name"
	Database Password: "$db_user_passwd"
	Create Database Username: "$pgsql_admin"
	Create Database Password: "$pgsql_admin_passwd"
DELIM
else
	clear
	echo
	echo " The $wui_name install has finished...  "
	echo
	echo " Now Waiting on you to finish the installation via web browser "
	echo
	printf ' Please open a web-browser to http://'; ip -f inet addr show dev eth0 | sed -n 's/^ *inet *\([.0-9]*\).*/\1/p'
	cat << DELIM
	or the Domain name asigned to the machine like http://"$(hostname)".yourdomin.com.
	On the First Configuration page of the web user interface "$wui_name".
	Also Please fill in the SuperUser Name and Password fields.
	Freeswitch & FusionPBX Web User Interface Installation Completed
	Now you can configure FreeSWITCH using the FusionPBX web user interface
DELIM
fi

echo -ne " The Install will clean up the last bit of permissions when "
echo 
echo " you finish entering the required information and return here. "
echo
echo " Waiting on /etc/$wui_name/config.php "
while [ ! -e /etc/$wui_name/config.php ]
do
	echo -ne '.'
	sleep 1
done
echo
echo " /etc/$wui_name/config.php Found!"
echo
echo "   Waiting 60 more seconds to be sure the database is fully populated..... "
SLEEPTIME=0
while [ "$SLEEPTIME" -lt 60 ]
do
	echo -ne '.'
	sleep 1
	let "SLEEPTIME = $SLEEPTIME + 1"
done

##################################################
#configuring freeswitch to start with new layout.
##################################################
#Freeswitch layout for FHS with fusionpbx
##################################################
cat > '/etc/default/freeswitch' << DELIM
CONFDIR="/etc/fusionpbx/switch/conf"
#
# Uncooment extra lines and make sure to add cut and paste them to the DAEMON_ARGS 
# Options to control locations of files: 
fs_conf="/etc/fusionpbx/switch/conf"
fs_db="/var/lib/freeswitch/db"
fs_log="/var/log/freeswitch"
fs_recordings="/var/lib/fusionpbx/recordings"
fs_run="/var/run/freeswitch"
fs_scripts="/var/lib/fusionpbx/scripts"
fs_storage="/var/lib/fusionpbx/storage"
fs_usr=freeswitch
fs_grp=\$fs_usr
#
#These are the optional arguments you can pass to freeswitch: (add options to fs_options line)
# -nf                    -- no forking
# -reincarnate           -- restart the switch on an uncontrolled exit
# -reincarnate-reexec    -- run execv on a restart (helpful for upgrades)
# -u [user]              -- specify user to switch to
# -g [group]             -- specify group to switch to
# -core                  -- dump cores
# -rp                    -- enable high(realtime) priority settings
# -lp                    -- enable low priority settings
# -np                    -- enable normal priority settings
# -vg                    -- run under valgrind
# -nosql                 -- disable internal sql scoreboard
# -heavy-timer           -- Heavy Timer, possibly more accurate but at a cost
# -nonat                 -- disable auto nat detection
# -nonatmap              -- disable auto nat port mapping
# -nocal                 -- disable clock calibration
# -nort                  -- disable clock clock_realtime
# -stop                  -- stop freeswitch
# -nc                    -- do not output to a console and background
# -ncwait                -- do not output to a console and background but wait until the system is ready before exiting (implies -nc)
# -c                     -- output to a console and stay in the foreground
fs_options="-nc -rp -reincarnate"
#
# Reads in the arguments into 1 line command
DAEMON_ARGS="-u \$fs_usr -g \$fs_grp -conf \$fs_conf -db \$fs_db -log \$fs_log -scripts \$fs_scripts -run \$fs_run -storage \$fs_storage -recordings \$fs_recordings \$fs_options"
DELIM

#################################################################
#restartng services with thefusionpbx freeswitch fhs dir layoout
#################################################################
echo " Restarting freeswitch for changes to take effect...."
service freeswitch restart

##################################
#fixing permissions for sqlite db
##################################
find "/var/lib/fusionpbx/db" -type d -exec chmod 777 {} +
find "/var/lib/fusionpbx/db" -type f -exec chmod 666 {} +

########################################################
# Setting up Music on Hold based on fusion FHS
########################################################
mkdir /usr/share/freeswitch/sounds/music
#Linking moh dir so freeswitch can read in the moh files
ln -s /var/lib/fusionpbx/sounds/music /usr/share/freeswitch/sounds/music/fusionpbx
#Linking custom dir so freeswitch can read in the custom sounds files
ln -s /var/lib/fusionpbx/sounds/custom /usr/share/freeswitch/sounds/
# setting permissions on the dir
chown -R www-data:www-data /var/lib/fusionpbx/sounds

#########################################################
#-----Installing Fail2Ban/monit Protection services------
##########################################################
# SEE http://wiki.freeswitch.org/wiki/Fail2ban
##########################################################
#Fail2ban
##########
for i in fail2ban monit ;do apt-get -y install "${i}" ; done

#################################################################################################
#Taken From http://wiki.fusionpbx.com/index.php?title=Monit and edited to work with debian pkgs.
#################################################################################################
#Adding Monit to keep freeswitch running.
##########################################
cat > "/etc/monit/conf.d/freeswitch"  <<DELIM
set daemon 60
set logfile syslog facility log_daemon

check process freeswitch with pidfile /var/run/freeswitch/freeswitch.pid
restart program = "/etc/init.d/freeswitch restart"
start program = "/etc/init.d/freeswitch start"
stop program = "/etc/init.d/freeswitch stop"

DELIM

#############################################
#Setting up Fail2ban freeswitch config files.
#############################################
cat > "/etc/fail2ban/filter.d/freeswitch.conf" <<DELIM
##############################
# Fail2Ban configuration file
##############################

[Definition]

failregex = ^\.\d+ \[WARNING\] sofia_reg\.c:\d+ SIP auth (failure|challenge) \((REGISTER|INVITE)\) on sofia profile \'[^']+\' for \[.*\] from ip <HOST>$
            ^\.\d+ \[WARNING\] sofia_reg\.c:\d+ Can't find user \[\d+@\d+\.\d+\.\d+\.\d+\] from <HOST>$

ignoreregex =
DELIM

cat > /etc/fail2ban/filter.d/freeswitch-dos.conf  <<DELIM

# Fail2Ban DOS configuration file

[Definition]

failregex = \[WARNING\] sofia_reg.c:\d+ SIP auth challenge \(REGISTER\) on sofia profile \'\w+\' for \[.*\] from ip <HOST>

ignoreregex =
DELIM

cat >> "/etc/fail2ban/jail.local" <<DELIM
[freeswitch-tcp]
enabled  = true
port     = 5060,5061,5080,5081
protocol = tcp
filter   = freeswitch
logpath  = /var/log/freeswitch/freeswitch.log
action   = iptables-allports[name=freeswitch-tcp, protocol=all]
maxretry = 5
findtime = 600
bantime  = 600

[freeswitch-udp]
enabled  = true
port     = 5060,5061,5080,5081
protocol = udp
filter   = freeswitch
logpath  = /var/log/freeswitch/freeswitch.log
action   = iptables-allports[name=freeswitch-udp, protocol=all]
maxretry = 5
findtime = 600
bantime  = 600

[freeswitch-dos]
enabled = true
port = 5060,5061,5080,5081
protocol = udp
filter = freeswitch-dos
logpath = /var/log/freeswitch/freeswitch.log
action = iptables-allports[name=freeswitch-dos, protocol=all]
maxretry = 50
findtime = 30
bantime  = 6000
DELIM
###############################################################
#Pulled From http://wiki.fusionpbx.com/index.php?title=Fail2Ban
###############################################################
# Adding fusionpbx to fail2ban
###############################
cat > "/etc/fail2ban/filter.d/fusionpbx.conf"  <<DELIM
##############################
# Fail2Ban configuration file
##############################

[Definition]
failregex = .* fusionpbx: \[<HOST>\] authentication failed for
          = .* fusionpbx: \[<HOST>\] provision attempt bad password for

ignoreregex =
DELIM

cat >> /etc/fail2ban/jail.local  <<DELIM

[fusionpbx]
enabled  = true
port     = 80,443
protocol = tcp
filter   = fusionpbx
logpath  = /var/log/auth.log
action   = iptables-allports[name=fusionpbx, protocol=all]

maxretry = 5
findtime = 600
bantime  = 600
DELIM

cat > "/etc/fail2ban/filter.d/fusionpbx-inbound.conf" <<DELIM
##############################
# Fail2Ban configuration file
###############################
# inbound route - 404 not found
###############################

[Definition]

# Option:  failregex
# Notes.:  regex to match the password failures messages in the logfile. The
#          host must be matched by a group named "host". The tag "<HOST>" can
#          be used for standard IP/hostname matching and is only an alias for
#          (?:::f{4,6}:)?(?P<host>[\w\-.^_]+)
# Values:  TEXT
#
#failregex = [hostname] FusionPBX: \[<HOST>\] authentication failed
#[hostname] variable doesn't seem to work in every case. Do this instead:
failregex = 404 not found <HOST>

# Option:  ignoreregex
# Notes.:  regex to ignore. If this regex matches, the line is ignored.
# Values:  TEXT
#
ignoreregex =

DELIM

cat >> /etc/fail2ban/jail.local  <<DELIM

[fusionpbx-inbound]
enabled  = true
port 	= 5080
protocol = udp
filter   = fusionpbx-inbound
logpath  = /var/log/freeswitch/freeswitch.log
action   = iptables-allports[name=fusionpbx-inbound, protocol=all]
#sendmail-whois[name=fusionpbx-inbound, dest=root, sender=fail2ban@example.org] #no smtp server installed
maxretry = 5
findtime = 300
bantime  = 3600
DELIM

####################
#restarting fail2ban
####################
service fail2ban restart

#########################################################
#Turning off Repeated Msg Reduction in /etc/rsyslog.conf"
#########################################################
sed -i 's/RepeatedMsgReduction\ on/RepeatedMsgReduction\ off/' /etc/rsyslog.conf 

############################
# Restarting rsyslog service
############################
service rsyslog restart

sed -i /usr/bin/fail2ban-client -e s,^\.setInputCmd\(c\),'time.sleep\(0\.1\)\n\t\t\tbeautifier.setInputCmd\(c\)',

##############################
#Restarting Nginx and PHP FPM
##############################
for i in freeswitch fail2ban
do service "${i}" restart  > /dev/null 2>&1
done

############################################################
# see http://wiki.fusionpbx.com/index.php?title=RotateFSLogs
############################################################
cat > "/etc/cron.daily/freeswitch_log_rotation" <<DELIM
#!/usr/bin/bash

#number of days of logs to keep
NUMBERDAYS="$keep_logs"
FSPATH="/var/log/freeswitch"

freeswitch_cli -x "fsctl send_sighup" |grep '+OK' >/tmp/rotateFSlogs

if [ $? -eq 0 ]; then
       #-cmin 2 could bite us (leave some files uncompressed, eg 11M auto-rotate). Maybe -1440 is better?
       find "$FSPATH" -name "freeswitch.log.*" -cmin -2 -exec gzip {} \;
       find "$FSPATH" -name "freeswitch.log.*.gz" "-mtime" "+$NUMBERDAYS" -exec rm {} \;
       chown freeswitch:freeswitch "$FSPATH"/freeswitch.log
       chmod 664 "$FSPATH"/freeswitch.log
       logger FreeSWITCH Logs rotated
       rm /tmp/<<DELIM
else
       logger FreeSWITCH Log Rotation Script FAILED
       mail -s '$HOST FS Log Rotate Error' root < /tmp/<<DELIM
       rm /tmp/<<DELIM
fi

DELIM

chmod 664 /etc/cron.daily/freeswitch_log_rotation

###########################################################
# restarting services after fail2ban/monit services install
###########################################################
for i in php5-fpm niginx monit fail2ban freeswitch ;do service "${i}" restart  >/dev/null 2>&1 ; done

#########################
#end of fusionpbx install
#########################

########################################################
#---Setup scanner blocking service in iptables----------
########################################################
echo "blocking scanners via iptables"
iptables -I INPUT -j DROP -p udp --dport 5060 -m string --string "friendly-scanner" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5061 -m string --string "friendly-scanner" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5062 -m string --string "friendly-scanner" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5063 -m string --string "friendly-scanner" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5064 -m string --string "friendly-scanner" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5065 -m string --string "friendly-scanner" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5066 -m string --string "friendly-scanner" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5067 -m string --string "friendly-scanner" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5068 -m string --string "friendly-scanner" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5069 -m string --string "friendly-scanner" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5080 -m string --string "friendly-scanner" --algo bm

##################################
# Option to disable xml_cdr files
# Logs cdr only to database. 
##################################
if [[ $xml_cdr_files == "y" ]]; then
	sed -i "$WWW_PATH"/"$wui_name"/app/vars/app_defaults.php -e 's#{"var_name":"xml_cdr_archive","var_value":"dir","var_cat":"Defaults","var_enabled":"true","var_description":""}#{"var_name":"xml_cdr_archive","var_value":"none","var_cat":"Defaults","var_enabled":"true","var_description":""}#'
fi

##########################################
# option to disable some loging execpt for
# warnings and errors
###########################################
if [[ $logging_level == "y" ]]; then
	sed -i /usr/share/examples/fusionpbx/resources/templates/conf/autoload_configs/logfile.conf.xml -e 's#<map name="all" value="debug,info,notice,warning,err,crit,alert"/>#<map name="all" value="warning,err,crit,alert"/>#'
fi

##############################
#Set a reboot if Kernel Panic
##############################
cat > /etc/sysctl.conf << DELIM
kernel.panic = 10
DELIM

####################################
# Set fs to run in a tempfs ramdrive
####################################
cat >> /etc/fstab << DELIM
tmpfs	/tmp	tmpfs	defaults	0	0
tmpfs	/var/lib/freeswitch/db	tmpfs	defaults	0	0
tmpfs   /var/tmp	tmpfs	defaults	0	0
DELIM

###########################################################
#Ajenti admin portal. Makes maintaining the system easier.
###########################################################
#ADD Ajenti repo & ajenti
##########################
if [[ $install_ajenti == "y" ]]; then
	echo "Installing Ajenti Admin Portal"
	cat > "/etc/apt/sources.list.d/ajenti.list" <<DELIM
	deb http://repo.ajenti.org/debian main main debian
DELIM

######################
# add ajenti repo key
######################
wget http://repo.ajenti.org/debian/key -O- | apt-key add -

#######################
# install ajenti
#######################
apt-get update &> /dev/null && apt-get -y install ajenti
fi

echo " ########################################################################################## "
echo " # The Freeswitch / Fusionpbx Install is now complete and your system is ready for use... # "
echo " ########################################################################################## "
echo " #                   Please send any feed back to Ian Oaks ian@                      # "
echo " ########################################################################################## "

