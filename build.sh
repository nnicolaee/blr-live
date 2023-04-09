#!/bin/bash

ts=$(date '+%Y-%m-%d-%H-%M-%S')

echo '=== Building BLR Live (' $ts ') ==='

rm -r deployable
mkdir deployable

echo ''
echo '--- Preparing backend & frontend ---'
(cd api && composer install && cd ..) &
(cd frontend && npm install && npm run build && cd ..) &
wait

echo ''
echo '--- Copying backend & frontend ---'
cp -rT frontend/dist deployable &
cp -rT api deployable/api &
wait

echo ''
echo '--- Build prepared ---'
echo 'Deploy size: ' $(du -sh deployable)

echo ''
echo '--- Zipping ---'
zf=deployable-$ts.zip
zip -r $zf deployable >/dev/null 2>/dev/null
echo 'Zipped: ' $(du -h $zf)

#echo ''
#echo '--- Building docker image blr-live:latest ---'
#docker build -t blr-live:latest . >/dev/null 2>/dev/null
