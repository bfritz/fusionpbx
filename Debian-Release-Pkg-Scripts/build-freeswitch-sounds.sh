#!/bin/bash

#Build Canada English Sounds
rm -rf /usr/src/freeswitch-sounds
svn co https://fusionpbx.googlecode.com/svn/branches/dev/Debian-Devel-Pkg-Scripts/freeswitch-sounds /usr/src/freeswitch-sounds
cd  /usr/src/freeswitch-sounds
/bin/sed -i debian/freeswitch-sounds-en-ca-june.changelog -e s,unstable,stable,
time ./debian/bootstrap.sh -p freeswitch-sounds-en-ca-june
time ./debian/rules get-orig-source
/bin/tar -xv --strip-components=1 -f *_*.orig.tar.xz && mv *_*.orig.tar.xz ../
dpkg-buildpackage -uc -us -Zxz -z9


#Build US English Sounds
rm -rf /usr/src/freeswitch-sounds
svn co https://fusionpbx.googlecode.com/svn/branches/dev/Debian-Devel-Pkg-Scripts/freeswitch-sounds /usr/src/freeswitch-sounds
cd  /usr/src/freeswitch-sounds
/bin/sed -i debian/freeswitch-sounds-en-us-callie.changelog -e s,unstable,stable,
time ./debian/bootstrap.sh -p freeswitch-sounds-en-us-callie
time ./debian/rules get-orig-source
/bin/tar -xv --strip-components=1 -f *_*.orig.tar.xz && mv *_*.orig.tar.xz ../
dpkg-buildpackage -uc -us -Zxz -z9

#Build Canada French Sounds
rm -rf /usr/src/freeswitch-sounds
svn co https://fusionpbx.googlecode.com/svn/branches/dev/Debian-Devel-Pkg-Scripts/freeswitch-sounds /usr/src/freeswitch-sounds
cd  /usr/src/freeswitch-sounds
/bin/sed -i debian/freeswitch-sounds-fr-ca-june.changelog -e s,unstable,stable,
time ./debian/bootstrap.sh -p freeswitch-sounds-fr-ca-june
time ./debian/rules get-orig-source
/bin/tar -xv --strip-components=1 -f *_*.orig.tar.xz && mv *_*.orig.tar.xz ../
dpkg-buildpackage -uc -us -Zxz -z9

#Build Brazil Portgause Sounds
rm -rf /usr/src/freeswitch-sounds
svn co https://fusionpbx.googlecode.com/svn/branches/dev/Debian-Devel-Pkg-Scripts/freeswitch-sounds /usr/src/freeswitch-sounds
cd  /usr/src/freeswitch-sounds
/bin/sed -i debian/freeswitch-sounds-pt-br-karina.changelog -e s,unstable,stable,
time ./debian/bootstrap.sh -p freeswitch-sounds-pt-br-karina
time ./debian/rules get-orig-source
/bin/tar -xv --strip-components=1 -f *_*.orig.tar.xz && mv *_*.orig.tar.xz ../
dpkg-buildpackage -uc -us -Zxz -z9

#Build Russian Russian Sounds
rm -rf /usr/src/freeswitch-sounds
svn co https://fusionpbx.googlecode.com/svn/branches/dev/Debian-Devel-Pkg-Scripts/freeswitch-sounds /usr/src/freeswitch-sounds
cd  /usr/src/freeswitch-sounds
/bin/sed -i debian/freeswitch-sounds-ru-ru-elena.changelog -e s,unstable,stable,
time ./debian/bootstrap.sh -p freeswitch-sounds-ru-ru-elena
time ./debian/rules get-orig-source
/bin/tar -xv --strip-components=1 -f *_*.orig.tar.xz && mv *_*.orig.tar.xz ../
dpkg-buildpackage -uc -us -Zxz -z9

#Build Swedish Sounds
rm -rf /usr/src/freeswitch-sounds
svn co https://fusionpbx.googlecode.com/svn/branches/dev/Debian-Devel-Pkg-Scripts/freeswitch-sounds /usr/src/freeswitch-sounds
cd  /usr/src/freeswitch-sounds
/bin/sed -i debian/freeswitch-sounds-sv-se-jakob.changelog -e s,unstable,stable,
time ./debian/bootstrap.sh -p freeswitch-sounds-sv-se-jakob
time ./debian/rules get-orig-source
/bin/tar -xv --strip-components=1 -f *_*.orig.tar.xz && mv *_*.orig.tar.xz ../
dpkg-buildpackage -uc -us -Zxz -z9

#Build Cantones Sounds
rm -rf /usr/src/freeswitch-sounds
svn co https://fusionpbx.googlecode.com/svn/branches/dev/Debian-Devel-Pkg-Scripts/freeswitch-sounds /usr/src/freeswitch-sounds
cd  /usr/src/freeswitch-sounds
/bin/sed -i debian/freeswitch-sounds-zh-cn-sinmei.changelog -e s,unstable,stable,
time ./debian/bootstrap.sh -p freeswitch-sounds-zh-cn-sinmei
time ./debian/rules get-orig-source
/bin/tar -xv --strip-components=1 -f *_*.orig.tar.xz && mv *_*.orig.tar.xz ../
dpkg-buildpackage -uc -us -Zxz -z9

#Build Mandarin Sounds
rm -rf /usr/src/freeswitch-sounds
svn co https://fusionpbx.googlecode.com/svn/branches/dev/Debian-Devel-Pkg-Scripts/freeswitch-sounds /usr/src/freeswitch-sounds
cd  /usr/src/freeswitch-sounds
/bin/sed -i debian/freeswitch-sounds-zh-hk-sinmei.changelog -e s,unstable,stable,
time ./debian/bootstrap.sh -p freeswitch-sounds-zh-hk-sinmei
time ./debian/rules get-orig-source
/bin/tar -xv --strip-components=1 -f *_*.orig.tar.xz && mv *_*.orig.tar.xz ../
dpkg-buildpackage -uc -us -Zxz -z9

cd /usr/src

mkdir debs-freeswitch-sounds

mv *.deb debs-freeswitch-sounds
mv *.changes debs-freeswitch-sounds
mv *.xz debs-freeswitch-sounds
mv *.dsc debs-freeswitch-sounds


#rm -rf /usr/src/debs-freeswitch-sounds

