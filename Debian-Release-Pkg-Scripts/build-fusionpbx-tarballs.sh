#!/bin/bash
# Mon Aug 18, 2014 Time: 12:30 CST

#clean  up old src
rm -rf /usr/src/fusionpbx
rm /usr/src/*.tgz
rm /usr/src/*.md5

# Select if to build stable/devel pkgs
BUILD_RELEASE_TARBALL="n"

if [[ $BUILD_RELEASE_TARBALL == "y" ]]; then
SVN_SRC=http://fusionpbx.googlecode.com/svn/trunk/fusionpbx
SRC_DIR=fusionpbx
WRK_DIR=/usr/src
VERSION=3.6.3-19-Release
else
SVN_SRC=http://fusionpbx.googlecode.com/svn/branches/dev/fusionpbx
SRC_DIR=fusionpbx-devel
WRK_DIR=/usr/src
VERSION=3.7.1-62-Devel
fi

svn export $SVN_SRC $WRK_DIR/$SRC_DIR

cd $WRK_DIR

tar czvf fusionpbx-"$VERSION".tgz $SRC_DIR

md5sum fusionpbx-$VERSION.tgz > fusionpbx-$VERSION.md5

cp $WRK_DIR/fusionpbx-$VERSION.tgz /home/repo/fusionpbx-tarballs
cp $WRK_DIR/fusionpbx-$VERSION.md5 /home/repo/fusionpbx-tarballs
