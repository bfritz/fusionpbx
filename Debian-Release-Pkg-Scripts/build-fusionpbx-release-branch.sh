#!/bin/bash

# Sun Aug 17, 2014 Time: 07:30 CST

# Select if to build stable/devel pkgs
BUILD_RELEASE_PKGS="y"

#Set Pkg Version Number for build and for changelog
PKGVER=3.6.0-1 # this is the version number you update

#Set Timestamp in the change logs
TIME=$(date +"%a, %d %b %Y %X")

if [[ $BUILD_RELEASE_PKGS == "y" ]]; then
SVN_SRC=http://fusionpbx.googlecode.com/svn/trunk
SVN_SRC_2=http://fusionpbx.googlecode.com/svn/trunk/Debian-Release-Pkg-Scripts
REPO=/usr/home/repo/deb/debian
WRK_DIR=/usr/src/fusionpbx-release pkg-build
else
SVN_SRC=http://fusionpbx.googlecode.com/svn/branches/dev
SVN_SRC_2=http://fusionpbx.googlecode.com/svn/branches/dev/Debian-Devel-Pkgs-Scripts
REPO=/usr/home/repo/deb-dev/debian
WRK_DIR=/usr/src/fusionpbx-devel-pkg-build
fi

WRKDIR=$WRK_DIR

rm -rf $WRKDIR

#get pkg system scripts
svn export $SVN_SRC_2 "$WRKDIR"

##set version in the changelog files for core
cat > $WRKDIR/fusionpbx-core/debian/changelog << DELIM
fusionpbx-core ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-core

 -- Richard Neese <r.neese@gmail.com>  $TIME -0600

DELIM

##set version in the changelog files for conf files
cat > $WRKDIR/fusionpbx-conf/debian/changelog << DELIM
fusionpbx-conf ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-conf

 -- Richard Neese <r.neese@gmail.com>  $TIME -0600

DELIM

##set version in the changelog files for scripts
cat > $WRKDIR/fusionpbx-scripts/debian/changelog << DELIM
fusionpbx-scripts ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-scripts

 -- Richard Neese <r.neese@gmail.com>  $TIME -0600

DELIM


##set version in the changelog files for sounds 
cat > $WRKDIR/fusionpbx-sounds/debian/changelog << DELIM
fusionpbx-sounds ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-sounds

 -- Richard Neese <r.neese@gmail.com>  $TIME -0600

DELIM

##set version in the changelog files for sqldb 
cat > $WRKDIR/fusionpbx-sql/debian/changelog << DELIM
fusionpbx-sqldb ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-sqldb

 -- Richard Neese <r.neese@gmail.com>  $TIME -0600

DELIM

##set version in the changelog files for provisioing templates
for i in aastra cisco grandstream linksys panasonic polycom snom yealink
do cat > $WRKDIR/fusionpbx-templates/fusionpbx-provisioning-template-"${i}"/debian/changelog << DELIM
fusionpbx-provisioning-template-${i} ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-provisioning-template-"${i//_/-}"

 -- Richard Neese <r.neese@gmail.com>  $TIME -0600

DELIM
done

#set version in the changelog files for themes
for i in enhanced minimized
do cat > $WRKDIR/fusionpbx-themes/fusionpbx-theme-"${i}"/debian/changelog << DELIM
fusionpbx-theme-${i} ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-theme-"${i//_/-}"

 -- Richard Neese <r.neese@gmail.com>  $TIME -0600

DELIM
done

#set version in the changelog files for apps
for i in adminer backup call_block call_broadcast call_center call_center_active call_flows calls \
	calls_active click_to_call conference_centers conferences conferences_active contacts content \
	destinations devices dialplan dialplan_inbound dialplan_outbound edit exec extensions fax fifo \
	fifo_list follow_me gateways hot_desking ivr_menu login log_viewer meetings modules music_on_hold \
	park provision recordings registrations ring_groups schemas services settings sipml5 sip_profiles \
	sip_status sql_query system time_conditions traffic_graph vars voicemail_greetings voicemails xml_cdr \
	xmpp
	do cat > $WRKDIR/fusionpbx-apps/fusionpbx-app-"${i//_/-}"/debian/changelog << DELIM
fusionpbx-app-${i//_/-} ($PKGVER) stable; urgency=low

  * new deb pkg for fusionpbx-app-"${i//_/-}"

 -- Richard Neese <r.neese@gmail.com>  $TIME -0600

DELIM
done

#get src for apps
for i in adminer backup call_block call_broadcast call_center call_center_active call_flows calls \
	calls_active click_to_call conference_centers conferences conferences_active contacts content \
	destinations devices dialplan dialplan_inbound dialplan_outbound edit exec extensions fax fifo \
	fifo_list follow_me gateways hot_desking ivr_menu login log_viewer meetings modules music_on_hold \
	park provision recordings registrations ring_groups schemas services settings sipml5 sip_profiles \
	sip_status sql_query system time_conditions traffic_graph vars voicemail_greetings voicemails xml_cdr \
	xmpp
do svn export "$SVN_SRC"/fusionpbx/app/"${i}" "$WRKDIR"/fusionpbx-apps/fusionpbx-app-"${i//_/-}"/"${i}"
done

#get src for extra apps
for i in sipml5
do svn export "$SVN_SRC"/apps/"${i}" "$WRKDIR"/fusionpbx-apps/fusionpbx-app-"${i//_/-}"/"${i}"
done

#get src for core
svn export --force "$SVN_SRC"/fusionpbx "$WRKDIR"/fusionpbx-core
for i in app/* themes/* resources/templates/provision resources/templates/conf resources/install/sounds resources/install/scripts resources/install/sql
do rm -rf "$WRKDIR"/fusionpbx-core/"${i}"
done

#conf dir src
svn export --force "$SVN_SRC"/fusionpbx/resources/templates/conf "$WRKDIR"/fusionpbx-conf/conf

#scripts dir src
svn export --force "$SVN_SRC"/fusionpbx/resources/install/scripts "$WRKDIR"/fusionpbx-scripts/scripts

#sounds dir src
svn export --force "$SVN_SRC"/fusionpbx/resources/install/sounds "$WRKDIR"/fusionpbx-sounds/sounds

#scripts dir src
svn export --force "$SVN_SRC"/fusionpbx/resources/install/sql "$WRKDIR"/fusionpbx-sql/sql

#phone provisioing templates
for i in aastra cisco grandstream linksys panasonic polycom snom yealink
do svn export "$SVN_SRC"/fusionpbx/resources/templates/provision/"${i}" "$WRKDIR"/fusionpbx-templates/fusionpbx-provisioning-template-"${i}"/"${i}"
done

#get src for theme
for i in enhanced minimized
do svn export "$SVN_SRC"/fusionpbx/themes/"${i}" "$WRKDIR"/fusionpbx-themes/fusionpbx-theme-"${i}"/"${i}"
done

#patch config files
#remove unused extensions from configs dir
for i in "$WRKDIR"/fusionpbx-conf/conf/directory/default/*.xml ;do rm "$i" ; done
for i in "$WRKDIR"/fusionpbx-conf/conf/directory/default/*.noload ;do rm "$i" ; done

#fix sounds dir
/bin/sed "$WRKDIR"/fusionpbx-conf/conf/autoload_configs/local_stream.conf.xml -i -e s,'<directory name="default" path="$${sounds_dir}/music/8000">','<directory name="default" path="$${sounds_dir}/music/default/8000">',g

#Adding changes to freeswitch profiles
#Enableing device login auth failures ing the sip profiles.
sed "$WRKDIR"/fusionpbx-conf/conf/sip_profiles/internal.xml -i -e s,'<param name="log-auth-failures" value="false"/>','<param name="log-auth-failures" value="true"/>',g

sed "$WRKDIR"/fusionpbx-conf/conf/sip_profiles/internal.xml -i -e s,'<!-- *<param name="log-auth-failures" value="false"/>','<param name="log-auth-failures" value="true"/>', \
				-e s,'<param name="log-auth-failures" value="false"/> *-->','<param name="log-auth-failures" value="true"/>', \
				-e s,'<!--<param name="log-auth-failures" value="false"/>','<param name="log-auth-failures" value="true"/>', \
				-e s,'<param name="log-auth-failures" value="false"/>-->','<param name="log-auth-failures" value="true"/>',g

#Build pkgs
#build app pkgs
for i in adminer backup call-block call-broadcast call-center call-center-active call-flows calls \
calls-active click-to-call conference-centers conferences conferences-active contacts content \
destinations devices dialplan dialplan-inbound dialplan-outbound edit exec extensions fax fifo \
fifo-list follow-me gateways hot-desking ivr-menu login log-viewer meetings modules music-on-hold \
park provision recordings registrations ring-groups schemas services settings sipml5 sip-profiles \
sip-status sql-query system time-conditions traffic-graph vars voicemail-greetings voicemails xml-cdr \
xmpp
do cd "$WRKDIR"/fusionpbx-apps/fusionpbx-app-"${i}" 
dpkg-buildpackage -rfakeroot -i
done

#build core pkg
cd "$WRKDIR"/fusionpbx-core
dpkg-buildpackage -rfakeroot -i

#Build conf pkg
cd "$WRKDIR"/fusionpbx-conf
dpkg-buildpackage -rfakeroot -i

#Build scripts pkg
cd "$WRKDIR"/fusionpbx-scripts
dpkg-buildpackage -rfakeroot -i

#Build sounds pkg
cd "$WRKDIR"/fusionpbx-sounds
dpkg-buildpackage -rfakeroot -i

#Build sql pkg
cd "$WRKDIR"/fusionpbx-sql
dpkg-buildpackage -rfakeroot -i

#Build provision pkg
for i in aastra cisco grandstream linksys panasonic polycom snom yealink
do cd "$WRKDIR"/fusionpbx-templates/fusionpbx-provisioning-template-"${i}"
dpkg-buildpackage -rfakeroot -i
done

#build theme pkgs
for i in enhanced minimized
do cd "$WRKDIR"/fusionpbx-themes/fusionpbx-theme-"${i}"
dpkg-buildpackage -rfakeroot -i
done

cd "$WRKDIR"
mkdir -p "$WRKDIR"/debs-fusionpbx-wheezy

for i in "$WRKDIR" "$WRKDIR"/fusionpbx-apps "$WRKDIR"/fusionpbx-themes "$WRKDIR"/fusionpbx-templates
do
mv "${i}"/*.deb "$WRKDIR"/debs-fusionpbx-wheezy
mv "${i}"/*.changes "$WRKDIR"/debs-fusionpbx-wheezy
mv "${i}"/*.gz "$WRKDIR"/debs-fusionpbx-wheezy
mv "${i}"/*.dsc "$WRKDIR"/debs-fusionpbx-wheezy
done

cp -rp "$WRKDIR"/debs-fusionpbx-wheezy/* "$REPO"/incoming

cd "$REPO" && ./import-new-pkgs.sh
