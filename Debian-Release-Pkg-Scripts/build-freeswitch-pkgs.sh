#!/bin/bash
cores="1"
build="release" #release or head
arch="amd64" #armhf i386 amd64
fsgit="https://stash.freeswitch.org/scm/fs/freeswitch.git"
if [[ $build == "release" ]]; then
	branch="v1.4" #v1.4 or v1.5
	fstagver="1.4.17~1" #1.4.x~1 or 1.5.x~1
else
	branch="v1.5" #v1.4 or v1.5
fi
distro=`lsb_release -cs` # wheezy jessie sid `lsb_release -cs`
workdir="/usr/src/freeswitch-$branch-$build-$distro"
fssrcdir=""$workdir"/freeswitch"
# SIGN_KEY= (Optional)

#set what repo to post to
if [[ $build == "release" ]]; then
	repo="/home/repo/freeswitch/release/debian"
else
	repo="/home/repo/freeswitch/head/debian"
fi

#echo "cleaning work dir"
rm -rf "$workdir"/*

#echo "cloning freeswitch src"
if [[ $build == "release" ]]; then
	time git clone $fsgit -b$branch $fssrcdir
else
	time git clone $fsgit $fssrcdir
fi

#echo " cd to freeswitch src dir "
cd "$fssrcdir"

#echo " checking out $fstagver "
#if [[ $build == "release" ]]
#	time git checkout $fstagver
#fi

#check to see if using a custom modules.conf file
echo " building pkgs "
#if [[ -f /root/modules_1.4.conf ]]; then
#	echo " Using custom modules.conf file "
#	time ./debian/util.sh build-all -bn -z9 -a "$arch" -c "$distro" -v "$fstagver" #-f /root/modules_1.4.conf

#elif [[ -f /root/modules_1.5.conf ]]
#	echo " Using custom modules.conf file "
#	time ./debian/util.sh build-all -bn -z9 -a "$arch" -c "$distro" -v "$fstagver" #-f /root/modules_1.5.conf

#else
	time ./debian/util.sh build-all -bn -z9 -a "$arch" -c "$distro" -v "$fstagver"
#fi

cd "$workdir"
mkdir -p "$workdir"/debs-freeswitch-$branch-$distro

mv *.deb debs-freeswitch-$branch-$distro
mv *.changes debs-freeswitch-$branch-$distro
mv *.xz debs-freeswitch-$branch-$distro
mv *.dsc debs-freeswitch-$branch-$distro

cp -rp "$workdir"/debs-freeswitch-$branch-$distro/* "$repo"/incoming

cd "$repo" && ./import-new-pkgs.sh
