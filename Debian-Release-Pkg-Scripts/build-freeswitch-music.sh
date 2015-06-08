#!/bin/bash
REPO1="/home/repo/freeswitch-armhf/release/debian"
REPO2="/home/repo/freeswitch-armhf/head/debian"
REPO3="/home/repo/freeswitch/head/debian"

WORKDIR="/usr/src"

rm -rf /usr/src/freeswitch-moh
rm -rf /usr/src/debs-freeswitch-moh

git clone https://github.com/traviscross/freeswitch-sounds.git "$WORKDIR"/freeswitch-moh

/bin/sed -i "$WORKDIR"/freeswitch-moh/debian/freeswitch-music-default.changelog -e s,1.0.8-2,1.0.51-1,
/bin/sed -i "$WORKDIR"/freeswitch-moh/debian/freeswitch-music-default.changelog -e s,unstable,stable,

cd  "$WORKDIR"/freeswitch-moh
time ./debian/bootstrap.sh -p freeswitch-music-default
time ./debian/rules get-orig-source
/bin/tar -xv --strip-components=1 -f *_*.orig.tar.xz && mv *_*.orig.tar.xz ../
dpkg-buildpackage -uc -us -Zxz -z9 -sa

cd /usr/src

mkdir debs-freeswitch-moh

mv *.deb debs-freeswitch-moh
mv *.changes debs-freeswitch-moh
mv *.xz debs-freeswitch-moh
mv *.dsc debs-freeswitch-moh

cp -rp "$WORKDIR"/debs-freeswitch-moh/* "$REPO1"/incoming
cp -rp "$WORKDIR"/debs-freeswitch-moh/* "$REPO2"/incoming
cp -rp "$WORKDIR"/debs-freeswitch-moh/* "$REPO3"/incoming

cd "$REPO1" && ./import-new-pkgs.sh
cd "$REPO2" && ./import-new-pkgs.sh
cd "$REPO3" && ./import-new-pkgs.sh


