#!/bin/bash
REPO1="/usr/home/repo/release/debian"
REPO2="/usr/home/repo/head/debian"


rm -rf /usr/src/fusionpbx-moh
rm -rf /usr/src/debs-fusionpbx-moh

svn export http://fusionpbx.googlecode.com/svn/branches/dev/Debian-Devel-Pkg-Scripts/fusionpbx-moh /usr/src/fusionpbx-moh

cd  /usr/src/fusionpbx-moh
time ./debian/bootstrap.sh -p fusionpbx-music-default
time ./debian/rules get-orig-source
/bin/tar -xv --strip-components=1 -f *_*.orig.tar.xz && mv *_*.orig.tar.xz ../
dpkg-buildpackage -uc -us -Zxz -z9 -sa

cd /usr/src

mkdir debs-fusionpbx-moh

mv *.deb debs-fusionpbx-moh
mv *.changes debs-fusionpbx-moh
mv *.xz debs-fusionpbx-moh
mv *.dsc debs-fusionpbx-moh

cp -rp "$WORKDIR"/debs-fusionpbx-moh/* "$REPO1"/incoming
cp -rp "$WORKDIR"/debs-fusionpbx-moh/* "$REPO2"/incoming

cd "$REPO1" && ./import-stable-pkgs.sh
cd "$REPO2" && ./import-head-pkgs.sh

