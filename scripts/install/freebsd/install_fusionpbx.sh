#!/bin/sh

# Set variables
PORTSDIR=/usr/ports
BASESRC=/usr/src
LOCALBASE=/usr/local
WWW_PATH="${LOCALBASE}/www"
GUI_NAME=fusionpbx

# Get Base-src and ports tree
#pkg_add -r fastest_cvsup && rehash
#csup -h `fastest_cvsup -c tld -q` -L2 /usr/share/examples/cvsup/ports-supfile
#csup -h `fastest_cvsup -c tld -q` /usr/share/examples/cvsup/standard-supfile
portsnap fetch && portsnap-extract

#help freeswitch to detect required odbc libraries
setenv LDFLAGS -L/usr/local/lib
setenv CPPFLAGS -I/usr/local/include 

# Fix a bug in freeswitch-devel
#sed -e 's|RUN_DEPENDS=    freeswitch:${PORTSDIR}/net/freeswitch-core|#RUN_DEPENDS=    freeswitch:${PORTSDIR}/net/freeswitch-core|g' ${PORTSDIR}/audio/freeswitch-music/Makefile
#sed -e 's|RUN_DEPENDS=    freeswitch:${PORTSDIR}/net/freeswitch-core|#RUN_DEPENDS=    freeswitch:${PORTSDIR}/net/freeswitch-core|g' ${PORTSDIR}/audio/freeswitch-sounds/Makefile

# Config options
#
# enable HTTP_SSL
cd ${PORTSDIR}/www/nginx && make config-recursive
# pcre - Default
# libiconv - Default

# Default
cd ${PORTSDIR}/databases/unixODBC/ && make config-recursive
cd /usr/ports/databases/libodbc++/ && make config-recursive

# Default
cd /usr/ports/graphics/tiff && make config-recursive

# enable FPM
cd ${PORTSDIR}/lang/php5 && make config-recursive

# perl - Default
# m4 - Default

# Default
cd ${PORTSDIR}/devel/m4 && make config-recursive

# php5-pdo - Default

# Default
cd ${PORTSDIR}/databases/php5-pdo_odbc && make config-recursive

# Enable VANILLA, SOUNDS, MUSIC
#cd ${PORTSDIR}/net/freeswitch-devel && make config-recursive

# freswitch-core-devel - Default
# freeswitch-sounds - enable DOWNLOAD, 8K, 16K, English
# freeswitch-music - enable 8K, 16K
# Python - Default
# Curl - Default
# GDBM - Default
# ca_root_nss - enable ETCSYMLINK

# Default
cd ${PORTSDIR}/sysutils/screen && make config-recursive

# Default
cd ${PORTSDIR}/sysutils/monit && make config-recursive

# Default
#cd ${PORTSDIR}/sysutils/logwatch && make config-recursive

# Default
#cd ${PORTSDIR}/sysutils/logrotate && make config-recursive

# Default
#cd ${PORTSDIR}/mail/ssmtp && make config-recursive

# Default
#cd ${PORTSDIR}/mail/heirloom-mailx && make config-recursive

# Default
cd ${PORTSDIR}/ports-mgmt/portmanager && make config-recursive
#portmanager -u (to upgrade ports)

#--------------------------------------------------------
# freeswitch compile
#--------------------------------------------------------
cd /usr/ports/devel/autoconf/ && make config-recursive

# gcc - disable JAVA
cd /usr/ports/lang/gcc/ && make config-recursive

# binutils - default
# gmp - default

cd /usr/ports/devel/automake/ && make config-recursive

# git - defaults
cd /usr/ports/devel/git/ && make config-recursive

#p5-IO-Socket-SSL - defaults

cd /usr/ports/devel/gmake/ && make config-recursive

cd /usr/ports/devel/libtool/ && make config-recursive

# not working
#/usr/ports/devel/ncurses/ && make config-recursive

# wget - defaults
cd /usr/ports/ftp/wget/ && make config-recursive

cd /usr/ports/devel/pkg-config/ && make config-recursive

# openssl - defaults
cd /usr/ports/security/openssl/ && make config-recursive

#--------------------------------------------------------

# Default
cd ${PORTSDIR}/net/ngrep && make config-recursive

# Default
cd ${PORTSDIR}/security/fail2ban && make config-recursive

# Default
cd ${PORTSDIR}/devel/subversion/ && make config-recursive

# SQLite3 - default
# Neon29 - default
# apr-ipv6-devrandom-gdbm-db42 - default

# Install the ports

cd ${PORTSDIR}/sysutils/screen && make install
#pkg_add -r screen

cd ${PORTSDIR}/sysutils/monit && make install
#pkg_add -r monit

#cd ${PORTSDIR}/sysutils/logwatch && make install
#cd ${PORTSDIR}/sysutils/logrotate && make install
#cd ${PORTSDIR}/mail/ssmtp && make install
#cd ${PORTSDIR}/mail/heirloom-mailx && make install

cd ${PORTSDIR}/ports-mgmt/portmanager && make install
#pkg_add -r portmanager

cd ${PORTSDIR}/net/ngrep && make install
#pkg_add -r ngrep

cd ${PORTSDIR}/security/fail2ban && make install
#pkg_add -r py27-fail2ban

#cd ${PORTSDIR}/security/bruteforceblocker && make install
#cd ${PORTSDIR}/security/sshguard && make install

# install the freeswitch-meta port 
#cd ${PORTSDIR}/net/freeswitch-devel make install

cd ${PORTSDIR}/www/nginx && make install
#pkg_add -r nginx

cd ${PORTSDIR}/databases/unixODBC/ && make install
#pkg_add -r unixODBC

cd /usr/ports/databases/postgresql90-server/ && make install
#pkg_add -r postgresql90-server

cd /usr/ports/graphics/tiff && make install

# php and php extensions
cd ${PORTSDIR}/lang/php5/ && make install
#pkg_add -r php5

cd ${PORTSDIR}/www/php5-session/ && make install
#pkg_add -r php5-session

cd ${PORTSDIR}/databases/php5-pdo/ && make install
#pkg_add -r php5-pdo

cd ${PORTSDIR}/databases/php5-pdo_sqlite/ && make install
#pkg_add -r php5-pdo_sqlite

cd ${PORTSDIR}/databases/php5-pdo_odbc/ && make install
#pkg_add -r php5-pdo_odbc

cd ${PORTSDIR}/databases/php5-pdo_pgsql/ && make install
#pkg_add -r php5-pdo_pgsql

cd ${PORTSDIR}/databases/php5-pgsql/ && make install
#pkg_add -r php5-pgsql

#cd ${PORTSDIR}/databases/php5-pdo_mysql/ && make install
#pkg_add -r php52-pdo_mysql

cd ${PORTSDIR}/devel/php5-json/ && make install
#pkg_add -r php5-json

cd ${PORTSDIR}/security/php5-openssl/ && make install
#pkg_add -r php5-openssl

cd ${PORTSDIR}/textproc/php5-simplexml/ && make install
#pkg_add -r php5-simplexml

cd ${PORTSDIR}/net/php5-sockets/ && make install
#pkg_add -r php5-sockets

#--------------------------------------------------------
# freeswitch compile
#--------------------------------------------------------
cd /usr/ports/devel/autoconf/ && make install clean
#pkg_add -r autoconf

cd /usr/ports/lang/gcc/ && make install clean
#pkg_add -r gcc

cd /usr/ports/devel/automake/ && make install clean
#pkg_add -r automake

cd /usr/ports/devel/git/ && make install clean
#pkg_add -r git && rehash

cd /usr/ports/devel/gmake/ && make install clean
#pkg_add -r gmake

cd /usr/ports/devel/libtool/ && make install clean
#pkg_add -r libtool

#/usr/ports/devel/ncurses/ && make install clean
pkg_add -r ncurses

cd /usr/ports/ftp/wget/ && make install clean
#pkg_add -r wget

cd /usr/ports/devel/pkg-config/ && make install clean
#pkg_add -r pkg-config

cd /usr/ports/security/openssl/ && make install clean
#pkg_add -r openssl

rehash
cd /usr/src
/usr/local/bin/git clone  git://git.freeswitch.org/freeswitch.git freeswitch
cd /usr/src/freeswitch
git checkout 3ef0d
./bootstrap.sh

#save the modules.conf to enable or disable desired modules. 
cat << EOF > "/usr/src/freeswitch/modules.conf"
#applications/mod_abstraction
applications/mod_avmd
#applications/mod_blacklist
applications/mod_callcenter
applications/mod_cidlookup
#applications/mod_cluechoo
applications/mod_commands
applications/mod_conference
#applications/mod_curl
applications/mod_db
applications/mod_directory
#applications/mod_distributor
applications/mod_dptools
applications/mod_easyroute
applications/mod_enum
applications/mod_esf
#applications/mod_esl
applications/mod_expr
applications/mod_fifo
#applications/mod_fsk
applications/mod_fsv
applications/mod_hash
applications/mod_httapi
#applications/mod_http_cache
#applications/mod_ladspa
applications/mod_lcr
#applications/mod_memcache
#applications/mod_mongo
applications/mod_nibblebill
#applications/mod_osp
#applications/mod_redis
#applications/mod_rss
applications/mod_sms
#applications/mod_snapshot
#applications/mod_snipe_hunt
#applications/mod_snom
#applications/mod_soundtouch
applications/mod_spandsp
#applications/mod_spy
#applications/mod_stress
applications/mod_valet_parking
#applications/mod_vmd
applications/mod_voicemail
applications/mod_voicemail_ivr
#applications/mod_random
#asr_tts/mod_cepstral
asr_tts/mod_flite
asr_tts/mod_pocketsphinx
asr_tts/mod_tts_commandline
#asr_tts/mod_unimrcp
codecs/mod_amr
#codecs/mod_amrwb
codecs/mod_bv
#codecs/mod_celt
#codecs/mod_codec2
#codecs/mod_com_g729
#codecs/mod_dahdi_codec
codecs/mod_g723_1
codecs/mod_g729
codecs/mod_h26x
codecs/mod_ilbc
#codecs/mod_isac
codecs/mod_opus
#codecs/mod_sangoma_codec
#codecs/mod_silk
#codecs/mod_siren
codecs/mod_speex
dialplans/mod_dialplan_asterisk
#dialplans/mod_dialplan_directory
dialplans/mod_dialplan_xml
#directories/mod_ldap
#endpoints/mod_alsa
endpoints/mod_dingaling
#endpoints/mod_h323
#endpoints/mod_khomp
endpoints/mod_loopback
#endpoints/mod_opal
#endpoints/mod_portaudio
endpoints/mod_rtmp
#endpoints/mod_skinny
#endpoints/mod_skypopen
endpoints/mod_sofia
#event_handlers/mod_cdr_csv
#event_handlers/mod_cdr_mongodb
#event_handlers/mod_cdr_pg_csv
event_handlers/mod_cdr_sqlite
#event_handlers/mod_erlang_event
#event_handlers/mod_event_multicast
event_handlers/mod_event_socket
#event_handlers/mod_event_zmq
#event_handlers/mod_radius_cdr
#event_handlers/mod_snmp
formats/mod_local_stream
formats/mod_native_file
#formats/mod_portaudio_stream
#formats/mod_shell_stream
#formats/mod_shout
formats/mod_sndfile
formats/mod_tone_stream
#formats/mod_vlc
#languages/mod_java
languages/mod_lua
#languages/mod_managed
#languages/mod_perl
#languages/mod_python
#languages/mod_spidermonkey
#languages/mod_yaml
loggers/mod_console
loggers/mod_logfile
loggers/mod_syslog
#say/mod_say_de
say/mod_say_en
#say/mod_say_es
#say/mod_say_fr
#say/mod_say_he
#say/mod_say_hu
#say/mod_say_it
#say/mod_say_nl
#say/mod_say_pt
#say/mod_say_ru
#say/mod_say_th
#say/mod_say_zh
#timers/mod_posix_timer
#timers/mod_timerfd
xml_int/mod_xml_cdr
#xml_int/mod_xml_curl
#xml_int/mod_xml_ldap
xml_int/mod_xml_rpc
xml_int/mod_xml_scgi

#../../libs/freetdm/mod_freetdm
#../../libs/openzap/mod_openzap

## Experimental Modules (don't cry if they're broken)
#../../contrib/mod/xml_int/mod_xml_odbc
EOF

./configure
gmake install
gmake samples
gmake sounds-install
gmake moh-install
gmake hd-sounds-install
gmake hd-moh-install

# add freswitch rc.d
cat << EOF > "${LOCALBASE}/etc/rc.d/freeswitch "
#!/bin/sh
#
# PROVIDE: freeswitch
# REQUIRE: LOGIN cleanvar
# KEYWORD: shutdown
#
# Add the following lines to /etc/rc.conf to enable freeswitch:
# freeswitch_enable:       Set it to "YES" to enable freeswitch.
#                          Default is "NO".
# freeswitch_flags:        Flags passed to freeswitch-script on startup.
#                          Default is "".
#

. /etc/rc.subr

name="freeswitch"
rcvar=${name}_enable

load_rc_config $name

: ${freeswitch_enable="NO"}
: ${freeswitch_pidfile="/usr/local/freeswitch/run/freeswitch.pid"}

start_cmd=${name}_start
stop_cmd=${name}_stop

pidfile=${freeswitch_pidfile}

freeswitch_start() {
        /usr/local/freeswitch/bin/freeswitch ${freeswitch_flags}
		echo -n "Starting FreeSWITCH: "
}

freeswitch_stop() {
        /usr/local/freeswitch/bin/freeswitch -stop
}

run_rc_command "$1"
EOF

#--------------------------------------------------------

cd ${PORTSDIR}/devel/subversion/ && make install
#pkg_add -r subversion && rehash



svn checkout http://fusionpbx.googlecode.com/svn/trunk/fusionpbx ${LOCALBASE}/www/fusionpbx
chown -R www:www ${LOCALBASE}/www/fusionpbx
chmod -R 755 ${LOCALBASE}/www/fusionpbx

# Instal spawn fast cgi - alternative to php fpm
#cd /usr/ports/www/spawn-fcgi/ && make install clean
#pkg_add -r spawn-fcgi
#echo "spawn_fcgi_enable=\"YES\"" >> /etc/rc.conf

#
#cd ${PORTSDIR}/www/fusionpbx
#make config
#make install clean
#
cd /root
#
# Generate ssl keys for nginx
mkdir -p /etc/ssl/www
cd /etc/ssl/www
openssl req -new -x509 -nodes -out nginx.crt -keyout nginx.key
chmod 640 /etc/ssl/www/*
#
cd /root
#
## Install custom configs for fail2ban and nginx nginx-https
#
mkdir -p ${LOCALBASE}/etc/nginx/
mkdir -p /var/log/nginx/
rm ${LOCALBASE}/etc/nginx/nginx.conf
 
cat << EOF > "${LOCALBASE}/etc/nginx/nginx.conf"
#user  nobody;
worker_processes  1;

#error_log  logs/error.log;
#error_log  logs/error.log  notice;
#error_log  logs/error.log  info;

#pid        logs/nginx.pid;


events {
    worker_connections  1024;
}


http {
    include       mime.types;
    default_type  application/octet-stream;

    #log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
    #                  '$status $body_bytes_sent "$http_referer" '
    #                  '"$http_user_agent" "$http_x_forwarded_for"';

    #access_log  logs/access.log  main;

    sendfile        on;
    #tcp_nopush     on;

    #keepalive_timeout  0;
    keepalive_timeout  65;

    #gzip  on;

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
			fastcgi_pass 127.0.0.1:9000;
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
		error_log /var/log/nginx/error.log;

		client_max_body_size 10M;
		client_body_buffer_size 128k;

		location / {
		  root $WWW_PATH/$GUI_NAME;
		  index index.php;
		}

		location ~ \.php$ {
			fastcgi_pass 127.0.0.1:9000;
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
		ssl_certificate         /etc/ssl/www/nginx.crt;
		ssl_certificate_key     /etc/ssl/www/nginx.key;
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
			fastcgi_pass 127.0.0.1:9000;
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
}
EOF

#-----------------
# Locking Down  FreeSWITCH & FusionPBX
#-----------------
# Configure Monit
mkdir -p ${LOCALBASE}/etc/monit/
cat << EOF > "${LOCALBASE}/etc/monit/fail2ban"
check process fail2ban with pidfile /var/run/fail2ban/fail2ban.pid
  group services
  start program = "${LOCALBASE}/etc/rc.d/fail2ban start"
  stop  program = "${LOCALBASE}/etc/rc.d/fail2ban stop"
  if 5 restarts within 5 cycles then timeout
EOF

cat << EOF > "${LOCALBASE}/etc/monit/freeswitch"
check process freeswitch with pidfile /var/run/freeswitch/freeswitch.pid
   group daemon
   start program = "${LOCALBASE}/etc/rc.d/freeswitch start"
   stop  program = "${LOCALBASE}/etc/rc.d/freeswitch stop"

# Checks sip port on localhost, not wlways suitable
# Checks mod_event_socket on localhost. Maybe more suitable
   if failed port 8021 type TCP then restart
   if 5 restarts within 5 cycles then timeout
   depends on freeswitch_bin
   depends on freeswitch_rc

 check file freeswitch_bin with path ${LOCALBASE}/bin/freeswitch
   group daemon
   if failed checksum then unmonitor
   if failed permission 750 then unmonitor
   if failed uid freeswitch then unmonitor

#   if failed gid daemon then unmonitor

 check file freeswitch_rc with path ${LOCALBASE}/etc/rc.dfreeswitch
   group daemon
   if failed checksum then unmonitor
   if failed permission 755 then unmonitor
   if failed uid root then unmonitor
   if failed gid root then unmonitor
EOF

# Configure Fail2ban
mkdir -p ${LOCALBASE}/etc/fail2ban/action.d
cat << EOF > "${LOCALBASE}/etc/fail2ban/action.d/bsd-pf"      
[Definition]

actionstart =
actionstop =
actioncheck =
actionban = pfctl -t fail2ban-<name> -T add <ip>
actionunban = pfctl -t fail2ban-<name> -T delete <ip>
 
[Init]

port = freeswitch
localhost = 127.0.0.1
EOF    
       
cat << EOF > "${LOCALBASE}/etc/fail2ban/filter.d/$GUI_NAME.conf"
# Fail2Ban configuration file
#

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
failregex = .* FusionPBX: \[<HOST>\] authentication failed for
          = .* FusionPBX: \[<HOST>\] provision attempt bad password for

# Option:  ignoreregex
# Notes.:  regex to ignore. If this regex matches, the line is ignored.
# Values:  TEXT
#
ignoreregex =
EOF

mkdir -p ${LOCALBASE}/etc/fail2ban/filter.d
cat << EOF > "${LOCALBASE}/etc/fail2ban/filter.d/freeswitch.conf"
# Fail2Ban configuration file
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
EOF

cat << EOF > "${LOCALBASE}/etc/fail2ban/filter.d/freeswitch-dos.conf"
# Fail2Ban configuration file
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

EOF

cat << EOF >> "${LOCALBASE}/etc/fail2ban/jail.local"
[freeswitch-tcp]
enabled  = true
port     = 5060,5061,5080,5081
protocol = tcp
filter   = freeswitch
logpath  = /var/log/freeswitch/freeswitch.log
action   = bsd-pf[name=freeswitch-tcp, protocol=all]
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
action   = bsd-pf[name=freeswitch-udp, protocol=all]
maxretry = 5
findtime = 600
bantime  = 600
#          sendmail-whois[name=FreeSwitch, dest=root, sender=fail2ban@example.org] #no smtp server installed

[freeswitch-dos]
enabled = true
port = 55060,5061,5080,5081
protocol = udp
filter = freeswitch-dos
logpath = /var/log/freeswitch/freeswitch.log
action = bsd-pf[name=freeswitch-dos, protocol=all]
maxretry = 50
findtime = 30
bantime  = 6000

[fusionpbx]

enabled  = true
port     = 80,443
protocol = tcp
filter   = fusionpbx
logpath  = /var/log/auth.log
action   = bsd-pf[name=fusionpbx, protocol=all]
EOF
 
grep -A 1 'time.sleep(0\.1)' ${LOCALBASE}/bin/fail2ban-client |grep beautifier > /dev/null
if [ $? -ne 0 ]; then
        sed -i -e s,beautifier\.setInputCmd\(c\),'time.sleep\(0\.1\)\n\t\t\tbeautifier.setInputCmd\(c\)', ${LOCALBASE}/bin/fail2ban-client
        #this does slow the restart down quite a bit.
else
        #echo "time.sleep(0.1) already added to ${LOCALBASE}/bin/fail2ban-client"
fi
 
# Copy php.ini into place
cp ${LOCALBASE}/etc/php.ini-production ${LOCALBASE}/etc/php.ini
 
# Configure bruteforceblocker
#cat << EOF >> 
#
#EOF

# Configure sshguard
#cat << EOF >>
# 
#EOF
#

## Add required lines to /etc/rc.conf
#echo "logrotate_enable=\"YES\"" >> /etc/rc.conf
#echo "ssmptpd_enable\"YES\"" >> /etc/rc.conf
echo "monit_enable=\"YES\"" >> /etc/rc.conf
echo "freeswitch_enable=\"YES\"" >> /etc/rc.conf
echo "freeswitch_flags=\"-nc\"" >> /etc/rc.conf
echo "php_fpm_enable=\"YES\"" >> /etc/rc.conf
echo "nginx_enable=\"YES\"" >> /etc/rc.conf
echo "fail2ban_enable=\"YES\"" >> /etc/rc.conf
echo "postgresql_enable=\"YES\"" >> /etc/rc.conf
#
## Add user www to the freeswitch group for rw permissions
#pw usermod www -G freeswitch
#
## Cleanup
#rm -rf ${BASESRC}
#rm -rf ${PORTSDIR}
#
# Reboot System
#reboot
#
# Install Complete
echo "Install Complete"