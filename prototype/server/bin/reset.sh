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
app/console --env=$ENV cache:warm
chown -R $USER: app/cache/$ENV
