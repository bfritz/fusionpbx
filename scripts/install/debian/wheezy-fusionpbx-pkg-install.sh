#!/bin/bash

LICENSE=$( cat << DELIM
#------------------------------------------------------------------------------
#
# "THE WISH LIST LICENSE" (version 1)
# This is the Wish List Acceptance Factor (WL) License.  
# richarddotneeseatgmaildotcom  wrote this file.  As long as you retain this
# notice you can do whatever you want with it. If you appreciate the work,
# please consider purchasing something from my wife's wishlist. That pays
# bigger dividends to this coder than anything else I can think of ;).  It also
# keeps her happy while she's being ignored; so I can work on this stuff.
#   Richard Neese
#
# latest wishlist: http://www.voyagepbx.com/wishlist.html
#
# Credit: Based off of the BEER-WARE LICENSE (REVISION 42) by Poul-Henning Kamp
#
#------------------------------------------------------------------------------
DELIM
)

#<------Start Edit HERE--------->
#INSTALL Freeswitch 
#DO_FS_INSTALL="ALL" # This is a metapackage which recommends or suggests all packaged FreeSWITCH modules.
#DO_FS_INSTALL="BARE" # This is a metapackage which depends on the packages needed for a very bare FreeSWITCH install.
#DO_FS_INSTALL="CODECS" # This is a metapackage which depends on the packages needed to install most FreeSWITCH codecs.
DO_FS_INSTALL="CUSTOM" # This is a custom install for most use.
#DO_FS_INSTALL="DEFAULT" # This is a metapackage which depends on the packages needed for a reasonably basic FreeSWITCH install.
#DO_FS_INSTALL="SORBET" # This is a metapackage which recommends most packaged FreeSWITCH modules except a few which aren't recommended.
#DO_FS_INSTALL="VANILLA" # This is a metapackage which depends on the packages needed for running the FreeSWITCH vanilla example configuration.

#Dahdi Codec (Requires freetdm/dahdi)(future Use not currently working)
#DO_DAHDI_CODEC=n

#Install Freeswitch Sounds & Music on hold
DO_FS_SOUNDS=y

# to start FreeSWITCH with -nonat option set SETNONAT to y
# Set to yes if on public IP
SETNONAT=n

# use freedtm/dahdi? y/n (future Use not currently working)
#DO_DAHDI=n

#Use Fusionpbx Stable debian pkg. 
#(Currently the Fusionpkx Stable does not work on wheezy)
# You should use the fusionpbx dev pkg for now
DO_INST_FPBX_STABLE=n

# for sqlite set s. for postgresql 9.1 set p
SQLITEPGSQL=s

#<------Stop Edit Here--------> 

#FQDN
FQDN=$(hostname -f)

#Freeswitch Dir's / Fusionpbx Dir's Structure
FS_BIN="/usr/bin"
# Freeswitch logs dir
FS_LOG="/var/log/freeswitch"
#freeswitch db/recording/storage/voicemail/fax dir
FS_DB="/var/lib/freeswitch/db"
FS_REC="/var/lib/freeswitch/recordings"
FS_STOR="/var/lib/freeswitch/storage"
#freeswitch modules dir
FS_MOD="/usr/lib/freeswitch/mod"
#defalt configs / Grammar / Language/scripts
FS_DFLT_CONF="/usr/share/freeswitch/conf"
FS_GRAM="/usr/share/freeswitch/grammar"
FS_LANG="/usr/share/freeswitch/lang"
FS_SCRIPTS="/usr/share/freeswitch/scripts"
#Freeswitch active config files
FS_ACT_CONF="/etc/freeswitch"
#Freeswitch Sounds
FS_SNDS="/usr/share/freeswitch/sounds"
#Nginx 
WWW_PATH="/usr/share/nginx/www" #debian nginx default
GUI_NAME=fusionpbx

#---------------------
# OS ENVIRONMENT CHECKS
#---------------------
#check for root
if [ $EUID -ne 0 ]; then
   /bin/echo "This script must be run as root" 1>&2
   exit 1
fi
echo "Good, you are root."

if [ ! -s /usr/bin/lsb_release ]; then
	/bin/echo "Tell your upstream distro to include lsb_release"
	/bin/echo
	apt-get update && apt-get -y install lsb-release
fi

#check for internet connection
/usr/bin/wget -q --tries=10 --timeout=5 http://www.google.com -O /tmp/index.google &> /dev/null
if [ ! -s /tmp/index.google ];then
	echo "No Internet connection. Exiting."
	/bin/rm /tmp/index.google
	exit 1
else
	echo "Internet connection is working, continuing!"
	/bin/rm /tmp/index.google
fi

lsb_release -c |grep -i wheezy > /dev/null
if [ $? -eq 0 ]; then
	DISTRO=wheezy
	/bin/echo "OK you're running Debian 7 [wheezy].  This script is"
	/bin/echo "a work in progress.  It is  recommended that you try it"
	/bin/echo "at this time. And Report Any Issues to r.neese@gmail.com"
	/bin/echo 
	CONTINUE=YES
fi

case "$CONTINUE" in
	[yY]*)
		/bin/echo "Ok, this doesn't always work..,"
		/bin/echo "  but we'll give it a go."
	;;

	*)
		/bin/echo "Quitting. Reqires Debian Wheezy"
		exit 1
	;;
esac

#add voyagepbx-repo (temp Repo until freeswitch gets a repo working for x86)
/bin/echo "Adding voyagepbx repo"
/bin/cat > /etc/apt/sources.list.d/voyagepbx.list <<"DELIM"
deb http://repo.voyagepbx.com/ wheezy main
deb-src http://repo.voyagepbx.com/ wheezy main
DELIM
/bin/echo " Done"

#Adding freeswitch repo
#echo "Adding freeswitch pkg repo"
#/bin/cat > /etc/apt/sources.list.d/freeswitch.list <<"DELIM"
#deb http://files.freeswitch.org/repo/deb/debian/ wheezy main
#deb-src http://files.freeswitch.org/repo/deb/debian/ wheezy main 
#DELIM
#/bin/echo " Done"

#----------------
#updating OS and installed pre deps
#----------------
echo "Updating base os install"
apt-get update > /dev/null
apt-get upgrade -y

#------------------
# Installing Freeswitch Deps
#------------------
#install Freeswitch Deps
/bin/echo
/bin/echo "installing freeswitch deps"
/usr/bin/apt-get -y install vim unzip libncurses5 libjpeg8 libjpeg62 screen htop pkg-config bzip2 curl libtiff5 libtiff-tools \
						ntp time bison libssl1.0.0 nano aptitude \
						autotalent ladspa-sdk tap-plugins swh-plugins libgsm1 libfftw3-3 python libpython2.7 \
						perl libperl5.14 scons libgdbm3 libdb5.1 libpq5 unixodbc uuid ngrep libusb-1.0-0 gettext libvlc5 \
						rsyslog sox flac

#---------------
# Dahdi Opinions (for future use) 
#---------------
#if [ $DO_DAHDI == "y" ]; then
#		/bin/echo echo "Installing Dahdi and kernel headers"
		#add stuff for free_tdm/dahdi
#		/usr/bin/apt-get -y install linux-headers-`uname -r`
		#add the headers so dahdi can build the modules...
#		/usr/bin/apt-get -y install dahdi
#fi						

#----------------
# Installing Freeswitch All/bare/codecs/default/sorbet/vanilla/custom
#----------------

if [ $DO_FS_INSTALL == "ALL" ]; then
/usr/bin/apt-get -y --force-yes install	freeswitch-meta-all
fi

if [ $DO_FS_INSTALL == "BARE" ]; then
/usr/bin/apt-get -y --force-yes install	freeswitch-meta-bare
fi

if [ $DO_FS_INSTALL == "CODECS" ]; then
/usr/bin/apt-get -y --force-yes install	freeswitch-meta-codecs
fi

if [ $DO_FS_INSTALL == "DEFAULT" ]; then
/usr/bin/apt-get -y --force-yes install	freeswitch-meta-default
fi

if [ $DO_FS_INSTALL == "SORBET" ]; then
/usr/bin/apt-get -y --force-yes install	freeswitch-meta-sorbet
fi

if [ $DO_FS_INSTALL == "VANILLA" ]; then
/usr/bin/apt-get -y --force-yes install	freeswitch-meta-vanilla
fi

#install for Fullest use. (Does not include Freeswitch Sounds OR Music On Hold)
# Please set FREESWITCH_SOUNDS=y to install sounds.
if [ $DO_FS_INSTALL == "CUSTOM" ]; then
/usr/bin/apt-get -y --force-yes install freeswitch freeswitch-conf-vanilla freeswitch-doc freeswitch-init freeswitch-lang freeswitch-lang-en freeswitch-meta-all \
				freeswitch-mod-abstraction freeswitch-mod-amr freeswitch-mod-amrwb freeswitch-mod-avmd freeswitch-mod-blacklist freeswitch-mod-bv freeswitch-mod-callcenter \
				freeswitch-mod-cdr-sqlite freeswitch-mod-celt freeswitch-mod-cidlookup freeswitch-mod-codec2 freeswitch-mod-commands freeswitch-mod-conference \
				freeswitch-mod-console freeswitch-mod-curl freeswitch-mod-db freeswitch-mod-dialplan-directory freeswitch-mod-dialplan-xml freeswitch-mod-dingaling \
				freeswitch-mod-directory freeswitch-mod-distributor freeswitch-mod-dptools freeswitch-mod-easyroute	freeswitch-mod-enum freeswitch-mod-esf freeswitch-mod-esl \
				freeswitch-mod-event-multicast freeswitch-mod-event-socket freeswitch-mod-event-test freeswitch-mod-expr freeswitch-mod-fifo freeswitch-mod-flite freeswitch-mod-fsk \
				freeswitch-mod-fsv freeswitch-mod-g723-1 freeswitch-mod-g729 freeswitch-mod-h26x freeswitch-mod-hash freeswitch-mod-html5 freeswitch-mod-httapi freeswitch-mod-http-cache \
				freeswitch-mod-isac freeswitch-mod-json-cdr freeswitch-mod-ladspa freeswitch-mod-lcr freeswitch-mod-ldap freeswitch-mod-local-stream freeswitch-mod-logfile \
				freeswitch-mod-loopback freeswitch-mod-lua freeswitch-mod-memcache freeswitch-mod-mp4v freeswitch-mod-native-file freeswitch-mod-nibblebill freeswitch-mod-opus \
				freeswitch-mod-oreka freeswitch-mod-perl freeswitch-mod-pocketsphinx freeswitch-mod-portaudio freeswitch-mod-portaudio-stream freeswitch-mod-posix-timer \
				freeswitch-mod-python freeswitch-mod-random freeswitch-mod-rss freeswitch-mod-rtmp freeswitch-mod-say-en freeswitch-mod-shell-stream freeswitch-mod-silk \
				freeswitch-mod-skinny freeswitch-mod-sms freeswitch-mod-snapshot freeswitch-mod-sndfile freeswitch-mod-snom freeswitch-mod-sofia freeswitch-mod-soundtouch \
				freeswitch-mod-spandsp freeswitch-mod-speex freeswitch-mod-spy freeswitch-mod-syslog freeswitch-mod-theora freeswitch-mod-timerfd freeswitch-mod-tone-stream \
				freeswitch-mod-tts-commandline freeswitch-mod-unimrcp freeswitch-mod-valet-parking freeswitch-mod-vlc freeswitch-mod-vmd freeswitch-mod-voicemail \
				freeswitch-mod-voicemail-ivr freeswitch-mod-vp8 freeswitch-mod-xml-rpc freeswitch-mod-xml-scgi freeswitch-systemd freeswitch-sysvinit libfreeswitch1
fi

if [ $DO_FS_SOUNDS == "y" ]; then
/bin/echo "installing Freeswitch 8/16/32/48k sounds from Freeswitch sounds and music pkgs"
/usr/bin/apt-get -y --force-yes install freeswitch-sounds freeswitch-music
fi

#Requires Dahdi
if [ $DO_DAHDI_CODEC == "y" ]; then
/bin/echo " Installing dahdi Codec"
/usr/bin/apt-get -y --force-yes freeswitch-mod-dahdi-codec
/bin/echo "Done"
fi
  
#Make the Active Conf Dir > /etc/freeswitch dir <
/bin/echo
/bin/echo "Making freeswitch active conf dir /etc/freeswitch"
/bin/echo
mkdir $FS_ACT_CONF

#Copy configs into Freeswitch active conf dir
/bin/echo
/bin/echo "installing freeswitch vanilla configs into the active conf dir"
/bin/echo
cp -rp $FS_DFLT_CONF/vanilla/* $FS_ACT_CONF
/bin/echo
/bin/echo "Done"

# FREESWITCH INIT reset group/user to www-data. Set run time arguments. set Working FS dir.

/bin/echo "Configuring /etc/init.d/freeswitch"

#DAEMON
/bin/sed -i /etc/init.d/freeswitch -e s,^DAEMON=.*,DAEMON=/usr/bin/freeswitch,

#USER
/bin/sed -i /etc/init.d/freeswitch -e s,^USER=freeswitch,USER=www-data,

#GROUP
/bin/sed -i /etc/init.d/freeswitch -e s,^GROUP=freeswitch,GROUP=www-data,

#DAEMON_ARGS
/bin/sed -i /etc/init.d/freeswitch -e s,'^DAEMON_ARGS=.*','DAEMON_ARGS="-rp -nc"',

#PIDFILE
/bin/sed -i /etc/init.d/freeswitch -e s,^PIDFILE=.*,PIDFILE=/var/run/freeswitch/\$NAME.pid,

#WORKDIR
/bin/sed -i /etc/init.d/freeswitch -e s,^WORKDIR=.*,WORKDIR=/var/lib/\$NAME,

#DAEMON_Optional ARGS
/bin/sed -i /etc/default/freeswitch -e s,'^DAEMON_OPTS=.*','DAEMON_OPTS="-rp"',

case "$SETNONAT" in
		[Yy]*)
			/bin/sed -i /etc/default/freeswitch -e s,'DAEMON_OPTS="-rp"','DAEMON_OPTS="-rp -nonat"',
			/bin/echo "init script set to start 'freeswitch -nc -rp -nonat'"
		;;

		*)
			/bin/echo "OK, not using -nonat option."
		;;
esac

#move the default extensions .noload
#We will leave them for reference.
/bin/echo "renaming default FreeSWITCH extensions .noload"
for i in /etc/freeswitch/directory/default/1*.xml;do mv $i $i.noload ; done

#Setting basic Freeswitch Permissions used for first time freeswitch run
/bin/echo
/bin/echo "removing 'other' permissions on freeswitch"
/bin/chmod -R o-rwx $FS_LOG $FS_LIB $FS_DB $FS_REC $FS_STOR $FS_SHARE $FS_GRAM $FS_LANG $FS_DFLT_CONF $FS_ACT_CONF $FS_SNDS
/bin/echo "setting FreeSWITCH / fusionpbx owned by www-dat.www-data"
/bin/chown -R www-data:www-data $FS_LOG $FS_DB $FS_SHARE $FS_REC $FS_STOR $FS_MOD $FS_DFLT_CONF $FS_ACT_CONF $FS_GRAM $FS_LANG $FS_SNDS
/bin/echo "FreeSWITCH directories now owned by www-data:www-data"
/usr/bin/find $FS_LOG $FS_LIB $FS_DB $FS_REC $FS_STOR $FS_SHARE $FS_GRAM $FS_LANG $FS_DFLT_CONF $FS_ACT_CONF $FS_SNDS -type d -exec /bin/chmod u=rwx,g=srwx,o= {} \;
#make sure FreeSWITCH directories have group write
/bin/echo "Setting Group Write for FreeSWITCH files"
/usr/bin/find $FS_LOG $FS_LIB $FS_DB $FS_REC $FS_STOR $FS_SHARE $FS_GRAM $FS_LANG $FS_DFLT_CONF $FS_ACT_CONF $FS_SNDS -type f -exec /bin/chmod g+w {} \;
#make sure FreeSWITCH files have group write
/bin/echo "Setting Group Write for FreeSWITCH directories"
/usr/bin/find $FS_LOG $FS_LIB $FS_DB $FS_REC $FS_STOR $FS_SHARE $FS_GRAM $FS_LANG $FS_DFLT_CONF $FS_ACT_CONF $FS_SNDS -type d -exec /bin/chmod g+w {} \;
/bin/echo
/bin/echo "FreeSWITCH directories now sticky group. This will cause any files created"
/bin/echo "  to default to the daemon group so FreeSWITCH can read them"
/bin/echo
/bin/echo " Stating Freeswitch"
/etc/init.d/freeswitch start
/bin/mkdir -p $FS_SCRPT $FS_HTDOCS
/bin/chmod -R o-rwx $FS_SCRPT $FS_HTDOCS
/bin/chown -R www-data:www-data $FS_SCRPT $FS_HTDOCS
/usr/bin/find $FS_SCRPT $FS_HTDOCS -type d -exec /bin/chmod u=rwx,g=srwx,o= {} \;
/usr/bin/find $FS_SCRPT $FS_HTDOCS -type f -exec /bin/chmod g+w {} \;
/usr/bin/find $FS_SCRPT $FS_HTDOCS -type d -exec /bin/chmod g+w {} \;
/bin/echo " Restating Freeswitch"
/etc/init.d/freeswitch restart

#----------------
#Fail2ban
#----------------
/usr/bin/apt-get -y install fail2ban
/bin/echo
/bin/sed -i -e s,'<param name="log-auth-failures" value="false"/>','<param name="log-auth-failures" value="true"/>', \
				/etc/freeswitch/sip_profiles/internal.xml

/bin/sed -i -e s,'<!-- *<param name="log-auth-failures" value="false"/>','<param name="log-auth-failures" value="true"/>', \
				-e s,'<param name="log-auth-failures" value="false"/> *-->','<param name="log-auth-failures" value="true"/>', \
				-e s,'<!--<param name="log-auth-failures" value="false"/>','<param name="log-auth-failures" value="true"/>', \
				-e s,'<param name="log-auth-failures" value="false"/>-->','<param name="log-auth-failures" value="true"/>', \
				/etc/freeswitch/sip_profiles/internal.xml

/bin/cat > /etc/fail2ban/filter.d/freeswitch.conf  <<"DELIM"

# Fail2Ban configuration file
#
# Author: Rupa SChomaker
#

[Definition]

# Option:  failregex
# Notes.:  regex to match the password failures messages in the logfile. The
#          host must be matched by a group named "host". The tag "<HOST>" can
#          be used for standard IP/hostname matching and is only an alias for
#          (?:::f{4,6}:)?(?P<host>[\w\-.^_]+)
# Values:  TEXT
#
failregex = \[WARNING\] sofia_reg.c:\d+ SIP auth failure \(REGISTER\) on sofia profile \'\w+\' for \[.*\] from ip <HOST>
            \[WARNING\] sofia_reg.c:\d+ SIP auth failure \(INVITE\) on sofia profile \'\w+\' for \[.*\] from ip <HOST>

# Option:  ignoreregex
# Notes.:  regex to ignore. If this regex matches, the line is ignored.
# Values:  TEXT
#
ignoreregex =
DELIM

/bin/cat > /etc/fail2ban/filter.d/freeswitch-dos.conf  <<"DELIM"

# Fail2Ban configuration file
#
# Author: 
#

[Definition]

# Option:  failregex
# Notes.:  regex to match the password failures messages in the logfile. The
#          host must be matched by a group named "host". The tag "<HOST>" can
#          be used for standard IP/hostname matching and is only an alias for
#          (?:::f{4,6}:)?(?P<host>[\w\-.^_]+)
# Values:  TEXT
#
failregex = \[WARNING\] sofia_reg.c:\d+ SIP auth challenge \(REGISTER\) on sofia profile \'\w+\' for \[.*\] from ip <HOST>

# Option:  ignoreregex
# Notes.:  regex to ignore. If this regex matches, the line is ignored.
# Values:  TEXT
#
ignoreregex =
DELIM

/bin/cat >> /etc/fail2ban/jail.local  <<'DELIM'
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
#          sendmail-whois[name=FreeSwitch, dest=root, sender=fail2ban@example.org] #no smtp server installed

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
#          sendmail-whois[name=FreeSwitch, dest=root, sender=fail2ban@example.org] #no smtp server installed

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

/bin/echo "Turning off RepeatedMsgReduction in /etc/rsyslog.conf"
/bin/sed -i 's/RepeatedMsgReduction\ on/RepeatedMsgReduction\ off/' /etc/rsyslog.conf
/etc/init.d/rsyslog restart

/bin/grep -A 1 'time.sleep(0\.1)' /usr/bin/fail2ban-client |/bin/grep beautifier > /dev/null
if [ $? -ne 0 ]; then
	/bin/sed -i -e s,beautifier\.setInputCmd\(c\),'time.sleep\(0\.1\)\n\t\t\tbeautifier.setInputCmd\(c\)', /usr/bin/fail2ban-client
else
	/bin/echo '   time.sleep(0.1) already added to /usr/bin/fail2ban-client'
fi

/etc/init.d/freeswitch start
/etc/init.d/fail2ban restart

/bin/echo "     fail2ban for ssh enabled by default"
/bin/echo "     Default is 3 failures before your IP gets blocked for 600 seconds"
/bin/echo "      SEE http://wiki.freeswitch.org/wiki/Fail2ban"

/bin/echo
/bin/echo "logrotate not happy with FS: see http://wiki.fusionpbx.com/index.php?title=RotateFSLogs doing differently now..."
/bin/echo "       SEE: /etc/cron.daily/freeswitch_log_rotation"
	
/bin/cat > /etc/cron.daily/freeswitch_log_rotation <<'DELIM'
#!/bin/bash
# logrotate replacement script
# put in /etc/cron.daily
# don't forget to make it executable
# you might consider changing /etc/freeswitch/autoload_configs/logfile.conf.xml
#  <param name="rollover" value="0"/>

#number of days of logs to keep
NUMBERDAYS=30
FS_BIN="/usr/bin/freeswitch"
FS_LOG="/var/log/freeswitch"

$FS_BIN/fs_cli -x "fsctl send_sighup" |grep '+OK' >/tmp/rotateFSlogs
if [ $? -eq 0 ]; then
       #-cmin 2 could bite us (leave some files uncompressed, eg 11M auto-rotate). Maybe -1440 is better?
       find $FS_LOG/ -name "freeswitch.log.*" -cmin -2 -exec gzip {} \;
       find $FS_LOG/ -name "freeswitch.log.*.gz" -mtime +$NUMBERDAYS -exec /bin/rm {} \;
       chown www-data.www-data $FS_LOG/freeswitch.log
       chmod 660 $FS_LOG/freeswitch.log
       logger FreeSWITCH Logs rotated
       /bin/rm /tmp/rotateFSlogs
else
       logger FreeSWITCH Log Rotation Script FAILED
       mail -s '$HOST FS Log Rotate Error' root < /tmp/rotateFSlogs
       /bin/rm /tmp/rotateFSlogs
fi
DELIM

/bin/chmod 755 /etc/cron.daily/freeswitch_log_rotation

/bin/echo "Now dropping 10MB limit from FreeSWITCH"
/bin/echo "  This is so the rotation/compression part of the cron script"
/bin/echo "  will work properly."
/bin/echo "  SEE: /etc/freeswitch/autoload_configs/logfile.conf.xml"

/bin/sed /etc/freeswitch/autoload_configs/logfile.conf.xml -i -e s,\<param.*name\=\"rollover\".*value\=\"10485760\".*/\>,\<\!\-\-\<param\ name\=\"rollover\"\ value\=\"10485760\"/\>\ INSTALL_SCRIPT\-\-\>,g

#---------------------
# Add in Monit
#---------------------
/usr/bin/apt-get -y install monit

/bin/cat >> /etc/monit/monitrc/fail2ban  <<'DELIM'
check process fail2ban with pidfile /var/run/fail2ban/fail2ban.pid
	group services
	start program = "/etc/init.d/fail2ban start"
	stop  program = "/etc/init.d/fail2ban stop"
	if 5 restarts within 5 cycles then timeout
DELIM

/bin/cat >> /etc/monit/monitrc/freeswitch  <<'DELIM'
	set daemon 60
	set logfile syslog facility log_daemon
 
check process freeswitch with pidfile /var/run/freeswitch/freeswitch.pid
	start program = "/etc/init.d/freeswitch start"
	stop program = "/etc/init.d/freeswitch stop"
	if 5 restarts within 5 cycles then timeout
	if cpu > 60% for 2 cycles then alert
	if cpu > 80% for 5 cycles then alert
	if totalmem > 2000.0 MB for 5 cycles then restart
	if children > 2500 then restart
DELIM

/etc/init.d/monit restart

#---------------------
# Installing SSL Certs
#---------------------
echo "Installing ssl certs"
apt-get install -y ssl-cert
ln -s /etc/ssl/private/ssl-cert-snakeoil.key /etc/ssl/private/nginx.key
ln -s /etc/ssl/certs/ssl-cert-snakeoil.pem /etc/ssl/certs/nginx.crt

#--------------------------
#Install and configure  PHP + Nginx + sqlite3 
#--------------------------
echo "installing Php Ngingx Sqlite3"
/usr/bin/apt-get -y install sqlite3 nginx php5-cli php5-sqlite php5-odbc php-db php5-fpm \
						php5-common php5-gd php-pear php5-memcache php-apc

echo "configuring php"
PHPINIFILE="/etc/php5/fpm/php.ini"
#also exists, but www.conf used by default...
#PHPCONFFILE="/etc/php5/fpm/php-fpm.conf"
#max_children set in /etc/php5/fpm/pool.d/www.conf
PHPCONFFILE="/etc/php5/fpm/pool.d/www.conf"

# setting file upload size to 10M
echo " Seting upload size from 2m to 10 m"
/bin/sed -i -e s,"upload_max_filesize = 2M","upload_max_filesize = 10M", $PHPINIFILE

#-------------------------
#Install NGINX config file
#-------------------------
echo "installing Nginx config file"
/bin/cat > /etc/nginx/sites-available/$GUI_NAME  <<DELIM
server{
        listen 127.0.0.1:80;
        server_name 127.0.0.1;
        access_log /var/log/nginx/access.log;
        error_log /var/log/nginx/error.log;

        client_max_body_size 10M;
        client_body_buffer_size 128k;

        location / {
          root $WWW_PATH/$GUI_NAME;
          index index.php;
        }

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php5-fpm.sock;
            #fastcgi_pass /var/run/php5-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param   SCRIPT_FILENAME $WWW_PATH/$GUI_NAME\$fastcgi_script_name;
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
        server_name $GUI_NAME;
        if (\$uri !~* ^.*provision.*$) {
                rewrite ^(.*) https://\$host\$1 permanent;
                break;
        }
        access_log /var/log/nginx/access.log;
        error_log /var/log/nginx/.error.log;

        client_max_body_size 10M;
        client_body_buffer_size 128k;

        location / {
          root $WWW_PATH/$GUI_NAME;
          index index.php;
        }

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php5-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param   SCRIPT_FILENAME $WWW_PATH/$GUI_NAME\$fastcgi_script_name;
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
        server_name $GUI_NAME;
        ssl                     on;
        ssl_certificate         /etc/ssl/certs/nginx.crt;
        ssl_certificate_key     /etc/ssl/private/nginx.key;
        ssl_protocols           SSLv3 TLSv1;
        ssl_ciphers     HIGH:!ADH:!MD5;

        access_log /var/log/nginx/access.log;
        error_log /var/log/nginx/.error.log;

        client_max_body_size 10M;
        client_body_buffer_size 128k;

        location / {
          root $WWW_PATH/$GUI_NAME;
          index index.php;
        }

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php5-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param   SCRIPT_FILENAME $WWW_PATH/$GUI_NAME\$fastcgi_script_name;
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

echo " linking nginx config from avaible to enabled sites"
/bin/ln -s /etc/nginx/sites-available/$GUI_NAME /etc/nginx/sites-enabled/$GUI_NAME

echo "disable default site"
rm -rf /etc/nginx/sites-enabled/default

echo "Restarting Nginx and PHP FPM"
/etc/init.d/php5-fpm restart
/etc/init.d/nginx restart

#-----------------
#SQLite/PGSQL
#-----------------
#Lets ask... sqlite or pgsql -- for user option only

case "$SQLITEPGSQL" in
	[Pp]*)
		/bin/echo -ne "Installing PostgeSQL"
		/usr/bin/apt-get -y install postgresql-9.1 php5-pgsql
		
		/bin/su -l postgres -c "/usr/bin/createuser -s -e $GUI_NAME"
		#/bin/su -l postgres -c "/usr/bin/createdb -E UTF8 -O $GUI_NAME $GUI_NAME"
		/bin/su -l postgres -c "/usr/bin/createdb -E UTF8 -T template0 -O $GUI_NAME $GUI_NAME"
		PGSQLPASSWORD="dummy"
		PGSQLPASSWORD2="dummy2"
		while [ $PGSQLPASSWORD != $PGSQLPASSWORD2 ]; do
		/bin/echo
		/bin/echo
		/bin/echo "THIS PROBABLY ISN'T THE MOST SECURE THING TO DO."
		/bin/echo "IT IS; HOWEVER, AUTOMATED. WE ARE STORING THE PASSWORD"
		/bin/echo "AS A BASH VARIABLE, AND USING ECHO TO PIPE IT TO"
		/bin/echo "psql. THE COMMAND USED IS:"
		/bin/echo
		/bin/echo "/bin/su -l postgres -c \"/bin/echo 'ALTER USER $GUI_NAME with PASSWORD \$PGSQLPASSWORD;' | psql $GUI_NAME\""
		/bin/echo
		/bin/echo "AFTERWARDS WE OVERWRITE THE VARIABLE WITH RANDOM DATA"
		/bin/echo
		/bin/echo "The pgsql username is $GUI_NAME"
		/bin/echo "The pgsql database name is $GUI_NAME"
		/bin/echo "Please provide a password for the $GUI_NAME user"
		#/bin/stty -echo
		read -s -p "  Password: " PGSQLPASSWORD
		/bin/echo
		/bin/echo "Let's repeat that"
		read -s -p "  Password: " PGSQLPASSWORD2
		/bin/echo
		#/bin/stty echo
		done

		/bin/su -l postgres -c "/bin/echo \"ALTER USER $GUI_NAME with PASSWORD '$PGSQLPASSWORD';\" | /usr/bin/psql $GUI_NAME"
		/bin/echo "overwriting pgsql password variable with random data"
		PGSQLPASSWORD=$(/usr/bin/head -c 512 /dev/urandom)
		PGSQLPASSWORD2=$(/usr/bin/head -c 512 /dev/urandom)

			#nginx is installed.
			/etc/init.d/php5-fpm restart
			/etc/init.d/nginx restart
			
		/bin/echo "Now you'll need to manually finish the install and come back"
		/bin/echo "  This way I can finish up the last bit of permissions issues"
		/bin/echo "  Just go to"
		/bin/echo '  http://'`/sbin/ifconfig eth0 | /bin/grep 'inet addr:' | /usr/bin/cut -d: -f2 | /usr/bin/awk '{ print $1}'`
		/bin/echo "       MAKE SURE YOU CHOOSE PostgreSQL as your Database on the first page!!!"
		/bin/echo "       ON the Second Page:"
		/bin/echo "          Database Name: $GUI_NAME"
		/bin/echo "          Database Username: $GUI_NAME"
		/bin/echo "          Database Password: whateveryouentered"
		/bin/echo "          Database Username: Leave_Blank (remove pgsql)"
		/bin/echo "          Create Database Password: Leave_Blank"
		/bin/echo 
		/bin/echo "  I will wait here until you get done with that."
		/bin/echo -ne "  When PostgreSQL is configured come back and press enter. "
		read
	;;

	*)
	
		/bin/echo "SQLITE is chosen. already done. nothing left to install..."
		#nginx is installed.
		/etc/init.d/php5-fpm restart
		/etc/init.d/nginx restart
	
esac

#-------------------
#Install FusionPBX
#-------------------
echo "Installing Fusionpbx pkg"
WWW_PATH=/usr/share/nginx/www/
GUI_NAME=fusionpbx

if [ $DO_INST_FPBX_STABLE == "y" ]; then
	/usr/bin/apt-get -y --force-yes install fusionpbx
else
	/usr/bin/apt-get -y --force-yes install fusionpbx-dev 
fi

ln -s /etc/ssl/certs/nginx.crt $WWW_PATH/$GUI_NAME/$HOSTNAME.crt

/bin/echo "setting FusionPBX owned by www-data.www-data just in case"
	if [ -e $WWW_PATH/$GUI_NAME ]; then
		/bin/chown -R www-data.www-data $WWW_PATH/$GUI_NAME
	fi

/etc/init.d/php5-fpm restart
/etc/init.d/nginx restart

#Finish Fusion Setup
/bin/echo
/bin/echo "  Waiting on you to finish installation (via browser), I'll clean up"
/bin/echo -ne "  the last bit of permissions when you finish."
/bin/echo "Waiting on $WWW_PATH/$GUI_NAME/includes/config.php"
while [ ! -e $WWW_PATH/$GUI_NAME/includes/config.php ]
do
	/bin/echo -ne '.'
	sleep 1
done
#
/bin/echo
/bin/echo "$WWW_PATH/$GUI_NAME/includes/config.php Found!"
/bin/echo "   Waiting 5 more seconds to be sure. "
SLEEPTIME=0
while [ "$SLEEPTIME" -lt 5 ]
do
	/bin/echo -ne '.'
	sleep 1
	let "SLEEPTIME = $SLEEPTIME + 1"
done

#-----------------
#extra scripts
#-----------------
echo
echo " installing Extra scripts"
echo
echo "Installing  vpn scripts"
cd /usr/src
wget --no-check-certificat https://dl.dropbox.com/u/152504/voyage/vpn-scripts/confgen \
						https://dl.dropbox.com/u/152504/voyage/vpn-scripts/genclient.sh \
						https://dl.dropbox.com/u/152504/voyage/vpn-scripts/genserver.sh
chmod +x confgen genclient.sh genserver.sh
mv confgen /usr/local/bin
mv genclient.sh /usr/local/bin
mv genserver.sh /usr/local/bin

# Shell menu
echo
echo "installing debian shell menu"
cd /usr/src
wget --no-check-certificat https://dl.dropbox.com/u/152504/debian/menu.debian.sh

#install menu
chmod +x menu.debian.sh 
mv menu.debian.sh /usr/bin

#enable menu for next boot
cat << EOF >> /etc/profile
/usr/bin/menu.debian.sh
EOF

apt-get clean

/bin/echo
/bin/echo
/bin/echo "Installation Completed.  Now configure FreeSWITCH via the FusionPBX browser interface"
/bin/echo
/bin/echo '  http://'`/sbin/ifconfig eth0 | /bin/grep 'inet addr:' | /usr/bin/cut -d: -f2 | /usr/bin/awk '{ print $1}'`
/bin/echo " Default login is (whatever you picked in the GUI install):"
/bin/echo "  User: WhateverUsernameYouPicked"
/bin/echo "  Passwd: YourPasswordYouPicked"
/bin/echo
/bin/echo " Please reboot your system"
