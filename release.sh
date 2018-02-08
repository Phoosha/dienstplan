#!/bin/sh

OWD=$PWD
TMP=`mktemp -d`
COMMIT=`git rev-parse --short HEAD`

export NODE_ENV=production

git archive --format=tar $@ | ( cd $TMP && tar -xf - ) || exit 1
cp package-lock.json composer.lock $TMP

if test "x$NOREUSE" = "x"; then
    cp -r node_modules $TMP
    npm update || exit 2
else
    npm install || exit 2
fi

cd $TMP

npm run prod || exit 3

rm -rf node_modules

zip -9 $OWD/../dienstplan-${COMMIT}.zip -r * .[^.]*

cd $OWD; rm -rf $TMP
