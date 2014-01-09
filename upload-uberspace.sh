#!/bin/bash

SERVER=flopp@grus.uberspace.de
BASE=html/map


rm -rf deploy
mkdir -p deploy
mkdir -p deploy/css
mkdir -p deploy/img
mkdir -p deploy/js
mkdir -p deploy/lang
mkdir -p deploy/lib
mkdir -p deploy/ext/jquery-cookie

S=$(date +%s)
sed "s/TSTAMP/$S/g" map-template.php > deploy/map.php

sass css/main.scss > deploy/css/main.css
cp .htaccess download.php wartung.html google7caa54246d4da45f.html apple-touch-icon.png deploy
cp js/conversion.js js/cookies.js js/coordinates.js js/geographiclib.js js/lang.js js/map.js js/okapi.js js/tracking.js js/ui.js deploy/js
cp img/base.png img/favicon.png img/projection.png deploy/img
cp lang/info.en.html lang/info.de.html deploy/lang
cp lib/lang.php deploy/lib

# jquery cookies
if [ -d ext/jquery-cookie/.git ] ; then
    cd ext/jquery-cookie/
    git pull origin master
    cd -
else
    cd ext
    git clone https://github.com/carhartl/jquery-cookie.git
    cd -
fi
cp ext/jquery-cookie/jquery.cookie.js deploy/ext/jquery-cookie

cd deploy
tar -zcf deploy.tgz *

scp deploy.tgz $SERVER:$BASE
ssh $SERVER "cd $BASE; tar -zxf deploy.tgz"
