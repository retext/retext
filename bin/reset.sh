#!/bin/bash

ENV=$1
USER=$2
if [[ $USER == "" ]];
then
    USER=`whoami`
fi

app/console --env=$ENV cache:clear
app/console --env=$ENV doctrine:mongodb:generate:hydrators
app/console --env=$ENV doctrine:mongodb:generate:proxies
app/console --env=$ENV doctrine:mongodb:generate:repositories RetextApiBundle
app/console --env=$ENV doctrine:mongodb:schema:drop
app/console --env=$ENV doctrine:mongodb:schema:create
# Broken, atm
# app/console --env=$ENV cache:warm
chown -R $USER: app/cache/$ENV
if [ ! -d app/logs ]; then mkdir app/logs; fi;
if [ ! -f app/logs/$ENV.log ]; then touch app/logs/$ENV.log; fi;
chown $USER: app/logs/$ENV.log
