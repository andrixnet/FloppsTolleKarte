#!/bin/bash

D=.deploy
rm -rf $D
mkdir -p $D/css
mkdir -p $D/img
mkdir -p $D/js
mkdir -p $D/lang

JS=(
    js/conversion.js
    js/cookies.js
    js/geographiclib.js
    js/geolocation.js
    js/coordinates.js
    js/lines.js
    js/markers.js
    js/map.js
    js/okapi.js
    js/tracking.js
    js/ui.js
    js/lang.js
    js/jquery.ajax-cross-origin.js)

IMG=(
    img/avatar.jpg
    img/base.png
    img/favicon.png
    img/projection.png
    img/cachetype-1.png
    img/cachetype-2.png
    img/cachetype-3.png
    img/cachetype-4.png
    img/cachetype-5.png
    img/cachetype-6.png
    img/cachetype-7.png
    img/cachetype-8.png
    img/cachetype-9.png
    img/cachetype-10.png
    img/nsg.png)

LNG=(
    lang/info.de.html
    lang/info.en.html
    lang/info.ro.html
    lang/de.json
    lang/en.json
    lang/nl.json
    lang/ro.json)

ROOT=(
    img/apple-touch-icon.png
    php/download.php
    php/proxy.php
    private/google7caa54246d4da45f.html
    private/BingSiteAuth.xml
    static/.htaccess
    static/maintenance.html)

sed "s/TSTAMP/$(date +%s)/g" php/map.php > $D/map.php
cp -a ${ROOT[@]} $D/
sass scss/main.scss > $D/css/main.css
cat ${JS[@]} | yui-compressor --type js -o $D/js/compressed.js
cp -a ${IMG[@]} $D/img/
cp -a ${LNG[@]} $D/lang/

L=.local
mkdir -p $L
# jquery cookie
if [ -d $L/jquery-cookie/.git ] ; then
    (cd $L/jquery-cookie/ ; git pull origin master)
else
    (cd $L ; git clone https://github.com/carhartl/jquery-cookie.git)
fi
cp $L/jquery-cookie/src/jquery.cookie.js $D/js


# upload!
if [[ "$@" = *uberspace* ]]; then 
    SERVER=flopp@grus.uberspace.de
    BASE=html/map
    scp -r ${D}/* $SERVER:$BASE
else
    source ~/.server.data
    lftp -u $LOGIN:$PASSWD $SERVER -e "mirror -v -R ${D} $BASE ; quit"
    lftp -u $LOGIN:$PASSWD $SERVER -e "mirror -v -R ${D}/img $BASE/img ; quit"
    lftp -u $LOGIN:$PASSWD $SERVER -e "mirror -v -R ${D}/js $BASE/js ; quit"
    lftp -u $LOGIN:$PASSWD $SERVER -e "mirror -v -R ${D}/lang $BASE/lang ; quit"
fi
