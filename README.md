# BattleLab Robotica live scoring app

## Overview

This project consists of an astro frontend, located in `frontend/` and a PHP
8.2 backend located in `api/`. It is to be deployed on a shared hosting server,
with the two components merged. The `deployable/` folder contains the directory
structure to be published to the web server.

## Developing

Backend code lies in `api/`. Frontend code lies in `frontend/`.

```
docker compose up
```

Unfortunately, the current setup leads to `astro build` deleting the mounted
`api/` folder because they're nested. After each frontend build, you should
restart the entire web service. oof....

## Deploying to a shared host

Requirements: `composer`, `npm`, `zip`.

```
./build.sh
```

then copy the latest `deployable-YYYY-mm-DD-HH-SS-MM.zip` to the server :)
